<?php

namespace App\Listeners;

use App\Events\CuentaCobroEstadoCambiado;
use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EnviarNotificacionCambioEstado
{
    // Comentado temporalmente para pruebas síncronas
    // implements ShouldQueue
    // use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CuentaCobroEstadoCambiado $event): void
    {
        $cuentaCobro = $event->cuentaCobro;
        $estadoNuevo = $event->estadoNuevo;
        $usuarioQueRealizoAccion = $event->usuarioId;
        $contrato = $cuentaCobro->contrato;
        $organizacionId = $contrato->organizacion_id;

        // Determinar quién debe recibir la notificación según el nuevo estado
        $destinatarios = $this->obtenerDestinatarios($estadoNuevo, $cuentaCobro, $contrato, $organizacionId);

        // Obtener información de la notificación
        $infoNotificacion = $this->obtenerInformacionNotificacion($estadoNuevo, $cuentaCobro);

        // Crear notificaciones para cada destinatario
        foreach ($destinatarios as $usuarioId) {
            // No notificar al usuario que realizó la acción
            if ($usuarioId == $usuarioQueRealizoAccion) {
                continue;
            }

            Notificacion::crear(
                $usuarioId,
                $infoNotificacion['tipo'],
                $infoNotificacion['titulo'],
                $infoNotificacion['mensaje'],
                $cuentaCobro->id,
                $usuarioQueRealizoAccion,
                $infoNotificacion['prioridad'],
                [
                    'numero_cuenta' => $cuentaCobro->numero_cuenta_cobro,
                    'numero_contrato' => $contrato->numero_contrato,
                    'estado_anterior' => $event->estadoAnterior,
                    'estado_nuevo' => $estadoNuevo,
                    'comentario' => $event->comentario,
                ]
            );
        }
    }

    /**
     * Obtener los usuarios que deben recibir notificación según el estado
     */
    private function obtenerDestinatarios($estado, $cuentaCobro, $contrato, $organizacionId)
    {
        $destinatarios = [];

        switch ($estado) {
            case 'radicada':
                // Notificar al supervisor del contrato
                if ($contrato->supervisor_id) {
                    $destinatarios[] = $contrato->supervisor_id;
                }
                break;

            case 'certificado_supervisor':
                // Notificar a los revisores de contratación
                $destinatarios = $this->obtenerUsuariosPorRol('revisor_contratacion', $organizacionId);
                break;

            case 'en_correccion_supervisor':
                // Notificar al contratista que debe corregir
                $destinatarios[] = $contrato->contratista_id;
                break;

            case 'verificado_contratacion':
                // Notificar al tesorero para verificar presupuesto
                $destinatarios = $this->obtenerUsuariosPorRol('tesorero', $organizacionId);
                break;

            case 'en_correccion_contratacion':
                // Notificar al supervisor para que corrija con el contratista
                if ($contrato->supervisor_id) {
                    $destinatarios[] = $contrato->supervisor_id;
                }
                // También notificar al contratista
                $destinatarios[] = $contrato->contratista_id;
                break;

            case 'verificado_presupuesto':
                // Notificar al ordenador del gasto
                $destinatarios = $this->obtenerUsuariosPorRol('ordenador_gasto', $organizacionId);
                break;

            case 'aprobada_ordenador':
                // Notificar al tesorero para procesar el pago
                $destinatarios = $this->obtenerUsuariosPorRol('tesorero', $organizacionId);
                // También notificar al contratista que su cuenta fue aprobada
                $destinatarios[] = $contrato->contratista_id;
                break;

            case 'en_proceso_pago':
                // Notificar al contratista que el pago está en proceso
                $destinatarios[] = $contrato->contratista_id;
                break;

            case 'pagada':
                // Notificar al contratista que el pago se realizó
                $destinatarios[] = $contrato->contratista_id;
                // Notificar al supervisor para su conocimiento
                if ($contrato->supervisor_id) {
                    $destinatarios[] = $contrato->supervisor_id;
                }
                break;

            case 'anulada':
                // Notificar al contratista y supervisor
                $destinatarios[] = $contrato->contratista_id;
                if ($contrato->supervisor_id) {
                    $destinatarios[] = $contrato->supervisor_id;
                }
                break;
        }

        return array_unique($destinatarios);
    }

    /**
     * Obtener usuarios por rol en una organización
     */
    private function obtenerUsuariosPorRol($rolNombre, $organizacionId)
    {
        return Usuario::whereHas('roles', function ($query) use ($rolNombre, $organizacionId) {
            $query->where('roles.nombre', $rolNombre)
                  ->where('usuario_organizacion_rol.organizacion_id', $organizacionId)
                  ->where('usuario_organizacion_rol.estado', 'activo');
        })->pluck('id')->toArray();
    }

    /**
     * Obtener información de la notificación según el estado
     */
    private function obtenerInformacionNotificacion($estado, $cuentaCobro)
    {
        $numero = $cuentaCobro->numero_cuenta_cobro;
        $valorFormateado = '$' . number_format($cuentaCobro->valor_neto, 0, ',', '.');

        $configuraciones = [
            'radicada' => [
                'tipo' => 'cuenta_cobro_radicada',
                'titulo' => 'Nueva Cuenta de Cobro para Revisar',
                'mensaje' => "La cuenta de cobro {$numero} por valor de {$valorFormateado} ha sido radicada y requiere su revisión como supervisor.",
                'prioridad' => 'alta',
            ],
            'certificado_supervisor' => [
                'tipo' => 'cuenta_cobro_aprobada',
                'titulo' => 'Cuenta de Cobro Certificada - Requiere Verificación Legal',
                'mensaje' => "La cuenta de cobro {$numero} ha sido certificada por el supervisor y requiere verificación legal por parte de contratación.",
                'prioridad' => 'alta',
            ],
            'en_correccion_supervisor' => [
                'tipo' => 'cuenta_cobro_requiere_correccion',
                'titulo' => 'Cuenta de Cobro Requiere Correcciones',
                'mensaje' => "La cuenta de cobro {$numero} ha sido devuelta por el supervisor y requiere correcciones.",
                'prioridad' => 'urgente',
            ],
            'verificado_contratacion' => [
                'tipo' => 'cuenta_cobro_aprobada',
                'titulo' => 'Cuenta de Cobro Verificada - Requiere Revisión Presupuestal',
                'mensaje' => "La cuenta de cobro {$numero} ha sido verificada por contratación y requiere verificación presupuestal.",
                'prioridad' => 'alta',
            ],
            'en_correccion_contratacion' => [
                'tipo' => 'cuenta_cobro_requiere_correccion',
                'titulo' => 'Cuenta de Cobro Requiere Correcciones Legales',
                'mensaje' => "La cuenta de cobro {$numero} ha sido devuelta por contratación y requiere correcciones legales.",
                'prioridad' => 'urgente',
            ],
            'verificado_presupuesto' => [
                'tipo' => 'cuenta_cobro_aprobada',
                'titulo' => 'Cuenta de Cobro Verificada - Requiere Aprobación Final',
                'mensaje' => "La cuenta de cobro {$numero} ha sido verificada presupuestalmente y requiere aprobación final del ordenador del gasto.",
                'prioridad' => 'alta',
            ],
            'aprobada_ordenador' => [
                'tipo' => 'cuenta_cobro_aprobada',
                'titulo' => 'Cuenta de Cobro Aprobada',
                'mensaje' => "La cuenta de cobro {$numero} por valor de {$valorFormateado} ha sido aprobada y está lista para pago.",
                'prioridad' => 'alta',
            ],
            'en_proceso_pago' => [
                'tipo' => 'cuenta_cobro_en_proceso_pago',
                'titulo' => 'Pago en Proceso',
                'mensaje' => "Su cuenta de cobro {$numero} está en proceso de pago.",
                'prioridad' => 'normal',
            ],
            'pagada' => [
                'tipo' => 'cuenta_cobro_pagada',
                'titulo' => 'Cuenta de Cobro Pagada',
                'mensaje' => "Su cuenta de cobro {$numero} por valor de {$valorFormateado} ha sido pagada exitosamente.",
                'prioridad' => 'normal',
            ],
            'anulada' => [
                'tipo' => 'cuenta_cobro_anulada',
                'titulo' => 'Cuenta de Cobro Anulada',
                'mensaje' => "La cuenta de cobro {$numero} ha sido anulada.",
                'prioridad' => 'alta',
            ],
        ];

        return $configuraciones[$estado] ?? [
            'tipo' => 'cuenta_cobro_aprobada',
            'titulo' => 'Cambio de Estado en Cuenta de Cobro',
            'mensaje' => "La cuenta de cobro {$numero} ha cambiado de estado.",
            'prioridad' => 'normal',
        ];
    }
}
