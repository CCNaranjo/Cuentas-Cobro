<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarContratoEspecifico
{
    /**
     * Handle an incoming request.
     *
     * Verifica que el usuario tenga acceso al contrato específico
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

        // Obtener el contrato de la ruta
        $contrato = $request->route('contrato');

        if (!$contrato) {
            abort(404, 'Contrato no encontrado');
        }

        // Verificar acceso usando el método del modelo
        if (!$contrato->usuarioPuedeVer($user)) {
            abort(403, 'No tienes acceso a este contrato');
        }

        return $next($request);
    }
}