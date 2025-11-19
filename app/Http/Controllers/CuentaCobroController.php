<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\Contrato;
use App\Models\ItemCuentaCobro;
use App\Models\DocumentoSoporte;
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
            $query->where(function($q) use ($buscar) {
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
            $contratos = Contrato::where('contratista_id', $user->id)
                ->where('estado', 'activo')
                ->get();
        } else {
            $contratos = Contrato::where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->get();
        }

        return view('cuentas_cobro.create', compact('contratos'));
    }

    /**
     * Guardar nueva cuenta de cobro
     */
    public function store(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'fecha_radicacion' => 'nullable|date',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
            'observaciones' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'required|string',
            'items.*.cantidad' => 'required|numeric|min:0',
            'items.*.valor_unitario' => 'required|numeric|min:0',
            'items.*.porcentaje_avance' => 'nullable|numeric|min:0|max:100',
        ]);

        $contrato = Contrato::findOrFail($validated['contrato_id']);

        if ($contrato->organizacion_id != $organizacionId) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $numero = (new CuentaCobro())->generarNumero();

            $cuentaCobro = CuentaCobro::create([
                'contrato_id' => $validated['contrato_id'],
                'numero_cuenta_cobro' => $numero,
                'fecha_radicacion' => $validated['fecha_radicacion'],
                'periodo_inicio' => $validated['periodo_inicio'],
                'periodo_fin' => $validated['periodo_fin'],
                'observaciones' => $validated['observaciones'],
                'estado' => 'borrador',
                'created_by' => $user->id,
            ]);

            $valorBruto = 0;

            foreach ($validated['items'] as $item) {
                $itemCobro = ItemCuentaCobro::create([
                    'cuenta_cobro_id' => $cuentaCobro->id,
                    'descripcion' => $item['descripcion'],
                    'cantidad' => $item['cantidad'],
                    'valor_unitario' => $item['valor_unitario'],
                    'porcentaje_avance' => $item['porcentaje_avance'] ?? 0,
                ]);

                $valorBruto += $item['cantidad'] * $item['valor_unitario'];
            }

            $retencionFuente = $valorBruto * ($contrato->porcentaje_retencion_fuente / 100);
            $estampilla = $valorBruto * ($contrato->porcentaje_estampilla / 100);
            $valorNeto = $valorBruto - $retencionFuente - $estampilla;

            $cuentaCobro->update([
                'valor_bruto' => $valorBruto,
                'valor_neto' => $valorNeto,
                'retencion_fuente' => $retencionFuente,
                'estampilla' => $estampilla,
            ]);

            DB::commit();

            return redirect()->route('cuentas-cobro.show', $cuentaCobro->id)
                ->with('success', 'Cuenta de cobro creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la cuenta de cobro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle
     */
    public function show($id)
    {
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $cuentaCobro = CuentaCobro::with([
            'contrato.contratista',
            'contrato.supervisor',
            'items',
            'documentos',
            'historial.usuario'
        ])->findOrFail($id);

        if ($cuentaCobro->contrato->organizacion_id != $organizacionId) {
            abort(403);
        }

        return view('cuentas_cobro.show', compact('cuentaCobro'));
    }

        /**
     * Formulario edición (borrador o en corrección)
     */
    public function edit($id)
    {
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $cuentaCobro = CuentaCobro::with(['contrato', 'items'])->findOrFail($id);

        // Permitir edición en borrador o estados de corrección
        if (!in_array($cuentaCobro->estado, ['borrador', 'en_correccion_supervisor', 'en_correccion_contratacion']) || 
            $cuentaCobro->contrato->organizacion_id != $organizacionId ||
            $cuentaCobro->contrato->contratista_id != $user->id) {  // Solo el contratista puede editar en corrección
            abort(403, 'No puedes editar esta cuenta de cobro');
        }

        $contratos = Contrato::where('contratista_id', $user->id)->where('estado', 'activo')->get();

        return view('cuentas_cobro.edit', compact('cuentaCobro', 'contratos'));
    }

    /**
     * Actualizar cuenta (borrador o en corrección)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'fecha_radicacion' => 'nullable|date',
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

        $cuentaCobro = CuentaCobro::findOrFail($id);

        // Permitir actualización en borrador o estados de corrección
        if (!in_array($cuentaCobro->estado, ['borrador', 'en_correccion_supervisor', 'en_correccion_contratacion']) || 
            $cuentaCobro->contrato->organizacion_id != $organizacionId ||
            $cuentaCobro->contrato->contratista_id != $user->id) {
            abort(403, 'No puedes actualizar esta cuenta de cobro');
        }

        $contrato = Contrato::findOrFail($validated['contrato_id']);

        DB::beginTransaction();
        try {
            $cuentaCobro->update([
                'contrato_id' => $validated['contrato_id'],
                'fecha_radicacion' => $validated['fecha_radicacion'],
                'periodo_inicio' => $validated['periodo_inicio'],
                'periodo_fin' => $validated['periodo_fin'],
                'observaciones' => $validated['observaciones'],
            ]);

            $itemIds = [];

            $valorBruto = 0;

            foreach ($validated['items'] as $itemData) {
                $item = ItemCuentaCobro::updateOrCreate(
                    ['id' => $itemData['id'] ?? null, 'cuenta_cobro_id' => $cuentaCobro->id],
                    [
                        'descripcion' => $itemData['descripcion'],
                        'cantidad' => $itemData['cantidad'],
                        'valor_unitario' => $itemData['valor_unitario'],
                        'porcentaje_avance' => $itemData['porcentaje_avance'] ?? 0,
                    ]
                );

                $itemIds[] = $item->id;

                $valorBruto += $itemData['cantidad'] * $itemData['valor_unitario'];
            }

            ItemCuentaCobro::where('cuenta_cobro_id', $cuentaCobro->id)
                ->whereNotIn('id', $itemIds)
                ->delete();

            $retencionFuente = $valorBruto * ($contrato->porcentaje_retencion_fuente / 100);
            $estampilla = $valorBruto * ($contrato->porcentaje_estampilla / 100);
            $valorNeto = $valorBruto - $retencionFuente - $estampilla;

            $cuentaCobro->update([
                'valor_bruto' => $valorBruto,
                'valor_neto' => $valorNeto,
                'retencion_fuente' => $retencionFuente,
                'estampilla' => $estampilla,
            ]);

            DB::commit();

            return redirect()->route('cuentas-cobro.show', $cuentaCobro->id)
                ->with('success', 'Cuenta de cobro actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar cuenta (borrador)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $cuentaCobro = CuentaCobro::findOrFail($id);

        if ($cuentaCobro->estado != 'borrador' || $cuentaCobro->contrato->organizacion_id != $organizacionId) {
            abort(403);
        }

        $cuentaCobro->delete();

        return redirect()->route('cuentas-cobro.index')
            ->with('success', 'Cuenta de cobro eliminada');
    }

    /**
     * Cambiar estado de cuenta
     */
    public function cambiarEstado(Request $request, $id)
    {
        $validated = $request->validate([
            'nuevo_estado' => 'required|in:radicada,certificado_supervisor,en_correccion_supervisor,verificado_contratacion,en_correccion_contratacion,verificado_presupuesto,aprobada_ordenador,en_proceso_pago,pagada,anulada',
            'comentario' => 'nullable|string',
        ]);

        $cuentaCobro = CuentaCobro::findOrFail($id);
        $contrato = $cuentaCobro->contrato;

        $estadoAnterior = $cuentaCobro->estado;
        $nuevoEstado = $validated['nuevo_estado'];

        // Validar transiciones específicas
        // Por ejemplo, desde en_correccion_* solo permitir a radicada si es el contratista
        if (str_starts_with($estadoAnterior, 'en_correccion_') && $nuevoEstado === 'radicada') {
            $user = Auth::user();
            if ($contrato->contratista_id !== $user->id) {
                return back()->with('error', 'Solo el contratista puede radicar nuevamente una cuenta en corrección');
            }
        }

        // Otras validaciones de transiciones (agrega según tu lógica de flujo)

        DB::beginTransaction();
        try {
            $resultado = $cuentaCobro->cambiarEstado(
                $nuevoEstado,
                Auth::id(),
                $validated['comentario']
            );

            if (!$resultado) {
                DB::rollBack();
                return back()->with('error', 'No se pudo cambiar el estado');
            }

            // Si se aprueba por ordenador, preparar para pago (pero no cambiar aquí, ya que OP se crea separado)

            DB::commit();

            return back()->with('success', 'Estado cambiado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Subir documento soporte
     */
    public function subirDocumento(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|in:acta_recibido,informe,foto_evidencia,planilla,pila,formato_institucional,otro',
            'archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
        ]);

        $cuentaCobro = CuentaCobro::findOrFail($id);

        try {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $ruta = $archivo->storeAs('cuentas_cobro/' . $cuentaCobro->id, $nombreArchivo, 'public');

            DocumentoSoporte::create([
                'cuenta_cobro_id' => $cuentaCobro->id,
                'tipo_documento' => $validated['tipo_documento'],
                'nombre_archivo' => $nombreArchivo,
                'ruta_archivo' => $ruta,
                'tamano_kb' => round($archivo->getSize() / 1024),
            ]);

            return back()->with('success', 'Documento subido exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar documento soporte
     */
    public function eliminarDocumento($id, $documentoId)
    {
        $documento = DocumentoSoporte::where('cuenta_cobro_id', $id)
            ->where('id', $documentoId)
            ->firstOrFail();

        try {
            $documento->delete();
            return back()->with('success', 'Documento eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }
}