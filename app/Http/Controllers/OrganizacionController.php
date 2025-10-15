<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class OrganizacionController extends Controller
{
    /**
     * Mostrar lista de organizaciones (Admin Global)
     */

    

    public function index()
    {
        $organizaciones = Organizacion::with(['adminGlobal', 'usuarios'])
            ->withCount(['contratos', 'usuarios', 'vinculacionesPendientes'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('organizaciones.index', compact('organizaciones'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('organizaciones.create');
    }

    /**
     * Guardar nueva organización
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_oficial' => 'required|string|max:255',
            'nit' => 'required|string|unique:organizaciones,nit',
            'departamento' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'direccion' => 'required|string|max:255',
            'telefono_contacto' => 'required|string|max:20',
            'email_institucional' => 'required|email|unique:organizaciones,email_institucional',
            'dominios_email' => 'nullable|array',
            'dominios_email.*' => 'string|starts_with:@',
        ]);

        // Generar código de vinculación único
        $validated['codigo_vinculacion'] = $this->generarCodigoVinculacion();
        $validated['admin_global_id'] = Auth::user()->id;
        $validated['estado'] = 'activa';

        $organizacion = Organizacion::create($validated);

        // Clonar roles base para esta organización
        $this->clonarRolesBase($organizacion);

        return redirect()->route('organizaciones.show', $organizacion)
            ->with('success', 'Organización creada exitosamente. Código de vinculación: ' . $validated['codigo_vinculacion']);
    }

    /**
     * Mostrar detalle de organización
     */
    public function show(Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */ 
        $user = Auth::user();
        // Establecer esta organización como actual en sesión (para Admin Global)
        if ($user->esAdminGlobal()) {
            session(['organizacion_actual' => $organizacion->id]);
        }
        
        $organizacion->load([
            'usuarios' => function($query) {
                $query->withPivot('rol_id', 'estado', 'fecha_asignacion')->with('roles');
            },
            'contratos' => function($query) {
                $query->with(['contratista', 'supervisor'])->latest()->take(10);
            },
            'vinculacionesPendientes' => function($query) {
                $query->where('estado', 'pendiente')->with('usuario');
            }
        ]);

        // Estadísticas
        $estadisticas = [
            'usuarios_activos' => $organizacion->usuarios()->wherePivot('estado', 'activo')->count(),
            'contratos_activos' => $organizacion->contratos()->where('estado', 'activo')->count(),
            'vinculaciones_pendientes' => $organizacion->vinculacionesPendientes()->where('estado', 'pendiente')->count(),
            'valor_contratos_activos' => $organizacion->contratos()->where('estado', 'activo')->sum('valor_total'),
        ];

        return view('organizaciones.show', compact('organizacion', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Organizacion $organizacion)
    {
        return view('organizaciones.edit', compact('organizacion'));
    }

    /**
     * Actualizar organización
     */
    public function update(Request $request, Organizacion $organizacion)
    {
        $validated = $request->validate([
            'nombre_oficial' => 'required|string|max:255',
            'nit' => 'required|string|unique:organizaciones,nit,' . $organizacion->id,
            'departamento' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'direccion' => 'required|string|max:255',
            'telefono_contacto' => 'required|string|max:20',
            'email_institucional' => 'required|email|unique:organizaciones,email_institucional,' . $organizacion->id,
            'dominios_email' => 'nullable|array',
            'estado' => 'required|in:activa,inactiva,suspendida',
        ]);

        $organizacion->update($validated);

        return redirect()->route('organizaciones.show', $organizacion)
            ->with('success', 'Organización actualizada exitosamente');
    }

    /**
     * Seleccionar organización para trabajar (Admin Global)
     */
    public function seleccionar(Request $request, Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */ 
        $user = Auth::user();
        
        // Solo Admin Global puede seleccionar organizaciones
        if (!$user->esAdminGlobal()) {
            abort(403, 'No tienes permiso para realizar esta acción');
        }

        session(['organizacion_actual' => $organizacion->id]);

        return redirect()->route('organizaciones.index')
            ->with('success', 'Ahora estás trabajando con: ' . $organizacion->nombre_oficial);
    }

    /**
     * Asignar administrador a organización
     */
    public function asignarAdmin(Request $request, Organizacion $organizacion)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
        ]);

        $usuario = Usuario::findOrFail($validated['usuario_id']);
        
        // Obtener el rol de admin_organizacion
        $rolAdmin = Rol::where('nombre', 'admin_organizacion')
            ->where('organizacion_id', $organizacion->id)
            ->first();

        if (!$rolAdmin) {
            return back()->withErrors(['error' => 'No se encontró el rol de administrador para esta organización']);
        }

        // Asignar rol al usuario
        $organizacion->usuarios()->syncWithoutDetaching([
            $usuario->id => [
                'rol_id' => $rolAdmin->id,
                'estado' => 'activo',
                'fecha_asignacion' => now(),
                'asignado_por' => Auth::id(),
            ]
        ]);

        // Actualizar tipo de vinculación
        $usuario->update(['tipo_vinculacion' => 'organizacion']);

        return back()->with('success', 'Administrador asignado exitosamente');
    }

    /**
     * Generar código de vinculación único
     */
    private function generarCodigoVinculacion()
    {
        do {
            $codigo = 'ORG-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Organizacion::where('codigo_vinculacion', $codigo)->exists());

        return $codigo;
    }

    /**
     * Clonar roles base para la organización
     */
    private function clonarRolesBase($organizacion)
    {
        // Roles plantilla a clonar
        $rolesPlantilla = ['admin_organizacion', 'ordenador_gasto', 'supervisor', 'tesorero'];

        foreach ($rolesPlantilla as $nombreRol) {
            $rolBase = Rol::where('nombre', $nombreRol)
                ->whereNull('organizacion_id')
                ->where('es_sistema', true)
                ->first();

            if ($rolBase) {
                // Clonar rol para esta organización
                $nuevoRol = $rolBase->replicate();
                $nuevoRol->organizacion_id = $organizacion->id;
                $nuevoRol->es_sistema = false;
                $nuevoRol->save();

                // Copiar permisos
                $permisos = $rolBase->permisos()->pluck('permisos.id');
                $nuevoRol->permisos()->sync($permisos);
            }
        }
    }
}
