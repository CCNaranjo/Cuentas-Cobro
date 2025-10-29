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

class CuentaCobroController extends Controller
{
    /**
     * Listar todas las cuentas de cobro
     */
    public function index(Request $request)
    {
        $query = CuentaCobro::with(['contrato', 'creador']);

        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('contrato_id')) {
            $query->where('contrato_id', $request->contrato_id);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->porPeriodo($request->fecha_inicio, $request->fecha_fin);
        }

        // Búsqueda
        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('numero_cuenta_cobro', 'like', "%{$buscar}%")
                  ->orWhere('periodo_cobrado', 'like', "%{$buscar}%");
            });
        }

        $cuentasCobro = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('cuentas_cobro.index', compact('cuentasCobro'));
    }

    /**
     * Mostrar formulario de creación
     */
public function create()
{
    $contratos = Contrato::where('estado', 'activo')
        ->with(['contratista'])
        ->get();
    
    return view('cuentas_cobro.create', compact('contratos'));
}

    /**
     * Guardar nueva cuenta de cobro
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'fecha_radicacion' => 'required|date',
            'periodo_cobrado' => 'nullable|string|max:255',
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
                'periodo_cobrado' => $validated['periodo_cobrado'],
                'valor_bruto' => 0, // Se calculará con los items
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

            // Calcular retenciones (se hace automáticamente por los eventos del modelo)
            $cuentaCobro->fresh()->calcularRetenciones();

            DB::commit();

            return redirect()
                ->route('cuentas-cobro.show', $cuentaCobro->id)
                ->with('success', 'Cuenta de cobro creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
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
        $cuentaCobro = CuentaCobro::with([
            'contrato',
            'creador',
            'items',
            'documentos',
            'historial.usuario'
        ])->findOrFail($id);

        return view('cuentas_cobro.show', compact('cuentaCobro'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $cuentaCobro = CuentaCobro::with(['items'])->findOrFail($id);

        // Solo se puede editar si está en borrador
        if ($cuentaCobro->estado !== 'borrador') {
            return back()->with('error', 'Solo se pueden editar cuentas de cobro en estado borrador');
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

        // Validar que esté en borrador
        if ($cuentaCobro->estado !== 'borrador') {
            return back()->with('error', 'Solo se pueden editar cuentas de cobro en estado borrador');
        }

        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'fecha_radicacion' => 'required|date',
            'periodo_cobrado' => 'nullable|string|max:255',
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
            // Actualizar cuenta de cobro
            $cuentaCobro->update([
                'contrato_id' => $validated['contrato_id'],
                'fecha_radicacion' => $validated['fecha_radicacion'],
                'periodo_cobrado' => $validated['periodo_cobrado'],
                'observaciones' => $validated['observaciones'],
            ]);

            // Eliminar items que no están en la actualización
            $idsEnActualizacion = collect($validated['items'])
                ->pluck('id')
                ->filter()
                ->toArray();

            $cuentaCobro->items()
                ->whereNotIn('id', $idsEnActualizacion)
                ->delete();

            // Actualizar o crear items
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Actualizar existente
                    ItemCuentaCobro::where('id', $itemData['id'])->update([
                        'descripcion' => $itemData['descripcion'],
                        'cantidad' => $itemData['cantidad'],
                        'valor_unitario' => $itemData['valor_unitario'],
                        'porcentaje_avance' => $itemData['porcentaje_avance'] ?? null,
                    ]);
                } else {
                    // Crear nuevo
                    ItemCuentaCobro::create([
                        'cuenta_cobro_id' => $cuentaCobro->id,
                        'descripcion' => $itemData['descripcion'],
                        'cantidad' => $itemData['cantidad'],
                        'valor_unitario' => $itemData['valor_unitario'],
                        'porcentaje_avance' => $itemData['porcentaje_avance'] ?? null,
                    ]);
                }
            }

            // Recalcular retenciones
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

        // Solo se puede eliminar si está en borrador
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
     * Cambiar estado de la cuenta de cobro
     */
    public function cambiarEstado(Request $request, $id)
    {
        $validated = $request->validate([
            'nuevo_estado' => 'required|in:borrador,radicada,en_revision,aprobada,rechazada,pagada,anulada',
            'comentario' => 'nullable|string',
        ]);

        $cuentaCobro = CuentaCobro::findOrFail($id);

        $resultado = $cuentaCobro->cambiarEstado(
            $validated['nuevo_estado'],
            Auth::id(),
            $validated['comentario']
        );

        if ($resultado) {
            return back()->with('success', 'Estado cambiado exitosamente');
        }

        return back()->with('error', 'No se pudo cambiar el estado');
    }

    /**
     * Subir documento soporte
     */
    public function subirDocumento(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|in:acta_recibido,informe,foto_evidencia,planilla,otro',
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