<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel_jerarquico',
        'es_sistema',
        'organizacion_id',
    ];

    protected $casts = [
        'es_sistema' => 'boolean',
    ];

    // Relaciones
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_organizacion_rol', 'rol_id', 'usuario_id')
                    ->withPivot('organizacion_id', 'estado')
                    ->withTimestamps();
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permisos', 'rol_id', 'permiso_id')
                    ->withTimestamps();
    }

    // Métodos auxiliares
    public function tienePermiso($slugPermiso)
    {
        return $this->permisos()->where('slug', $slugPermiso)->exists();
    }

    public function asignarPermisos(array $permisosIds)
    {
        $this->permisos()->sync($permisosIds);
    }
}
