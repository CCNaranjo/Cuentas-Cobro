<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OrdenPago;
use App\Models\CuentaCobro;
use App\Models\CuentaBancariaOrg;
use App\Models\OpCuentaCobro;
use App\Models\Usuario;
use App\Models\Notificacion;

class OrdenPagoController extends Controller
{
    /**
     * Listado de Órdenes de Pago
     */
    public function index()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        if (!$user->tienePermiso('ver-ordenes-pago', $organizacionId)) {
            abort(403, 'No tienes permiso para ver órdenes de pago');
        }

        $query = OrdenPago::with(['cuentasCobro', 'cuentaOrigen.banco', 'ordenador', 'creador'])
            ->where('organizacion_id', $organizacionId)
            ->orderBy('created_at', 'desc');

        $ordenesPago = $query->paginate(15);

        return view('ordenes-pago.index', compact('ordenesPago'));
    }

    /**
     * Formulario para crear Orden de Pago
     */
    public function create()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        if (!$user->tienePermiso('crear-orden-pago', $organizacionId)) {
            abort(403, 'No tienes permiso para crear órdenes de pago');
        }

        // Cuentas de cobro aprobadas pendientes de pago
        $cuentasCobro = CuentaCobro::where('estado', 'aprobada_ordenador')
        // Usamos whereHas para filtrar por la columna que SÍ existe en la tabla 'contratos'
        ->whereHas('contrato', function ($query) use ($organizacionId) {
            $query->where('organizacion_id', $organizacionId);
        })
        ->with(['contrato.contratista'])
        ->get();

        // Cuentas bancarias de origen activas
        $cuentasOrigen = CuentaBancariaOrg::where('organizacion_id', $organizacionId)
            ->where('activa', true)
            ->with('banco')
            ->get();

        return view('ordenes-pago.create', compact('cuentasCobro', 'cuentasOrigen'));
    }

    /**
     * Guardar nueva Orden de Pago
     */
    public function store(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $validated = $request->validate([
            'cuenta_origen_id' => 'required|exists:cuentas_bancarias_org,id,organizacion_id,' . $organizacionId,
            'cuentas_cobro' => 'required|array|min:1',
            'cuentas_cobro.*' => 'exists:cuentas_cobro,id,estado,aprobada_ordenador',
        ]);

        DB::beginTransaction();
        try {
            // Calcular valor total neto
            $valorTotal = CuentaCobro::whereIn('id', $validated['cuentas_cobro'])->sum('valor_neto');

            // Crear OP
            $ordenPago = OrdenPago::create([
                'organizacion_id' => $organizacionId,
                'numero_op' => $this->generarNumeroOp($organizacionId),
                'cuenta_origen_id' => $validated['cuenta_origen_id'],
                'valor_total_neto' => $valorTotal,
                'fecha_emision' => now(),
                'estado' => 'creada',
                'created_by' => $user->id,
            ]);

            // Vincular cuentas cobro
            foreach ($validated['cuentas_cobro'] as $cuentaId) {
                OpCuentaCobro::create([
                    'orden_pago_id' => $ordenPago->id,
                    'cuenta_cobro_id' => $cuentaId,
                ]);
            }

            // Notificar al ordenador del gasto
            $ordenador = Usuario::whereHas('roles', function($q) use ($organizacionId) {
                // Filtra la tabla 'roles' por el nombre
                $q->where('nombre', 'ordenador_gasto');
                $q->where('organizacion_id', $organizacionId);
            })->first();

            if ($ordenador) {
                Notificacion::create([
                    'usuario_id' => $ordenador->id,
                    'tipo_notificacion' => 'orden_pago_creada',
                    'titulo' => 'Nueva Orden de Pago Pendiente de Autorización',
                    'mensaje' => 'Nueva orden de pago ' . $ordenPago->numero_op . ' pendiente de autorización',
                    'cuenta_cobro_id' => $cuentaId,
                    'generado_por_id' => $user->id,
                ]);
            }

            DB::commit();

            Log::info('Orden de pago creada', ['id' => $ordenPago->id, 'usuario_id' => $user->id]);

            return redirect()->route('pagos.op.show', $ordenPago->id)
                ->with('success', 'Orden de pago creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear orden de pago', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al crear la orden de pago']);
        }
    }

    /**
     * Mostrar detalle de Orden de Pago
     */
    public function show($id)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        $ordenPago = OrdenPago::with(['cuentasCobro.contrato.contratista', 'cuentaOrigen.banco', 'ordenador', 'creador'])
            ->where('id', $id)
            ->where('organizacion_id', $organizacionId)
            ->firstOrFail();

        if (!$user->tienePermiso('ver-ordenes-pago', $organizacionId)) {
            abort(403);
        }

        return view('ordenes-pago.show', compact('ordenPago'));
    }
    
   /**
     * Autorizar Orden de Pago (por Ordenador del Gasto)
     */
    public function autorizar($id)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        if (!$user->tienePermiso('aprobar-orden-pago', $organizacionId)) {
            abort(403, 'No tienes permiso para autorizar órdenes de pago');
        }

        $ordenPago = OrdenPago::with('cuentasCobro.contrato')
            ->where('id', $id)
            ->where('organizacion_id', $organizacionId)
            ->where('estado', 'creada')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Actualizar la orden de pago
            $ordenPago->update([
                'aprobada_por_ordenador' => true,
                'ordenador_id' => $user->id,
                'estado' => 'autorizada',
                'fecha_autorizacion' => now(),
            ]);

            // Cambiar estado de cada cuenta de cobro a 'en_proceso_pago'
            foreach ($ordenPago->cuentasCobro as $cuentaCobro) {
                
                // Validar que la cuenta de cobro esté en estado 'aprobada_ordenador'
                if ($cuentaCobro->estado !== 'aprobada_ordenador') {
                    throw new \Exception(
                        "La cuenta de cobro {$cuentaCobro->numero_cuenta_cobro} no está en estado 'aprobada_ordenador'. Estado actual: {$cuentaCobro->estado}"
                    );
                }

                $nuevoEstado = 'en_proceso_pago';
                $comentario = 'Orden de pago autorizada - ' . $ordenPago->numero_op;

                // 🔥 USAR DIRECTAMENTE TU MÉTODO cambiarEstado DEL MODELO
                $resultado = $cuentaCobro->cambiarEstado(
                    $nuevoEstado,
                    $user->id,
                    $comentario
                );

                if (!$resultado) {
                    throw new \Exception("No se pudo cambiar el estado de la cuenta de cobro {$cuentaCobro->numero_cuenta_cobro}");
                }

                Log::info('Estado de cuenta de cobro cambiado por autorización de OP', [
                    'cuenta_cobro_id' => $cuentaCobro->id,
                    'estado_anterior' => 'aprobada_ordenador',
                    'estado_nuevo' => $nuevoEstado,
                    'orden_pago_id' => $ordenPago->id,
                    'usuario_id' => $user->id
                ]);
            }

            DB::commit();

            Log::info('Orden de pago autorizada y cuentas de cobro actualizadas', [
                'orden_pago_id' => $id, 
                'usuario_id' => $user->id,
                'cuentas_actualizadas' => $ordenPago->cuentasCobro->count()
            ]);

            return back()->with('success', 'Orden de pago autorizada exitosamente. ' . 
                $ordenPago->cuentasCobro->count() . ' cuenta(s) de cobro cambiada(s) a "en proceso de pago".');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al autorizar orden de pago', [
                'error' => $e->getMessage(),
                'orden_pago_id' => $id,
                'usuario_id' => $user->id
            ]);
            
            return back()->with('error', 'Error al autorizar la orden de pago: ' . $e->getMessage());
        }
    }
    /**
     * Registrar pago de Orden de Pago (por Tesorero)
     */
    public function registrarPago(Request $request, $id)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        if (!$user->tienePermiso('registrar-orden-pago', $organizacionId)) {
            abort(403, 'No tienes permiso para registrar pagos');
        }

        $validated = $request->validate([
            'fecha_pago_efectivo' => 'required|date',
            'comprobante_bancario_id' => 'required|string|max:50',
        ]);

        $ordenPago = OrdenPago::where('id', $id)
            ->where('organizacion_id', $organizacionId)
            ->where('estado', 'autorizada')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Actualizar OP
            $ordenPago->update(['estado' => 'pagada_registrada']);

            // Actualizar pivote y cuentas cobro
            foreach ($ordenPago->cuentasCobro as $cuentaCobro) {
                
                // 💡 CAMBIO CRÍTICO: Usar el método updateExistingPivot()
                // Esto actualiza la fila en la tabla 'op_cuentas_cobro'
                $ordenPago->cuentasCobro()->updateExistingPivot($cuentaCobro->id, [
                    'fecha_pago_efectivo' => $validated['fecha_pago_efectivo'],
                    'comprobante_bancario_id' => $validated['comprobante_bancario_id'],
                ]);

                // Cambiar estado de CC a pagada usando método del model
                $cuentaCobro->cambiarEstado('pagada', $user->id, 'Pago registrado via OP ' . $ordenPago->numero_op);

                // Recalcular valor pagado en contrato
                $cuentaCobro->contrato->recalcularValorPagado();
            }

            DB::commit();

            Log::info('Pago registrado para orden', ['id' => $id, 'usuario_id' => $user->id]);

            return back()->with('success', 'Pago registrado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar pago', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al registrar el pago']);
        }
    }

    /**
     * Generar número único para OP usando bloqueo pesimista.
     */
    private function generarNumeroOp($organizacionId)
    {
        // 1. Asegúrate de que estás en una transacción.
        // (Esto ya lo estás haciendo en el controlador: DB::beginTransaction())
        
        // 2. Buscar el último número, pero bloquear esa fila hasta que la transacción termine.
        $ultimo = OrdenPago::where('organizacion_id', $organizacionId)
            ->whereYear('fecha_emision', date('Y')) // 💡 Recomendado: Reiniciar secuencia por año
            ->orderBy('numero_op', 'desc')
            ->lockForUpdate() // 🛑 BLOQUEA LA FILA
            ->first();
        
        // 3. Extraer el secuencial (asumiendo formato 'OP-XXX-YYYY')
        if ($ultimo) {
            // Obtenemos los 3 dígitos (ej: '026')
            $secuencialActual = (int) substr($ultimo->numero_op, 3, 3); 
            $secuencial = $secuencialActual + 1;
        } else {
            $secuencial = 1;
        }

        return 'OP-' . str_pad($secuencial, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
    }
}