<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ...

    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // ... otros middlewares ...
        
        'verificar.admin.global' => \App\Http\Middleware\VerificarAdminGlobal::class,
        'verificar.permiso' => \App\Http\Middleware\VerificarPermiso::class,
        'verificar.organizacion' => \App\Http\Middleware\VerificarAccesoOrganizacion::class,
    ];
}