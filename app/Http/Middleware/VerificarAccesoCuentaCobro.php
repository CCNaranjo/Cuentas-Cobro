<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarAccesoCuentaCobro
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

        $organizacionId = session('organizacion_actual');

        // Verificar si tiene permiso para ver todas las cuentas O sus propias cuentas
        $tieneAcceso = $user->tienePermiso('ver-todas-cuentas', $organizacionId) || 
                       $user->tienePermiso('ver-mis-cuentas', $organizacionId);

        if (!$tieneAcceso) {
            abort(403, 'No tienes permiso para ver cuentas de cobro');
        }

        return $next($request);
    }
}