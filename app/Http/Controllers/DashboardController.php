<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\Contrato;
use App\Models\VinculacionPendiente;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard según el tipo de usuario
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuario $user */ 
        $user = Auth::user();

        // Dashboard Admin Global
        if ($user->esAdminGlobal()) {
            return $this->dashboardAdminGlobal();
        }

        // Verificar si tiene organización activa
        $organizacionId = session('organizacion_actual');
        
        if (!$organizacionId) {
            // Si no tiene organización, buscar la primera vinculada
            $primeraOrg = $user->organizacionesVinculadas()->first();
            
            if ($primeraOrg) {
                session(['organizacion_actual' => $primeraOrg->id]);
                $organizacionId = $primeraOrg->id;
            } else {
                // Usuario sin vinculación
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

        // Dashboard según el rol
        switch ($rol->nombre) {
            case 'admin_organizacion':
                return $this->dashboardAdminOrganizacion($organizacionId);
            
            case 'ordenador_gasto':
                return $this->dashboardOrdenadorGasto($organizacionId);
            
            case 'supervisor':
                return $this->dashboardSupervisor($organizacionId, $user->id);
            
            case 'tesorero':
                return $this->dashboardTesorero($organizacionId);
            
            case 'contratista':
                return $this->dashboardContratista($user->id);
            
            default:
                return $this->dashboardBasico();
        }
    }

    /**
     * Dashboard Admin Global
     */
    private function dashboardAdminGlobal()
    {
        $estadisticas = [
            'total_organizaciones' => Organizacion::count(),
            'organizaciones_activas' => Organizacion::where('estado', 'activa')->count(),
            'total_usuarios' => Usuario::count(),
            'usuarios_activos' => Usuario::where('estado', 'activo')->count(),
            'total_contratos' => Contrato::count(),
            'contratos_activos' => Contrato::where('estado', 'activo')->count(),
        ];

        $organizacionesRecientes = Organizacion::with('adminGlobal')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin-global', compact('estadisticas', 'organizacionesRecientes'));
    }

    /**
     * Dashboard Admin Organización
     */
    private function dashboardAdminOrganizacion($organizacionId)
    {
        $organizacion = Organizacion::findOrFail($organizacionId);

        $estadisticas = [
            'usuarios_activos' => $organizacion->usuarios()
                ->wherePivot('estado', 'activo')
                ->count(),
            'usuarios_pendientes' => VinculacionPendiente::where('organizacion_id', $organizacionId)
                ->where('estado', 'pendiente')
                ->count(),
            'contratos_activos' => $organizacion->contratos()
                ->where('estado', 'activo')
                ->count(),
            'contratos_total' => $organizacion->contratos()->count(),
            'valor_contratos_activos' => $organizacion->contratos()
                ->where('estado', 'activo')
                ->sum('valor_total'),
        ];

        $contratosRecientes = $organizacion->contratos()
            ->with(['contratista', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin-organizacion', compact('estadisticas', 'organizacion', 'contratosRecientes'));
    }

    /**
     * Dashboard Ordenador del Gasto
     */
    private function dashboardOrdenadorGasto($organizacionId)
    {
        $organizacion = Organizacion::findOrFail($organizacionId);

        $estadisticas = [
            'contratos_activos' => Contrato::where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->count(),
            'valor_total_contratos' => Contrato::where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->sum('valor_total'),
        ];

        $contratos = Contrato::where('organizacion_id', $organizacionId)
            ->with(['contratista', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.ordenador-gasto', compact('estadisticas', 'organizacion', 'contratos'));
    }

    /**
     * Dashboard Supervisor
     */
    private function dashboardSupervisor($organizacionId, $userId)
    {
        $estadisticas = [
            'contratos_asignados' => Contrato::where('supervisor_id', $userId)
                ->where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->count(),
        ];

        $misContratos = Contrato::where('supervisor_id', $userId)
            ->where('organizacion_id', $organizacionId)
            ->with('contratista')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.supervisor', compact('estadisticas', 'misContratos'));
    }

    /**
     * Dashboard Tesorero
     */
    private function dashboardTesorero($organizacionId)
    {
        $organizacion = Organizacion::findOrFail($organizacionId);

        $estadisticas = [
            'contratos_activos' => Contrato::where('organizacion_id', $organizacionId)
                ->where('estado', 'activo')
                ->count(),
        ];

        return view('dashboard.tesorero', compact('estadisticas', 'organizacion'));
    }

    /**
     * Dashboard Contratista
     */
    private function dashboardContratista($userId)
    {
        $estadisticas = [
            'mis_contratos' => Contrato::where('contratista_id', $userId)
                ->where('estado', 'activo')
                ->count(),
            'valor_total_contratos' => Contrato::where('contratista_id', $userId)
                ->where('estado', 'activo')
                ->sum('valor_total'),
        ];

        $misContratos = Contrato::where('contratista_id', $userId)
            ->with(['organizacion', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.contratista', compact('estadisticas', 'misContratos'));
    }

    /**
     * Dashboard Usuario Básico
     */
    private function dashboardBasico()
    {
        return view('dashboard.basico');
    }

    /**
     * Dashboard Usuario Sin Vinculación
     */
    private function dashboardSinVinculacion()
    {
        $user = Auth::user();
        
        // Verificar si tiene vinculaciones pendientes
        $vinculacionesPendientes = VinculacionPendiente::where('usuario_id', $user->id)
            ->where('estado', 'pendiente')
            ->with('organizacion')
            ->get();

        return view('dashboard.sin-vinculacion', compact('vinculacionesPendientes'));
    }
}