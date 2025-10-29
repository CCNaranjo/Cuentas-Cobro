<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contrato;
use Illuminate\Support\Facades\DB;

class ContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contrato::create([
            'numero_contrato' => 'CONT-2024-001',
            'organizacion_id' => 1,
            'contratista_id' => 1,
            'supervisor_id' => 1,
            'objeto_contractual' => 'Construcción de infraestructura educativa en el municipio para la mejora de espacios académicos',
            'valor_total' => 500000000,
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-12-15',
            'porcentaje_retencion_fuente' => 10.00,
            'porcentaje_estampilla' => 2.00,
            'estado' => 'activo',
            'vinculado_por' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}