<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'items_cuenta_cobro';

    protected $fillable = [
        'cuenta_cobro_id',
        'descripcion',
        'cantidad',
        'valor_unitario',
        'valor_total',
        'porcentaje_avance',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'porcentaje_avance' => 'decimal:2',
    ];

    // ==================== RELACIONES ====================

    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    // ==================== EVENTOS ====================

    protected static function boot()
    {
        parent::boot();

        // Calcular valor total antes de guardar
        static::saving(function ($item) {
            $item->valor_total = round($item->cantidad * $item->valor_unitario, 2);
        });

        // Actualizar el valor bruto de la cuenta de cobro después de guardar/eliminar
        static::saved(function ($item) {
            $item->actualizarTotalCuentaCobro();
        });

        static::deleted(function ($item) {
            $item->actualizarTotalCuentaCobro();
        });
    }

    // ==================== MÉTODOS AUXILIARES ====================

    protected function actualizarTotalCuentaCobro()
    {
        $cuentaCobro = $this->cuentaCobro;
        if ($cuentaCobro) {
            $total = $cuentaCobro->items()->sum('valor_total');
            $cuentaCobro->valor_bruto = round($total, 2);
            $cuentaCobro->save();

            // Recalcular retenciones con el nuevo valor bruto
            $cuentaCobro->calcularRetenciones();
        }
    }
}
