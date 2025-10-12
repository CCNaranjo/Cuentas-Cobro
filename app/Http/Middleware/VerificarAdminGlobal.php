<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class VerificarAdminGlobal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Usuario $user */ 
        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('login');
        }
        
        
        if (!$user->esAdminGlobal()) {
            abort(403, 'Acceso denegado. Solo para administradores globales.');
        }

        return $next($request);
    }
}
