<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizacion;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlcaldiaEjemploSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $organizaciones = [
                [
                    'nombre_oficial' => 'Alcaldía Municipal de Chía',
                    'nit' => '899999001',
                    'departamento' => 'Cundinamarca',
                    'municipio' => 'Chía',
                    'direccion' => 'Calle 13 # 12-00, Centro',
                    'telefono_contacto' => '+57 1 8630000',
                    'email_institucional' => 'contacto@chia.gov.co',
                    'codigo_vinculacion' => $this->generarCodigoVinculacion(),
                    'admin_global_id' => 1,
                    'estado' => 'activa',
                    'dominios_email' => ['@chia.gov.co'],
                ]
            ];

            foreach ($organizaciones as $orgData) {
                $organizacion = Organizacion::firstOrCreate(
                    ['nit' => $orgData['nit']],
                    $orgData
                );

                $this->command->info("🏢 Organización creada: {$organizacion->nombre_oficial}");
            }
        });
    }
    /**
     * Generar código de vinculación único
     */
    private function generarCodigoVinculacion()
    {
        do {
            $codigo = 'ORG-' . date('Y') . '-CHIA' . rand(100, 999);
        } while (Organizacion::where('codigo_vinculacion', $codigo)->exists());

        return $codigo;
    }
}
