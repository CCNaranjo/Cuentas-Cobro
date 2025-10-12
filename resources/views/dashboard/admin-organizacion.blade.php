@extends('layouts.app-dashboard')

@section('title', 'Dashboard - ' . $organizacion->nombre_oficial)

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
                                <i class="fas fa-tachometer-alt text-primary mr-3"></i>
                                {{ $organizacion->nombre_oficial }}
                            </h1>
                            <p class="text-secondary mt-1">
                                Panel de administración - {{ $organizacion->municipio }}, {{ $organizacion->departamento }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('contratos.create', ['organizacion_id' => $organizacion->id]) }}" 
                               class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Nuevo Contrato
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Alertas Importantes -->
                @if($estadisticas['usuarios_pendientes'] > 0)
                <div class="mb-6 bg-warning/10 border-l-4 border-warning rounded-lg p-4 animate-slideIn">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-warning text-xl mr-3"></i>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $estadisticas['usuarios_pendientes'] }} usuario(s) pendiente(s) de asignar rol
                                </p>
                                <p class="text-sm text-secondary mt-1">Revisa y asigna roles a los nuevos usuarios</p>
                            </div>
                        </div>
                        <a href="{{ route('usuarios.pendientes', ['organizacion_id' => $organizacion->id]) }}" 
                           class="bg-warning text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors font-medium">
                            <i class="fas fa-user-check mr-2"></i>
                            Revisar
                        </a>
                    </div>
                </div>
                @endif

                <!-- KPIs Organización -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Usuarios Activos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-accent text-xl"></i>
                            </div>
                            @if($estadisticas['usuarios_pendientes'] > 0)
                                <span class="text-xs font-semibold text-warning bg-orange-100 px-2 py-1 rounded-full">
                                    {{ $estadisticas['usuarios_pendientes'] }} pendientes
                                </span>
                            @else
                                <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                    Al día
                                </span>
                            @endif
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Usuarios Activos</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $estadisticas['usuarios_activos'] }}</p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-user-check text-accent mr-1"></i>
                            <span>Usuarios en la organización</span>
                        </div>
                    </div>

                    <!-- Contratos Activos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-primary text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                {{ $estadisticas['contratos_activos'] }} activos
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Contratos</h3>
                        <p class="text-3xl font-bold text-primary">{{ $estadisticas['contratos_total'] }}</p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-handshake text-primary mr-1"></i>
                            <span>Total contratos registrados</span>
                        </div>
                    </div>

                    <!-- Valor Total Contratos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                Contratos
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Valor Total</h3>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($estadisticas['valor_contratos_activos'], 0) }}</p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-chart-line text-green-600 mr-1"></i>
                            <span>Contratos activos</span>
                        </div>
                    </div>

                    <!-- Actividad General -->
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 hover-lift cursor-pointer text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-pie text-white text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">
                                Este mes
                            </span>
                        </div>
                        <h3 class="text-white/80 text-sm font-medium mb-1">Actividad</h3>
                        <p class="text-3xl font-bold">{{ $estadisticas['contratos_total'] }}</p>
                        <div class="mt-3 flex items-center text-xs text-white/80">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>Movimientos registrados</span>
                        </div>
                    </div>
                </div>

                <!-- Gráficos y Acciones -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Distribución de Contratos -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-chart-pie text-primary mr-2"></i>
                                Estado de Contratos
                            </h2>
                            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:border-accent cursor-pointer">
                                <option>Este mes</option>
                                <option>Último trimestre</option>
                                <option>Este año</option>
                            </select>
                        </div>

                        @if($estadisticas['contratos_total'] > 0)
                            <!-- Estadísticas visuales -->
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-green-600 uppercase">Activos</span>
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <p class="text-2xl font-bold text-green-700">{{ $estadisticas['contratos_activos'] }}</p>
                                    <p class="text-xs text-green-600 mt-1">En ejecución</p>
                                </div>

                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-blue-600 uppercase">Total</span>
                                        <i class="fas fa-file-contract text-blue-600"></i>
                                    </div>
                                    <p class="text-2xl font-bold text-blue-700">{{ $estadisticas['contratos_total'] }}</p>
                                    <p class="text-xs text-blue-600 mt-1">Todos los contratos</p>
                                </div>

                                <div class="bg-purple-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-purple-600 uppercase">Valor</span>
                                        <i class="fas fa-dollar-sign text-purple-600"></i>
                                    </div>
                                    <p class="text-lg font-bold text-purple-700">${{ number_format($estadisticas['valor_contratos_activos'] / 1000000, 1) }}M</p>
                                    <p class="text-xs text-purple-600 mt-1">Millones</p>
                                </div>
                            </div>

                            <!-- Gráfico Placeholder -->
                            <div class="h-48 flex items-center justify-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <div class="text-center">
                                    <i class="fas fa-chart-bar text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-secondary">Gráfico de distribución</p>
                                    <p class="text-xs text-gray-400 mt-1">Implementar con Chart.js</p>
                                </div>
                            </div>
                        @else
                            <div class="h-64 flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-file-contract text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-secondary font-medium">No hay contratos registrados</p>
                                    <p class="text-sm text-gray-400 mt-2">Comienza creando tu primer contrato</p>
                                    <a href="{{ route('contratos.create', ['organizacion_id' => $organizacion->id]) }}" 
                                       class="inline-flex items-center mt-4 text-primary hover:text-primary-dark font-medium">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        Nuevo Contrato
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-rocket text-accent mr-2"></i>
                            Acciones Rápidas
                        </h2>

                        <div class="space-y-3">
                            <a href="{{ route('contratos.create', ['organizacion_id' => $organizacion->id]) }}" 
                               class="w-full bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-4 rounded-lg hover:shadow-lg transition-all flex items-center justify-center group">
                                <i class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
                                Nuevo Contrato
                            </a>

                            <a href="{{ route('usuarios.pendientes', ['organizacion_id' => $organizacion->id]) }}" 
                               class="w-full border-2 border-warning text-warning font-semibold py-3 px-4 rounded-lg hover:bg-warning hover:text-white transition-all flex items-center justify-center relative">
                                <i class="fas fa-user-clock mr-2"></i>
                                Usuarios Pendientes
                                @if($estadisticas['usuarios_pendientes'] > 0)
                                    <span class="absolute -top-2 -right-2 bg-danger text-white text-xs px-2 py-1 rounded-full font-bold">
                                        {{ $estadisticas['usuarios_pendientes'] }}
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('usuarios.index', ['organizacion_id' => $organizacion->id]) }}" 
                               class="w-full border-2 border-accent text-accent font-semibold py-3 px-4 rounded-lg hover:bg-accent hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-users mr-2"></i>
                                Gestionar Usuarios
                            </a>

                            <a href="{{ route('contratos.index') }}" 
                               class="w-full border-2 border-primary text-primary font-semibold py-3 px-4 rounded-lg hover:bg-primary hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-list mr-2"></i>
                                Ver Contratos
                            </a>

                            <button class="w-full border-2 border-secondary text-secondary font-semibold py-3 px-4 rounded-lg hover:bg-secondary hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-download mr-2"></i>
                                Exportar Reporte
                            </button>
                        </div>

                        <!-- Info de Organización -->
                        <div class="mt-6 bg-gradient-to-r from-primary/10 to-accent/10 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-building text-primary text-lg mr-3 mt-1"></i>
                                <div class="text-xs">
                                    <p class="font-semibold text-gray-800 mb-1">{{ $organizacion->nombre_oficial }}</p>
                                    <p class="text-secondary">NIT: {{ $organizacion->nit }}</p>
                                    <p class="text-secondary mt-1">
                                        <i class="fas fa-key mr-1"></i>
                                        Código: <code class="bg-white px-1 rounded">{{ $organizacion->codigo_vinculacion }}</code>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contratos Recientes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-contract text-primary mr-2"></i>
                            Contratos Recientes
                        </h2>
                        <a href="{{ route('contratos.index') }}" 
                           class="text-sm text-accent hover:text-primary font-medium flex items-center group">
                            Ver todos
                            <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Número</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Contratista</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Supervisor</th>
                                    <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Valor</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Estado</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Vigencia</th>
                                    <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($contratosRecientes as $contrato)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <span class="font-mono font-semibold text-gray-800">{{ $contrato->numero_contrato }}</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($contrato->contratista)
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold mr-2">
                                                    {{ substr($contrato->contratista->nombre, 0, 1) }}
                                                </div>
                                                <span class="text-sm text-gray-800">{{ $contrato->contratista->nombre }}</span>
                                            </div>
                                        @else
                                            <span class="text-sm text-secondary italic">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-sm text-gray-800">
                                            {{ $contrato->supervisor ? $contrato->supervisor->nombre : 'Sin asignar' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right font-semibold text-gray-800">
                                        ${{ number_format($contrato->valor_total, 0) }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($contrato->estado == 'activo')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Activo
                                            </span>
                                        @elseif($contrato->estado == 'borrador')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                <i class="fas fa-file mr-1"></i>Borrador
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center text-xs text-secondary">
                                        {{ $contrato->fecha_fin->format('d/m/Y') }}
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <a href="{{ route('contratos.show', $contrato) }}" 
                                           class="text-accent hover:text-primary transition-colors p-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <i class="fas fa-file-contract text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-secondary font-medium">No hay contratos registrados</p>
                                        <p class="text-sm text-gray-400 mt-2">Crea tu primer contrato</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Información y Actividad -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Usuarios Recientes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-users text-accent mr-2"></i>
                            Equipo de Trabajo
                        </h2>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie text-primary text-lg mr-3"></i>
                                    <span class="text-sm font-medium text-gray-800">Administradores</span>
                                </div>
                                <span class="text-sm font-bold text-gray-800">1</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-user-check text-green-600 text-lg mr-3"></i>
                                    <span class="text-sm font-medium text-gray-800">Supervisores</span>
                                </div>
                                <span class="text-sm font-bold text-gray-800">0</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-user-tag text-blue-600 text-lg mr-3"></i>
                                    <span class="text-sm font-medium text-gray-800">Contratistas</span>
                                </div>
                                <span class="text-sm font-bold text-gray-800">0</span>
                            </div>

                            <a href="{{ route('usuarios.index', ['organizacion_id' => $organizacion->id]) }}" 
                               class="block text-center text-sm text-accent hover:text-primary font-medium mt-4">
                                Ver todos los usuarios →
                            </a>
                        </div>
                    </div>

                    <!-- Tips y Ayuda -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-lightbulb text-warning mr-2"></i>
                            Primeros Pasos
                        </h2>
                        <div class="space-y-3">
                            <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                                <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                    1
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Gestiona usuarios</p>
                                    <p class="text-xs text-secondary mt-1">Asigna roles a los usuarios pendientes</p>
                                </div>
                            </div>

                            <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                                <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                    2
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Crea contratos</p>
                                    <p class="text-xs text-secondary mt-1">Registra los contratos de la organización</p>
                                </div>
                            </div>

                            <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                                <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                    3
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Vincula contratistas</p>
                                    <p class="text-xs text-secondary mt-1">Asigna contratistas a los contratos</p>
                                </div>
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
    // Animación de entrada
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