@extends('layouts.app-dashboard')

@section('title', 'Dashboard - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-primary shadow-xl flex-shrink-0 hidden lg:flex flex-col">
        <!-- Logo Section -->
        <div class="h-20 flex items-center justify-center border-b border-primary-dark/30 px-4 flex-shrink-0">
            <div class="w-40">
                <x-logo primary-color="#FFFFFF" secondary-color="#00BCD4"></x-logo>
            </div>
        </div>

        <!-- Navigation - Scrollable -->
        <nav class="mt-6 px-3"> 
            <div class="mb-6">
                <p class="px-4 text-xs font-semibold text-gray-300 uppercase tracking-wider mb-3">
                    Menú Principal
                </p>
                
                <ul class="list-none p-0 m-0">
                    <li class="mb-1">
                        <a href="{{ route('dashboard') }}" class="sidebar-link active group text-white"> 
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all"></div>
                            <i class="fas fa-tachometer-alt text-lg text-white"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#" class="sidebar-link group text-gray-300 hover:text-white"> 
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0 group-hover:opacity-100"></div>
                            <i class="fas fa-file-invoice text-lg"></i>
                            <span>Cuentas de Cobro</span>
                            <span class="ml-auto bg-accent text-primary px-2 py-0.5 rounded-full text-xs font-bold">0</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#" class="sidebar-link group text-gray-300 hover:text-white">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0 group-hover:opacity-100"></div>
                            <i class="fas fa-users text-lg"></i>
                            <span>Contratistas</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#" class="sidebar-link group text-gray-300 hover:text-white">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0 group-hover:opacity-100"></div>
                            <i class="fas fa-wallet text-lg"></i>
                            <span>Tesorería</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-6">
                <p class="px-4 text-xs font-semibold text-gray-300 uppercase tracking-wider mb-3">
                    Análisis
                </p>
                <ul class="list-none p-0 m-0">
                    <li class="mb-1">
                        <a href="#" class="sidebar-link group text-gray-300 hover:text-white">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0 group-hover:opacity-100"></div>
                            <i class="fas fa-chart-line text-lg"></i>
                            <span>Informes</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#" class="sidebar-link group text-gray-300 hover:text-white">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0 group-hover:opacity-100"></div>
                            <i class="fas fa-chart-pie text-lg"></i>
                            <span>Cartera</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="px-4 text-xs font-semibold text-gray-300 uppercase tracking-wider mb-3">
                    Sistema
                </p>
                <ul class="list-none p-0 m-0">
                    <li class="mb-1">
                        <a href="#" class="sidebar-link group text-gray-300 hover:text-white">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent rounded-r-full transition-all opacity-0 group-hover:opacity-100"></div>
                            <i class="fas fa-cog text-lg"></i>
                            <span>Configuración</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- User Section - Fixed at bottom -->
        <div class="flex-shrink-0 p-4 border-t border-primary-dark/30 bg-primary-dark/30">
            <div class="flex items-center space-x-3 text-white">
                <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center font-bold text-primary flex-shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-white/60 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Header -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm flex-shrink-0">
            <!-- Mobile Menu Button -->
            <button class="lg:hidden text-gray-600 hover:text-primary" onclick="toggleMobileMenu()">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-4">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Buscar cuentas, contratistas, documentos..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/20 transition-all"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Right Section -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full animate-pulse"></span>
                </button>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <i class="fas fa-chevron-down text-gray-600 text-xs"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div 
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                        style="display: none;"
                    >
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-user-circle mr-2 text-gray-400"></i>
                            Mi Perfil
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-cog mr-2 text-gray-400"></i>
                            Configuración
                        </a>
                        <hr class="my-2">
                        <a 
                            href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area - Scrollable -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Welcome Section -->
                <div class="mb-6 animate-slideIn">
                    <h1 class="text-3xl font-bold text-gray-800">
                        ¡Bienvenido, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="text-secondary mt-1">
                        Gestiona tus cuentas de cobro de manera eficiente
                    </p>
                </div>

                <!-- KPIs Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Cuentas -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-invoice text-primary text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                +0%
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Total Cuentas</h3>
                        <p class="text-3xl font-bold text-gray-800">0</p>
                        <p class="text-xs text-secondary mt-2">Cuentas registradas</p>
                    </div>

                    <!-- Recaudo Total -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-accent text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                +0%
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Recaudo Total</h3>
                        <p class="text-3xl font-bold text-accent">$0</p>
                        <p class="text-xs text-secondary mt-2">Este mes</p>
                    </div>

                    <!-- Cuentas Pendientes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-warning text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-warning bg-orange-100 px-2 py-1 rounded-full">
                                0 alertas
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Pendientes</h3>
                        <p class="text-3xl font-bold text-warning">0</p>
                        <p class="text-xs text-secondary mt-2">Por vencer pronto</p>
                    </div>

                    <!-- En Mora -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-danger/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-danger text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-danger bg-red-100 px-2 py-1 rounded-full">
                                Crítico
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">En Mora</h3>
                        <p class="text-3xl font-bold text-danger">0</p>
                        <p class="text-xs text-secondary mt-2">Cuentas vencidas</p>
                    </div>
                </div>

                <!-- Charts and Quick Actions Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Cartera por Antigüedad -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-chart-pie text-primary mr-2"></i>
                                Cartera por Antigüedad
                            </h2>
                            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:border-accent cursor-pointer">
                                <option>Este mes</option>
                                <option>Último trimestre</option>
                                <option>Este año</option>
                            </select>
                        </div>
                        
                        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <i class="fas fa-chart-pie text-6xl text-gray-300 mb-4"></i>
                                <p class="text-secondary font-medium">No hay datos para mostrar</p>
                                <p class="text-sm text-gray-400 mt-2">Comienza agregando cuentas de cobro</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-rocket text-accent mr-2"></i>
                            Acciones Rápidas
                        </h2>
                        
                        <div class="space-y-3">
                            <button class="w-full bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-4 rounded-lg hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center group">
                                <i class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
                                Nueva Cuenta
                            </button>
                            
                            <button class="w-full border-2 border-primary text-primary font-semibold py-3 px-4 rounded-lg hover:bg-primary hover:text-white transition-all flex items-center justify-center group">
                                <i class="fas fa-user-plus mr-2 group-hover:scale-110 transition-transform"></i>
                                Agregar Cliente
                            </button>
                            
                            <button class="w-full border-2 border-accent text-accent font-semibold py-3 px-4 rounded-lg hover:bg-accent hover:text-white transition-all flex items-center justify-center group">
                                <i class="fas fa-chart-line mr-2 group-hover:scale-110 transition-transform"></i>
                                Ver Reportes
                            </button>
                            
                            <button class="w-full border-2 border-secondary text-secondary font-semibold py-3 px-4 rounded-lg hover:bg-secondary hover:text-white transition-all flex items-center justify-center group">
                                <i class="fas fa-download mr-2 group-hover:translate-y-1 transition-transform"></i>
                                Exportar Datos
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cuentas Pendientes Destacadas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-list text-primary mr-2"></i>
                            Cuentas Pendientes Destacadas
                        </h2>
                        <a href="#" class="text-sm text-accent hover:text-primary font-medium flex items-center group">
                            Ver todas
                            <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Nº Cuenta</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Contratista</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Monto</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Vencimiento</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Estado</th>
                                    <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center py-12">
                                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-secondary font-medium">No hay cuentas pendientes</p>
                                        <p class="text-sm text-gray-400 mt-2">¡Excelente! Todo está al día</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-auto">
                <div class="px-6 py-4">
                    <!-- Top Footer Section -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pb-6 border-b border-gray-200">
                        <!-- About Section -->
                        <div>
                            <div class="w-32 mb-3">
                                <x-logo primary-color="#004AAD" secondary-color="#00BCD4"></x-logo>
                            </div>
                            <p class="text-sm text-secondary leading-relaxed">
                                Sistema de gestión de cuentas por cobrar para optimizar tu flujo de trabajo.
                            </p>
                        </div>

                        <!-- Quick Links -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Enlaces Rápidos</h4>
                            <ul class="space-y-2">
                                <li>
                                    <a href="#" class="text-sm text-secondary hover:text-primary transition-colors flex items-center group">
                                        <i class="fas fa-chevron-right text-xs mr-2 text-accent group-hover:translate-x-1 transition-transform"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm text-secondary hover:text-primary transition-colors flex items-center group">
                                        <i class="fas fa-chevron-right text-xs mr-2 text-accent group-hover:translate-x-1 transition-transform"></i>
                                        Cuentas de Cobro
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm text-secondary hover:text-primary transition-colors flex items-center group">
                                        <i class="fas fa-chevron-right text-xs mr-2 text-accent group-hover:translate-x-1 transition-transform"></i>
                                        Reportes
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Resources -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Recursos</h4>
                            <ul class="space-y-2">
                                <li>
                                    <a href="#" class="text-sm text-secondary hover:text-primary transition-colors flex items-center group">
                                        <i class="fas fa-book text-xs mr-2 text-accent"></i>
                                        Documentación
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm text-secondary hover:text-primary transition-colors flex items-center group">
                                        <i class="fas fa-question-circle text-xs mr-2 text-accent"></i>
                                        Centro de Ayuda
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm text-secondary hover:text-primary transition-colors flex items-center group">
                                        <i class="fas fa-headset text-xs mr-2 text-accent"></i>
                                        Soporte Técnico
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Contact -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Contacto</h4>
                            <ul class="space-y-2">
                                <li class="text-sm text-secondary flex items-start">
                                    <i class="fas fa-envelope text-accent mr-2 mt-1"></i>
                                    <span>soporte@arca-d.com</span>
                                </li>
                                <li class="text-sm text-secondary flex items-start">
                                    <i class="fas fa-phone text-accent mr-2 mt-1"></i>
                                    <span>+57 (1) 234-5678</span>
                                </li>
                                <li class="text-sm text-secondary flex items-start">
                                    <i class="fas fa-map-marker-alt text-accent mr-2 mt-1"></i>
                                    <span>Bogotá, Colombia</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Bottom Footer Section -->
                    <div class="flex flex-col md:flex-row justify-between items-center pt-4 space-y-4 md:space-y-0">
                        <!-- Copyright -->
                        <div class="text-sm text-secondary">
                            © {{ date('Y') }} <span class="font-semibold text-primary">ARCA-D</span>. Todos los derechos reservados.
                        </div>

                        <!-- Social Links -->
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-secondary">Síguenos:</span>
                            <a href="#" class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>

                        <!-- Legal Links -->
                        <div class="flex items-center space-x-4 text-sm">
                            <a href="#" class="text-secondary hover:text-primary transition-colors">Privacidad</a>
                            <span class="text-gray-300">|</span>
                            <a href="#" class="text-secondary hover:text-primary transition-colors">Términos</a>
                            <span class="text-gray-300">|</span>
                            <a href="#" class="text-secondary hover:text-primary transition-colors">Cookies</a>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-gray-400">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-accent mr-1"></i>
                                <span>Conexión Segura SSL</span>
                            </div>
                            <span class="text-gray-300">•</span>
                            <div class="flex items-center">
                                <i class="fas fa-server text-accent mr-1"></i>
                                <span>Versión 1.0.0</span>
                            </div>
                            <span class="text-gray-300">•</span>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-accent mr-1"></i>
                                <span>Última actualización: {{ date('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function toggleMobileMenu() {
        // Implementar menú móvil
        alert('Menú móvil - Implementar según necesidades');
    }

    // Animación de entrada para los KPIs
    document.addEventListener('DOMContentLoaded', function() {
        const kpiCards = document.querySelectorAll('.hover-lift');
        kpiCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.4s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endpush
@endsection