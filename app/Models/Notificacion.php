<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'titulo',
        'mensaje',
        'cuenta_cobro_id',
        'generado_por',
        'leida',
        'leida_en',
        'datos_adicionales',
        'prioridad',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'leida_en' => 'datetime',
        'datos_adicionales' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    public function generadoPor()
    {
        return $this->belongsTo(Usuario::class, 'generado_por');
    }

    // ============================================
    // MÉTODOS
    // ============================================

    /**
     * Marcar la notificación como leída
     */
    public function marcarComoLeida()
    {
        $this->update([
            'leida' => true,
            'leida_en' => now(),
        ]);
    }

    /**
     * Marcar la notificación como no leída
     */
    public function marcarComoNoLeida()
    {
        $this->update([
            'leida' => false,
            'leida_en' => null,
        ]);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Filtrar notificaciones no leídas
     */
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    /**
     * Filtrar notificaciones leídas
     */
    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }

    /**
     * Filtrar notificaciones por usuario
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Filtrar notificaciones por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Filtrar notificaciones recientes (últimos 30 días)
     */
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    /**
     * Ordenar por más recientes primero
     */
    public function scopeMasRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ============================================
    // MÉTODOS ESTÁTICOS PARA CREAR NOTIFICACIONES
    // ============================================

    /**
     * Crear notificación para un usuario
     */
    public static function crear($usuarioId, $tipo, $titulo, $mensaje, $cuentaCobroId = null, $generadoPor = null, $prioridad = 'normal', $datosAdicionales = null)
    {
        return self::create([
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'cuenta_cobro_id' => $cuentaCobroId,
            'generado_por' => $generadoPor,
            'prioridad' => $prioridad,
            'datos_adicionales' => $datosAdicionales,
        ]);
    }

    /**
     * Crear notificaciones masivas para múltiples usuarios
     */
    public static function crearParaMultiplesUsuarios(array $usuariosIds, $tipo, $titulo, $mensaje, $cuentaCobroId = null, $generadoPor = null, $prioridad = 'normal', $datosAdicionales = null)
    {
        $notificaciones = [];

        foreach ($usuariosIds as $usuarioId) {
            $notificaciones[] = [
                'usuario_id' => $usuarioId,
                'tipo' => $tipo,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'cuenta_cobro_id' => $cuentaCobroId,
                'generado_por' => $generadoPor,
                'prioridad' => $prioridad,
                'datos_adicionales' => is_array($datosAdicionales) ? json_encode($datosAdicionales) : $datosAdicionales,
                'leida' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return self::insert($notificaciones);
    }
}
