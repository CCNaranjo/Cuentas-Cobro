<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // Dashboard (modulo_id: 1)
            ['nombre' => 'Ver dashboard', 'slug' => 'ver-dashboard', 'modulo_id' => 1],
            ['nombre' => 'Ver estadísticas globales', 'slug' => 'ver-estadisticas-globales', 'modulo_id' => 1],
            
            // Alcaldías (modulo_id: 2)
            ['nombre' => 'Ver alcaldías', 'slug' => 'ver-organizacion', 'modulo_id' => 2],
            ['nombre' => 'Crear alcaldía', 'slug' => 'crear-organizacion', 'modulo_id' => 2],
            ['nombre' => 'Editar alcaldía', 'slug' => 'editar-organizacion', 'modulo_id' => 2],
            ['nombre' => 'Eliminar alcaldía', 'slug' => 'eliminar-organizacion', 'modulo_id' => 2],
            ['nombre' => 'Ver configuración alcaldía', 'slug' => 'ver-configuracion-organizacion', 'modulo_id' => 2],
            
            // Usuarios (modulo_id: 3)
            ['nombre' => 'Ver usuarios', 'slug' => 'ver-usuarios', 'modulo_id' => 3],
            ['nombre' => 'Crear usuario', 'slug' => 'crear-usuario', 'modulo_id' => 3],
            ['nombre' => 'Editar usuario', 'slug' => 'editar-usuario', 'modulo_id' => 3],
            ['nombre' => 'Asignar rol', 'slug' => 'asignar-rol', 'modulo_id' => 3],
            ['nombre' => 'Eliminar usuario', 'slug' => 'eliminar-usuario', 'modulo_id' => 3],
            ['nombre' => 'Ver usuarios pendientes', 'slug' => 'ver-usuarios-pendientes', 'modulo_id' => 3],
            
            // Contratos (modulo_id: 4)
            ['nombre' => 'Ver todos los contratos', 'slug' => 'ver-todos-contratos', 'modulo_id' => 4],
            ['nombre' => 'Ver mis contratos', 'slug' => 'ver-mis-contratos', 'modulo_id' => 4],
            ['nombre' => 'Crear contrato', 'slug' => 'crear-contrato', 'modulo_id' => 4],
            ['nombre' => 'Editar contrato', 'slug' => 'editar-contrato', 'modulo_id' => 4],
            ['nombre' => 'Eliminar contrato', 'slug' => 'eliminar-contrato', 'modulo_id' => 4],
            ['nombre' => 'Vincular contratista', 'slug' => 'vincular-contratista', 'modulo_id' => 4],
            ['nombre' => 'Asignar supervisor', 'slug' => 'asignar-supervisor', 'modulo_id' => 4],
            
            // Cuentas de Cobro (modulo_id: 5)
            ['nombre' => 'Ver todas las cuentas', 'slug' => 'ver-todas-cuentas', 'modulo_id' => 5],
            ['nombre' => 'Ver mis cuentas', 'slug' => 'ver-mis-cuentas', 'modulo_id' => 5],
            ['nombre' => 'Crear cuenta cobro', 'slug' => 'crear-cuenta-cobro', 'modulo_id' => 5],
            ['nombre' => 'Editar cuenta cobro borrador', 'slug' => 'editar-cuenta-cobro', 'modulo_id' => 5],
            ['nombre' => 'Radicar cuenta cobro', 'slug' => 'radicar-cuenta-cobro', 'modulo_id' => 5],
            ['nombre' => 'Revisar cuenta cobro', 'slug' => 'revisar-cuenta-cobro', 'modulo_id' => 5],
            ['nombre' => 'Aprobar cuenta cobro', 'slug' => 'aprobar-cuenta-cobro', 'modulo_id' => 5],
            ['nombre' => 'Rechazar cuenta cobro', 'slug' => 'rechazar-cuenta-cobro', 'modulo_id' => 5],
            ['nombre' => 'Registrar pago', 'slug' => 'registrar-pago', 'modulo_id' => 5],
            ['nombre' => 'Anular cuenta cobro', 'slug' => 'anular-cuenta-cobro', 'modulo_id' => 5],
            ['nombre' => 'Ver historial cuenta', 'slug' => 'ver-historial-cuenta', 'modulo_id' => 5],
            
            // Reportes (modulo_id: 6)
            ['nombre' => 'Ver reportes globales', 'slug' => 'ver-reportes-globales', 'modulo_id' => 6],
            ['nombre' => 'Ver reportes alcaldía', 'slug' => 'ver-reportes-organizacion', 'modulo_id' => 6],
            ['nombre' => 'Ver reportes financieros', 'slug' => 'ver-reportes-financieros', 'modulo_id' => 6],
            ['nombre' => 'Exportar reportes', 'slug' => 'exportar-reportes', 'modulo_id' => 6],
            
            // Configuración (modulo_id: 7)
            ['nombre' => 'Ver configuración', 'slug' => 'ver-configuracion', 'modulo_id' => 7],
            ['nombre' => 'Editar configuración global', 'slug' => 'editar-configuracion-global', 'modulo_id' => 7],
            ['nombre' => 'Editar configuración alcaldía', 'slug' => 'editar-configuracion-organizacion', 'modulo_id' => 7],
        ];

        foreach ($permisos as $permiso) {
            Permiso::create($permiso);
        }

        $this->command->info('✓ Permisos creados correctamente');
    }
}
