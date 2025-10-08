<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VinculacionPendiente extends Model
{
    use HasFactory;

    protected $table = 'vinculaciones_pendientes';

    protected $fillable = [
        'usuario_id',
        'organizacion_id',
        'codigo_vinculacion_usado',
        'estado',
        'token_verificacion',
        'expira_en',
    ];

    protected $casts = [
        'expira_en' => 'datetime',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeNoExpiradas($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expira_en')
              ->orWhere('expira_en', '>', now());
        });
    }
}
