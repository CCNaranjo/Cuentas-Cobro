<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';

    protected $fillable = [
        'nombre',
        'slug',
        'icono',
        'orden',
        'activo',
        'parent_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function parent()
    {
        return $this->belongsTo(Modulo::class, 'parent_id');
    }

    public function submodulos()
    {
        return $this->hasMany(Modulo::class, 'parent_id');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'modulo_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePadres($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }
}
