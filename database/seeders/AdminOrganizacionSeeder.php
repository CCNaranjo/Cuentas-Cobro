<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminOrganizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Buscar la organización de Chía
            $organizacion = Organizacion::where('nombre_oficial', 'Alcaldía Municipal de Chía')->first();

            if (!$organizacion) {
                $this->command->error('❌ No se encontró la organización de Chía.');
                return;
            }

            $this->command->info("🏢 Organización encontrada: {$organizacion->nombre_oficial} (ID: {$organizacion->id})");

            // 2. Buscar el rol de admin_organizacion para ESTA organización
            $rolAdmin = Rol::where('nombre', 'admin_organizacion')
                ->first();

            if (!$rolAdmin) {
                $this->command->error("❌ No se encontró el rol admin_organizacion para la organización ID: {$organizacion->id}");
                
                // Debug: mostrar todos los roles de esta organización
                $rolesOrganizacion = Rol::where('organizacion_id', $organizacion->id)->get();
                $this->command->info("📋 Roles disponibles en la organización:");
                foreach ($rolesOrganizacion as $rol) {
                    $this->command->info("   - {$rol->nombre} (ID: {$rol->id})");
                }
                return;
            }

            $this->command->info("🎯 Rol encontrado: {$rolAdmin->nombre} (ID: {$rolAdmin->id})");

            // 3. Crear o actualizar el usuario
            $admin = Usuario::updateOrCreate(
                ['email' => 'jorduna@chia.gov.co'],
                [
                    'nombre' => 'Juan Orduña',
                    'password' => Hash::make('chia12345'),
                    'telefono' => '+57 238127183',
                    'documento_identidad' => '2316786178',
                    'tipo_vinculacion' => 'organizacion',
                    'estado' => 'activo',
                    'email_verificado_en' => now(),
                    'ultimo_acceso' => now(),
                ]
            );

            $this->command->info("👤 Usuario creado/actualizado: {$admin->nombre} (ID: {$admin->id})");

            // 4. Vincular usuario-organizacion-rol en la tabla triple
            // Primero eliminar cualquier vinculación existente para evitar duplicados
            DB::table('usuario_organizacion_rol')
                ->where('usuario_id', $admin->id)
                ->where('organizacion_id', $organizacion->id)
                ->delete();

            // Insertar nueva vinculación
            DB::table('usuario_organizacion_rol')->insert([
                'usuario_id' => $admin->id,
                'organizacion_id' => $organizacion->id,
                'rol_id' => $rolAdmin->id,
                'estado' => 'activo',
                'fecha_asignacion' => now(),
                'asignado_por' => 1, // ID del admin global
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("🔗 Vinculación creada en usuario_organizacion_rol");

            // 5. Verificar que la vinculación se creó correctamente
            $vinculacion = DB::table('usuario_organizacion_rol')
                ->where('usuario_id', $admin->id)
                ->where('organizacion_id', $organizacion->id)
                ->where('rol_id', $rolAdmin->id)
                ->first();

            if ($vinculacion) {
                $this->command->info('✅ Administrador de organización creado exitosamente:');
                $this->command->info("   👤 Usuario: {$admin->nombre}");
                $this->command->info("   🏢 Organización: {$organizacion->nombre_oficial}");
                $this->command->info("   🎯 Rol: {$rolAdmin->nombre}");
                $this->command->info("   🔑 Contraseña: chia12345");
            } else {
                $this->command->error('❌ Error al crear la vinculación en usuario_organizacion_rol');
            }
        });
    }
}
