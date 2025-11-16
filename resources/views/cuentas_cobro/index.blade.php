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
                                @php
                                    $user = auth()->user();
                                    $organizacionId = session('organizacion_actual');
                                    $rol = $user->roles()
                                        ->wherePivot('organizacion_id', $organizacionId)
                                        ->wherePivot('estado', 'activo')
                                        ->first();
                                @endphp

                                @if($rol)
                                    @if($rol->nombre === 'contratista')
                                        Gestión de mis cuentas de cobro
                                    @elseif($rol->nombre === 'supervisor')
                                        Cuentas pendientes de certificación
                                    @elseif($rol->nombre === 'revisor_contratacion')
                                        Cuentas pendientes de verificación legal
                                    @elseif($rol->nombre === 'tesorero')
                                        Gestión de presupuesto y pagos
                                    @elseif($rol->nombre === 'ordenador_gasto')
                                        Cuentas pendientes de aprobación final
                                    @else
                                        Gestión y control de cuentas de cobro
                                    @endif
                                @else
                                    Gestión y control de cuentas de cobro
                                @endif
                            </p>
                        </div>
                        
                        @if($user->tienePermiso('crear-cuenta-cobro', $organizacionId))
                        <a href="{{ route('cuentas-cobro.create') }}" 
                           class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center group">
                            <i class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
                            Nueva Cuenta de Cobro
                        </a>
                        @endif
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
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
                    </div>

                    <!-- Borradores -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer"
                         onclick="filtrarPorEstado('borrador')">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file text-gray-600 text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Borradores</h3>
                        <p class="text-3xl font-bold text-gray-600">
                            {{ $cuentasCobro->where('estado', 'borrador')->count() }}
                        </p>
                    </div>

                    <!-- Pendientes (Estados intermedios) -->
                    @php
                        $estadosPendientes = ['radicada', 'certificado_supervisor', 'verificado_contratacion', 'verificado_presupuesto'];
                        $cuentasPendientes = $cuentasCobro->whereIn('estado', $estadosPendientes)->count();
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-warning text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">En Proceso</h3>
                        <p class="text-3xl font-bold text-warning">{{ $cuentasPendientes }}</p>
                    </div>

                    <!-- Correcciones -->
                    @php
                        $estadosCorreccion = ['en_correccion_supervisor', 'en_correccion_contratacion'];
                        $cuentasCorreccion = $cuentasCobro->whereIn('estado', $estadosCorreccion)->count();
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Correcciones</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $cuentasCorreccion }}</p>
                    </div>

                    <!-- Aprobadas/Pagadas -->
                    @php
                        $estadosFinales = ['aprobada_ordenador', 'en_proceso_pago', 'pagada'];
                        $cuentasFinales = $cuentasCobro->whereIn('estado', $estadosFinales)->count();
                    @endphp
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 hover-lift cursor-pointer text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-double text-white text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-white/80 text-sm font-medium mb-1">Aprobadas/Pagadas</h3>
                        <p class="text-3xl font-bold">{{ $cuentasFinales }}</p>
                    </div>
                </div>

                <!-- Filtros y Tabla -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Formulario de Filtros -->
                    <form method="GET" action="{{ route('cuentas-cobro.index') }}" class="mb-6" id="formFiltros">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div class="md:col-span-2">
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
                                        id="selectEstado"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Todos los estados</option>
                                    <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                    <option value="radicada" {{ request('estado') == 'radicada' ? 'selected' : '' }}>Radicada</option>
                                    <option value="en_correccion_supervisor" {{ request('estado') == 'en_correccion_supervisor' ? 'selected' : '' }}>En Corrección (Supervisor)</option>
                                    <option value="certificado_supervisor" {{ request('estado') == 'certificado_supervisor' ? 'selected' : '' }}>Certificado Supervisor</option>
                                    <option value="en_correccion_contratacion" {{ request('estado') == 'en_correccion_contratacion' ? 'selected' : '' }}>En Corrección (Legal)</option>
                                    <option value="verificado_contratacion" {{ request('estado') == 'verificado_contratacion' ? 'selected' : '' }}>Verificado Legal</option>
                                    <option value="verificado_presupuesto" {{ request('estado') == 'verificado_presupuesto' ? 'selected' : '' }}>Verificado Presupuesto</option>
                                    <option value="aprobada_ordenador" {{ request('estado') == 'aprobada_ordenador' ? 'selected' : '' }}>Aprobada</option>
                                    <option value="en_proceso_pago" {{ request('estado') == 'en_proceso_pago' ? 'selected' : '' }}>En Proceso de Pago</option>
                                    <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                                    <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
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

                        @if(request()->hasAny(['buscar', 'estado', 'fecha_inicio', 'fecha_fin']))
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-filter mr-2"></i>
                                Filtros activos
                            </p>
                            <a href="{{ route('cuentas-cobro.index') }}" 
                               class="text-sm text-red-600 hover:text-red-800 font-semibold">
                                <i class="fas fa-times mr-1"></i>
                                Limpiar filtros
                            </a>
                        </div>
                        @endif
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
                                        {{ $cuenta->fecha_radicacion ? $cuenta->fecha_radicacion->format('d/m/Y') : 'No radicada' }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-700">
                                        <span class="text-xs">
                                            {{ $cuenta->periodo_inicio->format('d/m/Y') }} - {{ $cuenta->periodo_fin->format('d/m/Y') }}
                                        </span>
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
                                                'en_correccion_supervisor' => 'bg-orange-100 text-orange-800',
                                                'certificado_supervisor' => 'bg-cyan-100 text-cyan-800',
                                                'en_correccion_contratacion' => 'bg-yellow-100 text-yellow-800',
                                                'verificado_contratacion' => 'bg-teal-100 text-teal-800',
                                                'verificado_presupuesto' => 'bg-indigo-100 text-indigo-800',
                                                'aprobada_ordenador' => 'bg-green-100 text-green-800',
                                                'en_proceso_pago' => 'bg-purple-100 text-purple-800',
                                                'pagada' => 'bg-green-600 text-white',
                                                'anulada' => 'bg-red-100 text-red-800',
                                            ];
                                            $badgeIcons = [
                                                'borrador' => 'fa-file',
                                                'radicada' => 'fa-paper-plane',
                                                'en_correccion_supervisor' => 'fa-exclamation-triangle',
                                                'certificado_supervisor' => 'fa-check-circle',
                                                'en_correccion_contratacion' => 'fa-exclamation-triangle',
                                                'verificado_contratacion' => 'fa-stamp',
                                                'verificado_presupuesto' => 'fa-wallet',
                                                'aprobada_ordenador' => 'fa-check-double',
                                                'en_proceso_pago' => 'fa-money-check-alt',
                                                'pagada' => 'fa-check-circle',
                                                'anulada' => 'fa-ban',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColors[$cuenta->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas {{ $badgeIcons[$cuenta->estado] ?? 'fa-circle' }} mr-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $cuenta->estado)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            {{-- VER DETALLE - Siempre visible --}}
                                            <a href="{{ route('cuentas-cobro.show', $cuenta->id) }}" 
                                               class="text-accent hover:text-primary transition-colors p-2"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            {{-- EDITAR - Solo en borrador y con permiso --}}
                                            @if(($cuenta->estado === 'borrador' || $cuenta->estado === 'en_correccion_supervisor' || $cuenta->estado === 'en_correccion_contratacion') && $user->tienePermiso('editar-cuenta-cobro', $organizacionId))
                                                <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" 
                                                   class="text-primary hover:text-primary-dark transition-colors p-2"
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                {{-- ELIMINAR - Solo en borrador --}}
                                                <form action="{{ route('cuentas-cobro.destroy', $cuenta->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Está seguro de eliminar esta cuenta de cobro?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    @if ($cuenta->estado === 'borrador')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 transition-colors p-2"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endif
                                                </form>
                                            @endif

                                            {{-- INDICADOR DE ACCIÓN DISPONIBLE --}}
                                            @php
                                                $accionesDisponibles = [
                                                    'borrador' => ['permiso' => 'radicar-cuenta-cobro', 'icono' => 'fa-paper-plane', 'color' => 'text-blue-600', 'tooltip' => 'Puede radicar'],
                                                    'radicada' => ['permiso' => 'revisar-cuenta-cobro', 'icono' => 'fa-check-circle', 'color' => 'text-cyan-600', 'tooltip' => 'Puede certificar'],
                                                    'certificado_supervisor' => ['permiso' => 'verificar-legal-cuenta-cobro', 'icono' => 'fa-stamp', 'color' => 'text-teal-600', 'tooltip' => 'Puede verificar'],
                                                    'verificado_contratacion' => ['permiso' => 'verificar-presupuesto-cuenta-cobro', 'icono' => 'fa-wallet', 'color' => 'text-indigo-600', 'tooltip' => 'Puede verificar presupuesto'],
                                                    'verificado_presupuesto' => ['permiso' => 'aprobar-finalmente', 'icono' => 'fa-check-double', 'color' => 'text-green-600', 'tooltip' => 'Puede aprobar'],
                                                    'aprobada_ordenador' => ['permiso' => 'generar-ordenes-pago', 'icono' => 'fa-file-invoice-dollar', 'color' => 'text-purple-600', 'tooltip' => 'Puede generar O.P.'],
                                                    'en_proceso_pago' => ['permiso' => 'procesar-pago', 'icono' => 'fa-money-check-alt', 'color' => 'text-green-600', 'tooltip' => 'Puede confirmar pago'],
                                                ];

                                                $accion = $accionesDisponibles[$cuenta->estado] ?? null;
                                            @endphp

                                            @if($accion && $user->tienePermiso($accion['permiso'], $organizacionId))
                                                <span class="{{ $accion['color'] }} p-2" title="{{ $accion['tooltip'] }}">
                                                    <i class="fas {{ $accion['icono'] }} animate-pulse"></i>
                                                </span>
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
                                            <p class="text-secondary font-medium text-lg mb-2">
                                                @if(request()->hasAny(['buscar', 'estado', 'fecha_inicio', 'fecha_fin']))
                                                    No se encontraron cuentas con los filtros aplicados
                                                @else
                                                    No hay cuentas de cobro registradas
                                                @endif
                                            </p>
                                            <p class="text-sm text-gray-400 mb-4">
                                                @if(request()->hasAny(['buscar', 'estado', 'fecha_inicio', 'fecha_fin']))
                                                    Intenta ajustar los filtros de búsqueda
                                                @else
                                                    Comienza creando tu primera cuenta de cobro
                                                @endif
                                            </p>
                                            @if($user->tienePermiso('crear-cuenta-cobro', $organizacionId) && !request()->hasAny(['buscar', 'estado', 'fecha_inicio', 'fecha_fin']))
                                            <a href="{{ route('cuentas-cobro.create') }}" 
                                               class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all inline-flex items-center">
                                                <i class="fas fa-plus-circle mr-2"></i>
                                                Nueva Cuenta de Cobro
                                            </a>
                                            @endif
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

    // Función para filtrar por estado desde los KPIs
    function filtrarPorEstado(estado) {
        document.getElementById('selectEstado').value = estado;
        document.getElementById('formFiltros').submit();
    }
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

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection