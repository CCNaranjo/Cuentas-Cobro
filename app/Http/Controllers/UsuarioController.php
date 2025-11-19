<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\VinculacionPendiente;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\UsuarioOrganizacionRol;
use App\Models\Banco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $query = Usuario::whereHas('organizacionesVinculadas', function ($q) use ($organizacionId) {
            $q->where('organizacion_id', $organizacionId);
        })
            ->with(['roles' => function ($q) use ($organizacionId) {
                $q->wherePivot('organizacion_id', $organizacionId);
            }]);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('documento_identidad', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->filled('rol_id')) {
            $query->whereHas('roles', function ($q) use ($request, $organizacionId) {
                $q->where('roles.id', $request->rol_id)
                  ->wherePivot('organizacion_id', $organizacionId);
            });
        }

        // Filtro por estado (tab activo/inactivo)
        $estadoFiltro = $request->get('tab', 'activos');
        if ($estadoFiltro === 'activos') {
            $query->whereHas('organizacionesVinculadas', function ($q) use ($organizacionId) {
                $q->where('organizacion_id', $organizacionId)
                  ->wherePivot('estado', 'activo');
            });
        } elseif ($estadoFiltro === 'inactivos') {
            $query->whereHas('organizacionesVinculadas', function ($q) use ($organizacionId) {
                $q->where('organizacion_id', $organizacionId)
                  ->wherePivot('estado', '!=', 'activo');
            });
        }

        $usuarios = Usuario::whereHas('organizacionesVinculadas', function ($query) use ($organizacionId) {
            $query->where('organizaciones.id', $organizacionId);
        })
        ->with(['roles' => function ($query) use ($organizacionId) {
            // CORRECCIÓN: Filtrar roles por organización usando wherePivot
            $query->wherePivot('organizacion_id', $organizacionId);
        }])
        ->paginate(15);

        // Roles disponibles para filtros y asignación
        $roles = Rol::where('nombre', '!=', 'admin_organizacion') // No permitir asignar admin desde aquí
            ->where('nombre', '!=', 'admin_global') // No permitir asignar admin global
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

        $roles = Rol::where('nombre', '!=', 'admin_organizacion') // No permitir asignar admin desde aquí
            ->where('nombre', '!=', 'admin_global') // No permitir asignar admin global
            ->orderBy('nivel_jerarquico')
            ->get();

        return view('usuarios.pendientes', compact('pendientes', 'roles', 'organizacion'));
    }

    /**
     * Muestra el formulario para editar un usuario.
     * * @param int $id El ID del usuario a editar.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        // 1. Obtener la organización actual (Validado por middleware)
        $organizacionId = Session::get('organizacion_actual');
        if (!$organizacionId) {
            return redirect()->route('dashboard')->with('error', 'Debe seleccionar una organización.');
        }

        // 2. Cargar el usuario SIN pivot, ya que el estado es un campo directo.
        $usuario = Usuario::findOrFail($id);

        // 3. Obtener el rol actual del usuario en esta organización
        $rolActual = $usuario->roles()
            ->wherePivot('organizacion_id', $organizacionId) // <-- Esto resuelve el error 1052
            ->first();

        // Cargar todos los roles disponibles de la organización actual para el <select>
        $roles = Rol::get();

        // Obtener el objeto de la organización para el breadcrumb y el formulario
        $organizacion = Organizacion::findOrFail($organizacionId);

        // 4. Pasar datos a la vista, incluyendo el rol actual
        return view('usuarios.edit', compact('usuario', 'organizacion', 'roles', 'rolActual'));
    }

    /**
     * Procesa la actualización de un usuario.
     * * @param \Illuminate\Http\Request $request
     * @param int $id El ID del usuario a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $organizacionId = Session::get('organizacion_actual');
        $usuario = Usuario::findOrFail($id);

        // 1. VALIDACIÓN
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($usuario->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'documento_identidad' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('usuarios', 'documento_identidad')->ignore($usuario->id),
            ],
            'telefono' => 'nullable|string|max:20',

            // VALIDACIÓN: El estado es un campo directo del usuario
            'estado' => ['required', 'string', Rule::in(['activo', 'inactivo', 'suspendido'])],

            // VALIDACIÓN: El rol es para la relación Many-to-Many
            'rol_id' => ['required', 'integer', Rule::exists('roles', 'id')],
        ]);

        // 2. ACTUALIZAR CAMPOS BÁSICOS Y ESTADO DEL USUARIO (Directamente en la tabla 'usuarios')
        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->documento_identidad = $request->documento_identidad;
        $usuario->telefono = $request->telefono;
        $usuario->estado = $request->estado; // <-- ¡Actualización directa!

        // Actualizar contraseña solo si se proporcionó una
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        // 3. ACTUALIZAR LA ASIGNACIÓN DE ROL (Esto sigue siendo una tabla intermedia: rol_usuario)

        // Asumimos una relación muchos-a-muchos entre usuarios y roles, filtrada por organización.
        // Se recomienda usar el método detach/attach o sync sin pivot, ya que la tabla
        // intermedia rol_usuario debería tener 'usuario_id', 'rol_id', 'organizacion_id'.

        // Usamos una lógica de sincronización más precisa para el rol en la organización actual.
        // Nota: Esto requiere que tengas configurado el 'organizacion_id' en la tabla pivot 'rol_usuario'
        // y que la relación en el modelo 'Usuario' sea:
        // public function roles() { return $this->belongsToMany(Rol::class)->withPivot('organizacion_id'); }

        // 3a. Obtener el rol anterior en esta organización
        $rolAnterior = $usuario->roles()->wherePivot('organizacion_id', $organizacionId)->first();

        // 3b. Desvincular el rol anterior
        if ($rolAnterior) {
            $usuario->roles()->detach($rolAnterior->id);
        }

        // 3c. Vincular el nuevo rol
        $usuario->roles()->attach($request->rol_id, ['organizacion_id' => $organizacionId]);

        // 4. Redirección
        return redirect()->route('usuarios.index', ['organizacion_id' => $organizacionId])
            ->with('success', "El usuario {$usuario->nombre} ha sido actualizado exitosamente.");
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
            'estado' => 'required|in:activo,inactivo,suspendido',
        ]);

        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (Auth::id() === $id) {
            return back()->withErrors(['error' => 'No puedes cambiar tu propio estado de cuenta.']);
        }

        // Verificar permisos
        if (!$user->esAdminGlobal() && !$user->tienePermiso('cambiar-estado-usuario', $validated['organizacion_id'])) {
            abort(403, 'No tienes permiso para cambiar estados de usuario');
        }
        $usuario = Usuario::findOrFail($id);
        try {
            DB::beginTransaction();

            // 2. Aplicar el cambio de estado directamente al modelo Usuario
            $usuario->estado = $validated['estado'];
            $usuario->save();

            // 3. Confirmar la transacción
            DB::commit();

            return back()->with('success', "El estado del usuario {$usuario->nombre} ha sido actualizado a '{$validated['estado']}'.");

        } catch (\Exception $e) {
            DB::rollBack();
            // Recomendable: Loggear el error para futuras revisiones
            // \Log::error("Error al cambiar estado del usuario {$usuario->id}: " . $e->getMessage());

            return back()->withErrors(['error' => 'Ocurrió un error al intentar actualizar el estado del usuario. Intenta nuevamente.']);
        }
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

        // Verificar jerarquía (excepto Admin Global)
        if (!$user->esAdminGlobal()) {
            $miRol = $user->roles()
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
    /**
     * Mostrar perfil propio del usuario autenticado
     */
    public function perfil()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        // Cargar relaciones necesarias
        $usuario = Usuario::with([
            'organizacionesVinculadas',
            'roles',
            'contratosComoContratista',
            'contratosComoSupervisor',
            'datosFinancierosContratista.banco'  // Añadido para datos financieros si es contratista
        ])->findOrFail($user->id);

        // Obtener bancos para formulario si es contratista
        $bancos = Banco::all();

        return view('usuarios.perfil', compact('usuario', 'bancos'));
    }

    /**
     * Actualizar perfil propio
     */
    public function actualizarPerfil(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($user->id)
            ],
        ]);

        $user->update($validated);

        // Si es contratista, actualizar datos financieros
        if ($user->tieneRol('contratista')) {
            $validatedFinanciero = $request->validate([
                'cedula_o_nit_verificado' => 'required|string|max:50|unique:datos_financieros_contratista,cedula_o_nit_verificado,' . $user->datosFinancierosContratista?->id,
                'banco_id' => 'nullable|exists:bancos,id',
                'tipo_cuenta' => 'nullable|in:ahorros,corriente',
                'numero_cuenta_bancaria' => 'nullable|string|max:50',
            ]);

            if ($user->datosFinancierosContratista) {
                $user->datosFinancierosContratista->update($validatedFinanciero);
            } else {
                $user->datosFinancierosContratista()->create($validatedFinanciero);
            }
        }

        Log::info('Usuario actualizó su perfil', [
            'usuario_id' => $user->id,
            'cambios' => $validated
        ]);

        return back()->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Cambiar contraseña propia
     */
    public function cambiarPassword(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        $validated = $request->validate([
            'password_actual' => 'required|string',
            'password_nuevo' => 'required|string|min:8|confirmed',
        ]);

        // Verificar contraseña actual
        if (!Hash::check($validated['password_actual'], $user->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta']);
        }

        $user->update([
            'password' => Hash::make($validated['password_nuevo'])
        ]);

        Log::info('Usuario cambió su contraseña', ['usuario_id' => $user->id]);

        return back()->with('success', 'Contraseña actualizada exitosamente');
    }

    /**
     * Actualizar preferencias de notificaciones
     */
    public function actualizarNotificaciones(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        $validated = $request->validate([
            'notificaciones_email' => 'boolean',
            'notificaciones_sms' => 'boolean',
            'notificaciones_sistema' => 'boolean',
        ]);

        // Asumiendo que tienes un campo JSON para preferencias
        $user->update([
            'preferencias_notificaciones' => $validated
        ]);

        return back()->with('success', 'Preferencias de notificaciones actualizadas');
    }
}
