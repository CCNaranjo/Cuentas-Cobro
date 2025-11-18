<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CuentaBancariaOrg;
use App\Models\Organizacion;
use App\Models\Banco;

class CuentasBancariasOrgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizaciones = Organizacion::take(3)->get(); // Toma 3 organizaciones de ejemplo
        $bancos = Banco::all();

        foreach ($organizaciones as $organizacion) {
            CuentaBancariaOrg::create([
                'organizacion_id' => $organizacion->id,
                'banco_id' => $bancos->random()->id,
                'numero_cuenta' => 'ORG-CUENTA-' . rand(1000000000, 9999999999),
                'tipo_cuenta' => rand(0,1) ? 'ahorros' : 'corriente',
                'titular_cuenta' => $organizacion->nombre . ' Tesorería',
                'activa' => true,
            ]);
        }
    }
}