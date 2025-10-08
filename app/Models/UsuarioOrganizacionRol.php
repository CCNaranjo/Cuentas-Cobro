<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioOrganizacionRol extends Model
{
    use HasFactory;

    protected $table = 'usuario_organizacion_rol';

    protected $fillable = [
        'usuario_id',
        'organizacion_id',
        'rol_id',
        'fecha_asignacion',
        'asignado_por',
        'estado',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
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

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(Usuario::class, 'asignado_por');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
