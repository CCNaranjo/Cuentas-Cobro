<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\Contrato;
use App\Models\VinculacionPendiente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard según el tipo de usuario
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuario $user */ 
        $user = Auth::user();

        // 1. ADMIN GLOBAL - Dashboard Global
        if ($user->esAdminGlobal()) {
            return $this->dashboardAdminGlobal();
        }

        // 2. Verificar si tiene organización activa
        $organizacionId = session('organizacion_actual');
        
        if (!$organizacionId) {
            // Si no tiene organización, buscar la primera vinculada
            $primeraOrg = $user->organizacionesVinculadas()->first();
            
            if ($primeraOrg) {
                session(['organizacion_actual' => $primeraOrg->id]);
                $organizacionId = $primeraOrg->id;
            } else {
                // 3. USUARIO SIN VINCULACIÓN - Dashboard Básico
                return $this->dashboardSinVinculacion();
            }
        }

        // Obtener rol del usuario en la organización
        $rol = $user->roles()
            ->wherePivot('estado', 'activo')
            ->first();

        if (!$rol) {
            return $this->dashboardSinVinculacion();
        }

        // 4. CONTRATISTA - Dashboard Contratista
        if ($rol->nombre === 'contratista') {
            return $this->dashboardContratista($user->id, $organizacionId);
        }

        // 5. FUNCIONARIOS - Dashboard Organización (según rol)
        return $this->dashboardOrganizacion($organizacionId, $rol->nombre, $user->id);
    }

    /**
     * ============================================
     * 1. DASHBOARD ADMIN GLOBAL
     * ============================================
     */
    private function dashboardAdminGlobal()
    {
        $estadisticas = [
            // KPIs de Licencia y Uso
            'total_organizaciones' => Organizacion::count(),
            'organizaciones_activas' => Organizacion::where('estado', 'activa')->count(),
            'organizaciones_por_expirar' => 0, // TODO: Implementar cuando haya licencias
            
            // Usuarios
            'total_usuarios' => Usuario::count(),
            'usuarios_activos' => Usuario::where('estado', 'activo')->count(),
            'usuarios_nuevos_mes' => Usuario::whereMonth('created_at', now()->month)->count(),
            
            // Contratos
            'total_contratos' => Contrato::count(),
            'contratos_activos' => Contrato::where('estado', 'activo')->count(),
            'contratos_vencidos' => Contrato::where('estado', 'activo')
                ->where('fecha_fin', '<', now())
                ->count(),
            
            // Volumen de Transacciones (Placeholder para Cuentas de Cobro)
            'monto_total_radicado' => 0, // TODO: Sumar cuentas radicadas
            'monto_total_pagado' => 0, // TODO: Sumar cuentas pagadas
            
            // Valor Total de Contratos
            'valor_total_contratos' => Contrato::sum('valor_total'),
            'valor_contratos_activos' => Contrato::where('estado', 'activo')->sum('valor_total'),
        ];

        // Organizaciones Críticas (más contratos activos)
        $organizacionesCriticas = Organizacion::withCount([
                'contratos' => function($query) {
                    $query->where('estado', 'activo');
                }
            ])
            ->having('contratos_count', '>', 0)
            ->orderBy('contratos_count', 'desc')
            ->take(5)
            ->get();

        // Organizaciones Recientes
        $organizacionesRecientes = Organizacion::with(['usuarios', 'contratos'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Distribución de contratos por estado
        $distribucionContratos = Contrato::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado');

        // Actividad Reciente
        $actividadReciente = collect([
            // TODO: Implementar logs de actividad
        ]);

        return view('dashboard.admin-global', compact(
            'estadisticas',
            'organizacionesCriticas',
            'organizacionesRecientes',
            'distribucionContratos',
            'actividadReciente'
        ));
    }

    /**
     * ============================================
     * 2. DASHBOARD ORGANIZACIÓN (Funcionarios)
     * ============================================
     */
    private function dashboardOrganizacion($organizacionId, $rolNombre, $userId)
    {
        $organizacion = Organizacion::with(['usuarios', 'contratos'])->findOrFail($organizacionId);
        
        // KPIs Comunes
        $estadisticasComunes = [
            'usuarios_activos' => $organizacion->usuarios()
                ->wherePivot('estado', 'activo')
                ->count(),
            'contratos_activos' => $organizacion->contratos()
                ->where('estado', 'activo')
                ->count(),
            'valor_contratos' => $organizacion->contratos()
                ->where('estado', 'activo')
                ->sum('valor_total'),
        ];

        // Según el rol específico
        switch ($rolNombre) {
            case 'admin_organizacion':
                return $this->dashboardAdminOrganizacion($organizacion, $estadisticasComunes);
            
            case 'supervisor':
                return $this->dashboardSupervisor($organizacion, $estadisticasComunes, $userId);
            
            case 'ordenador_gasto':
                return $this->dashboardOrdenadorGasto($organizacion, $estadisticasComunes);
            
            case 'tesorero':
                return $this->dashboardTesorero($organizacion, $estadisticasComunes);
            
            default:
                return $this->dashboardFuncionarioGeneral($organizacion, $estadisticasComunes, $rolNombre);
        }
    }

    /**
     * Dashboard Admin Organización
     */
    private function dashboardAdminOrganizacion($organizacion, $estadisticasComunes)
    {
        $estadisticas = array_merge($estadisticasComunes, [
            'usuarios_pendientes' => VinculacionPendiente::where('organizacion_id', $organizacion->id)
                ->where('estado', 'pendiente')
                ->count(),
            'contratos_por_vencer' => $organizacion->contratos()
                ->where('estado', 'activo')
                ->whereBetween('fecha_fin', [now(), now()->addDays(30)])
                ->count(),
            'cuentas_pendientes' => 0, // TODO: Implementar
        ]);

        $contratosRecientes = $organizacion->contratos()
            ->with(['contratista', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $usuariosPendientes = VinculacionPendiente::where('organizacion_id', $organizacion->id)
            ->where('estado', 'pendiente')
            ->with('usuario')
            ->take(5)
            ->get();

        $contratosPorVencer = $organizacion->contratos()
            ->where('estado', 'activo')
            ->whereBetween('fecha_fin', [now(), now()->addDays(30)])
            ->with('contratista')
            ->orderBy('fecha_fin', 'asc')
            ->take(5)
            ->get();

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'contratosRecientes',
            'usuariosPendientes',
            'contratosPorVencer'
        ));
    }

    /**
     * Dashboard Supervisor
     */
    private function dashboardSupervisor($organizacion, $estadisticasComunes, $userId)
    {
        $estadisticas = array_merge($estadisticasComunes, [
            'contratos_supervisando' => Contrato::where('supervisor_id', $userId)
                ->where('organizacion_id', $organizacion->id)
                ->where('estado', 'activo')
                ->count(),
            'cuentas_pendientes_certificacion' => 0, // TODO: Implementar
            'contratos_por_vencer' => Contrato::where('supervisor_id', $userId)
                ->where('estado', 'activo')
                ->whereBetween('fecha_fin', [now(), now()->addDays(30)])
                ->count(),
        ]);

        $misContratos = Contrato::where('supervisor_id', $userId)
            ->where('organizacion_id', $organizacion->id)
            ->with('contratista')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $tareasPendientes = [
            // TODO: Implementar tareas de cuentas por certificar
        ];

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'misContratos',
            'tareasPendientes'
        ));
    }

    /**
     * Dashboard Ordenador del Gasto
     */
    private function dashboardOrdenadorGasto($organizacion, $estadisticasComunes)
    {
        $estadisticas = array_merge($estadisticasComunes, [
            'cuentas_pendientes_aprobacion' => 0, // TODO: Implementar
            'presupuesto_disponible' => 0, // TODO: Implementar
            'porcentaje_ejecucion' => 0, // TODO: Calcular
            'ordenes_pago_generadas' => 0, // TODO: Implementar
        ]);

        $tareasPendientes = [
            // TODO: Cuentas pendientes de aprobación ejecutiva
        ];

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'tareasPendientes'
        ));
    }

    /**
     * Dashboard Tesorero
     */
    private function dashboardTesorero($organizacion, $estadisticasComunes)
    {
        $estadisticas = array_merge($estadisticasComunes, [
            'ordenes_pago_pendientes' => 0, // TODO: Implementar
            'pagos_ejecutados_hoy' => 0, // TODO: Implementar
            'pagos_ejecutados_mes' => 0, // TODO: Implementar
            'saldo_disponible' => 0, // TODO: Integración bancaria
        ]);

        $tareasPendientes = [
            // TODO: Órdenes de pago pendientes de ejecución
        ];

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'tareasPendientes'
        ));
    }

    /**
     * Dashboard Funcionario General
     */
    private function dashboardFuncionarioGeneral($organizacion, $estadisticasComunes, $rolNombre)
    {
        return view('dashboard.organizacion', compact(
            'estadisticasComunes',
            'organizacion',
            'rolNombre'
        ));
    }

    /**
     * ============================================
     * 3. DASHBOARD CONTRATISTA
     * ============================================
     */
    private function dashboardContratista($userId, $organizacionId)
    {
        $estadisticas = [
            'mis_contratos' => Contrato::where('contratista_id', $userId)
                ->where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->count(),
            'total_a_recibir' => 0, // TODO: Sumar cuentas aprobadas pendientes de pago
            'pagos_recibidos_mes' => 0, // TODO: Implementar
            'cuentas_pendientes' => 0, // TODO: Cuentas en revisión
            'cuentas_devueltas' => 0, // TODO: Cuentas rechazadas
            'valor_total_contratos' => Contrato::where('contratista_id', $userId)
                ->where('estado', 'activo')
                ->sum('valor_total'),
        ];

        $misContratos = Contrato::where('contratista_id', $userId)
            ->where('organizacion_id', $organizacionId)
            ->with(['organizacion', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Contratos listos para crear cuenta de cobro
        $contratosListosParaCobro = $misContratos->filter(function($contrato) {
            // TODO: Verificar si puede crear cuenta según el contrato
            return $contrato->estado === 'activo';
        });

        $trazabilidadCuentas = [
            // TODO: Distribución de cuentas por estado
            'borradores' => 0,
            'radicadas' => 0,
            'en_revision' => 0,
            'aprobadas' => 0,
            'pagadas' => 0,
            'rechazadas' => 0,
        ];

        return view('dashboard.contratista', compact(
            'estadisticas',
            'misContratos',
            'contratosListosParaCobro',
            'trazabilidadCuentas'
        ));
    }

    /**
     * ============================================
     * 4. DASHBOARD SIN VINCULACIÓN
     * ============================================
     */
    private function dashboardSinVinculacion()
    {
        $user = Auth::user();
        
        // Verificar si tiene vinculaciones pendientes
        $vinculacionesPendientes = VinculacionPendiente::where('usuario_id', $user->id)
            ->where('estado', 'pendiente')
            ->with('organizacion')
            ->get();

        // Listado público de organizaciones
        $organizacionesDisponibles = Organizacion::where('estado', 'activa')
            ->select('id', 'nombre_oficial', 'municipio', 'departamento', 'telefono_contacto')
            ->orderBy('nombre_oficial')
            ->take(10)
            ->get();

        return view('dashboard.sin-vinculacion', compact(
            'vinculacionesPendientes',
            'organizacionesDisponibles'
        ));
    }
}