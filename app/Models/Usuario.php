<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * usuarios
     *
     * @var list<string>
     */
    protected $table = 'usuarios';
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'documento_identidad',
        'tipo_vinculacion',
        'estado',
        'email_verificado_en',
        'ultimo_acceso',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verificado_en' => 'datetime',
            'ultimo_acceso' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relaciones
    public function organizacionesVinculadas()
    {
        return $this->belongsToMany(Organizacion::class, 'usuario_organizacion_rol', 'usuario_id', 'organizacion_id')
                    ->withPivot('rol_id', 'estado', 'fecha_asignacion')
                    ->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_organizacion_rol', 'usuario_id', 'rol_id')
                    ->withPivot('organizacion_id', 'estado')
                    ->withTimestamps();
    }

    public function contratosComoContratista()
    {
        return $this->hasMany(Contrato::class, 'contratista_id');
    }

    public function contratosComoSupervisor()
    {
        return $this->hasMany(Contrato::class, 'supervisor_id');
    }

    /*
    public function cuentasCobro()
    {
        return $this->hasMany(CuentaCobro::class, 'created_by');
    }
    */

    /*
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_destino_id');
    }
    */

    public function vinculacionesPendientes()
    {
        return $this->hasMany(VinculacionPendiente::class, 'usuario_id');
    }

    // Métodos auxiliares
    public function tieneRol($nombreRol, $organizacionId = null)
    {
        return $this->roles()
                    ->where('nombre', $nombreRol)
                    ->when($organizacionId, function($query) use ($organizacionId) {
                        $query->wherePivot('organizacion_id', $organizacionId);
                    })
                    ->exists();
    }

    /**
     * Obtener el nivel jerárquico del usuario
     */
    public function obtenerNivelJerarquico(): int
    {
        if ($this->esAdminGlobal()) {
            return 1;
        }
        
        // Obtener el rol de menor nivel (más alto en jerarquía)
        $rol = $this->roles()->orderBy('nivel_jerarquico')->first();
        
        return $rol ? $rol->nivel_jerarquico : 5;
    }

    public function tienePermiso($slugPermiso, $organizacionId = null)
    {
        // Si es admin_global, tiene todos los permisos
        if ($this->esAdminGlobal()) {
            return true;
        }

        $organizacionId = $organizacionId ?? session('organizacion_actual');

        return $this->roles()
                    /*
                    ->when($organizacionId, function($query) use ($organizacionId) {
                        $query->wherePivot('organizacion_id', $organizacionId);
                    })
                    */
                    ->whereHas('permisos', function($query) use ($slugPermiso) {
                        $query->where('slug', $slugPermiso);
                    })
                    ->wherePivot('organizacion_id', $organizacionId) // CORRECCIÓN: usar wherePivot
                    ->wherePivot('estado', 'activo')
                    ->exists();
    }

    public function esAdminGlobal()
    {
        return $this->tipo_vinculacion === 'global_admin';
    }
}
