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
            Route::post('/rechazar-vinculacion', [UsuarioController::class, 'rechazarVinculacion'])
                ->middleware(VerificarPermiso::class . ':asignar-rol-usuario')
                ->name('usuarios.rechazar-vinculacion');
            Route::put('/cambiar-estado', [UsuarioController::class, 'cambiarEstado'])
                ->middleware(VerificarPermiso::class . ':cambiar-estado-usuario')
                ->name('usuarios.cambiar-estado');
            Route::put('/cambiar-rol', [UsuarioController::class, 'cambiarRol'])
                ->middleware(VerificarPermiso::class . ':asignar-rol-usuario')
                ->name('usuarios.cambiar-rol');
            Route::get('/{usuario}', [UsuarioController::class, 'show'])
                ->middleware('verificar.permiso:ver-usuarios')
                ->name('usuarios.show');
        });

    // ============================================
    // ROLES Y PERMISOS
    // ============================================
    Route::middleware(['verificar.acceso.organizacion', 'verificar.permiso:gestionar-roles'])
        ->prefix('roles')
        ->group(function () {
            Route::get('/', [RolesController::class, 'index'])->name('roles.index');
            Route::get('/{rol}', [RolesController::class, 'show'])->name('roles.show');
            Route::put('/{rol}', [RolesController::class, 'update'])->name('roles.update');
        });

    // ============================================
    // GESTIÓN DE CONTRATOS
    // ============================================
    Route::middleware(['verificar.acceso.organizacion'])->prefix('contratos')->group(function () {
            Route::get('/', [ContratoController::class, 'index'])
                ->middleware('verificar.permiso:ver-contratos')
                ->name('contratos.index');

            Route::get('/create', [ContratoController::class, 'create'])
                ->middleware('verificar.permiso:crear-contrato')
                ->name('contratos.create');

            Route::post('/', [ContratoController::class, 'store'])
                ->middleware('verificar.permiso:crear-contrato')
                ->name('contratos.store');

            Route::get('/{contrato}/edit', [ContratoController::class, 'edit'])
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
        
        // DOCUMENTOS
        Route::post('/{id}/documentos', [CuentaCobroController::class, 'subirDocumento'])
            ->middleware('verificar.permiso:cargar-documentos')
            ->name('cuentas-cobro.subir-documento');
        
        Route::delete('/{id}/documentos/{documentoId}', [CuentaCobroController::class, 'eliminarDocumento'])
            ->middleware('verificar.permiso:cargar-documentos')
            ->name('cuentas-cobro.eliminar-documento');
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
            ->middleware('verificar.permiso:registrar-pago-orden')
            ->name('pagos.op.registrar-pago');
    });
});

Route::get('/welcome', function () {
    return view('welcome');
});