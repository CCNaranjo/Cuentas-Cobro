<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'cuentas_cobro';

    protected $fillable = [
        'contrato_id',
        'numero_cuenta_cobro',
        'fecha_radicacion',
        'periodo_cobrado',
        'valor_bruto',
        'retenciones_calculadas',
        'valor_neto',
        'estado',
        'observaciones',
        'created_by',
    ];

    protected $casts = [
        'fecha_radicacion' => 'date',
        'valor_bruto' => 'decimal:2',
        'valor_neto' => 'decimal:2',
        'retenciones_calculadas' => 'array',
    ];

    // ==================== RELACIONES ====================
    
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(ItemCuentaCobro::class, 'cuenta_cobro_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoSoporte::class, 'cuenta_cobro_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialEstado::class, 'cuenta_cobro_id')
                    ->orderBy('created_at', 'desc');
    }

    // ==================== MÉTODOS AUXILIARES ====================
    
    public function calcularRetenciones()
    {
        $contrato = $this->contrato;
        $retenciones = [];

        // Retención en la fuente
        $retencionFuente = $this->valor_bruto * ($contrato->porcentaje_retencion_fuente / 100);
        $retenciones['retencion_fuente'] = round($retencionFuente, 2);

        // Estampilla
        $estampilla = $this->valor_bruto * ($contrato->porcentaje_estampilla / 100);
        $retenciones['estampilla'] = round($estampilla, 2);

        // Total
        $totalRetenciones = $retencionFuente + $estampilla;
        $retenciones['total'] = round($totalRetenciones, 2);

        $this->retenciones_calculadas = $retenciones;
        $this->valor_neto = round($this->valor_bruto - $totalRetenciones, 2);
        $this->save();

        return $retenciones;
    }

    public function cambiarEstado($nuevoEstado, $usuarioId, $comentario = null)
    {
        $estadoAnterior = $this->estado;
        
        if ($estadoAnterior === $nuevoEstado) {
            return false; // No cambiar si es el mismo estado
        }

        $this->estado = $nuevoEstado;
        $this->save();

        // Registrar en historial
        HistorialEstado::create([
            'cuenta_cobro_id' => $this->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $nuevoEstado,
            'usuario_id' => $usuarioId,
            'comentario' => $comentario,
        ]);

        return true;
    }

    public function calcularTotalItems()
    {
        return $this->items()->sum('valor_total');
    }

    public function generarNumero()
    {
        $year = date('Y');
        $ultimo = self::where('numero_cuenta_cobro', 'like', "CC-{$year}-%")
                      ->orderBy('id', 'desc')
                      ->first();

        if ($ultimo) {
            $parts = explode('-', $ultimo->numero_cuenta_cobro);
            $consecutivo = intval($parts[2]) + 1;
        } else {
            $consecutivo = 1;
        }

        return sprintf("CC-%s-%04d", $year, $consecutivo);
    }

    // ==================== SCOPES ====================
    
    public function scopeBorradores($query)
    {
        return $query->where('estado', 'borrador');
    }

    public function scopeRadicadas($query)
    {
        return $query->where('estado', 'radicada');
    }

    public function scopeEnRevision($query)
    {
        return $query->where('estado', 'en_revision');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagada');
    }

    public function scopePorContrato($query, $contratoId)
    {
        return $query->where('contrato_id', $contratoId);
    }

    public function scopePorPeriodo($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_radicacion', [$fechaInicio, $fechaFin]);
    }

    // ==================== ACCESSORS ====================
    
    public function getEstadoNombreAttribute()
    {
        $estados = [
            'borrador' => 'Borrador',
            'radicada' => 'Radicada',
            'en_revision' => 'En Revisión',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            'pagada' => 'Pagada',
            'anulada' => 'Anulada',
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    public function getEstadoColorAttribute()
    {
        $colores = [
            'borrador' => 'secondary',
            'radicada' => 'info',
            'en_revision' => 'warning',
            'aprobada' => 'success',
            'rechazada' => 'danger',
            'pagada' => 'primary',
            'anulada' => 'dark',
        ];

        return $colores[$this->estado] ?? 'secondary';
    }
}