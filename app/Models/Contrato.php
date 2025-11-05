<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
   use HasFactory;

    protected $table = 'contratos';

    protected $fillable = [
        'numero_contrato',
        'organizacion_id',
        'contratista_id',
        'supervisor_id',
        'objeto_contractual',
        'valor_total',
        'fecha_inicio',
        'fecha_fin',
        'porcentaje_retencion_fuente',
        'porcentaje_estampilla',
        'estado',
        'vinculado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor_total' => 'decimal:2',
        'porcentaje_retencion_fuente' => 'decimal:2',
        'porcentaje_estampilla' => 'decimal:2',
    ];

    // Relaciones
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function contratista()
    {
        return $this->belongsTo(Usuario::class, 'contratista_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'supervisor_id');
    }

    public function vinculadoPor()
    {
        return $this->belongsTo(Usuario::class, 'vinculado_por');
    }

    // NUEVA RELACIÓN
    public function archivos()
    {
        return $this->hasMany(ContratoArchivo::class, 'contrato_id');
    }

    /*
    public function cuentasCobro()
    {
        return $this->hasMany(CuentaCobro::class, 'contrato_id');
    }
    */

    // Métodos auxiliares
    public function valorCobrado()
    {
        return $this->cuentasCobro()
                    ->whereIn('estado', ['aprobada', 'pagada'])
                    ->sum('valor_neto');
    }

    public function valorDisponible()
    {
        return $this->valor_total - $this->valorCobrado();
    }

    public function porcentajeEjecucion()
    {
        if ($this->valor_total == 0) return 0;
        return ($this->valorCobrado() / $this->valor_total) * 100;
    }

    public function estaActivo()
    {
        return $this->estado === 'activo' && 
               now()->between($this->fecha_inicio, $this->fecha_fin);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_fin', '>=', now());
    } 
}