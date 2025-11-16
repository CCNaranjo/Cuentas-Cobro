<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * es_organizacion:
     * - true: Permiso disponible para Admin de Organización (nivel 2) y superiores
     * - null/false: Permiso exclusivo de Admin Global (nivel 1)
     */
    public function run(): void
    {
        $permisos = [
            // ============================================
            // DASHBOARD (modulo_id: 1)
            // ============================================
            ['nombre' => 'Ver dashboard', 'slug' => 'ver-dashboard', 'descripcion' => 'Acceso al dashboard principal', 'modulo_id' => 1, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Ver estadísticas globales', 'slug' => 'ver-estadisticas-globales', 'descripcion' => 'Ver métricas del sistema', 'modulo_id' => 1, 'tipo' => 'lectura', 'es_organizacion' => null], // Solo Admin Global
            
            // ============================================
            // ORGANIZACIONES (modulo_id: 2) - TODOS NULL (Solo Admin Global)
            // ============================================
            ['nombre' => 'Ver organizaciones', 'slug' => 'ver-organizaciones', 'descripcion' => 'Listar organizaciones', 'modulo_id' => 2, 'tipo' => 'lectura', 'es_organizacion' => null],
            ['nombre' => 'Crear organización', 'slug' => 'crear-organizacion', 'descripcion' => 'Crear nuevas organizaciones', 'modulo_id' => 2, 'tipo' => 'escritura', 'es_organizacion' => null],
            ['nombre' => 'Editar organización', 'slug' => 'editar-organizacion', 'descripcion' => 'Modificar organizaciones', 'modulo_id' => 2, 'tipo' => 'escritura', 'es_organizacion' => null],
            ['nombre' => 'Eliminar organización', 'slug' => 'eliminar-organizacion', 'descripcion' => 'Eliminar organizaciones', 'modulo_id' => 2, 'tipo' => 'eliminacion', 'es_organizacion' => null],
            ['nombre' => 'Asignar admin organización', 'slug' => 'asignar-admin-organizacion', 'descripcion' => 'Asignar administradores', 'modulo_id' => 2, 'tipo' => 'accion', 'es_organizacion' => null],
            ['nombre' => 'Seleccionar organización contexto', 'slug' => 'seleccionar-organizacion-contexto', 'descripcion' => 'Cambiar entre organizaciones', 'modulo_id' => 2, 'tipo' => 'accion', 'es_organizacion' => null],
            
            // ============================================
            // USUARIOS (modulo_id: 3)
            // ============================================
            ['nombre' => 'Ver usuarios', 'slug' => 'ver-usuarios', 'descripcion' => 'Listar usuarios de la organización', 'modulo_id' => 3, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Crear usuario', 'slug' => 'crear-usuario', 'descripcion' => 'Crear nuevos usuarios', 'modulo_id' => 3, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Editar usuario', 'slug' => 'editar-usuario', 'descripcion' => 'Modificar datos de usuarios', 'modulo_id' => 3, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Eliminar usuario', 'slug' => 'eliminar-usuario', 'descripcion' => 'Eliminar usuarios', 'modulo_id' => 3, 'tipo' => 'eliminacion', 'es_organizacion' => true],
            ['nombre' => 'Asignar rol a usuario', 'slug' => 'asignar-rol-usuario', 'descripcion' => 'Asignar roles', 'modulo_id' => 3, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Vincular usuario pendiente', 'slug' => 'vincular-usuario-pendiente', 'descripcion' => 'Aprobar vinculaciones pendientes', 'modulo_id' => 3, 'tipo' => 'accion', 'es_organizacion' => true],
            
            // ============================================
            // ROLES Y PERMISOS (modulo_id: 4)
            // ============================================
            ['nombre' => 'Ver roles', 'slug' => 'ver-roles', 'descripcion' => 'Listar roles', 'modulo_id' => 4, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Crear rol', 'slug' => 'crear-rol', 'descripcion' => 'Crear nuevos roles', 'modulo_id' => 4, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Editar rol', 'slug' => 'editar-rol', 'descripcion' => 'Modificar roles', 'modulo_id' => 4, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Eliminar rol', 'slug' => 'eliminar-rol', 'descripcion' => 'Eliminar roles', 'modulo_id' => 4, 'tipo' => 'eliminacion', 'es_organizacion' => true],
            ['nombre' => 'Ver permisos', 'slug' => 'ver-permisos', 'descripcion' => 'Listar permisos', 'modulo_id' => 4, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Editar permisos', 'slug' => 'editar-permisos', 'descripcion' => 'Modificar permisos', 'modulo_id' => 4, 'tipo' => 'escritura', 'es_organizacion' => true],
            
            // ============================================
            // CONTRATOS (modulo_id: 5)
            // ============================================
            ['nombre' => 'Ver mis contratos', 'slug' => 'ver-mis-contratos', 'descripcion' => 'Ver contratos asignados', 'modulo_id' => 5, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Ver todos contratos', 'slug' => 'ver-todos-contratos', 'descripcion' => 'Ver todos los contratos de la organización', 'modulo_id' => 5, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Ver información contratos', 'slug' => 'ver-informacion-contratos', 'descripcion' => 'Ver detalles de contratos', 'modulo_id' => 5, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Crear contrato', 'slug' => 'crear-contrato', 'descripcion' => 'Crear nuevos contratos', 'modulo_id' => 5, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Editar contrato', 'slug' => 'editar-contrato', 'descripcion' => 'Modificar contratos', 'modulo_id' => 5, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Eliminar contrato', 'slug' => 'eliminar-contrato', 'descripcion' => 'Eliminar contratos', 'modulo_id' => 5, 'tipo' => 'eliminacion', 'es_organizacion' => true],
            ['nombre' => 'Validar contratos', 'slug' => 'validar-contratos', 'descripcion' => 'Validar vigencia de contratos', 'modulo_id' => 5, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Vincular contratista', 'slug' => 'vincular-contratista', 'descripcion' => 'Vincular contratistas a contratos', 'modulo_id' => 5, 'tipo' => 'accion', 'es_organizacion' => true],
            
            // ============================================
            // CUENTAS DE COBRO (modulo_id: 6)
            // ============================================
            ['nombre' => 'Ver mis cuentas', 'slug' => 'ver-mis-cuentas', 'descripcion' => 'Ver cuentas propias', 'modulo_id' => 6, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Ver todas cuentas', 'slug' => 'ver-todas-cuentas', 'descripcion' => 'Ver todas las cuentas de la organización', 'modulo_id' => 6, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Crear cuenta de cobro', 'slug' => 'crear-cuenta-cobro', 'descripcion' => 'Crear nuevas cuentas', 'modulo_id' => 6, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Editar cuenta de cobro', 'slug' => 'editar-cuenta-cobro', 'descripcion' => 'Modificar cuentas', 'modulo_id' => 6, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Radicar cuenta de cobro', 'slug' => 'radicar-cuenta-cobro', 'descripcion' => 'Radicar cuentas', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Revisar cuenta de cobro', 'slug' => 'revisar-cuenta-cobro', 'descripcion' => 'Revisar cuentas', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Aprobar cuenta de cobro', 'slug' => 'aprobar-cuenta-cobro', 'descripcion' => 'Aprobar cuentas', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Rechazar cuenta de cobro', 'slug' => 'rechazar-cuenta-cobro', 'descripcion' => 'Rechazar cuentas', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Aprobar finalmente', 'slug' => 'aprobar-finalmente', 'descripcion' => 'Aprobación final', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Autorizar pago', 'slug' => 'autorizar-pago', 'descripcion' => 'Autorizar pagos', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Procesar pago', 'slug' => 'procesar-pago', 'descripcion' => 'Procesar pagos', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Generar órdenes de pago', 'slug' => 'generar-ordenes-pago', 'descripcion' => 'Generar órdenes', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Transferir a banco', 'slug' => 'transferir-banco', 'descripcion' => 'Transferir fondos', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Confirmar pagos', 'slug' => 'confirmar-pagos', 'descripcion' => 'Confirmar pagos', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Ver historial cuenta', 'slug' => 'ver-historial-cuenta', 'descripcion' => 'Ver historial', 'modulo_id' => 6, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Cargar documentos', 'slug' => 'cargar-documentos', 'descripcion' => 'Subir documentos', 'modulo_id' => 6, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Ver documentos', 'slug' => 'ver-documentos', 'descripcion' => 'Ver documentos adjuntos', 'modulo_id' => 6, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Ver presupuesto', 'slug' => 'ver-presupuesto', 'descripcion' => 'Ver presupuesto', 'modulo_id' => 6, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Gestionar presupuesto', 'slug' => 'gestionar-presupuesto', 'descripcion' => 'Gestionar presupuesto', 'modulo_id' => 6, 'tipo' => 'escritura', 'es_organizacion' => true],
            // Nuevos permisos añadidos
            ['nombre' => 'Verificar legal cuenta de cobro', 'slug' => 'verificar-legal-cuenta-cobro', 'descripcion' => 'Verificar legalidad de cuentas de cobro', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Verificar presupuesto cuenta de cobro', 'slug' => 'verificar-presupuesto-cuenta-cobro', 'descripcion' => 'Verificar presupuesto (CDP) para cuentas de cobro', 'modulo_id' => 6, 'tipo' => 'accion', 'es_organizacion' => true],
            
            // ============================================
            // REPORTES (modulo_id: 7)
            // ============================================
            ['nombre' => 'Ver reportes globales', 'slug' => 'ver-reportes-globales', 'descripcion' => 'Reportes del sistema', 'modulo_id' => 7, 'tipo' => 'lectura', 'es_organizacion' => null], // Solo Admin Global
            ['nombre' => 'Ver reportes organización', 'slug' => 'ver-reportes-organizacion', 'descripcion' => 'Reportes de la organización', 'modulo_id' => 7, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Ver reportes financieros', 'slug' => 'ver-reportes-financieros', 'descripcion' => 'Reportes contables', 'modulo_id' => 7, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Exportar reportes', 'slug' => 'exportar-reportes', 'descripcion' => 'Descargar reportes', 'modulo_id' => 7, 'tipo' => 'accion', 'es_organizacion' => true],
            ['nombre' => 'Ver reportes de contratos', 'slug' => 'ver-reportes-contratos', 'descripcion' => 'Ver informes de contratos', 'modulo_id' => 7, 'tipo' => 'lectura', 'es_organizacion' => true],
            
            // ============================================
            // OTROS (modulo_id: 8)
            // ============================================
            ['nombre' => 'Agregar comentarios', 'slug' => 'agregar-comentarios', 'descripcion' => 'Comentar en registros', 'modulo_id' => 8, 'tipo' => 'escritura', 'es_organizacion' => true],
            ['nombre' => 'Solicitar correcciones', 'slug' => 'solicitar-correcciones', 'descripcion' => 'Pedir modificaciones', 'modulo_id' => 8, 'tipo' => 'accion', 'es_organizacion' => true],
            
            // ============================================
            // CONFIGURACIÓN (modulo_id: 8)
            // ============================================
            ['nombre' => 'Ver configuración', 'slug' => 'ver-configuracion', 'descripcion' => 'Ver ajustes del sistema', 'modulo_id' => 8, 'tipo' => 'lectura', 'es_organizacion' => true],
            ['nombre' => 'Editar configuración global', 'slug' => 'editar-configuracion-global', 'descripcion' => 'Modificar configuración del sistema', 'modulo_id' => 8, 'tipo' => 'escritura', 'es_organizacion' => null], // Solo Admin Global
            ['nombre' => 'Editar configuración organización', 'slug' => 'editar-configuracion-organizacion', 'descripcion' => 'Modificar ajustes de organización', 'modulo_id' => 8, 'tipo' => 'escritura', 'es_organizacion' => true],
        ];

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(
                ['slug' => $permiso['slug']],
                $permiso
            );
        }

        $this->command->info('✓ Permisos creados/actualizados correctamente');
        $this->command->info('  → Total de permisos: ' . count($permisos));
        
        // Estadísticas
        $permisosOrg = Permiso::where('es_organizacion', true)->count();
        $permisosSistema = Permiso::whereNull('es_organizacion')->count();
        $this->command->info('  → Permisos de Organización: ' . $permisosOrg);
        $this->command->info('  → Permisos de Sistema (Admin Global): ' . $permisosSistema);
    }
}