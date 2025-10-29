<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    use HasFactory;

    protected $table = 'historial_estados';

    protected $fillable = [
        'cuenta_cobro_id',
        'estado_anterior',
        'estado_nuevo',
        'usuario_id',
        'comentario',
    ];

    // ==================== RELACIONES ====================
    
    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // ==================== ACCESSORS ====================
    
    public function getEstadoAnteriorNombreAttribute()
    {
        return $this->getNombreEstado($this->estado_anterior);
    }

    public function getEstadoNuevoNombreAttribute()
    {
        return $this->getNombreEstado($this->estado_nuevo);
    }

    protected function getNombreEstado($estado)
    {
        $estados = [
            'borrador' => 'Borrador',
            'radicada' => 'Radicada',
            'en_revision' => 'En Revisión',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            'pagada' => 'Pagada',
            'anulada' => 'Anulada',
        ];

        return $estados[$estado] ?? $estado;
    }

    public function getTiempoCambioAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}