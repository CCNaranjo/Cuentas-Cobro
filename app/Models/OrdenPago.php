<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenPago extends Model
{
    protected $table = 'ordenes_pago';
    use HasFactory;

    protected $fillable = [
        'organizacion_id',
        'numero_op',
        'cuenta_origen_id',
        'valor_total_neto',
        'fecha_emision',
        'aprobada_por_ordenador',
        'ordenador_id',
        'estado',
        'created_by',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'aprobada_por_ordenador' => 'boolean',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function cuentaOrigen()
    {
        return $this->belongsTo(CuentaBancariaOrg::class, 'cuenta_origen_id');
    }

    public function ordenador()
    {
        return $this->belongsTo(Usuario::class, 'ordenador_id');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function cuentasCobro()
    {
        return $this->belongsToMany(CuentaCobro::class, 'op_cuentas_cobro')
                    ->withPivot('fecha_pago_efectivo', 'comprobante_bancario_id')
                    ->withTimestamps();
    }
}