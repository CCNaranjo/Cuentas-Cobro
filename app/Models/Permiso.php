<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    protected $fillable = [
        'nombre',
        'slug',
        'modulo_id',
        'descripcion',
    ];

    // Relaciones
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    // En app/Models/Permiso.php
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permisos', 'permiso_id', 'rol_id')
            ->withTimestamps();
    }
}
