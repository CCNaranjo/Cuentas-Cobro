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
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RolesController;

/*
|--------------------------------------------------------------------------
| Web Routes - ARCA-D
|--------------------------------------------------------------------------
*/

// Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])
    ->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])
    ->name('verify-email');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Cambiar organización activa
    Route::get('/cambiar-organizacion/{id}', function ($id) {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        
        // Verificar acceso a la organización
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
        Route::get('/', [OrganizacionController::class, 'index'])
            ->name('organizaciones.index');
        Route::post('/organizaciones/{organizacion}/seleccionar', [OrganizacionController::class, 'seleccionar'])
            ->name('organizaciones.seleccionar');
        Route::get('/create', [OrganizacionController::class, 'create'])
            ->name('organizaciones.create');
        Route::post('/', [OrganizacionController::class, 'store'])
            ->name('organizaciones.store');
        Route::get('/{organizacion}', [OrganizacionController::class, 'show'])
            ->name('organizaciones.show');
        Route::get('/{organizacion}/edit', [OrganizacionController::class, 'edit'])
            ->name('organizaciones.edit');
        Route::put('/{organizacion}', [OrganizacionController::class, 'update'])
            ->name('organizaciones.update');
        Route::post('/organizaciones/{organizacion}/asignar-admin', [OrganizacionController::class, 'asignarAdmin'])
            ->name('organizaciones.asignar-admin');
        Route::put('/{organizacion}/actualizar-admin', [OrganizacionController::class, 'actualizarAdmin'])
            ->name('organizaciones.actualizar-admin');
        Route::put('/{organizacion}/cambiar-admin', [OrganizacionController::class, 'cambiarAdmin'])
            ->name('organizaciones.cambiar-admin');
    });
    Route::post('/vincular-codigo', [OrganizacionController::class, 'vincularCodigo'])
            ->name('vincular-codigo');

    // ============================================
    // GESTIÓN DE USUARIOS
    // Requiere: verificar acceso a organización + permiso ver-usuarios
    // ============================================
    Route::middleware([VerificarAccesoOrganizacion::class, VerificarPermiso::class . ':ver-usuarios'])
        ->prefix('usuarios')
        ->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])
                ->name('usuarios.index');
            Route::get('/pendientes', [UsuarioController::class, 'pendientes'])
                ->name('usuarios.pendientes');
            Route::post('/asignar-rol', [UsuarioController::class, 'asignarRol'])
                ->middleware(VerificarPermiso::class . ':asignar-rol')
                ->name('usuarios.asignar-rol');            
            Route::post('/{id}/rechazar', [UsuarioController::class, 'rechazarVinculacion'])
                ->name('usuarios.rechazar');            
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
            Route::get('/{id}', [UsuarioController::class, 'show'])
                ->name('usuarios.show');
        });

// ============================================
// GESTIÓN DE ROLES Y PERMISOS
// ============================================

// Index - Nivel 1, 2 y 3 pueden ver
Route::middleware([VerificarPermiso::class . ':ver-roles'])
    ->get('/roles', [RolesController::class, 'index'])
    ->name('roles.index');

// Crear - Nivel 1 y 2
Route::middleware([VerificarPermiso::class . ':crear-rol'])
    ->group(function () {
        Route::get('/roles/create', [RolesController::class, 'create'])
            ->name('roles.create');
        Route::post('/roles', [RolesController::class, 'store'])
            ->name('roles.store');
    });

// Ver - Nivel 1, 2 y 3
Route::middleware([VerificarPermiso::class . ':ver-roles'])
    ->get('/roles/{rol}', [RolesController::class, 'show'])
    ->name('roles.show');

// Editar - Nivel 1 y 2
Route::middleware([VerificarPermiso::class . ':asignar-permisos-rol'])
    ->group(function () {
        Route::get('/roles/{rol}/edit', [RolesController::class, 'edit'])
            ->name('roles.edit');
        Route::put('/roles/{rol}', [RolesController::class, 'update'])
            ->name('roles.update');
    });

