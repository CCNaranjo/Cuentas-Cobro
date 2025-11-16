<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    /**
     * Mostrar lista de roles
     * Nivel 1: Ve todos los roles
     * Nivel 2: Ve solo roles de nivel 3+
     * Nivel 3+: Ve solo roles de su nivel o inferior
     */
    public function index()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (!$user->tienePermiso('ver-roles')) {
            abort(403, 'No tienes permisos para ver roles');
        }

        $nivelUsuario = $user->obtenerNivelJerarquico();

        $query = Rol::withCount(['permisos', 'usuarios']);

        // Filtrar según nivel
        if ($nivelUsuario == 2) {
            $query->where('nivel_jerarquico', '>=', 3);
        } elseif ($nivelUsuario > 2) {
            $query->where('nivel_jerarquico', '>=', $nivelUsuario);
        }

        $roles = $query->orderBy('nivel_jerarquico')->get();

        return view('roles.index', compact('roles', 'nivelUsuario'));
    }

    /**
     * Mostrar formulario de creación de rol
     */
    public function create()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (!$user->tienePermiso('crear-rol')) {
            abort(403, 'No tienes permisos para crear roles');
        }

        $nivelUsuario = $user->obtenerNivelJerarquico();

        // Obtener módulos y permisos según nivel
        $modulos = $this->obtenerModulosConPermisos($nivelUsuario);

        return view('roles.create', compact('modulos', 'nivelUsuario'));
    }

    /**
     * Guardar nuevo rol
     */
    public function store(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (!$user->tienePermiso('crear-rol')) {
            return back()->withErrors(['error' => 'No tienes permisos para crear roles']);
        }

        $nivelUsuario = $user->obtenerNivelJerarquico();

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre|regex:/^[a-z_]+$/',
            'descripcion' => 'nullable|string',
            'nivel_jerarquico' => 'required|integer|min:1|max:5',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        // VALIDACIÓN DE NIVEL JERÁRQUICO
        if ($nivelUsuario == 2) {
            if ($validated['nivel_jerarquico'] < 3) {
                return back()->withErrors([
                    'nivel_jerarquico' => 'Solo puedes crear roles de nivel 3 o superior'
                ])->withInput();
            }
        } elseif ($nivelUsuario > 2) {
            if ($validated['nivel_jerarquico'] < $nivelUsuario) {
                return back()->withErrors([
                    'nivel_jerarquico' => 'Solo puedes crear roles de tu nivel o inferior'
                ])->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $rol = Rol::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'nivel_jerarquico' => $validated['nivel_jerarquico'],
                'es_sistema' => false,
            ]);

            // Asignar permisos (validar que sean permitidos para este nivel)
            if (!empty($validated['permisos'])) {
                $permisosPermitidos = $this->obtenerIdsPermisosPermitidosParaNivel($validated['nivel_jerarquico']);
                $permisosAAsignar = array_intersect($validated['permisos'], $permisosPermitidos);

                $rol->permisos()->attach($permisosAAsignar);
            }

            DB::commit();

            Log::info('Rol creado', [
                'rol_id' => $rol->id,
                'creado_por' => $user->id,
                'nivel_jerarquico' => $validated['nivel_jerarquico']
            ]);

            return redirect()->route('roles.index')
                ->with('success', 'Rol creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear rol', [
                'error' => $e->getMessage(),
                'usuario_id' => $user->id
            ]);

            return back()->withErrors(['error' => 'Error al crear el rol: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de un rol
     */
    public function show(Rol $rol)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (!$user->tienePermiso('ver-roles')) {
            abort(403, 'No tienes permisos para ver roles');
        }

        $nivelUsuario = $user->obtenerNivelJerarquico();

        // Validar que puede ver este rol
        if ($nivelUsuario == 2 && $rol->nivel_jerarquico < 3) {
            abort(403, 'No tienes permisos para ver este rol');
        } elseif ($nivelUsuario > 2 && $rol->nivel_jerarquico < $nivelUsuario) {
            abort(403, 'No tienes permisos para ver este rol');
        }

        // Obtener módulos y permisos según nivel
        $modulos = $this->obtenerModulosConPermisos($nivelUsuario);
        $rol->load('permisos.modulo', 'usuarios');

        return view('roles.show', compact('rol', 'modulos'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Rol $rol)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (!$user->tienePermiso('asignar-permisos-rol')) {
            abort(403, 'No tienes permisos para editar roles');
        }

        $nivelUsuario = $user->obtenerNivelJerarquico();

        // Validar que puede editar este rol
        if ($nivelUsuario == 2 && $rol->nivel_jerarquico < 3) {
            abort(403, 'No puedes editar roles de nivel inferior a 3');
        } elseif ($nivelUsuario > 2 && $rol->nivel_jerarquico < $nivelUsuario) {
            abort(403, 'No puedes editar roles de nivel superior al tuyo');
        }

        // No permitir editar rol admin_global
        if ($rol->nombre === 'admin_global') {
            return redirect()->route('roles.index')
                ->withErrors(['error' => 'No se puede editar el rol de administrador global']);
        }

        $modulos = $this->obtenerModulosConPermisos($nivelUsuario);
        $permisosAsignados = $rol->permisos->pluck('id')->toArray();

        return view('roles.edit', compact('rol', 'modulos', 'permisosAsignados', 'nivelUsuario'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, Rol $rol)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (!$user->tienePermiso('asignar-permisos-rol')) {
            return back()->withErrors(['error' => 'No tienes permisos para editar roles']);
        }

        $nivelUsuario = $user->obtenerNivelJerarquico();

        // Validar que puede editar este rol
        if ($nivelUsuario == 2 && $rol->nivel_jerarquico < 3) {
            return back()->withErrors(['error' => 'No puedes editar roles de nivel inferior a 3']);
        } elseif ($nivelUsuario > 2 && $rol->nivel_jerarquico < $nivelUsuario) {
            return back()->withErrors(['error' => 'No puedes editar roles de nivel superior al tuyo']);
        }

        // No permitir editar rol admin_global
        if ($rol->nombre === 'admin_global') {
            return back()->withErrors(['error' => 'No se puede editar el rol de administrador global']);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|regex:/^[a-z_]+$/|unique:roles,nombre,' . $rol->id,
            'descripcion' => 'nullable|string',
            'nivel_jerarquico' => 'required|integer|min:1|max:5',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        // VALIDACIÓN DE NIVEL JERÁRQUICO
        if ($nivelUsuario == 2) {
            if ($validated['nivel_jerarquico'] < 3) {
                return back()->withErrors([
                    'nivel_jerarquico' => 'Solo puedes asignar nivel 3 o superior'
                ])->withInput();
            }
        } elseif ($nivelUsuario > 2) {
            if ($validated['nivel_jerarquico'] < $nivelUsuario) {
                return back()->withErrors([
                    'nivel_jerarquico' => 'Solo puedes asignar tu nivel o inferior'
                ])->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $rol->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'nivel_jerarquico' => $validated['nivel_jerarquico'],
            ]);

            // Actualizar permisos (validar que sean permitidos)
            if (isset($validated['permisos'])) {
                $permisosPermitidos = $this->obtenerIdsPermisosPermitidosParaNivel($validated['nivel_jerarquico']);
                $permisosAAsignar = array_intersect($validated['permisos'], $permisosPermitidos);

                $rol->permisos()->sync($permisosAAsignar);
            } else {
                $rol->permisos()->detach();
            }

            DB::commit();

            Log::info('Rol actualizado', [
                'rol_id' => $rol->id,
                'actualizado_por' => $user->id
            ]);

            return redirect()->route('roles.index')
                ->with('success', 'Rol actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar rol', [
                'error' => $e->getMessage(),
                'rol_id' => $rol->id
            ]);

            return back()->withErrors(['error' => 'Error al actualizar el rol'])
                ->withInput();
        }
    }

    /**
     * Eliminar rol
     */
    public function destroy(Rol $rol)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (!$user->tienePermiso('gestionar-roles')) {
            abort(403, 'No tienes permisos para eliminar roles');
        }

        // No permitir eliminar roles del sistema
        if ($rol->es_sistema) {
            return back()->withErrors(['error' => 'No se pueden eliminar roles del sistema']);
        }

        // No permitir eliminar roles con usuarios asignados
        if ($rol->usuarios()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar el rol porque tiene usuarios asignados']);
        }

        try {
            $rol->delete();

            Log::info('Rol eliminado', [
                'rol_id' => $rol->id,
                'eliminado_por' => $user->id
            ]);

            return redirect()->route('roles.index')
                ->with('success', 'Rol eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar rol', [
                'error' => $e->getMessage(),
                'rol_id' => $rol->id
            ]);

            return back()->withErrors(['error' => 'Error al eliminar el rol']);
        }
    }

    /**
     * Obtener módulos con permisos según nivel jerárquico
     */
    private function obtenerModulosConPermisos(int $nivel)
    {
        $slugsPermitidos = $this->getSlugsPermitidosPorNivel($nivel);

        $modulos = Modulo::with(['permisos' => function ($query) use ($slugsPermitidos) {
            $query->whereIn('slug', $slugsPermitidos)->orderBy('nombre');
        }])->where('activo', true)
          ->orderBy('orden')
          ->get();

        // Filtrar módulos que tienen al menos un permiso
        return $modulos->filter(function ($modulo) {
            return $modulo->permisos->count() > 0;
        });
    }

    /**
     * Obtener IDs de permisos permitidos para un nivel
     */
    private function obtenerIdsPermisosPermitidosParaNivel(int $nivel)
    {
        $slugsPermitidos = $this->getSlugsPermitidosPorNivel($nivel);

        return Permiso::whereIn('slug', $slugsPermitidos)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Matriz de permisos por nivel jerárquico (según tabla proporcionada)
     */
    private function getSlugsPermitidosPorNivel(int $nivel)
    {
        $matriz = [
            1 => [ // Admin Global - TODOS los permisos
                'ver-dashboard', 'ver-estadisticas-globales',
                'ver-organizaciones', 'crear-organizacion', 'editar-organizacion', 'eliminar-organizacion',
                'asignar-admin-organizacion', 'seleccionar-organizacion-contexto',
                'ver-usuarios', 'crear-usuario', 'editar-usuario', 'asignar-rol', 'eliminar-usuario',
                'cambiar-estado-usuario', 'gestionar-usuarios',
                'ver-roles', 'crear-rol', 'asignar-permisos-rol', 'gestionar-roles', 'ver-permisos',
                'ver-todos-contratos', 'ver-mis-contratos', 'crear-contrato', 'editar-contrato',
                'eliminar-contrato', 'vincular-contratista', 'cambiar-estado-contrato',
                'ver-informacion-contratos', 'gestionar-contratos', 'validar-contratos', 'gestionar-contratista',
                'ver-todas-cuentas', 'ver-mis-cuentas', 'crear-cuenta-cobro', 'editar-cuenta-cobro',
                'radicar-cuenta-cobro', 'revisar-cuenta-cobro', 'aprobar-cuenta-cobro', 'rechazar-cuenta-cobro',
                'registrar-pago', 'anular-cuenta-cobro', 'ver-historial-cuenta', 'aprobar-finalmente',
                'cargar-documentos', 'ver-documentos',
                'autorizar-pago', 'procesar-pago', 'generar-cheques', 'transferir-banco',
                'confirmar-pagos', 'generar-ordenes-pago',
                'ver-presupuesto', 'gestionar-presupuesto', 'aprobacion-final',
                'ver-reportes-globales', 'ver-reportes-organizacion', 'ver-reportes-financieros',
                'exportar-reportes', 'ver-reportes-contratos',
                'agregar-comentarios', 'solicitar-correcciones',
                'ver-configuracion', 'editar-configuracion-global',
            ],
            2 => [ // Admin Organización
                'ver-dashboard',
                'ver-usuarios', 'crear-usuario', 'editar-usuario', 'asignar-rol', 'eliminar-usuario', 'cambiar-estado-usuario',
                'ver-roles', 'crear-rol', 'asignar-permisos-rol', 'gestionar-roles-organizacion',
                'ver-todos-contratos', 'crear-contrato', 'editar-contrato', 'vincular-contratista',
                'cambiar-estado-contrato', 'ver-informacion-contratos', 'gestionar-contratista',
                'ver-todas-cuentas', 'rechazar-cuenta-cobro', 'anular-cuenta-cobro', 'ver-historial-cuenta',
                'cargar-documentos', 'ver-documentos',
                'autorizar-pago',
                'ver-presupuesto', 'gestionar-presupuesto',
                'ver-reportes-organizacion', 'exportar-reportes', 'ver-reportes-contratos',
                'agregar-comentarios', 'solicitar-correcciones',
                'ver-configuracion', 'editar-configuracion-organizacion',
            ],
            3 => [ // Gestión Pública (Supervisores, Ordenadores, Tesoreros)
                'ver-dashboard',
                'ver-roles',
                'ver-todos-contratos', 'ver-mis-contratos', 'cambiar-estado-contrato',
                'ver-informacion-contratos', 'validar-contratos',
                'ver-todas-cuentas', 'revisar-cuenta-cobro', 'aprobar-cuenta-cobro', 'rechazar-cuenta-cobro',
                'registrar-pago', 'ver-historial-cuenta', 'aprobar-finalmente',
                'cargar-documentos', 'ver-documentos',
                'autorizar-pago', 'procesar-pago', 'generar-cheques', 'transferir-banco',
                'confirmar-pagos', 'generar-ordenes-pago',
                'ver-presupuesto', 'aprobacion-final',
                'ver-reportes-financieros', 'exportar-reportes', 'ver-reportes-contratos',
                'agregar-comentarios', 'solicitar-correcciones',
            ],
            4 => [ // Contratista
                'ver-dashboard',
                'ver-mis-contratos', 'ver-informacion-contratos',
                'ver-mis-cuentas', 'crear-cuenta-cobro', 'editar-cuenta-cobro', 'radicar-cuenta-cobro',
                'ver-historial-cuenta',
                'cargar-documentos', 'ver-documentos',
                'agregar-comentarios',
            ],
            5 => [ // Usuario Común
                'ver-dashboard',
                'ver-organizaciones',
            ],
        ];

        return $matriz[$nivel] ?? [];
    }
}
