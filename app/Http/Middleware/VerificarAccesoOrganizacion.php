<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarAccesoOrganizacion
{
    /**
     * Verificar que el usuario tenga acceso a la organización
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        // Admin Global puede acceder a cualquier organización
        if ($user->esAdminGlobal()) {
            // Si viene organizacion_id en request, establecerla en sesión
            if ($request->has('organizacion_id')) {
                session(['organizacion_actual' => $request->organizacion_id]);
            }

            // Si no hay organización en sesión, redirigir a seleccionar
            if (!session('organizacion_actual')) {
                return redirect()->route('organizaciones.index')
                    ->with('warning', 'Por favor, selecciona una organización primero');
            }

            return $next($request);
        }

        // Para otros usuarios, verificar que tengan acceso a la organización
        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');

        if (!$organizacionId) {
            // Si no hay organización, intentar obtener la primera del usuario
            $primeraOrg = $user->organizacionesVinculadas()->first();

            if (!$primeraOrg) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes ninguna organización vinculada');
            }

            session(['organizacion_actual' => $primeraOrg->id]);
            $organizacionId = $primeraOrg->id;
        }

        // Verificar que el usuario pertenece a esa organización
        $tieneAcceso = $user->organizacionesVinculadas()
            ->where('organizacion_id', $organizacionId)
            ->wherePivot('estado', 'activo')
            ->exists();

        if (!$tieneAcceso) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes acceso a esta organización');
        }

        // Establecer organización en sesión
        session(['organizacion_actual' => $organizacionId]);

        return $next($request);
    }
}
