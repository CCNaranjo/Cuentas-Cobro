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

        $revisorContratacion = Rol::create([
            'nombre' => 'revisor_contratacion',
            'descripcion' => 'Revisor de Contratación - Verifica legalidad de cuentas',
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
            $usuarioBasico,
            $revisorContratacion
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
        $usuarioBasico,
        $revisorContratacion
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
            'ver-dashboard',
            'ver-usuarios',
            'crear-usuario',
            'editar-usuario',
            'eliminar-usuario',
            'asignar-rol-usuario',
            'ver-roles',
            'ver-permisos',
            'editar-roles',
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
            'editar-configuracion-organizacion',
        ];
        $this->asignarPermisosPorSlug($adminOrganizacion, $slugsAdminOrg, 'Admin Organización');

        // ============================================
        // CONTRATISTA
        // ============================================
        $slugsContratista = [
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
        ];
        $this->asignarPermisosPorSlug($contratista, $slugsContratista, 'Contratista');

        // ============================================
        // SUPERVISOR
        // ============================================
        $slugsSupervisor = [
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
        ];
        $this->asignarPermisosPorSlug($supervisor, $slugsSupervisor, 'Supervisor');

        // ============================================
        // ORDENADOR DEL GASTO
        // ============================================
        $slugsOrdenador = [
            'ver-dashboard',
            'ver-todos-contratos',
            'ver-todas-cuentas',
            'aprobar-finalmente',
            'autorizar-pago',
            'ver-presupuesto',
            'gestionar-presupuesto',
            'ver-informacion-contratos',
            'ver-reportes-financieros',
            'agregar-comentarios',
            'ver-ordenes-pago',
            'aprobar-orden-pago',
        ];
        $this->asignarPermisosPorSlug($ordenadorGasto, $slugsOrdenador, 'Ordenador del Gasto');

        // ============================================
        // TESORERO
        // ============================================
        $slugsTesorero = [
            'ver-dashboard',
            'ver-todas-cuentas',
            'procesar-pago',
            'registrar-orden-pago',
            'transferir-banco',
            'confirmar-pagos',
            'ver-reportes-financieros',
            'ver-documentos',
            'verificar-presupuesto-cuenta-cobro',
            'ver-ordenes-pago',
            'crear-orden-pago',
            'anular-orden-pago',
        ];
        $this->asignarPermisosPorSlug($tesorero, $slugsTesorero, 'Tesorero');

        // ============================================
        // REVISOR CONTRATACIÓN (Nuevo)
        // ============================================
        $slugsRevisor = [
            'ver-dashboard',
            'ver-todas-cuentas',
            'ver-historial-cuenta',
            'ver-documentos',
            'agregar-comentarios',
            'verificar-legal-cuenta-cobro',
            'rechazar-cuenta-cobro',
        ];
        $this->asignarPermisosPorSlug($revisorContratacion, $slugsRevisor, 'Revisor Contratación');

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
