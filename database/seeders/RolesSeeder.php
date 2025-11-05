<?php

namespace Database\Seeders;

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
        // ============================================
        // 1. ROLES GLOBALES DEL SISTEMA
        // ============================================
        
        $adminGlobal = Rol::create([
            'nombre' => 'admin_global',
            'descripcion' => 'Administrador Global del Sistema - Acceso total',
            'nivel_jerarquico' => 1,
            'es_sistema' => true,
        ]);

        $contratista = Rol::create([
            'nombre' => 'contratista',
            'descripcion' => 'Contratista - Presenta cuentas de cobro',
            'nivel_jerarquico' => 4,
            'es_sistema' => true,
        ]);

        $usuarioBasico = Rol::create([
            'nombre' => 'usuario_basico',
            'descripcion' => 'Usuario sin vinculación específica',
            'nivel_jerarquico' => 5,
            'es_sistema' => true,
        ]);

        // ============================================
        // 2. ROLES PLANTILLA PARA ORGANIZACIONES
        // ============================================
        
        $adminOrganizacion = Rol::create([
            'nombre' => 'admin_organizacion',
            'descripcion' => 'Administrador de Organización',
            'nivel_jerarquico' => 2,
            'es_sistema' => true,
        ]);

        $ordenadorGasto = Rol::create([
            'nombre' => 'ordenador_gasto',
            'descripcion' => 'Ordenador del Gasto - Autoriza los pagos',
            'nivel_jerarquico' => 3,
            'es_sistema' => true,
        ]);

        $supervisor = Rol::create([
            'nombre' => 'supervisor',
            'descripcion' => 'Supervisor - Revisa y valida las cuentas de cobro',
            'nivel_jerarquico' => 3,
            'es_sistema' => true,
        ]);

        $tesorero = Rol::create([
            'nombre' => 'tesorero',
            'descripcion' => 'Tesorero - Procesa los pagos',
            'nivel_jerarquico' => 3,
            'es_sistema' => true,
        ]);

        // ============================================
        // 3. ASIGNAR PERMISOS A ROLES
        // ============================================
        
        $this->asignarPermisos(
            $adminGlobal,
            $adminOrganizacion,
            $contratista,
            $supervisor,
            $ordenadorGasto,
            $tesorero,
            $usuarioBasico
        );

        $this->command->info('✓ Roles creados y permisos asignados correctamente');
    }

    /**
     * Asignar permisos a cada rol
     */
    private function asignarPermisos(
        $adminGlobal,
        $adminOrganizacion,
        $contratista,
        $supervisor,
        $ordenadorGasto,
        $tesorero,
        $usuarioBasico
    ) {
        // ============================================
        // ADMIN GLOBAL - TODOS LOS PERMISOS
        // ============================================
        $todosPermisos = Permiso::all()->pluck('id');
        $adminGlobal->permisos()->sync($todosPermisos);
        $this->command->info('  → Admin Global: ' . $todosPermisos->count() . ' permisos (TODOS)');

        // ============================================
        // ADMIN ORGANIZACIÓN
        // ============================================
        $slugsAdminOrg = [
            // Dashboard
            'ver-dashboard',
            'ver-estadisticas-globales',
            
            // Usuarios
            'ver-usuarios',
            'crear-usuario',
            'editar-usuario',
            'asignar-rol',
            'cambiar-estado-usuario',
            'gestionar-usuarios',
            
            // Contratos
            'ver-todos-contratos',
            'crear-contrato',
            'editar-contrato',
            'vincular-contratista',
            'cambiar-estado-contrato',
            'gestionar-contratos',
            'validar-contratos',
            'gestionar-contratista',
            
            // Cuentas de Cobro
            'ver-todas-cuentas',
            
            // Reportes
            'ver-reportes-organizacion',
        ];
        $this->asignarPermisosPorSlug($adminOrganizacion, $slugsAdminOrg, 'Admin Organización');

        // ============================================
        // CONTRATISTA
        // ============================================
        $slugsContratista = [
            'ver-dashboard',
            'ver-mis-contratos',
            'ver-mis-cuentas',
            'crear-cuenta-cobro',
            'editar-cuenta-cobro',
            'radicar-cuenta-cobro',
            'ver-historial-cuenta',
            'cargar-documentos',
            'ver-documentos',
        ];
        $this->asignarPermisosPorSlug($contratista, $slugsContratista, 'Contratista');

        // ============================================
        // SUPERVISOR
        // ============================================
        $slugsSupervisor = [
            'ver-dashboard',
            'ver-mis-contratos',
            'ver-todas-cuentas',
            'revisar-cuenta-cobro',
            'aprobar-cuenta-cobro',
            'rechazar-cuenta-cobro',
            'agregar-comentarios',
            'solicitar-correcciones',
            'ver-documentos',
            'validar-contratos',
        ];
        $this->asignarPermisosPorSlug($supervisor, $slugsSupervisor, 'Supervisor');

        // ============================================
        // ORDENADOR DEL GASTO
        // ============================================
        $slugsOrdenador = [
            'ver-dashboard',
            'ver-todas-cuentas',
            'aprobar-cuenta-cobro',
            'aprobacion-final',
            'autorizar-pago',
            'ver-presupuesto',
            'gestionar-presupuesto',
            'generar-ordenes-pago',
            'ver-reportes-financieros',
            'agregar-comentarios',
        ];
        $this->asignarPermisosPorSlug($ordenadorGasto, $slugsOrdenador, 'Ordenador del Gasto');

        // ============================================
        // TESORERO
        // ============================================
        $slugsTesorero = [
            'ver-dashboard',
            'ver-todas-cuentas',
            'procesar-pago',
            'registrar-pago',
            'transferir-banco',
            'confirmar-pagos',
            'ver-reportes-financieros',
            'ver-documentos',
        ];
        $this->asignarPermisosPorSlug($tesorero, $slugsTesorero, 'Tesorero');

        // ============================================
        // USUARIO BÁSICO
        // ============================================
        $slugsBasico = [
            'ver-dashboard',
        ];
        $this->asignarPermisosPorSlug($usuarioBasico, $slugsBasico, 'Usuario Básico');
    }

    /**
     * Helper para asignar permisos por slug
     */
    private function asignarPermisosPorSlug($rol, $slugs, $nombreRol)
    {
        $permisos = Permiso::whereIn('slug', $slugs)->pluck('id');
        
        if ($permisos->count() !== count($slugs)) {
            $encontrados = Permiso::whereIn('slug', $slugs)->pluck('slug')->toArray();
            $faltantes = array_diff($slugs, $encontrados);
            $this->command->warn("  ⚠ {$nombreRol}: Faltan " . count($faltantes) . " permisos");
            $this->command->warn("    Permisos faltantes: " . implode(', ', $faltantes));
        }
        
        $rol->permisos()->sync($permisos);
        $this->command->info("  → {$nombreRol}: {$permisos->count()} permisos asignados");
    }
}