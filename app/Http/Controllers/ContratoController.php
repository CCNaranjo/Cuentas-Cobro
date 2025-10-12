<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Usuario;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratoController extends Controller
{
    /**
     * Listar contratos según rol
     */
    public function index(Request $request)
    {
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');
        $user = Auth::user();
        dd(get_class($user));

        $query = Contrato::with(['organizacion', 'contratista', 'supervisor']);

        // Filtrar según rol
        if ($user->tienePermiso('ver-todos-contratos', $organizacionId)) {
            $query->where('organizacion_id', $organizacionId);
        } elseif ($user->tienePermiso('ver-mis-contratos', $organizacionId)) {
            $query->where(function($q) use ($user) {
                $q->where('contratista_id', $user->id)
                  ->orWhere('supervisor_id', $user->id);
            });
        } else {
            abort(403, 'No tienes permiso para ver contratos');
        }

        // Filtros adicionales
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('contratista_id')) {
            $query->where('contratista_id', $request->contratista_id);
        }

        $contratos = $query->orderBy('created_at', 'desc')->paginate(15);

        $organizacion = Organizacion::find($organizacionId);

        // Contratistas y supervisores para filtros
        $contratistas = Usuario::whereHas('contratosComoContratista')->get();
        $supervisores = Usuario::whereHas('contratosComoSupervisor')->get();

        return view('contratos.index', compact('contratos', 'organizacion', 'contratistas', 'supervisores'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(Request $request)
    {
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');
        $organizacion = Organizacion::findOrFail($organizacionId);

        // Obtener supervisores de la organización
        $supervisores = Usuario::whereHas('roles', function($query) use ($organizacionId) {
                $query->where('nombre', 'supervisor')
                      ->wherePivot('organizacion_id', $organizacionId)
                      ->wherePivot('estado', 'activo');
            })
            ->get();

        return view('contratos.create', compact('organizacion', 'supervisores'));
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
            'objeto_contractual' => 'required|string',
            'valor_total' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'porcentaje_retencion_fuente' => 'required|numeric|min:0|max:100',
            'porcentaje_estampilla' => 'required|numeric|min:0|max:100',
        ]);

        $validated['estado'] = 'borrador';
        $validated['vinculado_por'] = Auth::user()->id;

        $contrato = Contrato::create($validated);

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato creado exitosamente. Ahora puedes vincular un contratista.');
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
        if (!$user->tienePermiso('ver-todos-contratos', $contrato->organizacion_id) &&
            $contrato->contratista_id != $user->id &&
            $contrato->supervisor_id != $user->id) {
            abort(403, 'No tienes acceso a este contrato');
        }

        // Calcular estadísticas (cuando se implemente cuentas de cobro)
        $estadisticas = [
            'valor_cobrado' => 0, // $contrato->valorCobrado(),
            'valor_disponible' => $contrato->valor_total, // $contrato->valorDisponible(),
            'porcentaje_ejecucion' => 0, // $contrato->porcentajeEjecucion(),
        ];

        return view('contratos.show', compact('contrato', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Contrato $contrato)
    {
        // Solo editable en estado borrador
        if ($contrato->estado != 'borrador') {
            return back()->withErrors(['error' => 'Solo se pueden editar contratos en borrador']);
        }

        $supervisores = Usuario::whereHas('roles', function($query) use ($contrato) {
                $query->where('nombre', 'supervisor')
                      ->wherePivot('organizacion_id', $contrato->organizacion_id)
                      ->wherePivot('estado', 'activo');
            })
            ->get();

        return view('contratos.edit', compact('contrato', 'supervisores'));
    }

    /**
     * Actualizar contrato
     */
    public function update(Request $request, Contrato $contrato)
    {
        if ($contrato->estado != 'borrador') {
            return back()->withErrors(['error' => 'Solo se pueden editar contratos en borrador']);
        }

        $validated = $request->validate([
            'numero_contrato' => 'required|string|unique:contratos,numero_contrato,' . $contrato->id,
            'supervisor_id' => 'required|exists:usuarios,id',
            'objeto_contractual' => 'required|string',
            'valor_total' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'porcentaje_retencion_fuente' => 'required|numeric|min:0|max:100',
            'porcentaje_estampilla' => 'required|numeric|min:0|max:100',
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
            return back()->withErrors(['error' => 'Este contrato ya tiene un contratista asignado']);
        }

        $contratista = Usuario::findOrFail($validated['contratista_id']);

        // Actualizar contrato
        $contrato->update([
            'contratista_id' => $validated['contratista_id'],
            'estado' => 'activo',
        ]);

        // Actualizar tipo de vinculación del usuario
        if ($contratista->tipo_vinculacion != 'contratista') {
            $contratista->update(['tipo_vinculacion' => 'contratista']);
        }

        return back()->with('success', 'Contratista vinculado exitosamente');
    }

    /**
     * Buscar usuarios para vincular como contratista
     */
    public function buscarContratista(Request $request)
    {
        $search = $request->input('q');

        $usuarios = Usuario::where(function($query) use ($search) {
                $query->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('documento_identidad', 'LIKE', "%{$search}%");
            })
            ->where('estado', 'activo')
            ->whereIn('tipo_vinculacion', ['contratista', 'sin_vinculacion'])
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
        if (!$supervisor->tieneRol('supervisor', $contrato->organizacion_id)) {
            return back()->withErrors(['error' => 'El usuario seleccionado no es supervisor']);
        }

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

        // TODO: Registrar en historial
        // TODO: Notificar a las partes involucradas

        return back()->with('success', 'Estado del contrato actualizado');
    }
}