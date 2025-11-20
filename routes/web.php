<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Middleware\VerificarAdminGlobal;
use App\Http\Middleware\VerificarPermiso;
use App\Http\Middleware\VerificarAccesoOrganizacion;
use App\Http\Middleware\VerificarAccesoContrato;
use App\Http\Middleware\VerificarAccesoCuentaCobro;
use App\Http\Middleware\VerificarContratoEspecifico;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\CuentaCobroController;
use App\Http\Controllers\OrdenPagoController;
use App\Http\Controllers\NotificacionController;

// REGISTRO MANUAL DE MIDDLEWARES
app('router')->aliasMiddleware('verificar.permiso', VerificarPermiso::class);
app('router')->aliasMiddleware('verificar.acceso.organizacion', VerificarAccesoOrganizacion::class);
app('router')->aliasMiddleware('verificar.admin.global', VerificarAdminGlobal::class);
app('router')->aliasMiddleware('verificar.acceso.contrato', VerificarAccesoContrato::class);
app('router')->aliasMiddleware('verificar.acceso.cuenta.cobro', VerificarAccesoCuentaCobro::class);
app('router')->aliasMiddleware('verificar.contrato.especifico', VerificarContratoEspecifico::class);

/*
|--------------------------------------------------------------------------
| Web Routes - ARCA-D
|--------------------------------------------------------------------------
*/

// Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify-email');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Cambiar organización activa
    Route::get('/cambiar-organizacion/{id}', function ($id) {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        if (!$user->esAdminGlobal()) {
            $tieneAcceso = $user->organizacionesVinculadas()
                ->where('organizacion_id', $id)
                ->wherePivot('estado', 'activo')
                ->exists();
            if (!$tieneAcceso) {
                return redirect()->back()->with('error', 'No tienes acceso a esta organización');
            }
        }
        session(['organizacion_actual' => $id]);
        return redirect()->route('dashboard')->with('success', 'Organización cambiada exitosamente');
    })->name('cambiar-organizacion');

    // ============================================
    // ADMIN GLOBAL - Organizaciones
    // ============================================
    Route::middleware([VerificarAdminGlobal::class])
        ->prefix('organizaciones')
        ->group(function () {
            Route::get('/', [OrganizacionController::class, 'index'])->name('organizaciones.index');
            Route::post('/organizaciones/{organizacion}/seleccionar', [OrganizacionController::class, 'seleccionar'])->name('organizaciones.seleccionar');
            Route::get('/create', [OrganizacionController::class, 'create'])->name('organizaciones.create');
            Route::post('/', [OrganizacionController::class, 'store'])->name('organizaciones.store');
            Route::get('/{organizacion}', [OrganizacionController::class, 'show'])->name('organizaciones.show');
            Route::get('/{organizacion}/edit', [OrganizacionController::class, 'edit'])->name('organizaciones.edit');
            Route::put('/{organizacion}', [OrganizacionController::class, 'update'])->name('organizaciones.update');
            Route::post('/organizaciones/{organizacion}/asignar-admin', [OrganizacionController::class, 'asignarAdmin'])->name('organizaciones.asignar-admin');
            Route::put('/{organizacion}/actualizar-admin', [OrganizacionController::class, 'actualizarAdmin'])->name('organizaciones.actualizar-admin');
            Route::put('/{organizacion}/cambiar-admin', [OrganizacionController::class, 'cambiarAdmin'])->name('organizaciones.cambiar-admin');
        });

    Route::post('/vincular-codigo', [OrganizacionController::class, 'vincularCodigo'])->name('vincular-codigo');

   // ============================================
    // GESTIÓN DE USUARIOS
    // ============================================
    Route::middleware([VerificarAccesoOrganizacion::class, 'verificar.permiso:ver-usuarios'])
        ->prefix('usuarios')
        ->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
            Route::get('/pendientes', [UsuarioController::class, 'pendientes'])->name('usuarios.pendientes');
            Route::post('/asignar-rol', [UsuarioController::class, 'asignarRol'])
                ->middleware(VerificarPermiso::class . ':asignar-rol-usuario')
                ->name('usuarios.asignar-rol');
            Route::post('/{id}/rechazar', [UsuarioController::class, 'rechazarVinculacion'])->name('usuarios.rechazar');
            Route::post('/{id}/cambiar-estado', [UsuarioController::class, 'cambiarEstado'])
                ->middleware(VerificarPermiso::class . ':cambiar-estado-usuario')
                ->name('usuarios.cambiar-estado');
            Route::post('/{id}/cambiar-rol', [UsuarioController::class, 'cambiarRol'])
                ->middleware(VerificarPermiso::class . ':editar-usuario')
                ->name('usuarios.cambiar-rol');
            Route::get('/{id}/edit', [UsuarioController::class, 'edit'])
                ->middleware(VerificarPermiso::class . ':editar-usuario')
                ->name('usuarios.edit');
            Route::put('/{id}', [UsuarioController::class, 'update'])
                ->middleware(VerificarPermiso::class . ':editar-usuario')
                ->name('usuarios.update');
            Route::get('/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
        });

    // ============================================
    // GESTIÓN DE ROLES Y PERMISOS
    // ============================================
    Route::middleware([VerificarPermiso::class . ':ver-roles'])
        ->get('/roles', [RolesController::class, 'index'])
        ->name('roles.index');

    Route::middleware([VerificarPermiso::class . ':crear-rol'])
        ->group(function () {
            Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
        });

    Route::middleware([VerificarPermiso::class . ':ver-roles'])
        ->get('/roles/{rol}', [RolesController::class, 'show'])
        ->name('roles.show');

    Route::middleware([VerificarPermiso::class . ':asignar-permisos-rol'])
        ->group(function () {
            Route::get('/roles/{rol}/edit', [RolesController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{rol}', [RolesController::class, 'update'])->name('roles.update');
        });

    Route::middleware([VerificarPermiso::class . ':gestionar-roles'])
        ->delete('/roles/{rol}', [RolesController::class, 'destroy'])
        ->name('roles.destroy');

    Route::middleware([VerificarPermiso::class . ':ver-permisos'])
        ->prefix('permisos')
        ->group(function () {
            Route::get('/', [RolesController::class, 'indexPermisos'])->name('permisos.index');
            Route::post('/', [RolesController::class, 'storePermiso'])->name('permisos.store');
            Route::put('/{permiso}', [RolesController::class, 'updatePermiso'])->name('permisos.update');
            Route::delete('/{permiso}', [RolesController::class, 'destroyPermiso'])->name('permisos.destroy');
        });

    // ============================================
    // CONTRATOS
    // ============================================
    Route::middleware(['verificar.acceso.organizacion'])
        ->prefix('contratos')
        ->group(function () {
            Route::get('/', [ContratoController::class, 'index'])
                ->middleware('verificar.acceso.contrato')
                ->name('contratos.index');

            Route::get('/crear', [ContratoController::class, 'create'])
                ->middleware('verificar.permiso:crear-contrato')
                ->name('contratos.create');

            Route::post('/', [ContratoController::class, 'store'])
                ->middleware('verificar.permiso:crear-contrato')
                ->name('contratos.store');

            Route::get('/buscar/contratista', [ContratoController::class, 'buscarContratista'])
                ->middleware('verificar.permiso:vincular-contratista')
                ->name('contratos.buscar-contratista');

            Route::prefix('archivos')->group(function () {
                Route::get('/{archivo}/descargar', [ContratoController::class, 'descargarArchivo'])
                    ->name('contratos.archivos.descargar');
                Route::delete('/{archivo}', [ContratoController::class, 'eliminarArchivo'])
                    ->middleware('verificar.permiso:cargar-documentos')
                    ->name('contratos.archivos.eliminar');
            });

            Route::post('/{contrato}/archivos', [ContratoController::class, 'subirArchivo'])
                ->middleware('verificar.permiso:cargar-documentos')
                ->name('contratos.archivos.subir');

            Route::get('/{contrato}/editar', [ContratoController::class, 'edit'])
                ->middleware('verificar.permiso:editar-contrato')
                ->name('contratos.edit');

            Route::put('/{contrato}', [ContratoController::class, 'update'])
                ->middleware('verificar.permiso:editar-contrato')
                ->name('contratos.update');

            Route::put('/{contrato}/cambiar-supervisor', [ContratoController::class, 'cambiarSupervisor'])
                ->middleware('verificar.permiso:editar-contrato')
                ->name('contratos.cambiar-supervisor');

            Route::put('/{contrato}/cambiar-estado', [ContratoController::class, 'cambiarEstado'])
                ->middleware('verificar.permiso:cambiar-estado-contrato')
                ->name('contratos.cambiar-estado');

            Route::post('/{contrato}/vincular-contratista', [ContratoController::class, 'vincularContratista'])
                ->middleware('verificar.permiso:vincular-contratista')
                ->name('contratos.vincular-contratista');

            Route::get('/{contrato}', [ContratoController::class, 'show'])
                ->middleware('verificar.contrato.especifico')
                ->name('contratos.show');
        });

    // ============================================
    // CUENTAS DE COBRO - CON FLUJO COMPLETO
    // ============================================
    Route::middleware(['verificar.acceso.organizacion'])->prefix('cuentas-cobro')->group(function () {

        // INDEX - Ver cuentas (segmentado por permiso)
        Route::get('/', [CuentaCobroController::class, 'index'])
            ->middleware('verificar.acceso.cuenta.cobro')
            ->name('cuentas-cobro.index');

        // CREAR - Solo contratistas
        Route::get('/crear', [CuentaCobroController::class, 'create'])
            ->middleware('verificar.permiso:crear-cuenta-cobro')
            ->name('cuentas-cobro.create');

        Route::post('/', [CuentaCobroController::class, 'store'])
            ->middleware('verificar.permiso:crear-cuenta-cobro')
            ->name('cuentas-cobro.store');

        // RUTAS DE ARCHIVOS - CORREGIDAS
        Route::post('/{cuentaCobro}/archivos/subir', [CuentaCobroController::class, 'subirArchivo'])
            ->name('cuentas-cobro.archivos.subir');

        Route::get('/{cuentaCobro}/archivos/{archivo}/descargar', [CuentaCobroController::class, 'descargarArchivo'])
            ->name('cuentas-cobro.archivos.descargar');

        Route::delete('/{cuentaCobro}/archivos/{archivo}/eliminar', [CuentaCobroController::class, 'eliminarArchivo'])
            ->name('cuentas-cobro.archivos.eliminar');

        // VER DETALLE - Validación en controlador
        Route::get('/{id}', [CuentaCobroController::class, 'show'])
            ->name('cuentas-cobro.show');

        // EDITAR - Solo en borrador
        Route::get('/{id}/editar', [CuentaCobroController::class, 'edit'])
            ->middleware('verificar.permiso:editar-cuenta-cobro')
            ->name('cuentas-cobro.edit');

        Route::put('/{id}', [CuentaCobroController::class, 'update'])
            ->middleware('verificar.permiso:editar-cuenta-cobro')
            ->name('cuentas-cobro.update');

        // ELIMINAR - Solo en borrador
        Route::delete('/{id}', [CuentaCobroController::class, 'destroy'])
            ->middleware('verificar.permiso:editar-cuenta-cobro')
            ->name('cuentas-cobro.destroy');

        // CAMBIAR ESTADO - El permiso se valida dentro del controlador según el estado
        Route::post('/{id}/cambiar-estado', [CuentaCobroController::class, 'cambiarEstado'])
            ->name('cuentas-cobro.cambiar-estado');
    });

    // ============================================
    // CONFIGURACIÓN
    // ============================================
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

    Route::middleware([VerificarAdminGlobal::class])->group(function () {
        Route::get('/configuracion/global', [ConfiguracionController::class, 'global'])->name('configuracion.global');
        Route::post('/configuracion/global', [ConfiguracionController::class, 'actualizarGlobal'])->name('configuracion.actualizar-global');
        Route::get('/configuracion/exportar-logs', [ConfiguracionController::class, 'exportarLogs'])->name('configuracion.exportar-logs');
    });

    Route::middleware([VerificarAccesoOrganizacion::class])->group(function () {
        Route::get('/configuracion/organizacion', [ConfiguracionController::class, 'organizacion'])->name('configuracion.organizacion');
        Route::post('/configuracion/organizacion', [ConfiguracionController::class, 'actualizarOrganizacion'])->name('configuracion.actualizar-organizacion');
    });

    // ============================================
    // PERFIL DE USUARIO
    // ============================================
    Route::prefix('perfil')->group(function () {
        Route::get('/', [UsuarioController::class, 'perfil'])->name('perfil');
        Route::put('/actualizar', [UsuarioController::class, 'actualizarPerfil'])->name('perfil.actualizar');
        Route::put('/cambiar-password', [UsuarioController::class, 'cambiarPassword'])->name('perfil.cambiar-password');
        Route::put('/notificaciones', [UsuarioController::class, 'actualizarNotificaciones'])->name('perfil.actualizar-notificaciones');
        Route::post('/subir-foto', [UsuarioController::class, 'subirFotoPerfil'])->name('perfil.subir-foto');
    });
    
    // ============================================
    // MÓDULO DE PAGOS (TESORERÍA)
    // ============================================
    Route::middleware(['verificar.acceso.organizacion'])->prefix('pagos/ordenes-pago')->group(function () {
        // INDEX - Ver órdenes de pago
        Route::get('/', [OrdenPagoController::class, 'index'])
            ->middleware('verificar.permiso:ver-ordenes-pago')
            ->name('pagos.op.index');
        
        // CREATE - Formulario para nueva OP
        Route::get('/create', [OrdenPagoController::class, 'create'])
            ->middleware('verificar.permiso:crear-orden-pago')
            ->name('pagos.op.create');
        
        // STORE - Crear OP
        Route::post('/', [OrdenPagoController::class, 'store'])
            ->middleware('verificar.permiso:crear-orden-pago')
            ->name('pagos.op.store');
        
        // SHOW - Detalle de OP
        Route::get('/{op}', [OrdenPagoController::class, 'show'])
            ->middleware('verificar.permiso:ver-ordenes-pago')
            ->name('pagos.op.show');
        
        // AUTORIZAR - Por ordenador_gasto
        Route::post('/{op}/autorizar', [OrdenPagoController::class, 'autorizar'])
            ->middleware('verificar.permiso:aprobar-orden-pago')
            ->name('pagos.op.autorizar');
        
        // REGISTRAR PAGO - Por tesorero
        Route::put('/{op}/pagar', [OrdenPagoController::class, 'registrarPago'])
            ->middleware('verificar.permiso:registrar-orden-pago')
            ->name('pagos.op.registrar-pago');
    });
    // ============================================
    // NOTIFICACIONES
    // ============================================
    Route::prefix('notificaciones')->group(function () {
        // Obtener todas las notificaciones del usuario
        Route::get('/', [NotificacionController::class, 'index'])->name('notificaciones.index');

        // Obtener conteo de notificaciones no leídas
        Route::get('/conteo-no-leidas', [NotificacionController::class, 'conteoNoLeidas'])->name('notificaciones.conteo-no-leidas');

        // Obtener solo notificaciones no leídas (para el dropdown)
        Route::get('/no-leidas', [NotificacionController::class, 'noLeidas'])->name('notificaciones.no-leidas');

        // Marcar todas como leídas
        Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcar-todas-leidas');

        // Eliminar todas las notificaciones leídas
        Route::delete('/eliminar-leidas', [NotificacionController::class, 'eliminarLeidas'])->name('notificaciones.eliminar-leidas');

        // Obtener URL de redirección y marcar como leída
        Route::get('/{id}/url', [NotificacionController::class, 'obtenerUrlRedireccion'])->name('notificaciones.url-redireccion');

        // Ver una notificación específica (marca como leída)
        Route::get('/{id}', [NotificacionController::class, 'show'])->name('notificaciones.show');

        // Eliminar una notificación
        Route::delete('/{id}', [NotificacionController::class, 'destroy'])->name('notificaciones.destroy');
    });
});

Route::get('/welcome', function () {
    return view('welcome');
});