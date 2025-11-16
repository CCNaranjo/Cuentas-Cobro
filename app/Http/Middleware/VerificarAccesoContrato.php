<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarAccesoContrato
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $organizacionId = $request->organizacion_id ?? session('organizacion_actual');

        // Verificar si tiene permiso para ver todos los contratos O sus propios contratos
        $tieneAcceso = $user->tienePermiso('ver-todos-contratos', $organizacionId) || 
                       $user->tienePermiso('ver-mis-contratos', $organizacionId);

        if (!$tieneAcceso) {
            abort(403, 'No tienes permiso para ver contratos');
        }

        return $next($request);
    }
}