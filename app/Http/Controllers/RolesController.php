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
                    'nivel_jerarquico' => 'Solo puedes crear roles de nivel 3 o inferior'
                ])->withInput();
            }
        } elseif ($nivelUsuario > 2) {
            if ($validated['nivel_jerarquico'] < $nivelUsuario) {
                return back()->withErrors([
                    'nivel_jerarquico' => 'Solo puedes crear roles de tu nivel o superior'
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
        
        if ($nivelUsuario == 2 && $rol->nivel_jerarquico < 3) {
            abort(403, 'No tienes permisos para ver este rol');
        } elseif ($nivelUsuario > 2 && $rol->nivel_jerarquico < $nivelUsuario) {
            abort(403, 'No tienes permisos para ver este rol');
        }
        
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
        
        if ($nivelUsuario == 2 && $rol->nivel_jerarquico < 3) {
            abort(403, 'No puedes editar roles de nivel inferior a 3');
        } elseif ($nivelUsuario > 2 && $rol->nivel_jerarquico < $nivelUsuario) {
            abort(403, 'No puedes editar roles de nivel superior al tuyo');
        }
        
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
        
        if ($nivelUsuario == 2 && $rol->nivel_jerarquico < 3) {
            return back()->withErrors(['error' => 'No puedes editar roles de nivel inferior a 3']);
        } elseif ($nivelUsuario > 2 && $rol->nivel_jerarquico < $nivelUsuario) {
            return back()->withErrors(['error' => 'No puedes editar roles de nivel superior al tuyo']);
        }
        
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
        
        if ($rol->es_sistema) {
            return back()->withErrors(['error' => 'No se pueden eliminar roles del sistema']);
        }
        
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

    // ============================================
    // GESTIÓN DE PERMISOS
    // ============================================

    /**
     * Mostrar lista de permisos
     */
    public function indexPermisos()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->tienePermiso('ver-permisos')) {
            abort(403, 'No tienes permisos para ver permisos del sistema');
        }
        
        $nivelUsuario = $user->obtenerNivelJerarquico();
        
        // Obtener módulos con sus permisos filtrados por nivel
        $modulos = $this->obtenerModulosConPermisos($nivelUsuario);
        
        // Estadísticas
        if ($nivelUsuario == 1) {
            $totalPermisos = Permiso::count();
        } else {
            $totalPermisos = Permiso::where('es_organizacion', true)->count();
        }
        
        $rolesConPermisos = Rol::has('permisos')->count();
        
        return view('roles.permisos_index', compact(
            'modulos',
            'totalPermisos',
            'rolesConPermisos',
            'nivelUsuario'
        ));
    }

    /**
     * Crear nuevo permiso
     */
    public function storePermiso(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->tienePermiso('ver-permisos')) {
            return back()->withErrors(['error' => 'No tienes permisos para crear permisos']);
        }
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:permisos,slug|regex:/^[a-z\-]+$/',
            'modulo_id' => 'required|exists:modulos,id',
            'descripcion' => 'nullable|string|max:255',
            'tipo' => 'required|in:lectura,escritura,eliminacion,accion',
            'es_organizacion' => 'nullable|boolean',
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio',
            'slug.required' => 'El slug es obligatorio',
            'slug.unique' => 'Ya existe un permiso con este slug',
            'slug.regex' => 'El slug solo puede contener letras minúsculas y guiones',
            'modulo_id.required' => 'Debes seleccionar un módulo',
            'modulo_id.exists' => 'El módulo seleccionado no existe',
            'tipo.required' => 'Debes seleccionar un tipo de permiso',
        ]);
        
        try {
            $permiso = Permiso::create([
                'nombre' => $validated['nombre'],
                'slug' => $validated['slug'],
                'modulo_id' => $validated['modulo_id'],
                'descripcion' => $validated['descripcion'] ?? null,
                'tipo' => $validated['tipo'],
                'es_organizacion' => isset($validated['es_organizacion']) && $validated['es_organizacion'] ? true : null,
            ]);
            
            Log::info('Permiso creado', [
                'permiso_id' => $permiso->id,
                'creado_por' => $user->id,
                'slug' => $permiso->slug,
                'es_organizacion' => $permiso->es_organizacion
            ]);
            
            return redirect()->route('permisos.index')
                ->with('success', 'Permiso creado exitosamente');
                
        } catch (\Exception $e) {
            Log::error('Error al crear permiso', [
                'error' => $e->getMessage(),
                'usuario_id' => $user->id
            ]);
            
            return back()->withErrors(['error' => 'Error al crear el permiso: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Actualizar permiso existente
     */
    public function updatePermiso(Request $request, Permiso $permiso)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->tienePermiso('ver-permisos')) {
            return back()->withErrors(['error' => 'No tienes permisos para editar permisos']);
        }
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|max:100|regex:/^[a-z\-]+$/|unique:permisos,slug,' . $permiso->id,
            'modulo_id' => 'required|exists:modulos,id',
            'descripcion' => 'nullable|string|max:255',
            'tipo' => 'required|in:lectura,escritura,eliminacion,accion',
            'es_organizacion' => 'nullable|boolean',
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio',
            'slug.required' => 'El slug es obligatorio',
            'slug.unique' => 'Ya existe un permiso con este slug',
            'slug.regex' => 'El slug solo puede contener letras minúsculas y guiones',
            'modulo_id.required' => 'Debes seleccionar un módulo',
            'tipo.required' => 'Debes seleccionar un tipo de permiso',
        ]);
        
        try {
            $permiso->update([
                'nombre' => $validated['nombre'],
                'slug' => $validated['slug'],
                'modulo_id' => $validated['modulo_id'],
                'descripcion' => $validated['descripcion'] ?? null,
                'tipo' => $validated['tipo'],
                'es_organizacion' => isset($validated['es_organizacion']) && $validated['es_organizacion'] ? true : null,
            ]);
            
            Log::info('Permiso actualizado', [
                'permiso_id' => $permiso->id,
                'actualizado_por' => $user->id,
                'es_organizacion' => $permiso->es_organizacion
            ]);
            
            return redirect()->route('permisos.index')
                ->with('success', 'Permiso actualizado exitosamente');
                
        } catch (\Exception $e) {
            Log::error('Error al actualizar permiso', [
                'error' => $e->getMessage(),
                'permiso_id' => $permiso->id
            ]);
            
            return back()->withErrors(['error' => 'Error al actualizar el permiso'])
                ->withInput();
        }
    }

    /**
     * Eliminar permiso
     */
    public function destroyPermiso(Permiso $permiso)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->tienePermiso('ver-permisos')) {
            abort(403, 'No tienes permisos para eliminar permisos');
        }
        
        // Verificar que el permiso no esté asignado a ningún rol
        if ($permiso->roles()->count() > 0) {
            return back()->withErrors([
                'error' => 'No se puede eliminar el permiso porque está asignado a ' . 
                          $permiso->roles()->count() . ' rol(es)'
            ]);
        }
        
        try {
            $slug = $permiso->slug;
            $permiso->delete();
            
            Log::info('Permiso eliminado', [
                'permiso_slug' => $slug,
                'eliminado_por' => $user->id
            ]);
            
            return redirect()->route('permisos.index')
                ->with('success', 'Permiso eliminado exitosamente');
                
        } catch (\Exception $e) {
            Log::error('Error al eliminar permiso', [
                'error' => $e->getMessage(),
                'permiso_id' => $permiso->id
            ]);
            
            return back()->withErrors(['error' => 'Error al eliminar el permiso']);
        }
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Obtener módulos con permisos según nivel jerárquico
     */
    private function obtenerModulosConPermisos(int $nivel)
    {
        $modulos = Modulo::with(['permisos' => function($query) use ($nivel) {
            // Filtrar permisos según nivel
            if ($nivel == 1) {
                // Admin Global ve TODOS los permisos
                $query->orderBy('nombre');
            } elseif ($nivel == 2) {
                // Admin Organización solo ve permisos con es_organizacion = true
                $query->where('es_organizacion', true)->orderBy('nombre');
            } else {
                // Nivel 3+ ve permisos de organización
                $query->where('es_organizacion', true)->orderBy('nombre');
            }
        }])->where('activo', true)
          ->orderBy('orden')
          ->get();
        
        // Filtrar módulos que tienen al menos un permiso accesible
        return $modulos->filter(function($modulo) {
            return $modulo->permisos->count() > 0;
        });
    }

    /**
     * Obtener IDs de permisos permitidos para un nivel
     */
    private function obtenerIdsPermisosPermitidosParaNivel(int $nivel)
    {
        if ($nivel == 1) {
            // Admin Global: todos los permisos
            return Permiso::pluck('id')->toArray();
        } elseif ($nivel == 2) {
            // Admin Organización: solo permisos con es_organizacion = true
            return Permiso::where('es_organizacion', true)->pluck('id')->toArray();
        } else {
            // Nivel 3+: permisos de organización
            return Permiso::where('es_organizacion', true)->pluck('id')->toArray();
        }
    }
}