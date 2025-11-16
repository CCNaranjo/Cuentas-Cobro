<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\Contrato;
use App\Models\CuentaCobro;
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
            ->wherePivot('organizacion_id', $organizacionId)
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
            'total_organizaciones' => Organizacion::count(),
            'organizaciones_activas' => Organizacion::where('estado', 'activa')->count(),
            'total_usuarios' => Usuario::count(),
            'usuarios_activos' => Usuario::where('estado', 'activo')->count(),
            'usuarios_nuevos_mes' => Usuario::whereMonth('created_at', now()->month)->count(),
            'total_contratos' => Contrato::count(),
            'contratos_activos' => Contrato::where('estado', 'activo')->count(),
            'contratos_vencidos' => Contrato::where('estado', 'activo')
                ->where('fecha_fin', '<', now())
                ->count(),
            'valor_total_contratos' => Contrato::sum('valor_total'),
            'valor_contratos_activos' => Contrato::where('estado', 'activo')->sum('valor_total'),
            
            // Métricas de Cuentas de Cobro
            'total_cuentas_cobro' => CuentaCobro::count(),
            'monto_total_radicado' => CuentaCobro::whereIn('estado', ['radicada', 'certificado_supervisor', 'verificado_contratacion', 'verificado_presupuesto', 'aprobada_ordenador', 'en_proceso_pago', 'pagada'])->sum('valor_neto'),
            'monto_total_pagado' => CuentaCobro::where('estado', 'pagada')->sum('valor_neto'),
        ];

        $organizacionesCriticas = Organizacion::withCount([
                'contratos' => function ($query) {
                    $query->where('estado', 'activo');
                }
            ])
            ->having('contratos_count', '>', 0)
            ->orderBy('contratos_count', 'desc')
            ->take(5)
            ->get();

        $organizacionesRecientes = Organizacion::with(['usuarios', 'contratos'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $distribucionContratos = Contrato::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado');

        $actividadReciente = collect([]);

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
            
            case 'revisor_contratacion':
                return $this->dashboardRevisorContratacion($organizacion, $estadisticasComunes);
            
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
            
            // Métricas de Cuentas de Cobro
            'cuentas_pendientes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->whereIn('estado', ['radicada', 'certificado_supervisor', 'verificado_contratacion', 'verificado_presupuesto'])
                ->count(),
            'cuentas_pagadas_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'pagada')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'monto_pagado_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'pagada')
                ->whereMonth('updated_at', now()->month)
                ->sum('valor_neto'),
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
            
            // Cuentas pendientes de certificación (radicadas)
            'cuentas_pendientes_certificacion' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacion) {
                    $q->where('supervisor_id', $userId)
                      ->where('organizacion_id', $organizacion->id);
                })
                ->whereIn('estado', ['radicada', 'en_correccion_supervisor'])
                ->count(),
            
            'contratos_por_vencer' => Contrato::where('supervisor_id', $userId)
                ->where('estado', 'activo')
                ->whereBetween('fecha_fin', [now(), now()->addDays(30)])
                ->count(),
            
            'cuentas_certificadas_mes' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacion) {
                    $q->where('supervisor_id', $userId)
                      ->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'certificado_supervisor')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ]);

        $misContratos = Contrato::where('supervisor_id', $userId)
            ->where('organizacion_id', $organizacion->id)
            ->with('contratista')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Cuentas pendientes de certificar
        $cuentasPendientes = CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacion) {
                $q->where('supervisor_id', $userId)
                  ->where('organizacion_id', $organizacion->id);
            })
            ->whereIn('estado', ['radicada', 'en_correccion_supervisor'])
            ->with(['contrato.contratista'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'misContratos',
            'cuentasPendientes'
        ));
    }

    /**
     * Dashboard Ordenador del Gasto
     */
    private function dashboardOrdenadorGasto($organizacion, $estadisticasComunes)
    {
        $estadisticas = array_merge($estadisticasComunes, [
            // Cuentas pendientes de aprobación final
            'cuentas_pendientes_aprobacion' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'verificado_presupuesto')
                ->count(),
            
            'monto_pendiente_aprobacion' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'verificado_presupuesto')
                ->sum('valor_neto'),
            
            'cuentas_aprobadas_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'aprobada_ordenador')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            
            'monto_aprobado_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'aprobada_ordenador')
                ->whereMonth('updated_at', now()->month)
                ->sum('valor_neto'),
            
            'presupuesto_disponible' => 0, // TODO: Implementar
            'porcentaje_ejecucion' => 0, // TODO: Calcular
        ]);

        // Cuentas pendientes de aprobación
        $cuentasPendientes = CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                $q->where('organizacion_id', $organizacion->id);
            })
            ->where('estado', 'verificado_presupuesto')
            ->with(['contrato.contratista'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'cuentasPendientes'
        ));
    }

    /**
     * Dashboard Tesorero
     */
    private function dashboardTesorero($organizacion, $estadisticasComunes)
    {
        $estadisticas = array_merge($estadisticasComunes, [
            // Cuentas para verificar presupuesto (CDP/RP)
            'cuentas_verificar_presupuesto' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'verificado_contratacion')
                ->count(),
            
            // Órdenes de pago pendientes de generar
            'ordenes_pago_pendientes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'aprobada_ordenador')
                ->count(),
            
            // Pagos en proceso
            'pagos_en_proceso' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'en_proceso_pago')
                ->count(),
            
            'pagos_ejecutados_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'pagada')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            
            'monto_pagado_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'pagada')
                ->whereMonth('updated_at', now()->month)
                ->sum('valor_neto'),
            
            'saldo_disponible' => 0, // TODO: Integración bancaria
        ]);

        // Cuentas pendientes de acción de tesorería
        $cuentasPendientes = CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                $q->where('organizacion_id', $organizacion->id);
            })
            ->whereIn('estado', ['verificado_contratacion', 'aprobada_ordenador', 'en_proceso_pago'])
            ->with(['contrato.contratista'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'cuentasPendientes'
        ));
    }

    /**
     * Dashboard Revisor de Contratación
     */
    private function dashboardRevisorContratacion($organizacion, $estadisticasComunes)
    {
        $estadisticas = array_merge($estadisticasComunes, [
            // Cuentas pendientes de verificación legal
            'cuentas_pendientes_verificacion' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->whereIn('estado', ['certificado_supervisor', 'en_correccion_contratacion'])
                ->count(),
            
            'cuentas_verificadas_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'verificado_contratacion')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            
            'cuentas_devueltas_mes' => CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                    $q->where('organizacion_id', $organizacion->id);
                })
                ->where('estado', 'en_correccion_contratacion')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ]);

        // Cuentas pendientes de verificar
        $cuentasPendientes = CuentaCobro::whereHas('contrato', function($q) use ($organizacion) {
                $q->where('organizacion_id', $organizacion->id);
            })
            ->whereIn('estado', ['certificado_supervisor', 'en_correccion_contratacion'])
            ->with(['contrato.contratista'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.organizacion', compact(
            'estadisticas',
            'organizacion',
            'cuentasPendientes'
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
            'cuentas_pendientes' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)->where('organizacion_id', $organizacionId);
                })
                ->whereIn('estado', ['borrador', 'en_correccion_supervisor', 'en_correccion_contratacion'])
                ->count(),

            'mis_contratos' => Contrato::where('contratista_id', $userId)
                ->where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->count(),
            
            // Cuentas de cobro
            'cuentas_borradores' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)
                      ->where('organizacion_id', $organizacionId);
                })
                ->where('estado', 'borrador')
                ->count(),
            
            'cuentas_en_revision' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)
                      ->where('organizacion_id', $organizacionId);
                })
                ->whereIn('estado', ['radicada', 'certificado_supervisor', 'verificado_contratacion', 'verificado_presupuesto'])
                ->count(),
            
            'cuentas_devueltas' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)
                      ->where('organizacion_id', $organizacionId);
                })
                ->whereIn('estado', ['en_correccion_supervisor', 'en_correccion_contratacion'])
                ->count(),
            
            'total_a_recibir' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)
                      ->where('organizacion_id', $organizacionId);
                })
                ->whereIn('estado', ['aprobada_ordenador', 'en_proceso_pago'])
                ->sum('valor_neto'),
            
            'pagos_recibidos_mes' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)
                      ->where('organizacion_id', $organizacionId);
                })
                ->where('estado', 'pagada')
                ->whereMonth('updated_at', now()->month)
                ->sum('valor_neto'),
            
            'valor_total_contratos' => Contrato::where('contratista_id', $userId)
                ->where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->sum('valor_total'),
        ];

        $misContratos = Contrato::where('contratista_id', $userId)
            ->where('organizacion_id', $organizacionId)
            ->with(['organizacion', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Contratos listos para crear cuenta de cobro (activos)
        $contratosListosParaCobro = $misContratos->filter(function($contrato) {
            return $contrato->estado === 'activo';
        });

        // Cuentas devueltas que necesitan corrección
        $cuentasDevueltas = CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                $q->where('contratista_id', $userId)
                  ->where('organizacion_id', $organizacionId);
            })
            ->whereIn('estado', ['en_correccion_supervisor', 'en_correccion_contratacion'])
            ->with(['contrato', 'historial' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Cuentas borradores sin terminar
        $cuentasBorrador = CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                $q->where('contratista_id', $userId)
                  ->where('organizacion_id', $organizacionId);
            })
            ->where('estado', 'borrador')
            ->with(['contrato'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Últimas cuentas en proceso
        $cuentasEnProceso = CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                $q->where('contratista_id', $userId)
                  ->where('organizacion_id', $organizacionId);
            })
            ->whereIn('estado', ['radicada', 'certificado_supervisor', 'verificado_contratacion', 'verificado_presupuesto'])
            ->with(['contrato'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Trazabilidad de cuentas
        $trazabilidadCuentas = [
            'borradores' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)->where('organizacion_id', $organizacionId);
                })->where('estado', 'borrador')->count(),
            
            'radicadas' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)->where('organizacion_id', $organizacionId);
                })->where('estado', 'radicada')->count(),
            
            'en_revision' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)->where('organizacion_id', $organizacionId);
                })->whereIn('estado', ['certificado_supervisor', 'verificado_contratacion', 'verificado_presupuesto'])->count(),
            
            'aprobadas' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)->where('organizacion_id', $organizacionId);
                })->where('estado', 'aprobada_ordenador')->count(),
            
            'pagadas' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)->where('organizacion_id', $organizacionId);
                })->where('estado', 'pagada')->count(),
            
            'rechazadas' => CuentaCobro::whereHas('contrato', function($q) use ($userId, $organizacionId) {
                    $q->where('contratista_id', $userId)->where('organizacion_id', $organizacionId);
                })->whereIn('estado', ['en_correccion_supervisor', 'en_correccion_contratacion'])->count(),
        ];

        return view('dashboard.contratista', compact(
            'estadisticas',
            'misContratos',
            'contratosListosParaCobro',
            'cuentasDevueltas',
            'cuentasBorrador',
            'cuentasEnProceso',
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
        
        $vinculacionesPendientes = VinculacionPendiente::where('usuario_id', $user->id)
            ->where('estado', 'pendiente')
            ->with('organizacion')
            ->get();

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
