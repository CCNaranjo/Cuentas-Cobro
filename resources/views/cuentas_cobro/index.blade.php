@extends('layouts.app-dashboard')

@section('title', 'Cuentas de Cobro - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Header Section -->
                <div class="mb-6 animate-slideIn">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-invoice-dollar text-primary mr-3"></i>
                                Cuentas de Cobro
                            </h1>
                            <p class="text-secondary mt-1">
                                Gestión y control de cuentas de cobro de contratos
                            </p>
                        </div>
                        <a href="{{ route('cuentas-cobro.create') }}" 
                           class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center group">
                            <i class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
                            Nueva Cuenta de Cobro
                        </a>
                    </div>
                </div>

                <!-- Alertas -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 animate-slideIn">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <p class="text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 animate-slideIn">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                            <p class="text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- KPIs Resumen -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Cuentas -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-invoice text-primary text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                Total
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Total Cuentas</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentasCobro->total() }}</p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-list text-primary mr-1"></i>
                            <span>Registradas en el sistema</span>
                        </div>
                    </div>

                    <!-- Borradores -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file text-gray-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                                Borrador
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Borradores</h3>
                        <p class="text-3xl font-bold text-gray-600">
                            {{ $cuentasCobro->where('estado', 'borrador')->count() }}
                        </p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-edit text-gray-600 mr-1"></i>
                            <span>Pendientes de radicar</span>
                        </div>
                    </div>

                    <!-- En Revisión -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-warning text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-orange-600 bg-orange-100 px-2 py-1 rounded-full">
                                Revisión
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">En Revisión</h3>
                        <p class="text-3xl font-bold text-warning">
                            {{ $cuentasCobro->whereIn('estado', ['radicada', 'en_revision'])->count() }}
                        </p>
                        <div class="mt-3 flex items-center text-xs text-secondary">
                            <i class="fas fa-search text-warning mr-1"></i>
                            <span>Esperando aprobación</span>
                        </div>
                    </div>

                    <!-- Aprobadas/Pagadas -->
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 hover-lift cursor-pointer text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-double text-white text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">
                                Finalizadas
                            </span>
                        </div>
                        <h3 class="text-white/80 text-sm font-medium mb-1">Aprobadas</h3>
                        <p class="text-3xl font-bold">
                            {{ $cuentasCobro->whereIn('estado', ['aprobada', 'pagada'])->count() }}
                        </p>
                        <div class="mt-3 flex items-center text-xs text-white/80">
                            <i class="fas fa-check-circle mr-1"></i>
                            <span>Procesadas exitosamente</span>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Tabla -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Formulario de Filtros -->
                    <form method="GET" action="{{ route('cuentas-cobro.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                                <input type="text" 
                                       name="buscar" 
                                       value="{{ request('buscar') }}" 
                                       placeholder="Número o período..."
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select name="estado" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Todos los estados</option>
                                    <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                    <option value="radicada" {{ request('estado') == 'radicada' ? 'selected' : '' }}>Radicada</option>
                                    <option value="en_revision" {{ request('estado') == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                    <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                    <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                    <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                                <input type="date" 
                                       name="fecha_inicio" 
                                       value="{{ request('fecha_inicio') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                                <input type="date" 
                                       name="fecha_fin" 
                                       value="{{ request('fecha_fin') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-accent to-accent text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center justify-center">
                                    <i class="fas fa-search mr-2"></i>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de Cuentas de Cobro -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-secondary">Número</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-secondary">Contrato</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-secondary">Fecha Radicación</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-secondary">Período</th>
                                    <th class="text-right py-4 px-4 text-sm font-semibold text-secondary">Valor Bruto</th>
                                    <th class="text-right py-4 px-4 text-sm font-semibold text-secondary">Valor Neto</th>
                                    <th class="text-center py-4 px-4 text-sm font-semibold text-secondary">Estado</th>
                                    <th class="text-right py-4 px-4 text-sm font-semibold text-secondary">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($cuentasCobro as $cuenta)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <span class="font-mono font-semibold text-primary">
                                            {{ $cuenta->numero_cuenta_cobro }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <p class="font-medium text-gray-800">
                                                {{ $cuenta->contrato->numero_contrato ?? 'N/A' }}
                                            </p>
                                            @if($cuenta->contrato && $cuenta->contrato->contratista)
                                                <p class="text-xs text-secondary">
                                                    {{ $cuenta->contrato->contratista->nombre }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-gray-700">
                                        {{ $cuenta->fecha_radicacion->format('d/m/Y') }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-700">
                                        {{ $cuenta->periodo_cobrado ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4 text-right font-semibold text-gray-800">
                                        ${{ number_format($cuenta->valor_bruto, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-4 text-right font-semibold text-green-600">
                                        ${{ number_format($cuenta->valor_neto, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @php
                                            $badgeColors = [
                                                'borrador' => 'bg-gray-100 text-gray-800',
                                                'radicada' => 'bg-blue-100 text-blue-800',
                                                'en_revision' => 'bg-warning/10 text-warning',
                                                'aprobada' => 'bg-green-100 text-green-800',
                                                'rechazada' => 'bg-red-100 text-red-800',
                                                'pagada' => 'bg-primary/10 text-primary',
                                                'anulada' => 'bg-gray-200 text-gray-600',
                                            ];
                                            $badgeIcons = [
                                                'borrador' => 'fa-file',
                                                'radicada' => 'fa-paper-plane',
                                                'en_revision' => 'fa-clock',
                                                'aprobada' => 'fa-check-circle',
                                                'rechazada' => 'fa-times-circle',
                                                'pagada' => 'fa-money-check',
                                                'anulada' => 'fa-ban',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColors[$cuenta->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas {{ $badgeIcons[$cuenta->estado] ?? 'fa-circle' }} mr-1"></i>
                                            {{ $cuenta->estado_nombre }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('cuentas-cobro.show', $cuenta->id) }}" 
                                               class="text-accent hover:text-primary transition-colors p-2"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($cuenta->estado === 'borrador')
                                                <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" 
                                                   class="text-primary hover:text-primary-dark transition-colors p-2"
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('cuentas-cobro.destroy', $cuenta->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Está seguro de eliminar esta cuenta de cobro?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 transition-colors p-2"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-file-invoice text-5xl text-gray-400"></i>
                                            </div>
                                            <p class="text-secondary font-medium text-lg mb-2">No hay cuentas de cobro registradas</p>
                                            <p class="text-sm text-gray-400 mb-4">Comienza creando tu primera cuenta de cobro</p>
                                            <a href="{{ route('cuentas-cobro.create') }}" 
                                               class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all inline-flex items-center">
                                                <i class="fas fa-plus-circle mr-2"></i>
                                                Nueva Cuenta de Cobro
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($cuentasCobro->hasPages())
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-secondary">
                                Mostrando {{ $cuentasCobro->firstItem() }} - {{ $cuentasCobro->lastItem() }} de {{ $cuentasCobro->total() }} resultados
                            </div>
                            <div>
                                {{ $cuentasCobro->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    // Animación de entrada para las tarjetas KPI
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.hover-lift');
        cards.forEach((card, index) => {
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

<style>
    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .animate-slideIn {
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection