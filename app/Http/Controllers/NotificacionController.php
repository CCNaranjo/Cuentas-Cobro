<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Obtener todas las notificaciones del usuario autenticado
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        $notificaciones = Notificacion::where('usuario_id', $usuario->id)
            ->with(['cuentaCobro.contrato', 'generadoPor'])
            ->masRecientes()
            ->paginate(20);

        return response()->json($notificaciones);
    }

    /**
     * Obtener el conteo de notificaciones no leídas
     */
    public function conteoNoLeidas()
    {
        $usuario = Auth::user();

        $conteo = Notificacion::where('usuario_id', $usuario->id)
            ->noLeidas()
            ->count();

        return response()->json(['conteo' => $conteo]);
    }

    /**
     * Obtener notificaciones no leídas (para el dropdown)
     */
    public function noLeidas()
    {
        $usuario = Auth::user();

        $notificaciones = Notificacion::where('usuario_id', $usuario->id)
            ->noLeidas()
            ->with(['cuentaCobro.contrato', 'generadoPor'])
            ->masRecientes()
            ->limit(10)
            ->get();

        return response()->json($notificaciones);
    }

    /**
     * Mostrar una notificación específica y marcarla como leída
     */
    public function show($id)
    {
        $usuario = Auth::user();

        $notificacion = Notificacion::where('usuario_id', $usuario->id)
            ->with(['cuentaCobro.contrato', 'generadoPor'])
            ->findOrFail($id);

        // Marcar como leída si no lo está
        if (!$notificacion->leida) {
            $notificacion->marcarComoLeida();
        }

        return response()->json($notificacion);
    }

    /**
     * Obtener la URL de redirección según el tipo de notificación y rol del usuario
     */
    public function obtenerUrlRedireccion($id)
    {
        $usuario = Auth::user();

        $notificacion = Notificacion::where('usuario_id', $usuario->id)
            ->with(['cuentaCobro.contrato'])
            ->findOrFail($id);

        // Marcar como leída
        if (!$notificacion->leida) {
            $notificacion->marcarComoLeida();
        }

        $url = null;

        // Determinar la URL según el tipo de notificación
        if ($notificacion->tipo_notificacion == 'orden_pago_creada') {
            // Redirigir al index de pagos para aprobar
            $url = route('pagos.op.index');
        } elseif ($notificacion->cuentaCobro) {
            $cuentaCobroId = $notificacion->cuenta_cobro_id;
            $estado = $notificacion->cuentaCobro->estado;

            // URLs según el estado y rol
            switch ($estado) {
                case 'radicada':
                case 'en_correccion_supervisor':
                    // Supervisor debe revisar/certificar
                    $url = route('cuentas-cobro.show', $cuentaCobroId);
                    break;

                case 'certificado_supervisor':
                case 'en_correccion_contratacion':
                    // Revisor de contratación debe verificar
                    $url = route('cuentas-cobro.show', $cuentaCobroId);
                    break;

                case 'verificado_contratacion':
                    // Tesorero debe verificar presupuesto
                    $url = route('cuentas-cobro.show', $cuentaCobroId);
                    break;

                case 'verificado_presupuesto':
                    // Ordenador del gasto debe aprobar
                    $url = route('cuentas-cobro.show', $cuentaCobroId);
                    break;

                case 'aprobada_ordenador':
                case 'en_proceso_pago':
                    // Tesorero procesa pago o contratista ve el estado
                    $url = route('cuentas-cobro.show', $cuentaCobroId);
                    break;

                case 'pagada':
                case 'anulada':
                    // Ver detalles de la cuenta
                    $url = route('cuentas-cobro.show', $cuentaCobroId);
                    break;

                default:
                    $url = route('cuentas-cobro.index');
            }
        } else {
            // Si no hay cuenta de cobro asociada, ir al listado
            $url = route('cuentas-cobro.index');
        }

        return response()->json([
            'url' => $url,
            'notificacion' => $notificacion
        ]);
    }

    /**
     * Eliminar una notificación (después de que el usuario la haya visto)
     */
    public function destroy($id)
    {
        $usuario = Auth::user();

        $notificacion = Notificacion::where('usuario_id', $usuario->id)
            ->findOrFail($id);

        $notificacion->delete();

        return response()->json(['mensaje' => 'Notificación eliminada correctamente']);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasLeidas()
    {
        $usuario = Auth::user();

        Notificacion::where('usuario_id', $usuario->id)
            ->noLeidas()
            ->update([
                'leida' => true,
                'leida_en' => now()
            ]);

        return response()->json(['mensaje' => 'Todas las notificaciones han sido marcadas como leídas']);
    }

    /**
     * Eliminar todas las notificaciones leídas del usuario
     */
    public function eliminarLeidas()
    {
        $usuario = Auth::user();

        $eliminadas = Notificacion::where('usuario_id', $usuario->id)
            ->leidas()
            ->delete();

        return response()->json([
            'mensaje' => 'Notificaciones leídas eliminadas correctamente',
            'cantidad_eliminadas' => $eliminadas
        ]);
    }
}