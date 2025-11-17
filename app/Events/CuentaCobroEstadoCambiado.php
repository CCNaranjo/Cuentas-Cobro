<?php

namespace App\Events;

use App\Models\CuentaCobro;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CuentaCobroEstadoCambiado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cuentaCobro;
    public $estadoAnterior;
    public $estadoNuevo;
    public $usuarioId;
    public $comentario;

    /**
     * Create a new event instance.
     *
     * @param CuentaCobro $cuentaCobro
     * @param string $estadoAnterior
     * @param string $estadoNuevo
     * @param int $usuarioId - Usuario que realizó el cambio
     * @param string|null $comentario
     */
    public function __construct(CuentaCobro $cuentaCobro, $estadoAnterior, $estadoNuevo, $usuarioId, $comentario = null)
    {
        $this->cuentaCobro = $cuentaCobro;
        $this->estadoAnterior = $estadoAnterior;
        $this->estadoNuevo = $estadoNuevo;
        $this->usuarioId = $usuarioId;
        $this->comentario = $comentario;
    }
}
