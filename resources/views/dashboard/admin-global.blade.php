@extends('layouts.app-dashboard')

@section('title', 'Dashboard Admin Global - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Welcome Section -->
                <div class="mb-6 animate-slideIn">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-crown text-warning mr-3"></i>
                                Panel de Administración Global
                            </h1>
                            <p class="text-secondary mt-1">
                                Vista completa del sistema ARCA-D
                            </p>
                        </div>
                        <a href="{{ route('organizaciones.create') }}" 
                           class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Nueva Organización
                        </a>
                    </div>
                </div>

                <!-- KPIs Globales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Organizaciones -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-primary text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                {{ $estadisticas['organizaciones_activas'] }} activas
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Organizaciones</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $estadisticas['total_organizaciones'] }}</p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-chart-line text-green-500 mr-1"></i>
                            <span>Total en el sistema</span>
                        </div>
                    </div>

                    <!-- Total Usuarios -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-accent text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                {{ $estadisticas['usuarios_activos'] }} activos
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Usuarios</h3>
                        <p class="text-3xl font-bold text-accent">{{ $estadisticas['total_usuarios'] }}</p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-user-check text-accent mr-1"></i>
                            <span>Registrados totales</span>
                        </div>
                    </div>

                    <!-- Total Contratos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                {{ $estadisticas['contratos_activos'] }} activos
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Contratos</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $estadisticas['total_contratos'] }}</p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-handshake text-green-600 mr-1"></i>
                            <span>En todas las organizaciones</span>
                        </div>
                    </div>

                    <!-- Estadísticas del Mes -->
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 hover-lift cursor-pointer text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">
                                Este mes
                            </span>
                        </div>
                        <h3 class="text-white/80 text-sm font-medium mb-1">Nuevas Registros</h3>
                        <p class="text-3xl font-bold">{{ $estadisticas['total_organizaciones'] }}</p>
                        <div class="mt-3 flex items-center text-xs text-white/80">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>Organizaciones nuevas</span>
                        </div>
                    </div>
                </div>

                <!-- Gráficos y Organizaciones Recientes -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Distribución de Organizaciones -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-chart-bar text-primary mr-2"></i>
                                Estadísticas del Sistema
                            </h2>
                            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:border-accent cursor-pointer">
                                <option>Últimos 7 días</option>
                                <option>Últimos 30 días</option>
                                <option>Este año</option>
                            </select>
                        </div>

                        <!-- Mini Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-blue-600 uppercase">Organizaciones</span>
                                    <i class="fas fa-building text-blue-600"></i>
                                </div>
                                <p class="text-2xl font-bold text-blue-700">{{ $estadisticas['organizaciones_activas'] }}</p>
                                <p class="text-xs text-blue-600 mt-1">Activas</p>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-green-600 uppercase">Usuarios</span>
                                    <i class="fas fa-users text-green-600"></i>
                                </div>
                                <p class="text-2xl font-bold text-green-700">{{ $estadisticas['usuarios_activos'] }}</p>
                                <p class="text-xs text-green-600 mt-1">Activos</p>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-purple-600 uppercase">Contratos</span>
                                    <i class="fas fa-file-contract text-purple-600"></i>
                                </div>
                                <p class="text-2xl font-bold text-purple-700">{{ $estadisticas['contratos_activos'] }}</p>
                                <p class="text-xs text-purple-600 mt-1">Vigentes</p>
                            </div>
                        </div>

                        <!-- Gráfico Placeholder -->
                        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <i class="fas fa-chart-line text-5xl text-gray-400 mb-3"></i>
                                <p class="text-secondary font-medium">Gráfico de Tendencias</p>
                                <p class="text-sm text-gray-400 mt-1">Implementar con Chart.js</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas Admin -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-lightning-bolt text-warning mr-2"></i>
                            Acciones Rápidas
                        </h2>

                        <div class="space-y-3">
                            <a href="{{ route('organizaciones.create') }}" 
                               class="w-full bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-4 rounded-lg hover:shadow-lg transition-all flex items-center justify-center group">
                                <i class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
                                Nueva Organización
                            </a>

                            <a href="{{ route('organizaciones.index') }}" 
                               class="w-full border-2 border-primary text-primary font-semibold py-3 px-4 rounded-lg hover:bg-primary hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-list mr-2"></i>
                                Ver Organizaciones
                            </a>

                            <button class="w-full border-2 border-accent text-accent font-semibold py-3 px-4 rounded-lg hover:bg-accent hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-download mr-2"></i>
                                Exportar Reporte
                            </button>

                            <button class="w-full border-2 border-secondary text-secondary font-semibold py-3 px-4 rounded-lg hover:bg-secondary hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-cog mr-2"></i>
                                Configuración
                            </button>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 bg-gradient-to-r from-accent/10 to-primary/10 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-primary text-lg mr-3 mt-1"></i>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 mb-1">Admin Global</p>
                                    <p class="text-xs text-secondary">Tienes acceso completo a todas las funcionalidades del sistema</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organizaciones Recientes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-primary mr-2"></i>
                            Organizaciones Recientes
                        </h2>
                        <a href="{{ route('organizaciones.index') }}" 
                           class="text-sm text-accent hover:text-primary font-medium flex items-center group">
                            Ver todas
                            <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Organización</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Ubicación</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Usuarios</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Contratos</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Estado</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Creado</th>
                                    <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($organizacionesRecientes as $org)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mr-3">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $org->nombre_oficial }}</p>
                                                <p class="text-xs text-secondary">{{ $org->nit }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <p class="text-sm text-gray-800">{{ $org->municipio }}</p>
                                            <p class="text-xs text-secondary">{{ $org->departamento }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-accent/10 text-accent px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $org->usuarios->count() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $org->contratos->count() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($org->estado == 'activa')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Activa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                Inactiva
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center text-sm text-secondary">
                                        {{ $org->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('organizaciones.show', $org) }}" 
                                               class="text-accent hover:text-primary transition-colors p-2"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('organizaciones.edit', $org) }}" 
                                               class="text-primary hover:text-primary-dark transition-colors p-2"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-secondary font-medium">No hay organizaciones registradas</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Últimas Actividades -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-history text-accent mr-2"></i>
                            Actividad Reciente
                        </h2>
                        <div class="space-y-4">
                            <!-- Actividad item -->
                            <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-plus text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800">Nueva organización creada</p>
                                    <p class="text-xs text-secondary mt-1">Hace 2 horas</p>
                                </div>
                            </div>

                            <!-- Estado vacío temporal -->
                            <div class="text-center py-8">
                                <i class="fas fa-clock text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-secondary">No hay actividad reciente</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sistema Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-server text-primary mr-2"></i>
                            Estado del Sistema
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                                    <span class="text-sm font-medium text-gray-800">Servidor</span>
                                </div>
                                <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                    Operativo
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-database text-green-600 text-xl mr-3"></i>
                                    <span class="text-sm font-medium text-gray-800">Base de Datos</span>
                                </div>
                                <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                    Conectada
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-code-branch text-blue-600 text-xl mr-3"></i>
                                    <span class="text-sm font-medium text-gray-800">Versión</span>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                    v1.0.0
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
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