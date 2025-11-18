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
        'periodo_inicio',
        'periodo_fin',
        'valor_bruto',
        'retenciones_calculadas',
        'valor_neto',
        'numero_comprobante_pago',
        'fecha_pago_real',
        'pila_verificada',
        'estado',
        'observaciones',
        'created_by',
        'fecha_pago_real',
        'numero_comprobante_pago',
    ];

    protected $casts = [
        'fecha_radicacion' => 'date',
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'fecha_pago_real' => 'date',
        'valor_bruto' => 'decimal:2',
        'valor_neto' => 'decimal:2',
        'retenciones_calculadas' => 'array',
        'pila_verificada' => 'boolean',
    ];

    // === RELACIONES ===
    public function contrato() { return $this->belongsTo(Contrato::class); }
    public function creador() { return $this->belongsTo(Usuario::class, 'created_by'); }
    public function items() { return $this->hasMany(ItemCuentaCobro::class, 'cuenta_cobro_id'); }
    public function documentos() { return $this->hasMany(DocumentoSoporte::class, 'cuenta_cobro_id'); }
    public function historial() { return $this->hasMany(HistorialEstado::class, 'cuenta_cobro_id')->orderByDesc('created_at'); }

    // === ESTADOS Y COLORES ===
    public const ESTADOS = [
        'borrador'                  => ['nombre' => 'Borrador',                  'color' => 'secondary'],
        'radicada'                  => ['nombre' => 'Radicada',                  'color' => 'info'],
        'en_correccion_supervisor'  => ['nombre' => 'En Corrección (Supervisor)', 'color' => 'warning'],
        'certificado_supervisor'    => ['nombre' => 'Certificado Supervisor',    'color' => 'primary'],
        'en_correccion_contratacion'=> ['nombre' => 'En Corrección (Contratación)', 'color' => 'warning'],
        'verificado_contratacion'   => ['nombre' => 'Verificado Contratación',   'color' => 'indigo'],
        'verificado_presupuesto'    => ['nombre' => 'Verificado Presupuesto',    'color' => 'purple'],
        'aprobada_ordenador'        => ['nombre' => 'Aprobada Ordenador',        'color' => 'success'],
        'en_proceso_pago'           => ['nombre' => 'En Proceso de Pago',        'color' => 'teal'],
        'pagada'                    => ['nombre' => 'Pagada',                    'color' => 'green'],
        'anulada'                   => ['nombre' => 'Anulada',                   'color' => 'danger'],
    ];

    public function getEstadoNombreAttribute()
    {
        return self::ESTADOS[$this->estado]['nombre'] ?? ucfirst($this->estado);
    }

    public function getEstadoColorAttribute()
    {
        return self::ESTADOS[$this->estado]['color'] ?? 'secondary';
    }

    // === MÉTODOS AUXILIARES ===
    public function generarNumero()
    {
        $year = now()->format('Y');
        $ultimo = self::whereYear('created_at', $year)
                      ->orderBy('id', 'desc')
                      ->first();

        $secuencia = $ultimo ? (intval(substr($ultimo->numero_cuenta_cobro, -4)) + 1) : 1;

        return "CC-{$year}-" . str_pad($secuencia, 4, '0', STR_PAD_LEFT);
    }

    public function calcularRetenciones()
    {
        $contrato = $this->contrato;
        $bruto = $this->valor_bruto;

        $retenciones = [
            'retencion_fuente' => round($bruto * ($contrato->porcentaje_retencion_fuente / 100), 2),
            'estampilla'       => round($bruto * ($contrato->porcentaje_estampilla / 100), 2),
            'total'            => 0
        ];

        $retenciones['total'] = $retenciones['retencion_fuente'] + $retenciones['estampilla'];
        $this->retenciones_calculadas = $retenciones;
        $this->valor_neto = round($bruto - $retenciones['total'], 2);
        $this->save();

        return $retenciones;
    }

    public function cambiarEstado($nuevoEstado, $usuarioId, $comentario = null)
    {
        if (!array_key_exists($nuevoEstado, self::ESTADOS)) {
            return false;
        }

        $anterior = $this->estado;
        $this->estado = $nuevoEstado;
        $this->save();

        HistorialEstado::create([
            'cuenta_cobro_id' => $this->id,
            'estado_anterior' => $anterior,
            'estado_nuevo'    => $nuevoEstado,
            'usuario_id'      => $usuarioId,
            'comentario'      => $comentario,
        ]);

        return true;
    }

    public function ordenesPago()
    {
        return $this->belongsToMany(OrdenPago::class, 'op_cuentas_cobro')
                    ->withPivot('fecha_pago_efectivo', 'comprobante_bancario_id')
                    ->withTimestamps();
    }

    // Scopes útiles
    public function scopePendientesSupervisor($query)
    {
        return $query->whereIn('estado', ['radicada', 'en_correccion_supervisor']);
    }

    public function scopePendientesContratacion($query)
    {
        return $query->whereIn('estado', ['certificado_supervisor', 'en_correccion_contratacion']);
    }

    public function scopePorPagar($query)
    {
        return $query->where('estado', 'aprobada_ordenador');
    }
}
