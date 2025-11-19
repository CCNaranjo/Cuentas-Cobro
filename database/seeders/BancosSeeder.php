<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banco;

class BancosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bancos = [
            ['nombre' => 'Bancolombia', 'codigo_ach' => '007'],
            ['nombre' => 'Banco de Bogotá', 'codigo_ach' => '001'],
            ['nombre' => 'Davivienda', 'codigo_ach' => '051'],
            ['nombre' => 'BBVA Colombia', 'codigo_ach' => '013'],
            ['nombre' => 'Banco Popular', 'codigo_ach' => '002'],
            // Agrega más bancos colombianos según sea necesario
        ];

        foreach ($bancos as $banco) {
            Banco::create($banco);
        }
    }
}