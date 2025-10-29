<?php

namespace Database\Seeders;

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
            // ============================================
            // DASHBOARD (modulo_id: 1)
            // ============================================
            ['nombre' => 'Ver dashboard', 'slug' => 'ver-dashboard', 'descripcion' => 'Acceso al dashboard principal', 'modulo_id' => 1],
            ['nombre' => 'Ver estadísticas globales', 'slug' => 'ver-estadisticas-globales', 'descripcion' => 'Ver métricas del sistema', 'modulo_id' => 1],
            
            // ============================================
            // ORGANIZACIONES (modulo_id: 2)
            // ============================================
            ['nombre' => 'Ver organizaciones', 'slug' => 'ver-organizaciones', 'descripcion' => 'Listar organizaciones', 'modulo_id' => 2],
            ['nombre' => 'Crear organización', 'slug' => 'crear-organizacion', 'descripcion' => 'Crear nuevas organizaciones', 'modulo_id' => 2],
            ['nombre' => 'Editar organización', 'slug' => 'editar-organizacion', 'descripcion' => 'Modificar organizaciones', 'modulo_id' => 2],
            ['nombre' => 'Eliminar organización', 'slug' => 'eliminar-organizacion', 'descripcion' => 'Eliminar organizaciones', 'modulo_id' => 2],
            ['nombre' => 'Asignar admin organización', 'slug' => 'asignar-admin-organizacion', 'descripcion' => 'Asignar administradores', 'modulo_id' => 2],
            
            // ============================================
            // USUARIOS (modulo_id: 3)
            // ============================================
            ['nombre' => 'Ver usuarios', 'slug' => 'ver-usuarios', 'descripcion' => 'Listar usuarios de la organización', 'modulo_id' => 3],
            ['nombre' => 'Crear usuario', 'slug' => 'crear-usuario', 'descripcion' => 'Crear nuevos usuarios', 'modulo_id' => 3],
            ['nombre' => 'Editar usuario', 'slug' => 'editar-usuario', 'descripcion' => 'Modificar datos de usuarios', 'modulo_id' => 3],
            ['nombre' => 'Asignar rol', 'slug' => 'asignar-rol', 'descripcion' => 'Asignar roles a usuarios', 'modulo_id' => 3],
            ['nombre' => 'Eliminar usuario', 'slug' => 'eliminar-usuario', 'descripcion' => 'Eliminar usuarios', 'modulo_id' => 3],
            ['nombre' => 'Cambiar estado usuario', 'slug' => 'cambiar-estado-usuario', 'descripcion' => 'Activar/desactivar usuarios', 'modulo_id' => 3],
            ['nombre' => 'Gestionar usuarios', 'slug' => 'gestionar-usuarios', 'descripcion' => 'Administración completa de usuarios', 'modulo_id' => 3],
            
            // ============================================
            // Roles (modulo_id: 4)
            // ============================================
            ['nombre' => 'Ver roles', 'slug' => 'ver-roles', 'descripcion' => 'Listar todos los roles', 'modulo_id' => 4],
            ['nombre' => 'Crear rol', 'slug' => 'crear-rol', 'descripcion' => 'Crear nuevos roles', 'modulo_id' => 4],
            ['nombre' => 'Asignar permisos rol', 'slug' => 'asignar-permisos-rol', 'descripcion' => 'Asignar permisos a roles', 'modulo_id' => 4],
            ['nombre' => 'Gestionar roles', 'slug' => 'gestionar-roles', 'descripcion' => 'Administración completa de roles y permisos', 'modulo_id' => 4],
            ['nombre' => 'Gestionar roles de organizacion', 'slug' => 'gestionar-roles-organizacion', 'descripcion' => 'Administración de roles y permisos asociados a la organizacion', 'modulo_id' => 4],
            ['nombre' => 'Ver Permisos', 'slug' => 'ver-permisos', 'descripcion' => 'Ver los permisos', 'modulo_id' => 4],

            // ============================================
            // CONTRATOS (modulo_id: 5)
            // ============================================
            ['nombre' => 'Ver todos los contratos', 'slug' => 'ver-todos-contratos', 'descripcion' => 'Ver contratos de la organización', 'modulo_id' => 5],
            ['nombre' => 'Ver mis contratos', 'slug' => 'ver-mis-contratos', 'descripcion' => 'Ver solo contratos propios', 'modulo_id' => 5],
            ['nombre' => 'Crear contrato', 'slug' => 'crear-contrato', 'descripcion' => 'Crear nuevos contratos', 'modulo_id' => 5],
            ['nombre' => 'Editar contrato', 'slug' => 'editar-contrato', 'descripcion' => 'Modificar contratos', 'modulo_id' => 5],
            ['nombre' => 'Eliminar contrato', 'slug' => 'eliminar-contrato', 'descripcion' => 'Eliminar contratos', 'modulo_id' => 5],
            ['nombre' => 'Vincular contratista', 'slug' => 'vincular-contratista', 'descripcion' => 'Asignar contratistas a contratos', 'modulo_id' => 5],
            ['nombre' => 'Cambiar estado contrato', 'slug' => 'cambiar-estado-contrato', 'descripcion' => 'Modificar estado de contratos', 'modulo_id' => 5],
            ['nombre' => 'Ver información de contratos', 'slug' => 'ver-informacion-contratos', 'descripcion' => 'Consultar detalles de contratos', 'modulo_id' => 5],
            ['nombre' => 'Gestionar contratos', 'slug' => 'gestionar-contratos', 'descripcion' => 'Administración completa de contratos', 'modulo_id' => 5],
            ['nombre' => 'Validación de contratos', 'slug' => 'validar-contratos', 'descripcion' => 'Validar contratos', 'modulo_id' => 5],
            ['nombre' => 'Gestionar contratistas', 'slug' => 'gestionar-contratista', 'descripcion' => 'Administrar contratistas', 'modulo_id' => 5],
            
            // ============================================
            // CUENTAS DE COBRO (modulo_id: 6)
            // ============================================
            ['nombre' => 'Ver todas las cuentas', 'slug' => 'ver-todas-cuentas', 'descripcion' => 'Ver todas las cuentas de cobro', 'modulo_id' => 6],
            ['nombre' => 'Ver mis cuentas', 'slug' => 'ver-mis-cuentas', 'descripcion' => 'Ver solo cuentas propias', 'modulo_id' => 6],
            ['nombre' => 'Crear cuenta cobro', 'slug' => 'crear-cuenta-cobro', 'descripcion' => 'Crear nuevas cuentas de cobro', 'modulo_id' => 6],
            ['nombre' => 'Editar cuenta cobro', 'slug' => 'editar-cuenta-cobro', 'descripcion' => 'Modificar cuentas en borrador', 'modulo_id' => 6],
            ['nombre' => 'Radicar cuenta cobro', 'slug' => 'radicar-cuenta-cobro', 'descripcion' => 'Radicar cuentas para revisión', 'modulo_id' => 6],
            ['nombre' => 'Revisar cuenta cobro', 'slug' => 'revisar-cuenta-cobro', 'descripcion' => 'Revisar cuentas radicadas', 'modulo_id' => 6],
            ['nombre' => 'Aprobar cuenta cobro', 'slug' => 'aprobar-cuenta-cobro', 'descripcion' => 'Aprobar cuentas revisadas', 'modulo_id' => 6],
            ['nombre' => 'Rechazar cuenta cobro', 'slug' => 'rechazar-cuenta-cobro', 'descripcion' => 'Rechazar cuentas', 'modulo_id' => 6],
            ['nombre' => 'Registrar pago', 'slug' => 'registrar-pago', 'descripcion' => 'Registrar pagos realizados', 'modulo_id' => 6],
            ['nombre' => 'Anular cuenta cobro', 'slug' => 'anular-cuenta-cobro', 'descripcion' => 'Anular cuentas de cobro', 'modulo_id' => 6],
            ['nombre' => 'Ver historial cuenta', 'slug' => 'ver-historial-cuenta', 'descripcion' => 'Ver historial de cambios', 'modulo_id' => 6],
            ['nombre' => 'Aprobación final', 'slug' => 'aprovar-finalmente', 'descripcion' => 'Aprobación ejecutiva final', 'modulo_id' => 6],
            
            // ============================================
            // DOCUMENTOS
            // ============================================
            ['nombre' => 'Subir documentos', 'slug' => 'cargar-documentos', 'descripcion' => 'Cargar archivos al sistema', 'modulo_id' => 6],
            ['nombre' => 'Ver documentos', 'slug' => 'ver-documentos', 'descripcion' => 'Consultar documentos', 'modulo_id' => 6],
            
            // ============================================
            // PAGOS
            // ============================================
            ['nombre' => 'Autorizar pago', 'slug' => 'autorizar-pago', 'descripcion' => 'Autorizar pagos', 'modulo_id' => 6],
            ['nombre' => 'Procesar pago', 'slug' => 'procesar-pago', 'descripcion' => 'Ejecutar pagos', 'modulo_id' => 6],
            ['nombre' => 'Generar cheques', 'slug' => 'generar-chekes', 'descripcion' => 'Emitir cheques', 'modulo_id' => 6],
            ['nombre' => 'Transferencias bancarias', 'slug' => 'transferir-banco', 'descripcion' => 'Realizar transferencias', 'modulo_id' => 6],
            ['nombre' => 'Confirmación de pagos', 'slug' => 'confirmar-pagos', 'descripcion' => 'Confirmar pagos', 'modulo_id' => 6],
            ['nombre' => 'Generar órdenes de pago', 'slug' => 'generar-ordenes-de-pago', 'descripcion' => 'Crear órdenes de pago', 'modulo_id' => 6],
            
            // ============================================
            // PRESUPUESTO
            // ============================================
            ['nombre' => 'Ver presupuesto', 'slug' => 'ver-presupuesto', 'descripcion' => 'Consultar presupuesto', 'modulo_id' => 7],
            ['nombre' => 'Gestionar presupuesto', 'slug' => 'generar-presupuesto', 'descripcion' => 'Administrar presupuesto', 'modulo_id' => 7],
            
            // ============================================
            // REPORTES (modulo_id: 7)
            // ============================================
            ['nombre' => 'Ver reportes globales', 'slug' => 'ver-reportes-globales', 'descripcion' => 'Reportes del sistema completo', 'modulo_id' => 7],
            ['nombre' => 'Ver reportes organización', 'slug' => 'ver-reportes-organizacion', 'descripcion' => 'Reportes de la organización', 'modulo_id' => 7],
            ['nombre' => 'Ver reportes financieros', 'slug' => 'ver-reportes-financieros', 'descripcion' => 'Reportes contables', 'modulo_id' => 7],
            ['nombre' => 'Exportar reportes', 'slug' => 'exportar-reportes', 'descripcion' => 'Descargar reportes', 'modulo_id' => 7],
            ['nombre' => 'Ver reportes de contratos', 'slug' => 'ver-reportes-contratos', 'descripcion' => 'Ver informes de contratos', 'modulo_id' => 7],
            
            // ============================================
            // OTROS
            // ============================================
            ['nombre' => 'Agregar comentarios', 'slug' => 'agregar-comentarios', 'descripcion' => 'Comentar en registros', 'modulo_id' => 8],
            ['nombre' => 'Solicitar correcciones', 'slug' => 'solicitar-correcciones', 'descripcion' => 'Pedir modificaciones', 'modulo_id' => 8],
            
            // ============================================
            // CONFIGURACIÓN (modulo_id: 8)
            // ============================================
            ['nombre' => 'Ver configuración', 'slug' => 'ver-configuracion', 'descripcion' => 'Ver ajustes del sistema', 'modulo_id' => 8],
            ['nombre' => 'Editar configuración global', 'slug' => 'editar-configuracion-global', 'descripcion' => 'Modificar configuración del sistema', 'modulo_id' => 8],
            ['nombre' => 'Editar configuración organización', 'slug' => 'editar-configuracion-organizacion', 'descripcion' => 'Modificar ajustes de organización', 'modulo_id' => 8],
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(
                ['slug' => $permiso['slug']],
                $permiso
            );
        }

        $this->command->info('✓ Permisos creados/actualizados correctamente');
        $this->command->info('  → Total de permisos: ' . count($permisos));
    }
}