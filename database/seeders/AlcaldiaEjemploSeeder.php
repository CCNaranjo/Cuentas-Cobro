<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizacion;

class AlcaldiaEjemploSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organizacion::create([
            'id' => 1,
            'nombre_oficial' => 'Alcaldia Municipal de Chia',
            'nit' => '899999172-8',
            'departamento' => 'Cundinamarca',
            'municipio' => 'Chia',
            'direccion' => 'a 9-100, Cl. 11 #9-2, Chía, Cundinamarca',
            'telefono_contacto' => '(601) 8844444',
            'email_institucional' => 'contactenos@chia.gov.co',
            'codigo_vinculacion' => 'ORG-2025-E52HJU',
            'dominios_email' => ['@chia.gov.co'],
            'logo_url' => null,
            'estado' => 'activa',
            'admin_global_id' => 1,
            'created_at' => '2025-10-09 19:22:21',
            'updated_at' => '2025-10-09 19:22:21',
        ]);
    }
}
