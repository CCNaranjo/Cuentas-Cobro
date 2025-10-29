<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Usuario;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $contrato = Contrato::create($validated);

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato creado exitosamente.');
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
        // Solo editable en estado borrador
        if ($contrato->estado != 'borrador') {
            return back()->with('error', 'Solo se pueden editar contratos en borrador');
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
}