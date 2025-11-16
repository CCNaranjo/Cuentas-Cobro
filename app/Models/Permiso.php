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
        'tipo', // lectura, escritura, eliminacion, accion
        'es_organizacion', // true = permiso de organización, null/false = solo admin global
    ];

    protected $casts = [
        'es_organizacion' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * Módulo al que pertenece el permiso
     */
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    /**
     * Roles que tienen este permiso
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permisos', 'permiso_id', 'rol_id')
            ->withTimestamps();
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Filtrar permisos por módulo
     */
    public function scopePorModulo($query, $moduloId)
    {
        return $query->where('modulo_id', $moduloId);
    }

    /**
     * Filtrar permisos por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Filtrar solo permisos de organización (disponibles para Admin Org)
     */
    public function scopeDeOrganizacion($query)
    {
        return $query->where('es_organizacion', true);
    }

    /**
     * Filtrar solo permisos de sistema (exclusivos de Admin Global)
     */
    public function scopeDeSistema($query)
    {
        return $query->whereNull('es_organizacion')
                     ->orWhere('es_organizacion', false);
    }

    /**
     * Permisos accesibles según nivel jerárquico
     * 
     * @param int $nivelJerarquico
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccesiblesPorNivel($query, $nivelJerarquico)
    {
        if ($nivelJerarquico == 1) {
            // Admin Global ve TODOS los permisos
            return $query;
        } elseif ($nivelJerarquico == 2) {
            // Admin Organización solo ve permisos con es_organizacion = true
            return $query->where('es_organizacion', true);
        } else {
            // Nivel 3+ no deberían gestionar permisos directamente
            return $query->where('es_organizacion', true);
        }
    }

    /**
     * Buscar permisos por slug o nombre
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('slug', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Verificar si el permiso está asignado a algún rol
     */
    public function estaAsignado()
    {
        return $this->roles()->count() > 0;
    }

    /**
     * Verificar si es un permiso exclusivo de Admin Global
     */
    public function esSoloAdminGlobal()
    {
        return $this->es_organizacion === null || $this->es_organizacion === false;
    }

    /**
     * Verificar si es accesible para Admin de Organización
     */
    public function esAccesibleParaAdminOrg()
    {
        return $this->es_organizacion === true;
    }

    /**
     * Obtener el badge de tipo del permiso
     */
    public function getTipoBadgeAttribute()
    {
        $badges = [
            'lectura' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'eye'],
            'escritura' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'edit'],
            'eliminacion' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'trash'],
            'accion' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'bolt'],
        ];

        return $badges[$this->tipo] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'tag'];
    }

    /**
     * Obtener el badge de alcance del permiso (Sistema vs Organización)
     */
    public function getAlcanceBadgeAttribute()
    {
        if ($this->es_organizacion === true) {
            return [
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-800',
                'icon' => 'building',
                'label' => 'Organización'
            ];
        } else {
            return [
                'bg' => 'bg-purple-100',
                'text' => 'text-purple-800',
                'icon' => 'crown',
                'label' => 'Sistema'
            ];
        }
    }

    /**
     * Obtener formato legible del nombre
     */
    public function getNombreFormateadoAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->nombre));
    }
}