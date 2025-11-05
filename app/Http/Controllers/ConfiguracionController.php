<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use App\Models\ConfiguracionGlobal;
use App\Models\OrganizacionConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    /**
     * Vista principal de configuración (redirecciona según rol)
     */
    public function index()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if ($user->esAdminGlobal()) {
            return redirect()->route('configuracion.global');
        }
        
        $organizacionId = session('organizacion_actual');
        
        if (!$organizacionId) {
            return redirect()->route('dashboard')
                ->with('error', 'Selecciona una organización primero');
        }
        
        if ($user->tienePermiso('editar-configuracion-organizacion', $organizacionId)) {
            return redirect()->route('configuracion.organizacion');
        }
        
        abort(403, 'No tienes permisos para acceder a configuración');
    }

    /**
     * Configuración Global (Admin Global)
     */
    public function global(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal()) {
            abort(403, 'Solo administradores globales pueden acceder a esta sección');
        }
        
        $tab = $request->get('tab', 'seguridad');
        
        // Obtener configuración global
        $configuracion = ConfiguracionGlobal::firstOrCreate([
            'id' => 1
        ], [
            // Valores por defecto
            'min_longitud_password' => 8,
            'requiere_mayuscula' => true,
            'requiere_numero' => true,
            'requiere_caracter_especial' => true,
            'habilitar_2fa' => false,
            'dias_expiracion_password' => 90,
            'intentos_maximos_login' => 5,
            'plantillas_email' => [
                'bienvenida' => 'emails.bienvenida',
                'recuperacion_password' => 'emails.recuperacion',
                'cuenta_aprobada' => 'emails.cuenta_aprobada',
            ]
        ]);
        
        // Logs recientes (últimos 100)
        $logs = $this->obtenerLogsRecientes();
        
        return view('configuracion.global', compact('configuracion', 'tab', 'logs'));
    }

    /**
     * Actualizar configuración global
     */
    public function actualizarGlobal(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal()) {
            abort(403);
        }
        
        $seccion = $request->input('seccion');
        
        switch ($seccion) {
            case 'seguridad':
                return $this->actualizarSeguridad($request);
            case 'integraciones':
                return $this->actualizarIntegraciones($request);
            case 'plantillas':
                return $this->actualizarPlantillas($request);
            default:
                return back()->withErrors(['error' => 'Sección no válida']);
        }
    }

    /**
     * Actualizar configuración de seguridad
     */
    private function actualizarSeguridad(Request $request)
    {
        $validated = $request->validate([
            'min_longitud_password' => 'required|integer|min:6|max:32',
            'requiere_mayuscula' => 'boolean',
            'requiere_numero' => 'boolean',
            'requiere_caracter_especial' => 'boolean',
            'habilitar_2fa' => 'boolean',
            'dias_expiracion_password' => 'nullable|integer|min:0|max:365',
            'intentos_maximos_login' => 'required|integer|min:3|max:10',
        ]);
        
        $configuracion = ConfiguracionGlobal::first();
        $configuracion->update($validated);
        
        Log::info('Configuración de seguridad actualizada', [
            'usuario_id' => Auth::id(),
            'cambios' => $validated
        ]);
        
        return back()->with('success', 'Configuración de seguridad actualizada exitosamente');
    }

    /**
     * Actualizar integraciones API
     */
    private function actualizarIntegraciones(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_user' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'sms_provider' => 'nullable|string|max:100',
            'sms_api_key' => 'nullable|string|max:255',
        ]);
        
        $configuracion = ConfiguracionGlobal::first();
        $integraciones = $configuracion->integraciones_api ?? [];
        
        $integraciones = array_merge($integraciones, $validated);
        
        $configuracion->update(['integraciones_api' => $integraciones]);
        
        return back()->with('success', 'Integraciones actualizadas exitosamente');
    }

    /**
     * Actualizar plantillas de email
     */
    private function actualizarPlantillas(Request $request)
    {
        $validated = $request->validate([
            'tipo_plantilla' => 'required|string|in:bienvenida,recuperacion_password,cuenta_aprobada,contrato_asignado',
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);
        
        $configuracion = ConfiguracionGlobal::first();
        $plantillas = $configuracion->plantillas_email ?? [];
        
        $plantillas[$validated['tipo_plantilla']] = [
            'asunto' => $validated['asunto'],
            'contenido' => $validated['contenido'],
            'actualizado_en' => now(),
            'actualizado_por' => Auth::id(),
        ];
        
        $configuracion->update(['plantillas_email' => $plantillas]);
        
        return back()->with('success', 'Plantilla de email actualizada exitosamente');
    }

    /**
     * Configuración de Organización (Admin Organización)
     */
    public function organizacion(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        $organizacionId = session('organizacion_actual');
        
        if (!$organizacionId) {
            return redirect()->route('dashboard')
                ->with('error', 'Selecciona una organización primero');
        }
        
        if (!$user->tienePermiso('editar-configuracion-organizacion', $organizacionId)) {
            abort(403, 'No tienes permisos para editar la configuración de esta organización');
        }
        
        $tab = $request->get('tab', 'general');
        
        $organizacion = Organizacion::with('configuracion')->findOrFail($organizacionId);
        
        // Obtener o crear configuración de organización
        $configuracionOrg = $organizacion->configuracion ?? OrganizacionConfiguracion::create([
            'organizacion_id' => $organizacionId,
            'color_principal_hex' => '#004AAD',
            'vigencia_fiscal_fecha_inicio' => date('Y') . '-01-01',
            'vigencia_fiscal_fecha_fin' => date('Y') . '-12-31',
            'porcentaje_retencion_ica' => 0.966,
            'porcentaje_retencion_fuente' => 11.0,
            'dias_plazo_pago' => 30,
            'umbral_validacion_doble_monto' => 10000000,
            'requerir_paz_salvo_contratistas' => false,
            'habilitar_aprobacion_multiple' => false,
        ]);
        
        return view('configuracion.organizacion', compact('organizacion', 'configuracionOrg', 'tab'));
    }

    /**
     * Actualizar configuración de organización
     */
    public function actualizarOrganizacion(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        $organizacionId = session('organizacion_actual');
        
        if (!$user->tienePermiso('editar-configuracion-organizacion', $organizacionId)) {
            abort(403);
        }
        
        $seccion = $request->input('seccion');
        
        switch ($seccion) {
            case 'general':
                return $this->actualizarInfoGeneral($request, $organizacionId);
            case 'financiero':
                return $this->actualizarParametrosFinancieros($request, $organizacionId);
            case 'branding':
                return $this->actualizarBranding($request, $organizacionId);
            default:
                return back()->withErrors(['error' => 'Sección no válida']);
        }
    }

    /**
     * Actualizar información general de organización
     */
    private function actualizarInfoGeneral(Request $request, $organizacionId)
    {
        $validated = $request->validate([
            'nombre_oficial' => 'required|string|max:255',
            'email_institucional' => 'required|email|max:255',
            'telefono_contacto' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'vigencia_fiscal' => 'required|integer|min:2020|max:2099',
        ]);
        
        $organizacion = Organizacion::findOrFail($organizacionId);
        $organizacion->update($validated);
        
        $configuracionOrg = OrganizacionConfiguracion::where('organizacion_id', $organizacionId)->first();
        if ($configuracionOrg) {
            $configuracionOrg->update(['vigencia_fiscal' => $validated['vigencia_fiscal']]);
        }
        
        return back()->with('success', 'Información general actualizada exitosamente');
    }

    /**
     * Actualizar parámetros financieros
     */
    private function actualizarParametrosFinancieros(Request $request, $organizacionId)
    {
        $validated = $request->validate([
            'porcentaje_retencion_ica' => 'required|numeric|min:0|max:100',
            'porcentaje_retencion_fuente' => 'required|numeric|min:0|max:100',
            'dias_plazo_pago' => 'required|integer|min:1|max:365',
            'requiere_paz_y_salvo' => 'boolean',
        ]);
        
        $configuracionOrg = OrganizacionConfiguracion::where('organizacion_id', $organizacionId)->first();
        $configuracionOrg->update($validated);
        
        Log::info('Parámetros financieros actualizados', [
            'organizacion_id' => $organizacionId,
            'usuario_id' => Auth::id(),
            'cambios' => $validated
        ]);
        
        return back()->with('success', 'Parámetros financieros actualizados exitosamente');
    }

    /**
     * Actualizar branding (logo)
     */
    private function actualizarBranding(Request $request, $organizacionId)
    {
        $validated = $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        
        $organizacion = Organizacion::findOrFail($organizacionId);
        
        // Eliminar logo anterior si existe
        if ($organizacion->logo_path) {
            Storage::disk('public')->delete($organizacion->logo_path);
        }
        
        // Guardar nuevo logo
        $path = $request->file('logo')->store('logos', 'public');
        
        $organizacion->update(['logo_path' => $path]);
        
        return back()->with('success', 'Logo actualizado exitosamente');
    }

    /**
     * Obtener logs recientes del sistema
     */
    private function obtenerLogsRecientes($limit = 100)
    {
        // Simulación de logs - en producción leerías del archivo de logs
        return collect([
            [
                'nivel' => 'INFO',
                'mensaje' => 'Usuario inició sesión exitosamente',
                'fecha' => now()->subMinutes(5),
                'usuario' => 'admin@example.com'
            ],
            [
                'nivel' => 'WARNING',
                'mensaje' => 'Intento de acceso sin permisos',
                'fecha' => now()->subHours(1),
                'usuario' => 'user@example.com'
            ],
            [
                'nivel' => 'ERROR',
                'mensaje' => 'Error al procesar cuenta de cobro',
                'fecha' => now()->subHours(3),
                'usuario' => 'sistema'
            ],
        ]);
    }

    /**
     * Exportar logs
     */
    public function exportarLogs(Request $request)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user->esAdminGlobal()) {
            abort(403);
        }
        
        // TODO: Implementar exportación de logs
        return back()->with('info', 'Funcionalidad de exportación en desarrollo');
    }
}
