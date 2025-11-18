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

        if (!$user->tienePermiso('crear-orden-pago', $organizacionId)) {
            abort(403);
        }

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

        $ordenPago = OrdenPago::where('id', $id)
            ->where('organizacion_id', $organizacionId)
            ->where('estado', 'creada')
            ->firstOrFail();

        $ordenPago->update([
            'aprobada_por_ordenador' => true,
            'ordenador_id' => $user->id,
            'estado' => 'autorizada',
        ]);

        Log::info('Orden de pago autorizada', ['id' => $id, 'usuario_id' => $user->id]);

        return back()->with('success', 'Orden de pago autorizada exitosamente');
    }

    /**
     * Registrar pago de Orden de Pago (por Tesorero)
     */
    public function registrarPago(Request $request, $id)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $organizacionId = session('organizacion_actual');

        if (!$user->tienePermiso('registrar-pago-orden', $organizacionId)) {
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
                $pivot = OpCuentaCobro::where('orden_pago_id', $id)
                    ->where('cuenta_cobro_id', $cuentaCobro->id)
                    ->first();

                $pivot->update([
                    'fecha_pago_efectivo' => $validated['fecha_pago_efectivo'],
                    'comprobante_bancario_id' => $validated['comprobante_bancario_id'],
                ]);

                // Actualizar cuenta cobro a pagada
                $cuentaCobro->update([
                    'estado' => 'pagada',
                    'fecha_pago_real' => $validated['fecha_pago_efectivo'],
                    'numero_comprobante_pago' => $validated['comprobante_bancario_id'],
                ]);

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
     * Generar número único para OP
     */
    private function generarNumeroOp($organizacionId)
    {
        $ultimo = OrdenPago::where('organizacion_id', $organizacionId)
            ->orderBy('id', 'desc')
            ->first();

        $secuencial = $ultimo ? (int)substr($ultimo->numero_op, -3) + 1 : 1;

        return 'OP-' . str_pad($secuencial, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
    }
}