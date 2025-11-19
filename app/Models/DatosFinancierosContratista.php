<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosFinancierosContratista extends Model
{
    use HasFactory;

    protected $table = 'datos_financieros_contratista';

    protected $fillable = [
        'cedula_o_nit_verificado',
        'banco_id',
        'tipo_cuenta',
        'numero_cuenta_bancaria',
        'documentacion_completa',
        'verificado_tesoreria',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class); // Asumiendo modelo Usuario
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }
}