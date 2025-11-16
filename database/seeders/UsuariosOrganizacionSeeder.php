<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuariosOrganizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Obtener la organización de Chía
            $organizacion = Organizacion::where('nombre_oficial', 'Alcaldía Municipal de Chía')->first();

            if (!$organizacion) {
                $this->command->error('No se encontró la organización de Chía.');
                return;
            }

            $this->command->info("Organización encontrada: {$organizacion->nombre_oficial} (ID: {$organizacion->id})");

            // 2. Obtener roles necesarios
            $roles = Rol::whereIn('nombre', [
                'ordenador_gasto',
                'supervisor',
                'tesorero',
                'contratista',
                'usuario_basico',
                'revisor_contratacion'  // Añadido
            ])->get()->keyBy('nombre');

            if ($roles->count() < 6) {
                $this->command->error('Faltan roles en la organización. Roles encontrados: ' . $roles->keys()->implode(', '));
                return;
            }

            $this->command->info('Roles encontrados: ' . $roles->keys()->implode(', '));

            // 3. Datos de usuarios (nombre, email, documento, teléfono, rol)
            $usuariosData = [
                [
                    'nombre' => 'María Fernanda López',
                    'email' => 'mlopez@chia.gov.co',
                    'documento' => '1023456789',
                    'telefono' => '+57 310 456 7890',
                    'rol' => 'ordenador_gasto',
                ],
                [
                    'nombre' => 'Carlos Andrés Ramírez',
                    'email' => 'cramirez@chia.gov.co',
                    'documento' => '987654321',
                    'telefono' => '+57 315 123 4567',
                    'rol' => 'supervisor',
                ],
                [
                    'nombre' => 'Laura Patricia Gómez',
                    'email' => 'lgomez@chia.gov.co',
                    'documento' => '1122334455',
                    'telefono' => '+57 300 987 6543',
                    'rol' => 'tesorero',
                ],
                [
                    'nombre' => 'Julián David Morales',
                    'email' => 'jmorales@chia.gov.co',
                    'documento' => '5566778899',
                    'telefono' => '+57 321 654 9870',
                    'rol' => 'contratista',
                ],
                [
                    'nombre' => 'Ana Milena Torres',
                    'email' => 'atorres@chia.gov.co',
                    'documento' => '9988776655',
                    'telefono' => '+57 312 789 0123',
                    'rol' => 'usuario_basico',
                ],
                // Usuario nuevo para revisor_contratacion
                [
                    'nombre' => 'Diego Fernando Vargas',
                    'email' => 'dvargas@chia.gov.co',
                    'documento' => '6677889900',
                    'telefono' => '+57 319 345 6789',
                    'rol' => 'revisor_contratacion',
                ],
            ];

            $password = Hash::make('chia12345');

            foreach ($usuariosData as $data) {
                // 4. Crear o actualizar usuario
                $usuario = Usuario::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'nombre' => $data['nombre'],
                        'password' => $password,
                        'telefono' => $data['telefono'],
                        'documento_identidad' => $data['documento'],
                        'tipo_vinculacion' => 'organizacion',
                        'estado' => 'activo',
                        'email_verificado_en' => now(),
                        'ultimo_acceso' => now(),
                    ]
                );

                $this->command->info("Usuario: {$usuario->nombre} (ID: {$usuario->id})");

                // 5. Eliminar vinculación previa (evitar duplicados)
                DB::table('usuario_organizacion_rol')
                    ->where('usuario_id', $usuario->id)
                    ->where('organizacion_id', $organizacion->id)
                    ->delete();

                // 6. Crear nueva vinculación
                DB::table('usuario_organizacion_rol')->insert([
                    'usuario_id' => $usuario->id,
                    'organizacion_id' => $organizacion->id,
                    'rol_id' => $roles[$data['rol']]->id,
                    'estado' => 'activo',
                    'fecha_asignacion' => now(),
                    'asignado_por' => 1, // Admin global
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("Rol asignado: {$data['rol']}");
            }

            $this->command->info('Todos los usuarios de ejemplo creados exitosamente.');
            $this->command->warn('Contraseña para todos: chia12345');
        });
    }
}