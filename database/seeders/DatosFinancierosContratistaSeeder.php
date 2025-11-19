<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DatosFinancierosContratista;
use App\Models\Usuario; // Asumiendo que el modelo de usuarios es User
use App\Models\Banco;

class DatosFinancierosContratistaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asumiendo que hay usuarios con rol 'contratista' ya seedados
        $contratistas = Usuario::whereHas('roles', function($q) {
            $q->where('nombre', 'contratista');
        })->take(5)->get(); // Toma 5 contratistas de ejemplo

        $bancos = Banco::all();

        foreach ($contratistas as $contratista) {
            DatosFinancierosContratista::create([
                'usuario_id' => $contratista->id,
                'cedula_o_nit_verificado' => 'NIT-' . rand(1000000000, 9999999999),
                'banco_id' => $bancos->random()->id,
                'tipo_cuenta' => rand(0,1) ? 'ahorros' : 'corriente',
                'numero_cuenta_bancaria' => 'CUENTA-' . rand(1000000000, 9999999999),
                'documentacion_completa' => true,
                'verificado_tesoreria' => true,
            ]);
        }
    }
}