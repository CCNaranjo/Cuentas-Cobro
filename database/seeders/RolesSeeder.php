<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminGlobal = Rol::create([
            'nombre' => 'admin_global',
            'descripcion' => 'Administrador Global del Sistema',
            'nivel_jerarquico' => 1,
            'es_sistema' => true,
        ]);

        $contratista = Rol::create([
            'nombre' => 'contratista',
            'descripcion' => 'Contratista',
            'nivel_jerarquico' => 4,
            'es_sistema' => true,
        ]);

        $usuarioBasico = Rol::create([
            'nombre' => 'usuario_basico',
            'descripcion' => 'Usuario sin vinculación',
            'nivel_jerarquico' => 5,
            'es_sistema' => true,
        ]);

        // 2. CREAR ROLES BASE PARA ALCALDÍAS (plantillas)
        $rolesorganizacion = [
            [
                'nombre' => 'admin_organizacion',
                'descripcion' => 'Administrador de Alcaldía',
                'nivel_jerarquico' => 2,
                'es_sistema' => true,
            ],
            [
                'nombre' => 'ordenador_gasto',
                'descripcion' => 'Ordenador del Gasto',
                'nivel_jerarquico' => 3,
                'es_sistema' => true,
            ],
            [
                'nombre' => 'supervisor',
                'descripcion' => 'Supervisor de Contratos',
                'nivel_jerarquico' => 3,
                'es_sistema' => true,
            ],
            [
                'nombre' => 'tesorero',
                'descripcion' => 'Tesorero',
                'nivel_jerarquico' => 3,
                'es_sistema' => true,
            ],
        ];

        foreach ($rolesorganizacion as $rol) {
            Rol::create($rol);
        }

        // 3. ASIGNAR PERMISOS A ROLES
        $this->asignarPermisos($adminGlobal, $contratista, $usuarioBasico);

        $this->command->info('✓ Roles creados y permisos asignados correctamente');

    }

    private function asignarPermisos($adminGlobal, $contratista, $usuarioBasico)
    {
        // ADMIN GLOBAL - TODOS LOS PERMISOS
        $todosPermisos = Permiso::all()->pluck('id');
        $adminGlobal->permisos()->attach($todosPermisos);

        // CONTRATISTA - Solo sus recursos
        $slugsContratista = [
            'ver-dashboard',
            'ver-mis-contratos',
            'ver-mis-cuentas',
            'crear-cuenta-cobro',
            'editar-cuenta-cobro',
            'radicar-cuenta-cobro',
            'ver-historial-cuenta',
        ];

        // 1. Obtener solo los IDs de los permisos
        $permisosContratista = Permiso::whereIn('slug', $slugsContratista)->pluck('id');

        // Opcional: Para debug, verificar cuántos encontró
        if ($permisosContratista->count() !== count($slugsContratista)) {
            $this->command->warning("Seeder: No se encontraron todos los permisos para Contratista.");
        }

        // 2. Vincular (usando sync para mayor robustez)
        $contratista->permisos()->sync($permisosContratista); 
        $this->command->info("Permisos asignados al rol Contratista ({$permisosContratista->count()} permisos).");

        $slugsUsuarioBasico = [
            'ver-dashboard'
        ];

        // 1. Obtener solo los IDs de los permisos
        $permisosUsuarioBasico = Permiso::whereIn('slug', $slugsUsuarioBasico)->pluck('id');

        // Opcional: Para debug, verificar cuántos encontró
        if ($permisosUsuarioBasico->count() !== count($slugsUsuarioBasico)) {
            $this->command->warning("Seeder: No se encontraron todos los permisos para Contratista.");
        }

        // 2. Vincular (usando sync para mayor robustez)
        $usuarioBasico->permisos()->sync($permisosUsuarioBasico); 
        $this->command->info("Permisos asignados al rol UsuarioBasico ({$permisosUsuarioBasico->count()} permisos).");
    }
}
