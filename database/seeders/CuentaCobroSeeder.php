<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CuentaCobro;
use App\Models\ItemCuentaCobro;
use App\Models\Contrato;
use App\Models\User;

class CuentaCobroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener un contrato y usuario existente
        $contrato = Contrato::first();
        $usuario = User::first();

        if (!$contrato || !$usuario) {
            $this->command->warn('No hay contratos o usuarios en la base de datos. Ejecuta primero los seeders necesarios.');
            return;
        }

        $this->command->info('Creando cuentas de cobro de ejemplo...');

        // Cuenta de Cobro 1
        $cuenta1 = CuentaCobro::create([
            'contrato_id' => $contrato->id,
            'numero_cuenta_cobro' => 'CC-2025-0001',
            'fecha_radicacion' => '2025-10-15',
            'periodo_cobrado' => 'Octubre 2025',
            'valor_bruto' => 0,
            'valor_neto' => 0,
            'estado' => 'borrador',
            'observaciones' => 'Cuenta de cobro de prueba #1',
            'created_by' => $usuario->id,
        ]);

        // Items de la Cuenta 1
        ItemCuentaCobro::create([
            'cuenta_cobro_id' => $cuenta1->id,
            'descripcion' => 'Desarrollo de módulo de gestión',
            'cantidad' => 1,
            'valor_unitario' => 5000000,
            'porcentaje_avance' => 100,
        ]);

        ItemCuentaCobro::create([
            'cuenta_cobro_id' => $cuenta1->id,
            'descripcion' => 'Implementación de reportes',
            'cantidad' => 1,
            'valor_unitario' => 3000000,
            'porcentaje_avance' => 80,
        ]);

        // Recalcular retenciones
        $cuenta1->fresh()->calcularRetenciones();

        // Cuenta de Cobro 2
        $cuenta2 = CuentaCobro::create([
            'contrato_id' => $contrato->id,
            'numero_cuenta_cobro' => 'CC-2025-0002',
            'fecha_radicacion' => '2025-10-20',
            'periodo_cobrado' => 'Octubre 2025 - Semana 3',
            'valor_bruto' => 0,
            'valor_neto' => 0,
            'estado' => 'radicada',
            'observaciones' => 'Cuenta de cobro de prueba #2 - Radicada',
            'created_by' => $usuario->id,
        ]);

        // Items de la Cuenta 2
        ItemCuentaCobro::create([
            'cuenta_cobro_id' => $cuenta2->id,
            'descripcion' => 'Consultoría técnica',
            'cantidad' => 40,
            'valor_unitario' => 150000,
            'porcentaje_avance' => 100,
        ]);

        // Recalcular retenciones
        $cuenta2->fresh()->calcularRetenciones();

        $this->command->info('✓ Se crearon 2 cuentas de cobro de ejemplo con sus items');
    }
}
