<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaBancariaOrg extends Model
{
    use HasFactory;

    protected $table = 'cuentas_bancarias_org';

    protected $fillable = [
        'organizacion_id',
        'banco_id',
        'numero_cuenta',
        'tipo_cuenta',
        'titular_cuenta',
        'activa',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

    public function ordenesPago()
    {
        return $this->hasMany(OrdenPago::class, 'cuenta_origen_id');
    }
}