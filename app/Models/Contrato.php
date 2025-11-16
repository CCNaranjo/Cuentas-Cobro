<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizacion_id',
        'numero_contrato',
        'objeto_contractual',
        'contratista_id',
        'supervisor_id',
        'valor_total',
        'valor_pagado',
        'fecha_inicio',
        'fecha_fin',
        'porcentaje_retencion_fuente',
        'porcentaje_estampilla',
        'estado',
        'vinculado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor_total' => 'decimal:2',
        'valor_pagado' => 'decimal:2',
        'porcentaje_retencion_fuente' => 'decimal:2',
        'porcentaje_estampilla' => 'decimal:2',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function contratista()
    {
        return $this->belongsTo(Usuario::class, 'contratista_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'supervisor_id');
    }

    public function vinculadoPor()
    {
        return $this->belongsTo(Usuario::class, 'vinculado_por');
    }

    public function archivos()
    {
        return $this->hasMany(ContratoArchivo::class);
    }

    public function cuentasCobro()
    {
        return $this->hasMany(CuentaCobro::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Filtrar contratos por organización
     */
    public function scopePorOrganizacion($query, $organizacionId)
    {
        return $query->where('organizacion_id', $organizacionId);
    }

    /**
     * Filtrar contratos por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Filtrar contratos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Filtrar contratos por supervisor
     */
    public function scopePorSupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Filtrar contratos por contratista
     */
    public function scopePorContratista($query, $contratistaId)
    {
        return $query->where('contratista_id', $contratistaId);
    }

    /**
     * Scope para filtrar contratos según permisos del usuario
     */
    public function scopeAccesiblesParaUsuario($query, $usuario, $organizacionId = null)
    {
        // Si tiene permiso para ver todos los contratos
        if ($usuario->tienePermiso('ver-todos-contratos', $organizacionId)) {
            // Admin Global: ve todos los contratos
            if ($usuario->esAdminGlobal()) {
                return $query;
            }
            // Admin Organización u Ordenador Gasto: solo de su organización
            return $query->where('organizacion_id', $organizacionId);
        }
        
        // Si solo puede ver sus contratos (supervisor o contratista)
        if ($usuario->tienePermiso('ver-mis-contratos', $organizacionId)) {
            return $query->where(function ($q) use ($usuario) {
                $q->where('contratista_id', $usuario->id)
                  ->orWhere('supervisor_id', $usuario->id);
            });
        }

        // Si no tiene ningún permiso, no ve nada
        return $query->whereRaw('1 = 0');
    }

    // ============================================
    // MÉTODOS DE CÁLCULO FINANCIERO
    // ============================================

    /**
     * Recalcular el valor pagado consultando las cuentas de cobro
     */
    public function recalcularValorPagado()
    {
        $valorPagado = $this->cuentasCobro()
            ->whereIn('estado', ['pagada'])
            ->sum('valor_neto');

        $this->update(['valor_pagado' => $valorPagado]);

        return $valorPagado;
    }

    /**
     * Obtener el saldo disponible del contrato
     */
    public function getSaldoDisponibleAttribute()
    {
        return $this->valor_total - $this->valor_pagado;
    }

    /**
     * Obtener el porcentaje de ejecución
     */
    public function getPorcentajeEjecucionAttribute()
    {
        if ($this->valor_total == 0) {
            return 0;
        }

        return ($this->valor_pagado / $this->valor_total) * 100;
    }

    /**
     * Obtener estadísticas financieras del contrato
     */
    public function getEstadisticasFinancieras()
    {
        return [
            'valor_total' => $this->valor_total,
            'valor_pagado' => $this->valor_pagado,
            'saldo_disponible' => $this->saldo_disponible,
            'porcentaje_ejecucion' => round($this->porcentaje_ejecucion, 2),
            'cuentas_cobro_pagadas' => $this->cuentasCobro()->whereIn('estado', ['pagada', 'transferido'])->count(),
            'cuentas_cobro_pendientes' => $this->cuentasCobro()->whereIn('estado', ['radicada', 'en_revision', 'aprobada'])->count(),
        ];
    }

    // ============================================
    // MÉTODOS DE ESTADO
    // ============================================

    /**
     * Verificar si el contrato está activo
     */
    public function estaActivo()
    {
        return $this->estado === 'activo' && 
               $this->fecha_inicio <= now() && 
               $this->fecha_fin >= now();
    }

    /**
     * Verificar si el contrato está vencido
     */
    public function estaVencido()
    {
        return $this->fecha_fin < now();
    }

    /**
     * Días restantes hasta la finalización
     */
    public function getDiasRestantesAttribute()
    {
        if ($this->fecha_fin < now()) {
            return 0;
        }

        return now()->diffInDays($this->fecha_fin);
    }

    /**
     * Verificar si un usuario tiene acceso a este contrato
     */
    public function usuarioPuedeVer($usuario)
    {
        // Admin Global puede ver todos
        if ($usuario->esAdminGlobal()) {
            return true;
        }

        // Admin Organización u Ordenador Gasto con permiso ver-todos-contratos
        if ($usuario->tienePermiso('ver-todos-contratos', $this->organizacion_id)) {
            return true;
        }

        // Supervisor o Contratista del contrato
        if ($usuario->tienePermiso('ver-mis-contratos', $this->organizacion_id)) {
            return $this->contratista_id == $usuario->id || 
                   $this->supervisor_id == $usuario->id;
        }

        return false;
    }
}
