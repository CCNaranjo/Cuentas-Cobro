<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\Contrato;
use App\Models\ItemCuentaCobro;
use App\Models\CuentaCobroArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CuentaCobroController extends Controller
{
    /**
     * Listar todas las cuentas de cobro
     * SEGMENTADO POR PERMISOS
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $query = CuentaCobro::with(['contrato.contratista', 'creador']);

        // ========================================
        // SEGMENTACIÓN DE VISTAS POR PERMISOS
        // ========================================
        if ($user->tienePermiso('ver-todas-cuentas', $organizacionId)) {
            // Admin Organización, Ordenador Gasto, Supervisor, Tesorero, Revisor Contratación
            $query->whereHas('contrato', function($q) use ($organizacionId) {
                $q->where('organizacion_id', $organizacionId);
            });
        } elseif ($user->tienePermiso('ver-mis-cuentas', $organizacionId)) {
            // Contratista - Solo sus cuentas
            $query->whereHas('contrato', function($q) use ($user) {
                $q->where('contratista_id', $user->id);
            });
        } else {
            abort(403, 'No tienes permiso para ver cuentas de cobro');
        }

        // FILTROS ADICIONALES POR ROL
        $rolActual = $user->roles()
            ->wherePivot('organizacion_id', $organizacionId)
            ->wherePivot('estado', 'activo')
            ->first();

        if ($rolActual) {
            switch ($rolActual->nombre) {
                case 'supervisor':
                    // Ver solo las radicadas o en corrección de supervisor
                    if (!$request->has('estado')) {
                        $query->whereIn('estado', ['radicada', 'en_correccion_supervisor']);
                    }
                    break;
                
                case 'revisor_contratacion':
                    // Ver solo certificadas por supervisor o en corrección de contratación
                    if (!$request->has('estado')) {
                        $query->whereIn('estado', ['certificado_supervisor', 'en_correccion_contratacion']);
                    }
                    break;
                
                case 'tesorero':
                    // Ver verificadas de contratación, aprobadas y en proceso de pago
                    if (!$request->has('estado')) {
                        $query->whereIn('estado', ['verificado_contratacion', 'aprobada_ordenador', 'en_proceso_pago', 'pagada']);
                    }
                    break;
                
                case 'ordenador_gasto':
                    // Ver solo verificadas de presupuesto
                    if (!$request->has('estado')) {
                        $query->where('estado', 'verificado_presupuesto');
                    }
                    break;
            }
        }

        // Filtros del usuario
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('contrato_id')) {
            $query->where('contrato_id', $request->contrato_id);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha_radicacion', [$request->fecha_inicio, $request->fecha_fin]);
        }

        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_cuenta_cobro', 'like', "%{$buscar}%")
                  ->orWhere('periodo_inicio', 'like', "%{$buscar}%")
                  ->orWhere('periodo_fin', 'like', "%{$buscar}%");
            });
        }

        $cuentasCobro = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('cuentas_cobro.index', compact('cuentasCobro'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        if (!$user->tienePermiso('crear-cuenta-cobro', $organizacionId)) {
            abort(403, 'No tienes permiso para crear cuentas de cobro');
        }

        // Contratistas solo ven sus contratos
        if ($user->tienePermiso('ver-mis-contratos', $organizacionId)) {
            $contratos = Contrato::where('estado', 'activo')
                ->where('contratista_id', $user->id)
                ->with(['contratista'])
                ->get();
        } else {
            $contratos = Contrato::where('estado', 'activo')
                ->where('organizacion_id', $organizacionId)
                ->with(['contratista'])
                ->get();
        }

        // Pre-seleccionar contrato si viene por parámetro
        $contratoSeleccionado = null;
        if ($request->has('contrato_id')) {
            $contratoSeleccionado = $contratos->where('id', $request->contrato_id)->first();
        }
        
        return view('cuentas_cobro.create', compact('contratos', 'contratoSeleccionado'));
    }

    /**
     * Guardar nueva cuenta de cobro
     */
    public function store(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        if (!$user->tienePermiso('crear-cuenta-cobro', $organizacionId)) {
            return back()->withErrors(['error' => 'No tienes permiso para crear cuentas de cobro']);
        }

        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'fecha_radicacion' => 'required|date',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
            'observaciones' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'required|string',
            'items.*.cantidad' => 'required|numeric|min:0',
            'items.*.valor_unitario' => 'required|numeric|min:0',
            'items.*.porcentaje_avance' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Crear cuenta de cobro
            $cuentaCobro = CuentaCobro::create([
                'contrato_id' => $validated['contrato_id'],
                'numero_cuenta_cobro' => (new CuentaCobro())->generarNumero(),
                'fecha_radicacion' => $validated['fecha_radicacion'],
                'periodo_inicio' => $validated['periodo_inicio'],
                'periodo_fin' => $validated['periodo_fin'],
                'valor_bruto' => 0,
                'valor_neto' => 0,
                'estado' => 'borrador',
                'observaciones' => $validated['observaciones'],
                'created_by' => Auth::id(),
            ]);

            // Crear items
            foreach ($validated['items'] as $itemData) {
                ItemCuentaCobro::create([
                    'cuenta_cobro_id' => $cuentaCobro->id,
                    'descripcion' => $itemData['descripcion'],
                    'cantidad' => $itemData['cantidad'],
                    'valor_unitario' => $itemData['valor_unitario'],
                    'porcentaje_avance' => $itemData['porcentaje_avance'] ?? null,
                ]);
            }

            // Calcular retenciones
            $cuentaCobro->fresh()->calcularRetenciones();

            DB::commit();

            Log::info('Cuenta de cobro creada', [
                'cuenta_cobro_id' => $cuentaCobro->id,
                'creado_por' => $user->id
            ]);

            return redirect()
                ->route('cuentas-cobro.show', $cuentaCobro->id)
                ->with('success', 'Cuenta de cobro creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear cuenta de cobro', [
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error al crear la cuenta de cobro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de cuenta de cobro
     */
    public function show($id)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $cuentaCobro = CuentaCobro::with([
            'contrato.contratista',
            'contrato.supervisor',
            'creador',
            'items',
            'archivos.subidoPor', // Cargar archivos FTP
            'historial.usuario'
        ])->findOrFail($id);

        // Verificar acceso
        $contratoOrgId = $cuentaCobro->contrato->organizacion_id;
        
        if (!$user->tienePermiso('ver-todas-cuentas', $contratoOrgId)) {
            if ($user->tienePermiso('ver-mis-cuentas', $contratoOrgId)) {
                if ($cuentaCobro->contrato->contratista_id != $user->id) {
                    abort(403, 'No tienes acceso a esta cuenta de cobro');
                }
            } else {
                abort(403, 'No tienes acceso a esta cuenta de cobro');
            }
        }

        return view('cuentas_cobro.show', compact('cuentaCobro'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $cuentaCobro = CuentaCobro::with(['items', 'contrato'])->findOrFail($id);

        // Solo se puede editar si está en borrador
        $estadosPermitidos = [
            'borrador',
            'en_correccion_supervisor',
            'en_correccion_contratacion',
        ];

        // Verificamos si el estado actual NO está en la lista de estados permitidos
        if (!in_array($cuentaCobro->estado, $estadosPermitidos)) {
            return back()->with('error', 'Solo se pueden editar cuentas de cobro en estado borrador o en corrección');
        }

        $contratos = Contrato::where('estado', 'activo')->get();

        return view('cuentas_cobro.edit', compact('cuentaCobro', 'contratos'));
    }

    /**
     * Actualizar cuenta de cobro
     */
    public function update(Request $request, $id)
    {
        $cuentaCobro = CuentaCobro::findOrFail($id);

        $estadosPermitidos = [
            'borrador',
            'en_correccion_supervisor',
            'en_correccion_contratacion',
        ];

        // Verificamos si el estado actual NO está en la lista de estados permitidos
        if (!in_array($cuentaCobro->estado, $estadosPermitidos)) {
            return back()->with('error', 'Solo se pueden editar cuentas de cobro en estado borrador o en corrección');
        }

        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'fecha_radicacion' => 'required|date',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
            'observaciones' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:items_cuenta_cobro,id',
            'items.*.descripcion' => 'required|string',
            'items.*.cantidad' => 'required|numeric|min:0',
            'items.*.valor_unitario' => 'required|numeric|min:0',
            'items.*.porcentaje_avance' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $cuentaCobro->update([
                'contrato_id' => $validated['contrato_id'],
                'fecha_radicacion' => $validated['fecha_radicacion'],
                'periodo_inicio' => $validated['periodo_inicio'],
                'periodo_fin' => $validated['periodo_fin'],
                'observaciones' => $validated['observaciones'],
            ]);

            $idsEnActualizacion = collect($validated['items'])
                ->pluck('id')
                ->filter()
                ->toArray();

            $cuentaCobro->items()
                ->whereNotIn('id', $idsEnActualizacion)
                ->delete();

            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    ItemCuentaCobro::where('id', $itemData['id'])->update([
                        'descripcion' => $itemData['descripcion'],
                        'cantidad' => $itemData['cantidad'],
                        'valor_unitario' => $itemData['valor_unitario'],
                        'porcentaje_avance' => $itemData['porcentaje_avance'] ?? null,
                    ]);
                } else {
                    ItemCuentaCobro::create([
                        'cuenta_cobro_id' => $cuentaCobro->id,
                        'descripcion' => $itemData['descripcion'],
                        'cantidad' => $itemData['cantidad'],
                        'valor_unitario' => $itemData['valor_unitario'],
                        'porcentaje_avance' => $itemData['porcentaje_avance'] ?? null,
                    ]);
                }
            }

            $cuentaCobro->fresh()->calcularRetenciones();

            DB::commit();

            return redirect()
                ->route('cuentas-cobro.show', $cuentaCobro->id)
                ->with('success', 'Cuenta de cobro actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la cuenta de cobro: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar cuenta de cobro
     */
    public function destroy($id)
    {
        $cuentaCobro = CuentaCobro::findOrFail($id);

        if ($cuentaCobro->estado !== 'borrador') {
            return back()->with('error', 'Solo se pueden eliminar cuentas de cobro en estado borrador');
        }

        DB::beginTransaction();
        try {
            $cuentaCobro->delete();
            DB::commit();

            return redirect()
                ->route('cuentas-cobro.index')
                ->with('success', 'Cuenta de cobro eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la cuenta de cobro: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * CAMBIAR ESTADO - FLUJO COMPLETO
     * ========================================
     * Matriz de Transición de Estados y Permisos
     */
    public function cambiarEstado(Request $request, $id)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nuevo_estado' => 'required|in:borrador,radicada,en_correccion_supervisor,certificado_supervisor,en_correccion_contratacion,verificado_contratacion,verificado_presupuesto,aprobada_ordenador,en_proceso_pago,pagada,anulada',
            'comentario' => 'nullable|string',
        ]);

        $cuentaCobro = CuentaCobro::with('contrato')->findOrFail($id);
        $contrato = $cuentaCobro->contrato;
        $organizacionId = $contrato->organizacion_id;

        // ========================================
        // MATRIZ DE PERMISOS POR TRANSICIÓN
        // ========================================
        $transicionesPermitidas = [
            'borrador' => [
                'radicada' => 'radicar-cuenta-cobro', // Contratista
            ],
            'radicada' => [
                'certificado_supervisor' => 'revisar-cuenta-cobro', // Supervisor
                'en_correccion_supervisor' => 'rechazar-cuenta-cobro', // Supervisor
            ],
            'en_correccion_supervisor' => [
                'radicada' => 'radicar-cuenta-cobro', // Contratista corrige y re-radica
            ],
            'certificado_supervisor' => [
                'verificado_contratacion' => 'verificar-legal-cuenta-cobro', // Revisor Contratación
                'en_correccion_contratacion' => 'rechazar-cuenta-cobro', // Revisor Contratación
            ],
            'en_correccion_contratacion' => [
                'certificado_supervisor' => 'revisar-cuenta-cobro', // Supervisor re-certifica
            ],
            'verificado_contratacion' => [
                'verificado_presupuesto' => 'verificar-presupuesto-cuenta-cobro', // Tesorero (Fase 1)
            ],
            'verificado_presupuesto' => [
                'aprobada_ordenador' => 'aprobar-finalmente', // Ordenador del Gasto
            ],
            'aprobada_ordenador' => [
                'en_proceso_pago' => 'generar-ordenes-pago', // Tesorero (Fase 2)
            ],
            'en_proceso_pago' => [
                'pagada' => 'procesar-pago', // Tesorero
            ],
        ];

        // Validar que la transición sea válida
        $estadoActual = $cuentaCobro->estado;
        $nuevoEstado = $validated['nuevo_estado'];

        if (!isset($transicionesPermitidas[$estadoActual][$nuevoEstado])) {
            return back()->with('error', "Transición de estado no válida: {$estadoActual} → {$nuevoEstado}");
        }

        $permisoRequerido = $transicionesPermitidas[$estadoActual][$nuevoEstado];

        // Validar permiso
        if (!$user->tienePermiso($permisoRequerido, $organizacionId)) {
            return back()->with('error', "No tienes permiso para realizar esta acción. Permiso requerido: {$permisoRequerido}");
        }

        // VALIDACIONES ADICIONALES
        if ($nuevoEstado === 'radicada') {
            // Validar que tenga items
            if ($cuentaCobro->items()->count() === 0) {
                return back()->with('error', 'Debe agregar al menos un item antes de radicar');
            }

            // Validar saldo disponible del contrato
            $saldoDisponible = $contrato->valor_total - $contrato->valor_pagado;
            if ($cuentaCobro->valor_neto > $saldoDisponible) {
                return back()->with('error', 'El valor de la cuenta excede el saldo disponible del contrato');
            }
        }

        if ($nuevoEstado === 'verificado_presupuesto') {
            // Aquí se validaría CDP/RP (por implementar)
            // Por ahora solo validamos que exista saldo
            $saldoDisponible = $contrato->valor_total - $contrato->valor_pagado;
            if ($cuentaCobro->valor_neto > $saldoDisponible) {
                return back()->with('error', 'No hay saldo presupuestal disponible');
            }
        }

        DB::beginTransaction();
        try {
            $estadoAnterior = $cuentaCobro->estado;

            // Cambiar estado
            $resultado = $cuentaCobro->cambiarEstado(
                $nuevoEstado,
                Auth::id(),
                $validated['comentario']
            );

            if (!$resultado) {
                DB::rollBack();
                return back()->with('error', 'No se pudo cambiar el estado');
            }

            // Si se paga, actualizar valor_pagado del contrato
            if (in_array($nuevoEstado, ['pagada'])) {
                $contrato->recalcularValorPagado();
                
                Log::info('Cuenta pagada - Contrato actualizado', [
                    'contrato_id' => $contrato->id,
                    'cuenta_cobro_id' => $cuentaCobro->id,
                    'nuevo_valor_pagado' => $contrato->valor_pagado,
                ]);
            }

            // Si se revierte un pago
            if (in_array($estadoAnterior, ['pagada']) && !in_array($nuevoEstado, ['pagada'])) {
                $contrato->recalcularValorPagado();
                
                Log::info('Pago revertido - Contrato actualizado', [
                    'contrato_id' => $contrato->id,
                    'cuenta_cobro_id' => $cuentaCobro->id,
                    'nuevo_valor_pagado' => $contrato->valor_pagado
                ]);
            }

            DB::commit();

            // Mensajes personalizados según el estado
            $mensajes = [
                'radicada' => 'Cuenta de cobro radicada exitosamente. Ahora será revisada por el supervisor.',
                'certificado_supervisor' => 'Cuenta certificada. Ahora será verificada por el área de contratación.',
                'en_correccion_supervisor' => 'Cuenta devuelta al contratista para correcciones.',
                'verificado_contratacion' => 'Documentación legal verificada. Ahora será validado el presupuesto.',
                'en_correccion_contratacion' => 'Cuenta devuelta al supervisor para ajustes legales.',
                'verificado_presupuesto' => 'Presupuesto verificado. Pendiente de aprobación final del ordenador del gasto.',
                'aprobada_ordenador' => 'Cuenta aprobada. Se procederá a generar la orden de pago.',
                'en_proceso_pago' => 'Orden de pago generada. Pendiente de ejecución.',
                'pagada' => 'Pago registrado exitosamente.',
                'anulada' => 'Cuenta de cobro anulada.',
            ];

            return back()->with('success', $mensajes[$nuevoEstado] ?? 'Estado cambiado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cambiar estado de cuenta de cobro', [
                'error' => $e->getMessage(),
                'cuenta_cobro_id' => $id
            ]);

            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Subir archivo al FTP
     */
    public function subirArchivo(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|in:acta_recibido,informe,foto_evidencia,planilla,pila,formato_institucional,otro',
            'archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
        ]);

        $cuentaCobro = CuentaCobro::findOrFail($id);

        try {
            $archivo = $request->file('archivo');

            // Generar nombre único para el archivo
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $nombreArchivo = $cuentaCobro->numero_cuenta_cobro . '_' .
                $validated['tipo_documento'] . '_' .
                time() . '.' . $extension;

            // Definir directorio y ruta en el servidor FTP
            $directorio = 'cuentas_cobro/' . $cuentaCobro->contrato->organizacion_id;
            $ruta = $directorio . '/' . $nombreArchivo;

            // Crear directorios si no existen
            $this->crearDirectorioFTP($directorio);

            // Subir archivo al servidor FTP
            $contenido = file_get_contents($archivo->getRealPath());
            Storage::disk('ftp')->put($ruta, $contenido);

            // Guardar registro en la base de datos
            CuentaCobroArchivo::create([
                'cuenta_cobro_id' => $cuentaCobro->id,
                'subido_por' => Auth::id(),
                'nombre_original' => $nombreOriginal,
                'nombre_archivo' => $nombreArchivo,
                'ruta' => $ruta,
                'tipo_archivo' => $extension,
                'mime_type' => $archivo->getMimeType(),
                'tamaño' => $archivo->getSize(),
                'tipo_documento' => $validated['tipo_documento'],
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            return back()->with('success', 'Archivo subido exitosamente');

        } catch (\Exception $e) {
            \Log::error('Error al subir archivo: ' . $e->getMessage());
            return back()->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Crear directorios en FTP si no existen
     */
    private function crearDirectorioFTP($ruta)
    {
        $directorios = explode('/', $ruta);
        $rutaActual = '';

        foreach ($directorios as $directorio) {
            $rutaActual .= $directorio;

            if (!Storage::disk('ftp')->exists($rutaActual)) {
                try {
                    Storage::disk('ftp')->makeDirectory($rutaActual);
                } catch (\Exception $e) {
                    \Log::warning("No se pudo crear directorio FTP: {$rutaActual}. Error: " . $e->getMessage());
                }
            }

            $rutaActual .= '/';
        }
    }

    /**
     * Descargar archivo del FTP
     */
    public function descargarArchivo($archivoId)
    {
        try {
            $archivo = CuentaCobroArchivo::findOrFail($archivoId);
            $cuentaCobro = $archivo->cuentaCobro;

            // Verificar permisos (puedes agregar lógica adicional aquí)
            $user = Auth::user();

            // Descargar del servidor FTP
            if (!Storage::disk('ftp')->exists($archivo->ruta)) {
                return back()->with('error', 'El archivo no existe en el servidor');
            }

            $contenido = Storage::disk('ftp')->get($archivo->ruta);

            return response($contenido)
                ->header('Content-Type', $archivo->mime_type)
                ->header('Content-Disposition', 'attachment; filename="' . $archivo->nombre_original . '"');

        } catch (\Exception $e) {
            \Log::error('Error al descargar archivo: ' . $e->getMessage());
            return back()->with('error', 'Error al descargar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar archivo del FTP
     */
    public function eliminarArchivo($id, $archivoId)
    {
        try {
            $archivo = CuentaCobroArchivo::where('cuenta_cobro_id', $id)
                ->where('id', $archivoId)
                ->firstOrFail();

            $cuentaCobro = $archivo->cuentaCobro;

            // Solo permitir eliminar en borrador
            if ($cuentaCobro->estado !== 'borrador') {
                return back()->with('error', 'Solo se pueden eliminar archivos de cuentas en borrador');
            }

            // El modelo se encarga de eliminar del FTP mediante el evento boot
            $archivo->delete();

            return back()->with('success', 'Archivo eliminado exitosamente');

        } catch (\Exception $e) {
            \Log::error('Error al eliminar archivo: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }
}
