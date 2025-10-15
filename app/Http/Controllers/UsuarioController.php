<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\VinculacionPendiente;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\UsuarioOrganizacionRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    /**
     * Listar usuarios de la organización
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Obtener organización actual
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');
        
        if (!$organizacionId) {
            return redirect()->route('organizaciones.index')
                ->with('error', 'No hay una organización seleccionada');
        }
        
        $organizacion = Organizacion::findOrFail($organizacionId);
        
        // Verificar permisos
        if (!$user->esAdminGlobal() && !$user->tienePermiso('ver-usuarios', $organizacionId)) {
            abort(403, 'No tienes permiso para ver usuarios de esta organización');
        }
        
        // Query base con filtros
        $query = Usuario::whereHas('organizacionesVinculadas', function($q) use ($organizacionId) {
                $q->where('organizacion_id', $organizacionId);
            })
            ->with(['roles' => function($q) use ($organizacionId) {
                $q->wherePivot('organizacion_id', $organizacionId);
            }]);
        
        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('documento_identidad', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtro por rol
        if ($request->filled('rol_id')) {
            $query->whereHas('roles', function($q) use ($request, $organizacionId) {
                $q->where('roles.id', $request->rol_id)
                  ->wherePivot('organizacion_id', $organizacionId);
            });
        }
        
        // Filtro por estado (tab activo/inactivo)
        $estadoFiltro = $request->get('tab', 'activos');
        if ($estadoFiltro === 'activos') {
            $query->whereHas('organizacionesVinculadas', function($q) use ($organizacionId) {
                $q->where('organizacion_id', $organizacionId)
                  ->wherePivot('estado', 'activo');
            });
        } elseif ($estadoFiltro === 'inactivos') {
            $query->whereHas('organizacionesVinculadas', function($q) use ($organizacionId) {
                $q->where('organizacion_id', $organizacionId)
                  ->wherePivot('estado', '!=', 'activo');
            });
        }
        
        $usuarios = Usuario::whereHas('organizacionesVinculadas', function($query) use ($organizacionId) {
            $query->where('organizaciones.id', $organizacionId);
        })
        ->with(['roles' => function($query) use ($organizacionId) {
            // CORRECCIÓN: Filtrar roles por organización usando wherePivot
            $query->wherePivot('organizacion_id', $organizacionId);
        }])
        ->paginate(15);
            
        // Roles disponibles para filtros y asignación
        $roles = Rol::where('organizacion_id', $organizacionId)
            ->orderBy('nivel_jerarquico')
            ->get();
        
        return view('usuarios.index', compact('usuarios', 'organizacion', 'roles'));
    }

    /**
     * Listar vinculaciones pendientes
     */
    public function pendientes(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        $organizacionId = session('organizacion_actual');
        
        if (!$organizacionId) {
            return redirect()->route('dashboard')
                ->with('error', 'No hay una organización seleccionada');
        }
        
        $organizacion = Organizacion::findOrFail($organizacionId);
        
        // Verificar permisos
        if (!$user->esAdminGlobal() && !$user->tienePermiso('asignar-rol', $organizacionId)) {
            abort(403, 'No tienes permiso para gestionar vinculaciones');
        }

        $pendientes = VinculacionPendiente::where('organizacion_id', $organizacionId)
            ->where('estado', 'pendiente')
            ->with(['usuario', 'organizacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Rol::where('organizacion_id', $organizacionId)
            ->where('nombre', '!=', 'admin_organizacion') // No permitir asignar admin desde aquí
            ->orderBy('nivel_jerarquico')
            ->get();

        return view('usuarios.pendientes', compact('pendientes', 'roles', 'organizacion'));
    }

    /**
     * Asignar rol a usuario
     */
    public function asignarRol(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'organizacion_id' => 'required|exists:organizaciones,id',
            'rol_id' => 'required|exists:roles,id',
        ]);

        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Verificar acceso a la organización
        if (!$user->esAdminGlobal() && !$user->tienePermiso('asignar-rol', $validated['organizacion_id'])) {
            abort(403, 'No tienes permiso para asignar roles en esta organización');
        }

        $usuario = Usuario::findOrFail($validated['usuario_id']);
        $rol = Rol::findOrFail($validated['rol_id']);

        // Verificar que el rol pertenece a la organización
        if ($rol->organizacion_id != $validated['organizacion_id']) {
            return back()->withErrors(['error' => 'El rol no pertenece a esta organización']);
        }

        // Verificar jerarquía - no asignar rol superior al propio (excepto Admin Global)
        if (!$user->esAdminGlobal()) {
            $miRol = $user->roles()
                ->wherePivot('organizacion_id', $validated['organizacion_id'])
                ->first();

            if ($miRol && $rol->nivel_jerarquico < $miRol->nivel_jerarquico) {
                return back()->withErrors(['error' => 'No puedes asignar un rol superior al tuyo']);
            }
        }

        // Crear o actualizar vinculación
        UsuarioOrganizacionRol::updateOrCreate(
            [
                'usuario_id' => $validated['usuario_id'],
                'organizacion_id' => $validated['organizacion_id'],
            ],
            [
                'rol_id' => $validated['rol_id'],
                'estado' => 'activo',
                'fecha_asignacion' => now(),
                'asignado_por' => $user->id,
            ]
        );

        // Actualizar tipo de vinculación del usuario
        $usuario->update(['tipo_vinculacion' => 'organizacion']);

        // Actualizar vinculación pendiente si existe
        VinculacionPendiente::where('usuario_id', $validated['usuario_id'])
            ->where('organizacion_id', $validated['organizacion_id'])
            ->update(['estado' => 'aprobada']);

        return back()->with('success', 'Rol asignado exitosamente');
    }

    /**
     * Rechazar vinculación
     */
    public function rechazarVinculacion(Request $request, $id)
    {
        $vinculacion = VinculacionPendiente::findOrFail($id);
        
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->esAdminGlobal() && !$user->tienePermiso('asignar-rol', $vinculacion->organizacion_id)) {
            abort(403, 'No tienes permiso para rechazar vinculaciones');
        }

        $validated = $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $vinculacion->update([
            'estado' => 'rechazada',
            'motivo_rechazo' => $validated['motivo'],
        ]);

        // TODO: Enviar notificación/email al usuario

        return back()->with('success', 'Vinculación rechazada');
    }

    /**
     * Cambiar estado de usuario
     */
    public function cambiarEstado(Request $request, $id)
    {
        $validated = $request->validate([
            'organizacion_id' => 'required|exists:organizaciones,id',
            'estado' => 'required|in:activo,inactivo,suspendido',
        ]);
        
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->esAdminGlobal() && !$user->tienePermiso('cambiar-estado-usuario', $validated['organizacion_id'])) {
            abort(403, 'No tienes permiso para cambiar estados de usuario');
        }

        $vinculacion = UsuarioOrganizacionRol::where('usuario_id', $id)
            ->where('organizacion_id', $validated['organizacion_id'])
            ->firstOrFail();

        $vinculacion->update(['estado' => $validated['estado']]);

        return back()->with('success', 'Estado actualizado exitosamente');
    }

    /**
     * Mostrar perfil de usuario
     */
    public function show($id)
    {
        $usuario = Usuario::with([
            'organizacionesVinculadas',
            'roles',
            'contratosComoContratista',
            'contratosComoSupervisor'
        ])->findOrFail($id);

        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Cambiar rol de usuario
     */
    public function cambiarRol(Request $request, $id)
    {
        $validated = $request->validate([
            'organizacion_id' => 'required|exists:organizaciones,id',
            'rol_id' => 'required|exists:roles,id',
        ]);
        
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->esAdminGlobal() && !$user->tienePermiso('editar-usuario', $validated['organizacion_id'])) {
            abort(403, 'No tienes permiso para cambiar roles de usuario');
        }

        $rol = Rol::findOrFail($validated['rol_id']);
        
        // Verificar que el rol pertenece a la organización
        if ($rol->organizacion_id != $validated['organizacion_id']) {
            return back()->withErrors(['error' => 'El rol no pertenece a esta organización']);
        }
        
        // Verificar jerarquía (excepto Admin Global)
        if (!$user->esAdminGlobal()) {
            $miRol = $user->roles()
                ->wherePivot('organizacion_id', $validated['organizacion_id'])
                ->first();

            if ($miRol && $rol->nivel_jerarquico < $miRol->nivel_jerarquico) {
                return back()->withErrors(['error' => 'No puedes asignar un rol superior al tuyo']);
            }
        }

        $vinculacion = UsuarioOrganizacionRol::where('usuario_id', $id)
            ->where('organizacion_id', $validated['organizacion_id'])
            ->firstOrFail();

        $vinculacion->update([
            'rol_id' => $validated['rol_id'],
            'asignado_por' => $user->id,
        ]);

        return back()->with('success', 'Rol actualizado exitosamente');
    }
}
