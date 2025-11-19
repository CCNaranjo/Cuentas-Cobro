<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rol;
use App\Models\Permiso;

class SincronizarPermisos extends Command
{
    protected $signature = 'permisos:sincronizar';
    protected $description = 'Sincronizar permisos de todos los roles';

    public function handle()
    {
        $this->info('=== SINCRONIZACIÓN DE PERMISOS ===');
        $this->newLine();

        // Configuración de permisos por rol
        $configuracion = [
            'admin_global' => 'TODOS',

            'admin_organizacion' => [
                'ver-dashboard',
                'ver-usuarios',
                'crear-usuario',
                'editar-usuario',
                'eliminar-usuario',
                'ver-roles',
                'ver-permisos',
                'ver-todos-contratos',
                'crear-contrato',
                'editar-contrato',
                'eliminar-contrato',
                'validar-contratos',
                'vincular-contratista',
                'ver-todas-cuentas',
                'ver-historial-cuenta',
                'ver-documentos',
                'ver-reportes-organizacion',
                'exportar-reportes',
                'ver-reportes-contratos',
                'ver-configuracion',
            ],

            'contratista' => [
                'ver-dashboard',
                'ver-mis-contratos',
                'ver-informacion-contratos',
                'ver-mis-cuentas',
                'crear-cuenta-cobro',
                'editar-cuenta-cobro',
                'radicar-cuenta-cobro',
                'ver-historial-cuenta',
                'cargar-documentos',
                'ver-documentos',
                'agregar-comentarios',
            ],

            'supervisor' => [
                'ver-dashboard',
                'ver-mis-contratos',
                'ver-informacion-contratos',
                'ver-todas-cuentas',
                'revisar-cuenta-cobro',
                'aprobar-cuenta-cobro',
                'rechazar-cuenta-cobro',
                'agregar-comentarios',
                'solicitar-correcciones',
                'ver-documentos',
                'validar-contratos',
            ],

            'ordenador_gasto' => [
                'ver-dashboard',
                'ver-todos-contratos',
                'ver-todas-cuentas',
                'aprobar-cuenta-cobro',
                'autorizar-pago',
                'ver-presupuesto',
                'ver-informacion-contratos',
                'agregar-comentarios',
                'aprobar-finalmente',
            ],

            'tesorero' => [
                'ver-dashboard',
                'ver-todas-cuentas',
                'procesar-pago',
                'transferir-banco',
                'confirmar-pagos',
                'ver-documentos',
                'aprobar-cuenta-cobro',
                'verificar-presupuesto-cuenta-cobro',
                'generar-ordenes-pago',
            ],

            'revisor_contratacion' => [
                'ver-dashboard',
                'ver-todas-cuentas',
                'ver-historial-cuenta',
                'ver-documentos',
                'agregar-comentarios',
                'aprobar-cuenta-cobro',
                'rechazar-cuenta-cobro',
                'verificar-legal-cuenta-cobro',
                'revisar-cuenta-cobro',
            ],

            'usuario_basico' => [
                'ver-dashboard',
            ],
        ];

        foreach ($configuracion as $rolNombre => $permisosSlugs) {
            $rol = Rol::where('nombre', $rolNombre)->first();

            if (!$rol) {
                $this->warn("  ⚠ Rol '{$rolNombre}' no encontrado");
                continue;
            }

            if ($permisosSlugs === 'TODOS') {
                // Admin Global tiene todos los permisos
                $permisosIds = Permiso::pluck('id');
                $rol->permisos()->sync($permisosIds);
                $this->info("  → {$rol->nombre}: {$permisosIds->count()} permisos (TODOS)");
            } else {
                // Obtener IDs de permisos
                $permisosIds = Permiso::whereIn('slug', $permisosSlugs)->pluck('id');

                // Verificar si faltan permisos
                if ($permisosIds->count() !== count($permisosSlugs)) {
                    $encontrados = Permiso::whereIn('slug', $permisosSlugs)->pluck('slug')->toArray();
                    $faltantes = array_diff($permisosSlugs, $encontrados);
                    $this->warn("  ⚠ {$rol->nombre}: Faltan " . count($faltantes) . " permisos");
                    $this->line("    Permisos faltantes: " . implode(', ', $faltantes));
                }

                $rol->permisos()->sync($permisosIds);
                $this->info("  → {$rol->nombre}: {$permisosIds->count()} permisos asignados");
            }
        }

        $this->newLine();
        $this->info('✓ Sincronización completada');

        return 0;
    }
}
