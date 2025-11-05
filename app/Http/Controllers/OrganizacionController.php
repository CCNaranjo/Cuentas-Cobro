<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\UsuarioOrganizacionRol;
use App\Models\VinculacionPendiente;

class OrganizacionController extends Controller
{
    /**
     * Mostrar lista de organizaciones
     * - admin_global: Ve todo
     * - usuario_basico: Ve solo información básica (sin datos sensibles)
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Query base
        $query = Organizacion::query();
        
        // Si es usuario_basico, filtrar datos sensibles
        if (!$user->esAdminGlobal()) {
            // Solo organizaciones activas para usuarios básicos
            $query->where('estado', 'activa');
        } else {
            // Admin global puede ver todas
            $query->with(['adminGlobal', 'usuarios'])
                  ->withCount(['contratos', 'usuarios', 'vinculacionesPendientes']);
        }
        
        // Filtros (solo para admin_global)
        if ($user->esAdminGlobal() && $request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_oficial', 'LIKE', "%{$search}%")
                  ->orWhere('nit', 'LIKE', "%{$search}%")
                  ->orWhere('municipio', 'LIKE', "%{$search}%");
            });
        }
        
        if ($user->esAdminGlobal() && $request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        $organizaciones = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Si es usuario_basico, limpiar datos sensibles en la colección
        if (!$user->esAdminGlobal()) {
            $organizaciones->getCollection()->transform(function ($org) {
                // Ocultar datos sensibles
                $org->makeHidden(['nit', 'codigo_vinculacion', 'admin_global_id']);
                // Eliminar contadores
                unset($org->usuarios_count, $org->contratos_count, $org->vinculaciones_pendientes_count);
                return $org;
            });
        }
        
        return view('organizaciones.index', compact('organizaciones'));
    }

    /**
     * Mostrar formulario de creación
     * Requiere: crear-organizacion (admin_global)
     */
    public function create()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal() || !$user->tienePermiso('crear-organizacion')) {
            abort(403, 'No tienes permisos para crear organizaciones');
        }
        
        return view('organizaciones.create');
    }

    /**
     * Guardar nueva organización con administrador
     * Requiere: crear-organizacion (admin_global)
     */
    public function store(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal() || !$user->tienePermiso('crear-organizacion')) {
            return back()->withErrors(['error' => 'No tienes permisos para crear organizaciones']);
        }
        
        $validated = $request->validate([
            // Datos de la organización
            'nombre_oficial' => 'required|string|max:255',
            'nit' => 'required|string|unique:organizaciones,nit',
            'departamento' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'direccion' => 'required|string|max:255',
            'telefono_contacto' => 'required|string|max:20',
            'email_institucional' => 'required|email|unique:organizaciones,email_institucional',
            'dominios_email' => 'nullable|array',
            'dominios_email.*' => 'string|starts_with:@',
            
            // Datos del administrador
            'admin_nombre' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:usuarios,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'admin_telefono' => 'nullable|string|max:20',
            'admin_documento' => 'nullable|string|max:20|unique:usuarios,documento_identidad',
        ]);

        DB::beginTransaction();

        try {
            // Generar código de vinculación único
            $validated['codigo_vinculacion'] = $this->generarCodigoVinculacion();
            $validated['admin_global_id'] = $user->id;
            $validated['estado'] = 'activa';

            // Crear organización
            $organizacion = Organizacion::create($validated);

            // Crear usuario administrador
            $admin = Usuario::create([
                'nombre' => $validated['admin_nombre'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'telefono' => $validated['admin_telefono'],
                'documento_identidad' => $validated['admin_documento'],
                'tipo_vinculacion' => 'organizacion',
                'estado' => 'activo',
                'email_verificado_en' => now(),
            ]);

            // Obtener el rol de admin_organizacion
            $rolAdmin = Rol::where('nombre', 'admin_organizacion')->first();

            if (!$rolAdmin) {
                throw new \Exception('No se pudo encontrar el rol de administrador para la organización');
            }

            // Vincular administrador a la organización
            UsuarioOrganizacionRol::create([
                'usuario_id' => $admin->id,
                'organizacion_id' => $organizacion->id,
                'rol_id' => $rolAdmin->id,
                'fecha_asignacion' => now(),
                'asignado_por' => $user->id,
                'estado' => 'activo'
            ]);

            DB::commit();

            Log::info('Organización creada', [
                'organizacion_id' => $organizacion->id,
                'creado_por' => $user->id,
                'admin_id' => $admin->id
            ]);

            return redirect()->route('organizaciones.show', $organizacion)
                ->with('success', 'Organización creada exitosamente. Código de vinculación: ' . $validated['codigo_vinculacion']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear organización', [
                'error' => $e->getMessage(),
                'usuario_id' => $user->id
            ]);
            
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear la organización: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle de organización
     * Requiere: editar-organizacion (admin_global)
     */
    public function show(Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->esAdminGlobal() || !$user->tienePermiso('editar-organizacion')) {
            abort(403, 'No tienes permisos para ver los detalles de esta organización');
        }
        
        // Establecer esta organización como actual en sesión
        session(['organizacion_actual' => $organizacion->id]);
        
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
     * Requiere: editar-organizacion (admin_global)
     */
    public function edit(Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal() || !$user->tienePermiso('editar-organizacion')) {
            abort(403, 'No tienes permisos para editar esta organización');
        }
        
        return view('organizaciones.edit', compact('organizacion'));
    }

    /**
     * Actualizar organización
     * Requiere: editar-organizacion (admin_global)
     */
    public function update(Request $request, Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal() || !$user->tienePermiso('editar-organizacion')) {
            return back()->withErrors(['error' => 'No tienes permisos para editar esta organización']);
        }
        
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
        
        Log::info('Organización actualizada', [
            'organizacion_id' => $organizacion->id,
            'actualizado_por' => $user->id
        ]);

        return redirect()->route('organizaciones.show', $organizacion)
            ->with('success', 'Organización actualizada exitosamente');
    }

    /**
     * Seleccionar organización para trabajar
     * Requiere: seleccionar-organizacion-contexto (admin_global)
     */
    public function seleccionar(Request $request, Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal() || !$user->tienePermiso('seleccionar-organizacion-contexto')) {
            abort(403, 'No tienes permisos para seleccionar organizaciones');
        }

        session(['organizacion_actual' => $organizacion->id]);
        
        Log::info('Organización seleccionada como contexto', [
            'organizacion_id' => $organizacion->id,
            'usuario_id' => $user->id
        ]);

        return redirect()->route('organizaciones.index')
            ->with('success', 'Ahora estás trabajando con: ' . $organizacion->nombre_oficial);
    }

    /**
     * Asignar administrador a organización
     */
    public function asignarAdmin(Request $request, Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal()) {
            return back()->withErrors(['error' => 'No tienes permisos para asignar administradores']);
        }
        
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
        ]);

        $usuario = Usuario::findOrFail($validated['usuario_id']);
        
        $rolAdmin = Rol::where('nombre', 'admin_organizacion')->first();

        if (!$rolAdmin) {
            return back()->withErrors(['error' => 'No se encontró el rol de administrador']);
        }

        $organizacion->usuarios()->syncWithoutDetaching([
            $usuario->id => [
                'rol_id' => $rolAdmin->id,
                'estado' => 'activo',
                'fecha_asignacion' => now(),
                'asignado_por' => $user->id,
            ]
        ]);

        $usuario->update(['tipo_vinculacion' => 'organizacion']);

        return back()->with('success', 'Administrador asignado exitosamente');
    }

    /**
     * Actualizar información del administrador
     */
    public function actualizarAdmin(Request $request, Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal()) {
            return back()->withErrors(['error' => 'No tienes permisos para actualizar el administrador']);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $request->usuario_id,
            'telefono' => 'nullable|string|max:20',
            'documento_identidad' => 'nullable|string|max:20|unique:usuarios,documento_identidad,' . $request->usuario_id,
            'estado' => 'required|in:activo,inactivo,pendiente_verificacion',
            'tipo_vinculacion' => 'required|in:organizacion,contratista,sin_vinculacion',
            'usuario_id' => 'required|exists:usuarios,id'
        ]);

        DB::beginTransaction();

        try {
            $usuario = Usuario::findOrFail($validated['usuario_id']);
            
            $vinculacion = $organizacion->usuarios()
                ->where('usuario_id', $usuario->id)
                ->wherePivot('rol_id', function($query) {
                    $query->select('id')
                        ->from('roles')
                        ->where('nombre', 'admin_organizacion');
                })
                ->first();

            if (!$vinculacion) {
                throw new \Exception('El usuario no es administrador de esta organización');
            }

            $usuario->update([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'],
                'documento_identidad' => $validated['documento_identidad'],
                'estado' => $validated['estado'],
                'tipo_vinculacion' => $validated['tipo_vinculacion'],
            ]);

            DB::commit();

            return redirect()->route('organizaciones.edit', $organizacion)
                ->with('success', 'Administrador actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Cambiar administrador de la organización
     */
    public function cambiarAdmin(Request $request, Organizacion $organizacion)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal()) {
            return back()->withErrors(['error' => 'No tienes permisos para cambiar el administrador']);
        }

        $validated = $request->validate([
            'nuevo_admin_id' => 'required|exists:usuarios,id'
        ]);

        DB::beginTransaction();

        try {
            $rolAdmin = Rol::where('nombre', 'admin_organizacion')->first();

            if (!$rolAdmin) {
                throw new \Exception('No se encontró el rol de administrador');
            }

            $adminActual = $organizacion->usuarios()
                ->wherePivot('rol_id', $rolAdmin->id)
                ->wherePivot('estado', 'activo')
                ->first();

            $nuevoAdmin = Usuario::findOrFail($validated['nuevo_admin_id']);

            $vinculacionExistente = $organizacion->usuarios()
                ->where('usuario_id', $nuevoAdmin->id)
                ->first();

            if (!$vinculacionExistente) {
                throw new \Exception('El usuario no pertenece a esta organización');
            }

            if ($adminActual) {
                $organizacion->usuarios()->updateExistingPivot($adminActual->id, [
                    'rol_id' => null,
                    'estado' => 'activo'
                ]);
            }

            $organizacion->usuarios()->syncWithoutDetaching([
                $nuevoAdmin->id => [
                    'rol_id' => $rolAdmin->id,
                    'estado' => 'activo',
                    'fecha_asignacion' => now(),
                    'asignado_por' => $user->id,
                ]
            ]);

            $nuevoAdmin->update(['tipo_vinculacion' => 'organizacion']);

            DB::commit();

            return redirect()->route('organizaciones.edit', $organizacion)
                ->with('success', 'Administrador cambiado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Vincular usuario a organización mediante código
     */
    public function vincularCodigo(Request $request)
    {
        $request->validate([
            'codigo_vinculacion' => 'required|string|max:20'
        ]);

        try {
            DB::beginTransaction();

            $organizacion = Organizacion::where('codigo_vinculacion', $request->codigo_vinculacion)
                ->where('estado', 'activa')
                ->first();

            if (!$organizacion) {
                $errorMessage = 'El código de vinculación no es válido o la organización está inactiva.';
                
                Log::warning('Intento de vinculación con código inválido', [
                    'codigo' => $request->codigo_vinculacion,
                    'usuario_id' => Auth::id()
                ]);

                return back()->withErrors(['codigo_vinculacion' => $errorMessage]);
            }

            $usuario = Auth::user();

            $vinculacionExistente = VinculacionPendiente::where('usuario_id', $usuario->id)
                ->where('organizacion_id', $organizacion->id)
                ->first();

            if ($vinculacionExistente) {
                $estadoActual = $vinculacionExistente->estado;
                
                if ($estadoActual === 'activo') {
                    return back()->withErrors(['codigo_vinculacion' => 'Ya estás vinculado a esta organización.']);
                } elseif ($estadoActual === 'pendiente') {
                    return back()->withErrors(['codigo_vinculacion' => 'Ya tienes una solicitud pendiente para esta organización.']);
                } elseif ($estadoActual === 'rechazado') {
                    $vinculacionExistente->update([
                        'estado' => 'pendiente',
                        'motivo_rechazo' => null,
                        'fecha_aprobacion' => null,
                        'fecha_rechazo' => null,
                    ]);
                }
            } else {
                VinculacionPendiente::create([
                    'usuario_id' => $usuario->id,
                    'organizacion_id' => $organizacion->id,
                    'codigo_vinculacion_usado' => $request->codigo_vinculacion,
                    'estado' => 'pendiente',
                    'fecha_solicitud' => now(),
                ]);
            }

            DB::commit();

            Log::info('Solicitud de vinculación creada', [
                'usuario_id' => $usuario->id,
                'organizacion_id' => $organizacion->id
            ]);

            return back()->with([
                'success' => 'Solicitud de vinculación enviada correctamente. Espera la aprobación del administrador.',
                'organizacion' => $organizacion->nombre_oficial
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error en vinculación por código', [
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return back()->withErrors(['codigo_vinculacion' => 'Error al procesar la vinculación. Intenta nuevamente.']);
        }
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
}