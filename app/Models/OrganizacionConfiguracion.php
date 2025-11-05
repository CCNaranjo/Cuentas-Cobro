<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class OrganizacionConfiguracion extends Model
{
    protected $table = 'organizacion_configuracion';
    
    protected $fillable = [
        'organizacion_id',
        // Branding
        'logo_url',
        'color_principal_hex',
        'nombre_dominio_local',
        // Legal/Fiscal
        'nit_organizacion',
        'direccion_principal',
        'vigencia_fiscal_fecha_inicio',
        'vigencia_fiscal_fecha_fin',
        // Financiero Local
        'porcentaje_retencion_local',
        'porcentaje_retencion_ica',
        'porcentaje_retencion_fuente',
        'umbral_validacion_doble_monto',
        'dias_plazo_pago',
        // Contactos
        'correo_notificacion_tesoreria',
        'contacto_facturacion_nombre',
        'contacto_facturacion_email',
        'contacto_facturacion_telefono',
        // Flujo de Trabajo
        'requerir_paz_salvo_contratistas',
        'habilitar_aprobacion_multiple',
    ];
    
    protected $casts = [
        'vigencia_fiscal_fecha_inicio' => 'date',
        'vigencia_fiscal_fecha_fin' => 'date',
        'porcentaje_retencion_local' => 'decimal:2',
        'porcentaje_retencion_ica' => 'decimal:3',
        'porcentaje_retencion_fuente' => 'decimal:2',
        'umbral_validacion_doble_monto' => 'decimal:2',
        'requerir_paz_salvo_contratistas' => 'boolean',
        'habilitar_aprobacion_multiple' => 'boolean',
    ];
    
    /**
     * Relación con Organización
     */
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }
    
    /**
     * Obtener configuración de una organización (con caché)
     */
    public static function obtenerPorOrganizacion(int $organizacionId)
    {
        return Cache::remember("org_config_{$organizacionId}", 3600, function () use ($organizacionId) {
            return self::where('organizacion_id', $organizacionId)->first();
        });
    }
    
    /**
     * Obtener un parámetro específico de una organización
     */
    public static function obtenerParametro(int $organizacionId, string $parametro, $default = null)
    {
        $config = self::obtenerPorOrganizacion($organizacionId);
        
        if (!$config) {
            return $default;
        }
        
        return $config->$parametro ?? $default;
    }
    
    /**
     * Limpiar caché de una organización
     */
    public static function limpiarCache(int $organizacionId)
    {
        Cache::forget("org_config_{$organizacionId}");
    }
    
    /**
     * Calcular retenciones para un monto dado
     */
    public function calcularRetenciones(float $valorBruto): array
    {
        $montoICA = $valorBruto * ($this->porcentaje_retencion_ica / 100);
        $montoFuente = $valorBruto * ($this->porcentaje_retencion_fuente / 100);
        $valorNeto = $valorBruto - $montoICA - $montoFuente;
        
        return [
            'valor_bruto' => $valorBruto,
            'retencion_ica' => [
                'porcentaje' => $this->porcentaje_retencion_ica,
                'monto' => round($montoICA, 2),
            ],
            'retencion_fuente' => [
                'porcentaje' => $this->porcentaje_retencion_fuente,
                'monto' => round($montoFuente, 2),
            ],
            'total_retenciones' => round($montoICA + $montoFuente, 2),
            'valor_neto' => round($valorNeto, 2),
        ];
    }
    
    /**
     * Verificar si un monto requiere doble aprobación
     */
    public function requiereDobleAprobacion(float $monto): bool
    {
        if (!$this->habilitar_aprobacion_multiple) {
            return false;
        }
        
        return $monto >= $this->umbral_validacion_doble_monto;
    }
    
    /**
     * Obtener correos de notificación de tesorería como array
     */
    public function getCorreosTesoreriaAttribute()
    {
        if (empty($this->correo_notificacion_tesoreria)) {
            return [];
        }
        
        return array_map('trim', explode(',', $this->correo_notificacion_tesoreria));
    }
}