// Eliminar - Solo Nivel 1
Route::middleware([VerificarPermiso::class . ':gestionar-roles'])
    ->delete('/roles/{rol}', [RolesController::class, 'destroy'])
    ->name('roles.destroy');

    // ============================================
    // CONTRATOS
    // Requiere: verificar acceso a organización
    // ============================================
    Route::middleware([VerificarAccesoOrganizacion::class])
        ->prefix('contratos')
        ->group(function () {
            Route::get('/', [ContratoController::class, 'index'])
                ->name('contratos.index');
            
            Route::get('/create', [ContratoController::class, 'create'])
                ->middleware('verificar.permiso:crear-contrato')
                ->name('contratos.create');
            
            Route::post('/', [ContratoController::class, 'store'])
                ->middleware('verificar.permiso:crear-contrato')
                ->name('contratos.store');
            
            Route::get('/{contrato}', [ContratoController::class, 'show'])
                ->name('contratos.show');
            
            Route::get('/{contrato}/edit', [ContratoController::class, 'edit'])
                ->middleware('verificar.permiso:editar-contrato')
                ->name('contratos.edit');
            
            Route::put('/{contrato}', [ContratoController::class, 'update'])
                ->middleware('verificar.permiso:editar-contrato')
                ->name('contratos.update');
            
            Route::post('/{contrato}/vincular-contratista', [ContratoController::class, 'vincularContratista'])
                ->middleware('verificar.permiso:vincular-contratista')
                ->name('contratos.vincular-contratista');
            
            Route::put('/{contrato}/cambiar-supervisor', [ContratoController::class, 'cambiarSupervisor'])
                ->middleware('verificar.permiso:editar-contrato')
                ->name('contratos.cambiar-supervisor');
            
            Route::put('/{contrato}/cambiar-estado', [ContratoController::class, 'cambiarEstado'])
                ->middleware('verificar.permiso:editar-contrato')
                ->name('contratos.cambiar-estado');
            
            // API para búsqueda de contratistas
            Route::get('/buscar/contratista', [ContratoController::class, 'buscarContratista'])
                ->name('contratos.buscar-contratista');
        });

    // ========== RUTAS DE CONFIGURACIÓN ==========
    
    // Ruta principal (redirige según rol)
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])
        ->name('configuracion.index');
    
    // Configuración Global (Solo Admin Global)
    Route::middleware([VerificarAdminGlobal::class])->group(function () {
        Route::get('/configuracion/global', [ConfiguracionController::class, 'global'])
            ->name('configuracion.global');
        Route::post('/configuracion/global', [ConfiguracionController::class, 'actualizarGlobal'])
            ->name('configuracion.actualizar-global');
        Route::get('/configuracion/exportar-logs', [ConfiguracionController::class, 'exportarLogs'])
            ->name('configuracion.exportar-logs');
    });
    
    // Configuración de Organización (Admin Organización)
    Route::middleware([VerificarAccesoOrganizacion::class])->group(function () {
        Route::get('/configuracion/organizacion', [ConfiguracionController::class, 'organizacion'])
            ->name('configuracion.organizacion');
        Route::post('/configuracion/organizacion', [ConfiguracionController::class, 'actualizarOrganizacion'])
            ->name('configuracion.actualizar-organizacion');
    });

    // ============================================
    // PERFIL DE USUARIO
    // ============================================
    Route::prefix('perfil')->group(function () {
        Route::get('/', [UsuarioController::class, 'perfil'])
            ->name('perfil');
        Route::put('/actualizar', [UsuarioController::class, 'actualizarPerfil'])
            ->name('perfil.actualizar');
        Route::put('/cambiar-password', [UsuarioController::class, 'cambiarPassword'])
            ->name('perfil.cambiar-password');
        Route::put('/notificaciones', [UsuarioController::class, 'actualizarNotificaciones'])
            ->name('perfil.actualizar-notificaciones');
        Route::post('/subir-foto', [UsuarioController::class, 'subirFotoPerfil'])
            ->name('perfil.subir-foto');
    });
});

// Ruta de bienvenida (opcional)
Route::get('/welcome', function () {
    return view('welcome');
});