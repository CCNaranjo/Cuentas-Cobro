<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\UsuarioOrganizacionRol;
use Illuminate\Support\Facades\Hash;

class AdminGlobalSeeder extends Seeder
{
    public function run()
    {
        // Verificar si ya existe el usuario
        $usuario = Usuario::where('email', 'johndoe@example.com')->first();
        if (!$usuario) {
            // Crear usuario admin global
            $usuario = Usuario::create([
                'nombre' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => Hash::make('password'),
                'telefono' => '+57 300 123 4567',
                'documento_identidad' => '1234567890',
                'tipo_vinculacion' => 'global_admin',
                'estado' => 'activo',
                'email_verificado_en' => now(),
            ]);

            $this->command->info('✓ Usuario Admin Global creado: johndoe@example.com');
        } else {
            // Actualizar campos adicionales si el usuario ya existe
            $usuario->update([
                'telefono' => '+57 300 123 4567',
                'documento_identidad' => '1234567890',
                'tipo_vinculacion' => 'global_admin',
                'estado' => 'activo',
                'email_verificado_en' => now(),
            ]);

            $this->command->info('✓ Usuario Admin Global actualizado: johndoe@example.com');
        }

        // Asignar rol de admin global
        $rolAdminGlobal = Rol::where('nombre', 'admin_global')->first();

        if ($rolAdminGlobal) {
            // Verificar si ya tiene el rol asignado
            $yaAsignado = UsuarioOrganizacionRol::where('usuario_id', $usuario->id)
                ->where('rol_id', $rolAdminGlobal->id)
                ->exists();

            if (!$yaAsignado) {
                UsuarioOrganizacionRol::create([
                    'usuario_id' => $usuario->id,
                    'organizacion_id' => null, // Admin global no tiene alcaldía específica
                    'rol_id' => $rolAdminGlobal->id,
                    'fecha_asignacion' => now(),
                    'asignado_por' => $usuario->id, // Se auto-asigna
                    'estado' => 'activo'
                ]);

                $this->command->info('✓ Rol Admin Global asignado correctamente');
            } else {
                $this->command->warn('⚠ El usuario ya tiene el rol Admin Global asignado');
            }
        }
        $this->command->line('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  CREDENCIALES DE ACCESO');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  Email: johndoe@example.com');
        $this->command->info('  Password: password');
        $this->command->info('═══════════════════════════════════════');

    }
}
