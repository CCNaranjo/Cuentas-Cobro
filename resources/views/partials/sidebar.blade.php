<!-- Sidebar -->
<aside class="w-64 bg-primary shadow-xl flex-shrink-0 hidden lg:flex flex-col">
    <!-- Logo Section -->
    <div class="h-20 flex items-center justify-center border-b border-primary-dark/30 px-4 flex-shrink-0">
        <div class="w-40">
            <x-logo primary-color="#FFFFFF" secondary-color="#00BCD4"></x-logo>
        </div>
    </div>

    <!-- Navigation - Scrollable -->
    <nav class="flex-1 overflow-y-auto mt-6 px-3 pb-4">
        <!-- Menú Principal -->
        <div class="mb-6">
            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">
                Menú Principal
            </p>
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="sidebar-link group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all {{ request()->routeIs('dashboard') ? '' : 'opacity-0 group-hover:opacity-100' }}"></div>
                <i class="fas fa-tachometer-alt text-lg"></i>
                <span>Dashboard</span>
            </a>

            <!-- Organizaciones (Solo Admin Global) -->
            @if(auth()->user()->esAdminGlobal())
            <a href="{{ route('organizaciones.index') }}" 
               class="sidebar-link group {{ request()->routeIs('organizaciones.*') ? 'active' : '' }}">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all {{ request()->routeIs('organizaciones.*') ? '' : 'opacity-0 group-hover:opacity-100' }}"></div>
                <i class="fas fa-building text-lg"></i>
                <span>Organizaciones</span>
            </a>
            @endif

            <!-- Usuarios -->
            @if(auth()->user()->tienePermiso('ver-usuarios', session('organizacion_actual')) || auth()->user()->esAdminGlobal())
            <a href="{{ route('usuarios.index') }}" 
               class="sidebar-link group {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all {{ request()->routeIs('usuarios.*') ? '' : 'opacity-0 group-hover:opacity-100' }}"></div>
                <i class="fas fa-users text-lg"></i>
                <span>Usuarios</span>
                @if(session('organizacion_actual'))
                    @php
                        $pendientes = \App\Models\VinculacionPendiente::where('organizacion_id', session('organizacion_actual'))
                            ->where('estado', 'pendiente')
                            ->count();
                    @endphp
                    @if($pendientes > 0)
                        <span class="ml-auto bg-warning text-white px-2 py-0.5 rounded-full text-xs font-bold animate-pulse">
                            {{ $pendientes }}
                        </span>
                    @endif
                @endif
            </a>
            @endif

            <!-- Roles y permisos (Solo Admin Global) -->
            @if(auth()->user()->esAdminGlobal())
            <a href="{{ route('roles.index') }}" 
               class="sidebar-link group {{ request()->routeIs('organizaciones.*') ? 'active' : '' }}">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all {{ request()->routeIs('organizaciones.*') ? '' : 'opacity-0 group-hover:opacity-100' }}"></div>
                <i class="fas fa-building text-lg"></i>
                <span>Roles</span>
            </a>
            @endif
            
            <!-- Contratos -->
            @if(auth()->user()->tienePermiso('ver-todos-contratos', session('organizacion_actual')) || 
                auth()->user()->tienePermiso('ver-mis-contratos', session('organizacion_actual')))
            <a href="{{ route('contratos.index') }}" 
               class="sidebar-link group {{ request()->routeIs('contratos.*') ? 'active' : '' }}">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all {{ request()->routeIs('contratos.*') ? '' : 'opacity-0 group-hover:opacity-100' }}"></div>
                <i class="fas fa-file-contract text-lg"></i>
                <span>Contratos</span>
            </a>
            @endif

            <!-- Cuentas de Cobro (Placeholder para futuro) -->
            @if(auth()->user()->tienePermiso('ver-todas-cuentas', session('organizacion_actual')) || 
                auth()->user()->tienePermiso('ver-mis-cuentas', session('organizacion_actual')))
            <a href="#" 
               class="sidebar-link group opacity-50 cursor-not-allowed" 
               title="Próximamente">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0"></div>
                <i class="fas fa-file-invoice-dollar text-lg"></i>
                <span>Cuentas de Cobro</span>
                <span class="ml-auto text-xs bg-accent/20 text-white px-2 py-0.5 rounded">Próximo</span>
            </a>
            @endif
        </div>

        <!-- Análisis -->
        @if(auth()->user()->tienePermiso('ver-reportes-organizacion', session('organizacion_actual')) || 
            auth()->user()->tienePermiso('ver-reportes-globales', session('organizacion_actual')))
        <div class="mb-6">
            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">
                Análisis
            </p>
            
            <a href="#" class="sidebar-link group opacity-50 cursor-not-allowed" title="Próximamente">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0"></div>
                <i class="fas fa-chart-line text-lg"></i>
                <span>Informes</span>
                <span class="ml-auto text-xs bg-accent/20 text-white px-1.5 py-0.5 rounded">Próximo</span>
            </a>
            
            <a href="#" class="sidebar-link group opacity-50 cursor-not-allowed" title="Próximamente">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0"></div>
                <i class="fas fa-chart-pie text-lg"></i>
                <span>Estadísticas</span>
                <span class="ml-auto text-xs bg-accent/20 text-white px-1.5 py-0.5 rounded">Próximo</span>
            </a>
        </div>
        @endif

        <!-- Tesorería (Solo para Tesorero) -->
        @if(auth()->user()->tienePermiso('registrar-pago', session('organizacion_actual')))
        <div class="mb-6">
            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">
                Tesorería
            </p>
            
            <a href="#" class="sidebar-link group opacity-50 cursor-not-allowed" title="Próximamente">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0"></div>
                <i class="fas fa-money-check-alt text-lg"></i>
                <span>Pagos</span>
                <span class="ml-auto text-xs bg-accent/20 text-white px-1.5 py-0.5 rounded">Próximo</span>
            </a>
        </div>
        @endif

        <!-- Sistema -->
        <div class="mb-6">
            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">
                Sistema
            </p>
            
            <a href="#" class="sidebar-link group opacity-50 cursor-not-allowed" title="Próximamente">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0"></div>
                <i class="fas fa-cog text-lg"></i>
                <span>Configuración</span>
                <span class="ml-auto text-xs bg-accent/20 text-white px-1.5 py-0.5 rounded">Próximo</span>
            </a>

            <a href="#" class="sidebar-link group opacity-50 cursor-not-allowed" title="Próximamente">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0"></div>
                <i class="fas fa-question-circle text-lg"></i>
                <span>Ayuda</span>
                <span class="ml-auto text-xs bg-accent/20 text-white px-1.5 py-0.5 rounded">Próximo</span>
            </a>
        </div>
    </nav>

    <!-- User Section - Fixed at bottom -->
    <div class="flex-shrink-0 p-4 border-t border-primary-dark/30 bg-primary-dark/30">
        <div class="flex items-center space-x-3 text-white">
            <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center font-bold text-primary flex-shrink-0">
                {{ substr(auth()->user()->nombre, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ auth()->user()->nombre }}</p>
                <p class="text-xs text-white/60 truncate">
                    @if(auth()->user()->esAdminGlobal())
                        Admin Global
                    @else
                        {{ auth()->user()->email }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</aside>

<style>
/* Estilos para los enlaces del sidebar */
.sidebar-link {
    @apply relative flex items-center space-x-3 px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 mb-1;
}

.sidebar-link.active {
    @apply bg-white/10 text-white font-medium;
}

.sidebar-link:not(.active):hover {
    @apply translate-x-1;
}

.sidebar-link i {
    @apply w-5 flex-shrink-0;
}

/* Animación para badges de notificación */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>