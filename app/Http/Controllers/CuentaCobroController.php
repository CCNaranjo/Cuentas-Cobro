<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroArchivo;
use App\Models\Contrato;
use App\Models\ItemCuentaCobro;
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
            $query->whereHas('contrato', function ($q) use ($organizacionId) {
                $q->where('organizacion_id', $organizacionId);
            });
        } elseif ($user->tienePermiso('ver-mis-cuentas', $organizacionId)) {
            // Contratista - Solo sus cuentas
            $query->whereHas('contrato', function ($q) use ($user) {
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
            'archivos' => 'nullable|array',
            'archivos.*.archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'archivos.*.tipo_documento' => 'required|in:cuenta_cobro,acta_recibido,informe,foto_evidencia,planilla,soporte_pago,factura,otro',
            'archivos.*.descripcion' => 'nullable|string',
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
            $valorBruto = 0;
            foreach ($validated['items'] as $itemData) {
                $item = ItemCuentaCobro::create([
                    'cuenta_cobro_id' => $cuentaCobro->id,
                    'descripcion' => $itemData['descripcion'],
                    'cantidad' => $itemData['cantidad'],
                    'valor_unitario' => $itemData['valor_unitario'],
                    'porcentaje_avance' => $itemData['porcentaje_avance'] ?? null,
                ]);

                // Calcular valor bruto acumulado
                $valorBruto += $itemData['cantidad'] * $itemData['valor_unitario'];
            }

            // Actualizar valor bruto
            $cuentaCobro->update(['valor_bruto' => $valorBruto]);

            // Calcular retenciones
            $cuentaCobro->fresh()->calcularRetenciones();

            // Procesar archivos adjuntos
            if ($request->has('archivos')) {
                Log::info('Procesando archivos adjuntos', [
                    'count' => count($request->archivos),
                    'cuenta_cobro_id' => $cuentaCobro->id
                ]);

                foreach ($request->archivos as $index => $archivoData) {
                    try {
                        $archivo = $archivoData['archivo'];
                        $nombreOriginal = $archivo->getClientOriginalName();
                        $extension = $archivo->getClientOriginalExtension();

                        // Formatear el nombre del archivo
                        $numeroCuenta = $cuentaCobro->numero_cuenta_cobro;
                        $tipoDocumento = $archivoData['tipo_documento'];
                        $timestamp = time();
                        $indice = $index;

                        $nombreArchivo = "{$numeroCuenta}_{$tipoDocumento}_{$timestamp}_{$indice}.{$extension}";

                        // Crear directorio
                        $directorio = 'cuentas_cobro/' . $cuentaCobro->id;
                        $ruta = $directorio . '/' . $nombreArchivo;

                        Log::info('Subiendo archivo a FTP', [
                            'ruta' => $ruta,
                            'nombre_archivo' => $nombreArchivo,
                            'tamaño' => $archivo->getSize(),
                            'cuenta_cobro_id' => $cuentaCobro->id
                        ]);

                        // Verificar configuración FTP
                        if (!Storage::disk('ftp')) {
                            throw new \Exception('Disco FTP no configurado correctamente');
                        }

                        // Crear directorio si no existe
                        if (!Storage::disk('ftp')->exists($directorio)) {
                            Storage::disk('ftp')->makeDirectory($directorio);
                        }

                        // Subir archivo al FTP
                        $subido = Storage::disk('ftp')->put($ruta, fopen($archivo->getRealPath(), 'r+'));

                        if (!$subido) {
                            throw new \Exception('No se pudo subir el archivo al FTP');
                        }

                        Log::info('Archivo subido exitosamente al FTP', ['ruta' => $ruta]);

                        // Crear registro en cuenta_cobro_archivos
                        CuentaCobroArchivo::create([
                            'cuenta_cobro_id' => $cuentaCobro->id,
                            'subido_por' => Auth::id(),
                            'nombre_original' => $nombreOriginal,
                            'nombre_archivo' => $nombreArchivo,
                            'ruta' => $ruta,
                            'tipo_archivo' => $extension,
                            'mime_type' => $archivo->getMimeType(),
                            'tamaño' => $archivo->getSize(),
                            'tipo_documento' => $archivoData['tipo_documento'],
                            'descripcion' => $archivoData['descripcion'] ?? null,
                        ]);

                        Log::info('Registro creado en cuenta_cobro_archivos', [
                            'nombre_original' => $nombreOriginal,
                            'nombre_archivo' => $nombreArchivo,
                            'ruta' => $ruta
                        ]);

                    } catch (\Exception $e) {
                        Log::error('Error al procesar archivo ' . $index, [
                            'error' => $e->getMessage(),
                            'archivo' => $nombreOriginal ?? 'desconocido',
                            'cuenta_cobro_id' => $cuentaCobro->id
                        ]);

                        // Continuar con otros archivos pero registrar el error
                        continue;
                    }
                }
            } else {
                Log::info('No hay archivos para procesar', ['cuenta_cobro_id' => $cuentaCobro->id]);
            }

            DB::commit();

            Log::info('Cuenta de cobro creada exitosamente', [
                'cuenta_cobro_id' => $cuentaCobro->id,
                'creado_por' => $user->id,
                'items_count' => count($validated['items']),
                'archivos_count' => $request->has('archivos') ? count($request->archivos) : 0
            ]);

            // Para requests AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cuenta de cobro creada exitosamente',
                    'redirect' => route('cuentas-cobro.show', $cuentaCobro->id)
                ]);
            }

            return redirect()
                ->route('cuentas-cobro.show', $cuentaCobro->id)
                ->with('success', 'Cuenta de cobro creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear cuenta de cobro', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id
            ]);

            // Para requests AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la cuenta de cobro: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error al crear la cuenta de cobro: ' . $e->getMessage());
        }
    }

    /**
     * Método temporal para debug de archivos
     */
    public function debugArchivos(Request $request)
    {
        Log::info('Debug archivos recibidos', [
            'todos_los_datos' => $request->all(),
            'archivos_recibidos' => $request->has('archivos') ? count($request->archivos) : 0,
            'files' => $request->file() ? array_keys($request->file()) : []
        ]);

        return response()->json([
            'request_all' => $request->all(),
            'files_count' => $request->has('archivos') ? count($request->archivos) : 0,
            'files_structure' => $request->file()
        ]);
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
            'archivos.subidoPor',
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

            return redirect()->route('cuentas-cobro.show', $id)
                ->with('success', 'Cuenta de cobro actualizada exitosamente.');

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
     * Descargar archivo de cuenta de cobro
     */
    public function descargarArchivo($cuentaCobroId, $archivoId)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        // Buscar el archivo
        $archivo = CuentaCobroArchivo::with('cuentaCobro.contrato')->findOrFail($archivoId);

        // Verificar que el archivo pertenece a la cuenta de cobro
        if ($archivo->cuenta_cobro_id != $cuentaCobroId) {
            abort(404, 'Archivo no encontrado');
        }

        // Verificar permisos
        $organizacionId = $archivo->cuentaCobro->contrato->organizacion_id;

        if (!$user->tienePermiso('ver-todas-cuentas', $organizacionId)) {
            if ($user->tienePermiso('ver-mis-cuentas', $organizacionId)) {
                if ($archivo->cuentaCobro->contrato->contratista_id != $user->id) {
                    abort(403, 'No tienes acceso a este archivo');
                }
            } else {
                abort(403, 'No tienes acceso a este archivo');
            }
        }

        try {
            // Verificar que el archivo existe en el FTP
            if (!Storage::disk('ftp')->exists($archivo->ruta)) {
                abort(404, 'Archivo no encontrado en el servidor');
            }

            // Obtener el archivo del FTP
            $fileContent = Storage::disk('ftp')->get($archivo->ruta);

            // Headers para la descarga
            $headers = [
                'Content-Type' => $archivo->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $archivo->nombre_original . '"',
            ];

            return response($fileContent, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error al descargar archivo', [
                'archivo_id' => $archivo->id,
                'error' => $e->getMessage(),
                'ruta' => $archivo->ruta
            ]);

            abort(404, 'Error al descargar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Subir documento adicional a cuenta de cobro existente
     */
    public function subirArchivo(Request $request, $cuentaCobroId)
    {
        // Buscar la cuenta de cobro
        $cuentaCobro = CuentaCobro::findOrFail($cuentaCobroId);

        $estadosPermitidos = ['borrador', 'en_correccion_supervisor', 'en_correccion_contratacion'];
        if (!in_array($cuentaCobro->estado, $estadosPermitidos)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden agregar documentos en el estado actual de la cuenta de cobro'
                ], 403);
            }
            return back()->with('error', 'No se pueden agregar documentos en el estado actual');
        }

        $validated = $request->validate([
            'archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'tipo_documento' => 'required|in:cuenta_cobro,acta_recibido,informe,foto_evidencia,planilla,soporte_pago,factura,otro',
            'descripcion' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $archivo = $validated['archivo'];
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();

            $numeroCuenta = $cuentaCobro->numero_cuenta_cobro;
            $tipoDocumento = $validated['tipo_documento'];
            $timestamp = time();
            $random = uniqid();

            $nombreArchivo = "{$numeroCuenta}_{$tipoDocumento}_{$timestamp}_{$random}.{$extension}";
            $directorio = 'cuentas_cobro/' . $cuentaCobro->id;
            $ruta = $directorio . '/' . $nombreArchivo;

            Log::info('Subiendo archivo a FTP desde edición', [
                'ruta' => $ruta,
                'cuenta_cobro_id' => $cuentaCobro->id
            ]);

            if (!Storage::disk('ftp')->exists($directorio)) {
                Storage::disk('ftp')->makeDirectory($directorio);
            }

            $subido = Storage::disk('ftp')->put($ruta, fopen($archivo->getRealPath(), 'r+'));

            if (!$subido) {
                throw new \Exception('No se pudo subir el archivo al FTP');
            }

            $nuevoArchivo = CuentaCobroArchivo::create([
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

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Archivo subido exitosamente'
                ]);
            }

            return back()->with('success', 'Archivo subido exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al subir archivo', [
                'error' => $e->getMessage(),
                'cuenta_cobro_id' => $cuentaCobro->id
            ]);

            return back()->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar archivo de cuenta de cobro
     */
    public function eliminarArchivo($cuentaCobroId, $archivoId)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        $archivo = CuentaCobroArchivo::with('cuentaCobro.contrato')->findOrFail($archivoId);

        if ($archivo->cuenta_cobro_id != $cuentaCobroId) {
            abort(404, 'Archivo no encontrado');
        }

        $organizacionId = $archivo->cuentaCobro->contrato->organizacion_id;

        $puedeEliminar = $user->tienePermiso('ver-todas-cuentas', $organizacionId) ||
            $archivo->subido_por == $user->id;

        if (!$puedeEliminar) {
            abort(403, 'No tienes permiso para eliminar este archivo');
        }

        $estadosPermitidos = ['borrador', 'en_correccion_supervisor', 'en_correccion_contratacion'];
        if (!in_array($archivo->cuentaCobro->estado, $estadosPermitidos)) {
            return back()->with('error', 'Solo se pueden eliminar archivos de cuentas en estado editable');
        }

        DB::beginTransaction();
        try {
            if (Storage::disk('ftp')->exists($archivo->ruta)) {
                Storage::disk('ftp')->delete($archivo->ruta);
            }

            $archivo->delete();
            DB::commit();

            return back()->with('success', 'Archivo eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar archivo', [
                'archivo_id' => $archivo->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al eliminar el archivo');
        }
    }
}