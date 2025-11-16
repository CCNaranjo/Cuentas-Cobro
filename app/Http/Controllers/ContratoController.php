<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\ContratoArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContratoController extends Controller
{
    /**
     * Listar contratos según rol
     */
    public function index(Request $request)
    {
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');
        $user = Auth::user();

        $query = Contrato::with(['organizacion', 'contratista', 'supervisor'])
            ->where('organizacion_id', $organizacionId);

        // Filtrar según rol y permisos
        if (!$user->tienePermiso('ver-todos-contratos', $organizacionId)) {
            if ($user->tienePermiso('ver-mis-contratos', $organizacionId)) {
                $query->where(function ($q) use ($user) {
                    $q->where('contratista_id', $user->id)
                        ->orWhere('supervisor_id', $user->id);
                });
            } else {
                abort(403, 'No tienes permiso para ver contratos');
            }
        }

        // Filtros adicionales
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_contrato', 'LIKE', "%{$search}%")
                    ->orWhere('objeto_contractual', 'LIKE', "%{$search}%")
                    ->orWhereHas('contratista', function ($q2) use ($search) {
                        $q2->where('nombre', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('contratista_id')) {
            $query->where('contratista_id', $request->contratista_id);
        }

        $contratos = $query->orderBy('created_at', 'desc')->paginate(15);

        $organizacion = Organizacion::find($organizacionId);

        // Contratistas y supervisores para filtros
        $contratistas = Usuario::whereHas('contratosComoContratista', function ($q) use ($organizacionId) {
            $q->where('organizacion_id', $organizacionId);
        })->get();

        $supervisores = Usuario::whereHas('contratosComoSupervisor', function ($q) use ($organizacionId) {
            $q->where('organizacion_id', $organizacionId);
        })->get();

        return view('contratos.index', compact('contratos', 'organizacion', 'contratistas', 'supervisores'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(Request $request)
    {
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');
        $organizacion = Organizacion::findOrFail($organizacionId);

        // Obtener supervisores y contratistas
        $supervisores = Usuario::where('estado', 'activo')->get();
        $contratistas = Usuario::where('estado', 'activo')->get();

        return view('contratos.create', compact('organizacion', 'supervisores', 'contratistas'));
    }

    /**
     * Guardar nuevo contrato
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_contrato' => 'required|string|unique:contratos,numero_contrato',
            'organizacion_id' => 'required|exists:organizaciones,id',
            'supervisor_id' => 'required|exists:usuarios,id',
            'contratista_id' => 'nullable|exists:usuarios,id',
            'objeto_contractual' => 'required|string|max:1000',
            'valor_total' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'porcentaje_retencion_fuente' => 'required|numeric|min:0|max:100',
            'porcentaje_estampilla' => 'required|numeric|min:0|max:100',
            'estado' => 'required|in:borrador,activo,suspendido',
        ]);

        $validated['vinculado_por'] = Auth::id();

        // Si se asigna contratista, cambiar estado a activo
        if (!empty($validated['contratista_id']) && $validated['estado'] == 'borrador') {
            $validated['estado'] = 'activo';
        }

        DB::beginTransaction();
        try {
            // Crear el contrato
            $contrato = Contrato::create($validated);

            // Manejar archivos si existen
            if ($request->has('archivos')) {
                $archivos = $request->input('archivos');

                foreach ($archivos as $index => $archivoData) {
                    if ($request->hasFile("archivos.{$index}.archivo")) {
                        $archivo = $request->file("archivos.{$index}.archivo");
                        $tipoDocumento = $archivoData['tipo_documento'] ?? 'otro';
                        $descripcion = $archivoData['descripcion'] ?? null;

                        // Validar el archivo
                        $archivoValidado = $request->validate([
                            "archivos.{$index}.archivo" => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
                        ]);

                        // Generar nombre único para el archivo
                        $nombreOriginal = $archivo->getClientOriginalName();
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreArchivo = $contrato->numero_contrato . '_' .
                            $tipoDocumento . '_' .
                            time() . '_' . $index . '.' . $extension;

                        // Definir ruta en el servidor FTP
                        $ruta = 'contratos/' . $contrato->organizacion_id . '/' . $nombreArchivo;

                        // Subir archivo al servidor FTP
                        $contenido = file_get_contents($archivo->getRealPath());
                        Storage::disk('ftp')->put($ruta, $contenido);

                        // Guardar registro en la base de datos
                        ContratoArchivo::create([
                            'contrato_id' => $contrato->id,
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

            DB::commit();

            return redirect()->route('contratos.show', $contrato)
                ->with('success', 'Contrato creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear contrato: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return back()->with('error', 'Error al crear el contrato: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mostrar detalle del contrato
     */
    public function show(Contrato $contrato)
    {
        $contrato->load(['organizacion', 'contratista', 'supervisor', 'vinculadoPor']);

        // Verificar permisos
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = $contrato->organizacion_id;

        if (
            !$user->tienePermiso('ver-todos-contratos', $organizacionId) &&
            $contrato->contratista_id != $user->id &&
            $contrato->supervisor_id != $user->id
        ) {
            abort(403, 'No tienes acceso a este contrato');
        }

        // Calcular estadísticas
        $estadisticas = [
            'valor_cobrado' => 0, // Por implementar cuando tengas cuentas de cobro
            'valor_disponible' => $contrato->valor_total,
            'porcentaje_ejecucion' => 0,
        ];

        return view('contratos.show', compact('contrato', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Contrato $contrato)
    {
        // Verificar permisos de edición
        $user = Auth::user();
        $organizacionId = $contrato->organizacion_id;

        if (!$user->tienePermiso('editar-contrato', $organizacionId)) {
            abort(403, 'No tienes permiso para editar contratos');
        }

        // Obtener supervisores y contratistas
        $supervisores = Usuario::where('estado', 'activo')->get();
        $contratistas = Usuario::where('estado', 'activo')->get();

        return view('contratos.edit', compact('contrato', 'supervisores', 'contratistas'));
    }

    /**
     * Actualizar contrato
     */
    public function update(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'numero_contrato' => 'required|string|unique:contratos,numero_contrato,' . $contrato->id,
            'supervisor_id' => 'required|exists:usuarios,id',
            'contratista_id' => 'nullable|exists:usuarios,id',
            'objeto_contractual' => 'required|string|max:1000',
            'valor_total' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'porcentaje_retencion_fuente' => 'required|numeric|min:0|max:100',
            'porcentaje_estampilla' => 'required|numeric|min:0|max:100',
            'estado' => 'required|in:borrador,activo,terminado,suspendido',
        ]);

        $contrato->update($validated);

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato actualizado exitosamente');
    }

    /**
     * Vincular contratista al contrato
     */
    public function vincularContratista(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'contratista_id' => 'required|exists:usuarios,id',
        ]);

        // Verificar que el contrato no tenga contratista
        if ($contrato->contratista_id) {
            return back()->with('error', 'Este contrato ya tiene un contratista asignado');
        }

        $contratista = Usuario::findOrFail($validated['contratista_id']);

        // Actualizar contrato
        $contrato->update([
            'contratista_id' => $validated['contratista_id'],
            'estado' => 'activo',
        ]);

        return back()->with('success', 'Contratista vinculado exitosamente');
    }

    /**
     * Buscar usuarios para vincular como contratista
     */
    public function buscarContratista(Request $request)
    {
        $search = $request->input('q');

        $usuarios = Usuario::where(function ($query) use ($search) {
            $query->where('nombre', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('documento_identidad', 'LIKE', "%{$search}%");
        })
            ->where('estado', 'activo')
            ->limit(10)
            ->get(['id', 'nombre', 'email', 'documento_identidad']);

        return response()->json($usuarios);
    }

    /**
     * Cambiar supervisor del contrato
     */
    public function cambiarSupervisor(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:usuarios,id',
        ]);

        // Verificar que el nuevo supervisor tiene el rol correcto
        $supervisor = Usuario::findOrFail($validated['supervisor_id']);

        $contrato->update(['supervisor_id' => $validated['supervisor_id']]);

        return back()->with('success', 'Supervisor actualizado exitosamente');
    }

    /**
     * Cambiar estado del contrato
     */
    public function cambiarEstado(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'estado' => 'required|in:activo,suspendido,terminado,liquidado',
            'observaciones' => 'nullable|string',
        ]);

        $contrato->update(['estado' => $validated['estado']]);

        // Aquí puedes agregar lógica para registrar en historial o notificar

        return back()->with('success', 'Estado del contrato actualizado exitosamente');
    }

    /**
     * Subir archivo del contrato
     */
    public function subirArchivo(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // Max 10MB
            'tipo_documento' => 'required|in:contrato_firmado,adicion,suspension,acta_inicio,acta_liquidacion,otro',
            'descripcion' => 'nullable|string|max:500',
        ]);

        try {
            $archivo = $request->file('archivo');

            // Generar nombre único para el archivo
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $nombreArchivo = $contrato->numero_contrato . '_' .
                $validated['tipo_documento'] . '_' .
                time() . '.' . $extension;

            // Definir ruta en el servidor FTP
            $ruta = 'contratos/' . $contrato->organizacion_id . '/' . $nombreArchivo;

            // Subir archivo al servidor FTP
            $contenido = file_get_contents($archivo->getRealPath());
            Storage::disk('ftp')->put($ruta, $contenido);

            // Guardar registro en la base de datos
            $contratoArchivo = ContratoArchivo::create([
                'contrato_id' => $contrato->id,
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
            return back()->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Descargar archivo del contrato
     */
    public function descargarArchivo(ContratoArchivo $archivo)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $contrato = $archivo->contrato;

            if (
                !$user->tienePermiso('ver-todos-contratos', $contrato->organizacion_id) &&
                $contrato->contratista_id != $user->id &&
                $contrato->supervisor_id != $user->id
            ) {
                abort(403, 'No tienes permiso para descargar este archivo');
            }

            // Descargar del servidor FTP
            if (!Storage::disk('ftp')->exists($archivo->ruta)) {
                return back()->with('error', 'El archivo no existe en el servidor');
            }

            $contenido = Storage::disk('ftp')->get($archivo->ruta);

            return response($contenido)
                ->header('Content-Type', $archivo->mime_type)
                ->header('Content-Disposition', 'attachment; filename="' . $archivo->nombre_original . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al descargar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar archivo del contrato
     */
    public function eliminarArchivo(ContratoArchivo $archivo)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $contrato = $archivo->contrato;

            if (!$user->tienePermiso('eliminar-archivo-contrato', $contrato->organizacion_id)) {
                abort(403, 'No tienes permiso para eliminar archivos');
            }

            // Eliminar (el modelo se encarga de eliminar del FTP mediante el evento boot)
            $archivo->delete();

            return back()->with('success', 'Archivo eliminado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }
}
