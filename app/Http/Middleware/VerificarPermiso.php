<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = \Illuminate\Support\Facades\Auth::user();

        // Admin global tiene todos los permisos
        if ($usuario->esAdminGlobal()) {
            return $next($request);
        }

        // Obtener organización actual del contexto
        $organizacionId = $request->input('organizacion_id')
            ?? session('organizacion_actual')
            ?? $request->route('organizacion')?->id;

        if (!$organizacionId) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'No se ha seleccionado una organización']);
        }

        // Verificar si tiene el permiso en la organización (solo una vez)
        if ($usuario->tienePermiso($permiso, $organizacionId)) {
            return $next($request);
        }

        // No tiene permiso
        abort(403, 'No tienes permiso para realizar esta acción');
    }
}
