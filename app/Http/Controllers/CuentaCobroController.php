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
            $query->where(function ($q) use ($buscar) {
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

            // Manejar archivos si existen
            if ($request->has('archivos')) {
                $archivos = $request->input('archivos');

                foreach ($archivos as $index => $archivoData) {
                    if ($request->hasFile("archivos.{$index}.archivo")) {
                        $archivo = $request->file("archivos.{$index}.archivo");
                        $tipoDocumento = $archivoData['tipo_documento'] ?? 'otro';
                        $descripcion = $archivoData['descripcion'] ?? null;

                        // Validar el archivo
                        $request->validate([
                            "archivos.{$index}.archivo" => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
                        ]);

                        // Generar nombre único para el archivo
                        $nombreOriginal = $archivo->getClientOriginalName();
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreArchivo = $cuentaCobro->numero_cuenta_cobro . '_' .
                            $tipoDocumento . '_' .
                            time() . '_' . $index . '.' . $extension;

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
                            'tipo_documento' => $tipoDocumento,
                            'descripcion' => $descripcion,
                        ]);
                    }
                }
            }

            // Calcular retenciones
            $cuentaCobro->fresh()->calcularRetenciones();

            DB::commit();

            return redirect()
                ->route('cuentas-cobro.show', $cuentaCobro->id)
                ->with('success', 'Cuenta de cobro creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear cuenta de cobro: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

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
            'contrato.contratista',
            'creador',
            'items',
            'archivos.subidoPor', // Cargar archivos FTP
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
     * Subir archivo al FTP
     */
    public function subirArchivo(Request $request, $id)
    {
        $validated = $request->validate([
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
            'tipo_documento' => 'required|in:cuenta_cobro,acta_recibido,informe,foto_evidencia,planilla,soporte_pago,factura,otro',
            'descripcion' => 'nullable|string|max:500',
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
