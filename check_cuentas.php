<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CuentaCobro;

echo "=== CUENTAS DE COBRO EN LA BASE DE DATOS ===\n\n";

$cuentas = CuentaCobro::with(['items', 'contrato'])->get();

echo "Total de cuentas: " . $cuentas->count() . "\n\n";

foreach ($cuentas as $cuenta) {
    echo "ID: {$cuenta->id}\n";
    echo "Número: {$cuenta->numero_cuenta_cobro}\n";
    echo "Estado: {$cuenta->estado}\n";
    echo "Contrato: " . ($cuenta->contrato ? $cuenta->contrato->numero_contrato : 'N/A') . "\n";
    echo "Valor Bruto: $" . number_format($cuenta->valor_bruto, 2) . "\n";
    echo "Valor Neto: $" . number_format($cuenta->valor_neto, 2) . "\n";
    echo "Items: {$cuenta->items->count()}\n";
    echo "Fecha creación: {$cuenta->created_at}\n";
    echo "---\n\n";
}
