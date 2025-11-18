<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpCuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'op_cuentas_cobro';

    protected $fillable = [
        'orden_pago_id',
        'cuenta_cobro_id',
        'fecha_pago_efectivo',
        'comprobante_bancario_id',
    ];

    public function ordenPago()
    {
        return $this->belongsTo(OrdenPago::class);
    }

    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class);
    }
}