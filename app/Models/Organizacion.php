<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre_oficial',
        'nit',
        'departamento',
        'municipio',
        'direccion',
        'telefono_contacto',
        'email_institucional',
        'codigo_vinculacion',
        'dominios_email',
        'logo_url',
        'estado',
        'admin_global_id',
    ];

    protected $casts = [
        'dominios_email' => 'array',
    ];

    // Relaciones
    public function adminGlobal()
    {
        return $this->belongsTo(Usuario::class, 'admin_global_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_organizacion_rol', 'organizacion_id', 'usuario_id')
                    ->withPivot('rol_id', 'estado', 'fecha_asignacion')
                    ->withTimestamps();
    }

    public function roles()
    {
        return $this->hasMany(Rol::class, 'organizacion_id');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'organizacion_id');
    }

    public function vinculacionesPendientes()
    {
        return $this->hasMany(VinculacionPendiente::class, 'organizacion_id');
    }

    // Métodos auxiliares
    public function dominioCoincide($email)
    {
        if (!$this->dominios_email) {
            return false;
        }

        $dominioEmail = '@' . explode('@', $email)[1];
        return in_array($dominioEmail, $this->dominios_email);
    }
}
