<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\VinculacionPendiente;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\UsuarioOrganizacionRol;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Listar usuarios de la organización
     */
    public function index(Request $request)
    {
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');

        $usuarios = Usuario::whereHas('organizacionesVinculadas', function($query) use ($organizacionId) {
                $query->where('organizacion_id', $organizacionId);
            })
            ->with(['roles' => function($query) use ($organizacionId) {
                $query->wherePivot('organizacion_id', $organizacionId);
            }])
            ->paginate(15);

        $organizacion = Organizacion::find($organizacionId);

        return view('usuarios.index', compact('usuarios', 'organizacion'));
    }

    /**
     * Listar vinculaciones pendientes
     */
    public function pendientes(Request $request)
    {
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');

        $pendientes = VinculacionPendiente::where('organizacion_id', $organizacionId)
            ->where('estado', 'pendiente')
            ->with(['usuario', 'organizacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Rol::where('organizacion_id', $organizacionId)
            ->where('nombre', '!=', 'admin_organizacion') // No permitir asignar admin desde aquí
            ->orderBy('nivel_jerarquico')
            ->get();

        $organizacion = Organizacion::find($organizacionId);

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

        $usuario = Usuario::findOrFail($validated['usuario_id']);
        $rol = Rol::findOrFail($validated['rol_id']);

        // Verificar que el rol pertenece a la organización
        if ($rol->organizacion_id != $validated['organizacion_id']) {
            return back()->withErrors(['error' => 'El rol no pertenece a esta organización']);
        }

        // Verificar jerarquía - no asignar rol superior al propio
        $miRol = auth()->user()->roles()
            ->wherePivot('organizacion_id', $validated['organizacion_id'])
            ->first();

        if ($miRol && $rol->nivel_jerarquico < $miRol->nivel_jerarquico) {
            return back()->withErrors(['error' => 'No puedes asignar un rol superior al tuyo']);
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
                'asignado_por' => auth()->id(),
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

        $vinculacion = UsuarioOrganizacionRol::where('usuario_id', $id)
            ->where('organizacion_id', $validated['organizacion_id'])
            ->firstOrFail();

        $vinculacion->update([
            'rol_id' => $validated['rol_id'],
            'asignado_por' => auth()->id(),
        ]);

        return back()->with('success', 'Rol actualizado exitosamente');
    }
}
