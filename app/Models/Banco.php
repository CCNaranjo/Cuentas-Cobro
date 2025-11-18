<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo_ach',
    ];

    public function datosFinancierosContratistas()
    {
        return $this->hasMany(DatosFinancierosContratista::class);
    }

    public function cuentasBancariasOrg()
    {
        return $this->hasMany(CuentaBancariaOrg::class);
    }
}