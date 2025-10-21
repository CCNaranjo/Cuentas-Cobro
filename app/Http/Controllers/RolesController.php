<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Modulo;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Concerns\InteractsWithMiddleware;

class RolesController extends BaseController
{
    /**
     * @var \App\Models\Usuario
     */
    protected $user;
    /**
     * Constructor - Middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Middleware para asignar el usuario autenticado a la propiedad.
        // Usamos un closure anónimo para ejecutar el código DESPUÉS de que 'auth' corre.
        $this->middleware(function ($request, $next) {
            /** @var \App\Models\Usuario $authenticatedUser */ 
            $authenticatedUser = Auth::user();
            
            // Asignamos el usuario a la propiedad de la clase
            $this->user = $authenticatedUser; 

            return $next($request);
        });
    }
    
    /**
     * Mostrar lista de todos los roles
     */
    public function index()
    {
        $user = $this->user;
        // Solo admin_global puede ver todos los roles
        if (!$user->esAdminGlobal()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $roles = Rol::withCount('usuarios')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario para crear nuevo rol
     */
    public function create()
    {
        $user = $this->user;
        if (!$user->esAdminGlobal()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear roles.');
        }

        $modulos = Modulo::with('permisos')->get();
        return view('roles.create', compact('modulos'));
    }

    /**
     * Almacenar nuevo rol
     */
    public function store(Request $request)
    {
        $user = $this->user;
        if (!$user->esAdminGlobal()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear roles.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre|regex:/^[a-z_]+$/',
            'descripcion' => 'required|string|max:500',
            'nivel_jerarquico' => 'required|integer|min:1|max:5',
            'permisos' => 'array'
        ]);

        DB::transaction(function () use ($request) {
            $rol = Rol::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'nivel_jerarquico' => $request->nivel_jerarquico,
                'es_sistema' => false,
            ]);

            if ($request->has('permisos')) {
                $rol->permisos()->attach($request->permisos);
            }
        });

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Mostrar detalles de un rol específico
     */
    public function show(Rol $role)
    {
        $user = $this->user;
        if (!$user->esAdminGlobal()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para ver esta información.');
        }

        $role->load(['permisos.modulo', 'usuarios']);
        $modulos = Modulo::with('permisos')->get();
        
        return view('roles.show', compact('role', 'modulos'));
    }

    /**
     * Mostrar formulario de edición de un rol
     */
    public function edit(Rol $role)
    {
        $user = $this->user;
        if (!$user->esAdminGlobal()) {
            return redirect()->route('roles.index')->with('error', 'No tienes permisos para editar roles.');
        }

        if ($role->nombre === 'admin_global') {
            return redirect()->route('roles.index')->with('error', 'No se pueden editar roles del sistema.');
        }

        $role->load('permisos');
        $modulos = Modulo::with('permisos')->get();
        
        return view('roles.edit', compact('role', 'modulos'));
    }

    /**
     * Actualizar un rol existente
     */
    public function update(Request $request, Rol $role)
    {
        $user = $this->user;
        if (!$user->esAdminGlobal()) {
            return redirect()->route('roles.index')->with('error', 'No tienes permisos para actualizar roles.');
        }

        if ($role->nombre === 'admin_global') {
            return redirect()->route('roles.index')->with('error', 'No se pueden modificar roles del sistema.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-z_]+$/|unique:roles,nombre,' . $role->id,
            'descripcion' => 'required|string|max:500',
            'nivel_jerarquico' => 'required|integer|min:1|max:5',
            'permisos' => 'array'
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'nivel_jerarquico' => $request->nivel_jerarquico,
            ]);

            $role->permisos()->sync($request->permisos ?? []);
        });

        return redirect()->route('roles.show', $role->id)
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Eliminar un rol
     */
    public function destroy(Rol $role)
    {
        $user = $this->user;
        if (!$user->esAdminGlobal()) {
            return redirect()->route('roles.index')->with('error', 'No tienes permisos para eliminar roles.');
        }

        if ($role->es_sistema) {
            return redirect()->route('roles.index')->with('error', 'No se pueden eliminar roles del sistema.');
        }

        if ($role->usuarios()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar un rol con usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    /**
     * Obtener permisos por módulo (AJAX)
     */
    public function getPermisosByModulo($moduloId)
    {
        $user = $this->user;
        if (!$user->esAdminGlobal()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $permisos = Permiso::where('modulo_id', $moduloId)->get();
        
        return response()->json($permisos);
    }
}
