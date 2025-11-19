<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Modulo;

class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos = [
            [
                'nombre' => 'Dashboard',
                'slug' => 'dashboard',
                'icono' => 'fa-home',
                'orden' => 1,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Organizaciones',
                'slug' => 'organizaciones',
                'icono' => 'fa-building',
                'orden' => 2,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Usuarios',
                'slug' => 'usuarios',
                'icono' => 'fa-users',
                'orden' => 3,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Roles',
                'slug' => 'roles',
                'icono' => 'fa-user-shield',
                'orden' => 4,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Contratos',
                'slug' => 'contratos',
                'icono' => 'fa-file-contract',
                'orden' => 5,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Cuentas de Cobro',
                'slug' => 'cuentas-cobro',
                'icono' => 'fa-file-invoice-dollar',
                'orden' => 6,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Reportes',
                'slug' => 'reportes',
                'icono' => 'fa-chart-bar',
                'orden' => 7,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Configuración',
                'slug' => 'configuracion',
                'icono' => 'fa-cog',
                'orden' => 8,
                'activo' => true,
                'parent_id' => null
            ],
            [
                'nombre' => 'Notificaciones',
                'slug' => 'notificaciones',
                'icono' => 'fa-bell',
                'orden' => 9,
                'activo' => true,
                'parent_id' => null
            ],
        ];

        foreach ($modulos as $modulo) {
            Modulo::updateOrCreate(
                ['slug' => $modulo['slug']],
                $modulo
            );
        }

        $this->command->info('✓ Módulos creados correctamente');
    }
}
