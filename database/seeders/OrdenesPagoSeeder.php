<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrdenPago;
use App\Models\Organizacion;
use App\Models\CuentaBancariaOrg;
use App\Models\Usuario;
use App\Models\CuentaCobro;
use App\Models\OpCuentaCobro;

class OrdenesPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la organización de ejemplo (asumiendo solo una)
        $organizacion = Organizacion::where('nombre_oficial', 'Alcaldía Municipal de Chía')->first();

        if (!$organizacion) {
            $this->command->error('No se encontró la organización de ejemplo.');
            return;
        }

        // Obtener tesorero
        $tesorero = Usuario::whereHas('roles', function($q) {
            $q->where('nombre', 'tesorero');
        })->first();

        // Obtener ordenador_gasto
        $ordenador = Usuario::whereHas('roles', function($q) {
            $q->where('nombre', 'ordenador_gasto');
        })->first();

        if (!$tesorero || !$ordenador) {
            $this->command->error('Faltan usuarios con roles tesorero u ordenador_gasto.');
            return;
        }

        // Obtener cuenta bancaria de origen
        $cuentaOrigen = CuentaBancariaOrg::where('organizacion_id', $organizacion->id)->first();

        if (!$cuentaOrigen) {
            $this->command->error('No se encontró cuenta bancaria para la organización.');
            return;
        }

        // Crear 2 órdenes de pago de ejemplo
        for ($i = 1; $i <= 2; $i++) {
            $ordenPago = OrdenPago::create([
                'organizacion_id' => $organizacion->id,
                'numero_op' => 'OP-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '-' . date('Y'),
                'cuenta_origen_id' => $cuentaOrigen->id,
                'valor_total_neto' => rand(1000000, 10000000),
                'fecha_emision' => now(),
                'aprobada_por_ordenador' => true,
                'ordenador_id' => $ordenador->id,
                'estado' => 'autorizada',
                'created_by' => $tesorero->id,
            ]);

            $this->command->info("Orden de pago creada: {$ordenPago->numero_op}");
        }

        // Obtener órdenes de pago creadas
        $ordenesPago = OrdenPago::where('organizacion_id', $organizacion->id)->get();

        // Obtener cuentas de cobro aprobadas
        $cuentasCobro = CuentaCobro::where('estado', 'aprobada_ordenador')->get();

        if ($cuentasCobro->isEmpty()) {
            $this->command->warn('No hay cuentas de cobro en estado aprobada_ordenador para asignar.');
            return;
        }

        foreach ($ordenesPago as $ordenPago) {
            // Asignar 1-2 cuentas por OP, si hay disponibles
            $numCuentas = min(rand(1, 2), $cuentasCobro->count());
            $cuentasAsignadas = $cuentasCobro->random($numCuentas);

            foreach ($cuentasAsignadas as $cuentaCobro) {
                OpCuentaCobro::create([
                    'orden_pago_id' => $ordenPago->id,
                    'cuenta_cobro_id' => $cuentaCobro->id,
                    'fecha_pago_efectivo' => now()->addDays(rand(1, 10)),
                    'comprobante_bancario_id' => 'COMP-' . rand(1000, 9999),
                ]);

                $this->command->info("Asignada cuenta {$cuentaCobro->numero_cuenta_cobro} a OP {$ordenPago->numero_op}");
            }
        }
    }
}