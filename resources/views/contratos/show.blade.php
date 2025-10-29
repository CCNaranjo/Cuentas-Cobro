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
                <div class="flex justify-between items-center mb-6">
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
                <div class="mb-6">
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
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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

                        <!-- Información Financiera -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-dollar-sign text-accent mr-2"></i>
                                Información Financiera
                            </h2>
                            
                            <!-- Tarjetas de Valores -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gradient-to-br from-primary to-primary-dark text-white p-4 rounded-lg text-center">
                                    <div class="text-sm opacity-90 mb-1">Valor Total</div>
                                    <div class="text-2xl font-bold">${{ number_format($contrato->valor_total, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-accent to-blue-400 text-white p-4 rounded-lg text-center">
                                    <div class="text-sm opacity-90 mb-1">Valor Cobrado</div>
                                    <div class="text-2xl font-bold">${{ number_format($estadisticas['valor_cobrado'], 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg text-center">
                                    <div class="text-sm opacity-90 mb-1">Valor Disponible</div>
                                    <div class="text-2xl font-bold">${{ number_format($estadisticas['valor_disponible'], 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Barra de Progreso -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-sm text-secondary">Ejecución Financiera</label>
                                    <span class="bg-accent text-white text-xs px-2 py-1 rounded-full">
                                        {{ number_format($estadisticas['porcentaje_ejecucion'], 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-accent to-primary h-3 rounded-full" 
                                         style="width: {{ $estadisticas['porcentaje_ejecucion'] }}%"></div>
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

                        <!-- Personas Involucradas -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-users text-accent mr-2"></i>
                                Personas Involucradas
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Contratista -->
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm text-secondary mb-1">Contratista</div>
                                        @if($contrato->contratista)
                                            <div class="font-semibold text-gray-800 mb-1">{{ $contrato->contratista->nombre }}</div>
                                            <div class="text-sm text-secondary">{{ $contrato->contratista->email }}</div>
                                            <div class="text-sm text-secondary">{{ $contrato->contratista->documento_identidad }}</div>
                                        @else
                                            <div class="text-secondary italic">Sin asignar</div>
                                            @if(Auth::user()->tienePermiso('vincular-contratista', $contrato->organizacion_id))
                                                <button class="mt-2 text-primary hover:text-primary-dark text-sm font-medium flex items-center"
                                                        onclick="abrirModalVincular()">
                                                    <i class="fas fa-plus-circle mr-1"></i>
                                                    Vincular Contratista
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Supervisor -->
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-500 to-primary flex items-center justify-center text-white">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm text-secondary mb-1">Supervisor</div>
                                        @if($contrato->supervisor)
                                            <div class="font-semibold text-gray-800 mb-1">{{ $contrato->supervisor->nombre }}</div>
                                            <div class="text-sm text-secondary">{{ $contrato->supervisor->email }}</div>
                                            @if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                                                <button class="mt-2 text-secondary hover:text-gray-800 text-sm font-medium flex items-center"
                                                        onclick="abrirModalSupervisor()">
                                                    <i class="fas fa-sync-alt mr-1"></i>
                                                    Cambiar
                                                </button>
                                            @endif
                                        @else
                                            <div class="text-secondary italic">Sin asignar</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Lateral -->
                    <div class="space-y-6">
                        <!-- Fechas del Contrato -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
                                    $hoy = now();
                                    $diasRestantes = $hoy->diffInDays($contrato->fecha_fin, false);
                                @endphp

                                @if($contrato->estado == 'activo' && $diasRestantes >= 0)
                                    <div class="mt-4 p-3 bg-warning/10 border border-warning/20 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                            <div>
                                                <div class="font-semibold text-warning">{{ $diasRestantes }} días restantes</div>
                                                <div class="text-sm text-secondary">Hasta la finalización del contrato</div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($contrato->estado == 'activo' && $diasRestantes < 0)
                                    <div class="mt-4 p-3 bg-danger/10 border border-danger/20 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-times-circle text-danger mr-2"></i>
                                            <div>
                                                <div class="font-semibold text-danger">Contrato vencido</div>
                                                <div class="text-sm text-secondary">Hace {{ abs($diasRestantes) }} días</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Acciones Rápidas -->
                        @if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
                        <div class="bg-gray-50 rounded-xl p-6">
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

<!-- Aquí irían los modales (vincular contratista, cambiar supervisor, cambiar estado) -->
<!-- Mantén la misma estructura de modales que tenías pero adapta las clases a Tailwind -->

@endsection

@push('scripts')
<script>
    // Funciones para los modales (mantén las que ya tienes)
    function abrirModalVincular() {
        // Lógica para abrir modal de vincular contratista
        console.log('Abrir modal vincular contratista');
    }

    function abrirModalSupervisor() {
        // Lógica para abrir modal de cambiar supervisor
        console.log('Abrir modal cambiar supervisor');
    }

    function cambiarEstado(estado) {
        // Lógica para cambiar estado
        console.log('Cambiar estado a:', estado);
    }
</script>
@endpush