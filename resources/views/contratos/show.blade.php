@extends('layouts.app-dashboard')

@section('title', 'Detalle del Contrato')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Breadcrumb y Header -->
                <div class="flex justify-between items-center mb-6 animate-slideIn">
                    <div>
                        <nav class="flex items-center space-x-2 text-sm text-secondary mb-2">
                            <a href="{{ route('dashboard') }}" class="hover:text-primary">Dashboard</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <a href="{{ route('contratos.index') }}" class="hover:text-primary">Contratos</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <span class="text-gray-800">{{ $contrato->numero_contrato }}</span>
                        </nav>
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-contract text-primary mr-3"></i>
                            Contrato {{ $contrato->numero_contrato }}
                        </h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($contrato->estado == 'borrador' && Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                            <a href="{{ route('contratos.edit', $contrato) }}" 
                               class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Editar
                            </a>
                        @endif
                        <button onclick="window.print()" 
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center">
                            <i class="fas fa-print mr-2"></i>
                            Imprimir
                        </button>
                    </div>
                </div>

                <!-- Badge de Estado -->
                <div class="mb-6 animate-fadeIn">
                    @php
                        $estadoStyles = [
                            'borrador' => 'bg-gray-100 text-gray-800',
                            'activo' => 'bg-green-100 text-green-800',
                            'suspendido' => 'bg-yellow-100 text-yellow-800',
                            'terminado' => 'bg-blue-100 text-blue-800',
                            'liquidado' => 'bg-purple-100 text-purple-800'
                        ];
                        $estadoTextos = [
                            'borrador' => 'Borrador',
                            'activo' => 'Activo',
                            'suspendido' => 'Suspendido',
                            'terminado' => 'Terminado',
                            'liquidado' => 'Liquidado'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $estadoStyles[$contrato->estado] ?? 'bg-gray-100 text-gray-800' }}">
                        <i class="fas fa-circle mr-2" style="font-size: 0.5rem;"></i>
                        {{ $estadoTextos[$contrato->estado] ?? ucfirst($contrato->estado) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna Principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Información General -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-accent mr-2"></i>
                                Información General
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Número de Contrato</label>
                                    <p class="text-gray-800 font-semibold">{{ $contrato->numero_contrato }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Organización</label>
                                    <p class="text-gray-800 font-semibold">{{ $contrato->organizacion->nombre }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm text-secondary mb-1">Objeto Contractual</label>
                                    <p class="text-gray-800 leading-relaxed">{{ $contrato->objeto_contractual }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información Financiera ACTUALIZADA -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-dollar-sign text-accent mr-2"></i>
                                Información Financiera
                            </h2>
                            
                            <!-- Tarjetas de Valores ACTUALIZADAS -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gradient-to-br from-primary to-primary-dark text-white p-4 rounded-lg text-center shadow-lg transform hover:scale-105 transition-transform">
                                    <div class="text-sm opacity-90 mb-1">Valor Total</div>
                                    <div class="text-2xl font-bold">${{ number_format($contrato->valor_total, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-accent to-blue-400 text-white p-4 rounded-lg text-center shadow-lg transform hover:scale-105 transition-transform">
                                    <div class="text-sm opacity-90 mb-1">Valor Pagado</div>
                                    <div class="text-2xl font-bold">${{ number_format($estadisticas['valor_pagado'], 0, ',', '.') }}</div>
                                    <div class="text-xs opacity-75 mt-1">
                                        {{ $estadisticas['cuentas_cobro_pagadas'] }} cuentas pagadas
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg text-center shadow-lg transform hover:scale-105 transition-transform">
                                    <div class="text-sm opacity-90 mb-1">Saldo Disponible</div>
                                    <div class="text-2xl font-bold">${{ number_format($estadisticas['saldo_disponible'], 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Barra de Progreso ACTUALIZADA -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-sm font-semibold text-gray-700">Ejecución Financiera</label>
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-accent text-white text-xs px-3 py-1 rounded-full font-bold">
                                            {{ number_format($estadisticas['porcentaje_ejecucion'], 1) }}%
                                        </span>
                                        @if($estadisticas['porcentaje_ejecucion'] >= 90)
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-semibold">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Cerca del límite
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4 shadow-inner">
                                    @php
                                        $porcentaje = min($estadisticas['porcentaje_ejecucion'], 100);
                                        $colorClass = $porcentaje < 50 ? 'from-red-400 to-orange-500' : 
                                                     ($porcentaje < 80 ? 'from-yellow-400 to-orange-500' : 
                                                      ($porcentaje < 90 ? 'from-accent to-primary' : 'from-red-500 to-red-600'));
                                    @endphp
                                    <div class="bg-gradient-to-r {{ $colorClass }} h-4 rounded-full transition-all duration-500 shadow-md relative overflow-hidden" 
                                         style="width: {{ $porcentaje }}%">
                                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-2 text-xs text-gray-600">
                                    <span>Inicio</span>
                                    <span class="font-semibold">
                                        ${{ number_format($estadisticas['valor_pagado'], 0, ',', '.') }} de ${{ number_format($contrato->valor_total, 0, ',', '.') }}
                                    </span>
                                    <span>Total</span>
                                </div>
                            </div>

                            <!-- Estadísticas de Cuentas de Cobro -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                                    <div class="flex justify-between items-center">
                                        <span class="text-secondary font-medium">Cuentas Pagadas</span>
                                        <span class="font-bold text-blue-600 text-xl">{{ $estadisticas['cuentas_cobro_pagadas'] }}</span>
                                    </div>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                                    <div class="flex justify-between items-center">
                                        <span class="text-secondary font-medium">Cuentas Pendientes</span>
                                        <span class="font-bold text-yellow-600 text-xl">{{ $estadisticas['cuentas_cobro_pendientes'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Retenciones -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-warning">
                                    <div class="flex justify-between items-center">
                                        <span class="text-secondary">Retención en la Fuente</span>
                                        <span class="font-semibold text-warning">{{ $contrato->porcentaje_retencion_fuente }}%</span>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-danger">
                                    <div class="flex justify-between items-center">
                                        <span class="text-secondary">Estampilla</span>
                                        <span class="font-semibold text-danger">{{ $contrato->porcentaje_estampilla }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resto del contenido igual... (Personas Involucradas, Archivos) -->
                        <!-- [Mantener el código original de personas y archivos] -->
                    </div>

                    <!-- Columna Lateral -->
                    <div class="space-y-6">
                        <!-- Fechas del Contrato -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-accent mr-2"></i>
                                Vigencia
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Fecha de Inicio</label>
                                    <div class="flex items-center text-gray-800 font-semibold">
                                        <i class="fas fa-calendar-check text-accent mr-2"></i>
                                        {{ $contrato->fecha_inicio->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Fecha de Fin</label>
                                    <div class="flex items-center text-gray-800 font-semibold">
                                        <i class="fas fa-calendar-times text-warning mr-2"></i>
                                        {{ $contrato->fecha_fin->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <label class="block text-sm text-secondary mb-1">Duración</label>
                                    <div class="flex items-center text-gray-800 font-semibold">
                                        <i class="fas fa-hourglass-half text-primary mr-2"></i>
                                        @php
                                            $dias = $contrato->fecha_inicio->diffInDays($contrato->fecha_fin);
                                            $meses = floor($dias / 30);
                                            $diasRestantes = $dias % 30;
                                        @endphp
                                        {{ $meses > 0 ? $meses . ' ' . ($meses == 1 ? 'mes' : 'meses') : '' }}
                                        {{ $diasRestantes > 0 ? $diasRestantes . ' ' . ($diasRestantes == 1 ? 'día' : 'días') : '' }}
                                    </div>
                                </div>

                                @php
                                    $diasRestantesVigencia = $contrato->dias_restantes;
                                @endphp

                                @if($contrato->estado == 'activo' && $diasRestantesVigencia > 0)
                                    <div class="mt-4 p-3 bg-warning/10 border border-warning/20 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                            <div>
                                                <div class="font-semibold text-warning">{{ $diasRestantesVigencia }} días restantes</div>
                                                <div class="text-sm text-secondary">Hasta la finalización del contrato</div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($contrato->estado == 'activo' && $diasRestantesVigencia <= 0)
                                    <div class="mt-4 p-3 bg-danger/10 border border-danger/20 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-times-circle text-danger mr-2"></i>
                                            <div>
                                                <div class="font-semibold text-danger">Contrato vencido</div>
                                                <div class="text-sm text-secondary">Hace {{ abs($diasRestantesVigencia) }} días</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Acciones Rápidas -->
                        @if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-bolt text-accent mr-2"></i>
                                Acciones Rápidas
                            </h2>
                            <div class="space-y-2">
                                @if($contrato->estado == 'activo')
                                    <button class="w-full text-left p-3 border border-warning text-warning rounded-lg hover:bg-warning/5 transition-colors flex items-center"
                                            onclick="cambiarEstado('suspendido')">
                                        <i class="fas fa-pause-circle mr-2"></i>
                                        Suspender Contrato
                                    </button>
                                    <button class="w-full text-left p-3 border border-accent text-accent rounded-lg hover:bg-accent/5 transition-colors flex items-center"
                                            onclick="cambiarEstado('terminado')">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Terminar Contrato
                                    </button>
                                @elseif($contrato->estado == 'suspendido')
                                    <button class="w-full text-left p-3 border border-green-500 text-green-500 rounded-lg hover:bg-green-50 transition-colors flex items-center"
                                            onclick="cambiarEstado('activo')">
                                        <i class="fas fa-play-circle mr-2"></i>
                                        Reactivar Contrato
                                    </button>
                                @elseif($contrato->estado == 'terminado')
                                    <button class="w-full text-left p-3 border border-gray-700 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center"
                                            onclick="cambiarEstado('liquidado')">
                                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                                        Liquidar Contrato
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Información de Auditoría -->
                        <div class="bg-gray-50 rounded-xl p-6 animate-fadeIn">
                            <h3 class="text-sm font-semibold text-gray-600 mb-3 flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                Información de Registro
                            </h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div>
                                    <strong>Creado:</strong> {{ $contrato->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <strong>Actualizado:</strong> {{ $contrato->updated_at->format('d/m/Y H:i') }}
                                </div>
                                @if($contrato->vinculadoPor)
                                    <div>
                                        <strong>Vinculado por:</strong> {{ $contrato->vinculadoPor->nombre }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('styles')
<style>
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

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .animate-slideIn {
        animation: slideIn 0.5s ease-out;
    }

    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function cambiarEstado(estado) {
        if (confirm(`¿Estás seguro de cambiar el estado del contrato a "${estado}"?`)) {
            // Lógica para cambiar estado
            console.log('Cambiar estado a:', estado);
        }
    }
</script>
@endpush
@endsection