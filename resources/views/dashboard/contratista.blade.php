@extends('layouts.app-dashboard')

@section('title', 'Mi Dashboard - Contratista')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-semibold text-primary mb-1 flex items-center">
                    <i class="bi bi-briefcase mr-2"></i>Mi Panel de Contratista
                </h2>
                <p class="text-gray-500">Gestiona tus contratos y cuentas de cobro</p>
            </div>

            <!-- KPIs Estado Rápido de Pagos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total a Recibir -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md hover-lift overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="bi bi-wallet2 text-2xl"></i>
                            </div>
                            <i class="bi bi-arrow-up-circle text-2xl"></i>
                        </div>
                        <h6 class="text-sm opacity-75 mb-2">Total a Recibir</h6>
                        <h3 class="text-3xl font-bold mb-1">
                            ${{ number_format($estadisticas['total_a_recibir'], 0, ',', '.') }}
                        </h3>
                        <small class="opacity-75">Cuentas aprobadas</small>
                    </div>
                </div>

                <!-- Mis Contratos -->
                <div class="bg-white rounded-xl shadow-md hover-lift border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                                <i class="bi bi-file-earmark-text-fill text-2xl text-primary"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-500 mb-2">Mis Contratos</h6>
                        <h3 class="text-3xl font-bold text-primary mb-1">{{ $estadisticas['mis_contratos'] }}</h3>
                        <small class="text-gray-500">Activos</small>
                    </div>
                </div>

                <!-- Pagos del Mes -->
                <div class="bg-white rounded-xl shadow-md hover-lift border-l-4 border-accent">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center">
                                <i class="bi bi-cash-stack text-2xl text-accent"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-500 mb-2">Pagos este Mes</h6>
                        <h3 class="text-3xl font-bold text-accent mb-1">
                            ${{ number_format($estadisticas['pagos_recibidos_mes'], 0, ',', '.') }}
                        </h3>
                        <small class="text-gray-500">Recibidos</small>
                    </div>
                </div>

                <!-- Cuentas Pendientes -->
                <div class="bg-white rounded-xl shadow-md hover-lift border-l-4 border-warning">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center">
                                <i class="bi bi-clock-history text-2xl text-warning"></i>
                            </div>
                            @if($estadisticas['cuentas_devueltas'] > 0)
                            <span class="badge-danger">{{ $estadisticas['cuentas_devueltas'] }} devueltas</span>
                            @endif
                        </div>
                        <h6 class="text-sm text-gray-500 mb-2">En Revisión</h6>
                        <h3 class="text-3xl font-bold text-warning mb-1">{{ $estadisticas['cuentas_pendientes'] }}</h3>
                        <small class="text-gray-500">Cuentas pendientes</small>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna Principal (65%) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Próxima Acción Requerida -->
                    @if($contratosListosParaCobro->count() > 0)
                    <div class="bg-white rounded-xl shadow-md border-l-4 border-green-500">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-green-600 flex items-center">
                                <i class="bi bi-check-circle mr-2"></i>Próxima Acción: Crear Cuenta de Cobro
                            </h5>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-500 mb-4">Tienes {{ $contratosListosParaCobro->count() }} contrato(s) listo(s) para crear cuenta de cobro</p>
                            <div class="space-y-2">
                                @foreach($contratosListosParaCobro->take(3) as $contrato)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div>
                                        <h6 class="font-semibold text-gray-800 mb-1">{{ $contrato->numero_contrato }}</h6>
                                        <small class="text-gray-500">{{ $contrato->organizacion->nombre_oficial }}</small>
                                    </div>
                                    <button class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg opacity-50 cursor-not-allowed flex items-center" disabled title="Próximamente">
                                        <i class="bi bi-plus-circle mr-1"></i>Crear Cuenta
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Gráfico de Trazabilidad -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-primary flex items-center">
                                <i class="bi bi-graph-up mr-2"></i>Trazabilidad de Mis Cuentas
                            </h5>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <i class="bi bi-file-earmark text-5xl text-gray-400 mb-2"></i>
                                    <h4 class="text-2xl font-bold text-gray-800 mb-1">{{ $trazabilidadCuentas['borradores'] }}</h4>
                                    <small class="text-gray-500">Borradores</small>
                                </div>
                                <div class="p-4 bg-accent/10 rounded-lg">
                                    <i class="bi bi-send text-5xl text-accent mb-2"></i>
                                    <h4 class="text-2xl font-bold text-accent mb-1">{{ $trazabilidadCuentas['radicadas'] }}</h4>
                                    <small class="text-gray-500">Radicadas</small>
                                </div>
                                <div class="p-4 bg-warning/10 rounded-lg">
                                    <i class="bi bi-hourglass-split text-5xl text-warning mb-2"></i>
                                    <h4 class="text-2xl font-bold text-warning mb-1">{{ $trazabilidadCuentas['en_revision'] }}</h4>
                                    <small class="text-gray-500">En Revisión</small>
                                </div>
                                <div class="p-4 bg-green-50 rounded-lg">
                                    <i class="bi bi-check-circle text-5xl text-green-500 mb-2"></i>
                                    <h4 class="text-2xl font-bold text-green-500 mb-1">{{ $trazabilidadCuentas['aprobadas'] }}</h4>
                                    <small class="text-gray-500">Aprobadas</small>
                                </div>
                                <div class="p-4 bg-primary/10 rounded-lg">
                                    <i class="bi bi-cash-coin text-5xl text-primary mb-2"></i>
                                    <h4 class="text-2xl font-bold text-primary mb-1">{{ $trazabilidadCuentas['pagadas'] }}</h4>
                                    <small class="text-gray-500">Pagadas</small>
                                </div>
                                <div class="p-4 bg-red-50 rounded-lg">
                                    <i class="bi bi-x-circle text-5xl text-red-500 mb-2"></i>
                                    <h4 class="text-2xl font-bold text-red-500 mb-1">{{ $trazabilidadCuentas['rechazadas'] }}</h4>
                                    <small class="text-gray-500">Rechazadas</small>
                                </div>
                            </div>
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg flex items-start">
                                <i class="bi bi-info-circle text-blue-500 mr-2 mt-1"></i>
                                <div>
                                    <strong class="text-blue-700">Próximamente:</strong>
                                    <span class="text-blue-600"> Sistema completo de cuentas de cobro</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mis Contratos -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-primary flex items-center">
                                <i class="bi bi-file-earmark-text mr-2"></i>Mis Contratos
                            </h5>
                        </div>
                        <div class="overflow-hidden">
                            @if($misContratos->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organización</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($misContratos as $contrato)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('contratos.show', $contrato) }}" class="text-primary font-semibold hover:text-primary-dark">
                                                    {{ $contrato->numero_contrato }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">{{ $contrato->organizacion->nombre_oficial }}</td>
                                            <td class="px-6 py-4">{{ $contrato->supervisor->nombre ?? 'Sin asignar' }}</td>
                                            <td class="px-6 py-4 text-right font-semibold">${{ number_format($contrato->valor_total, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-center text-sm">
                                                {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }} - 
                                                {{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @php
                                                    $badges = [
                                                        'activo' => ['class' => 'status-active', 'text' => 'Activo'],
                                                        'borrador' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Borrador'],
                                                        'terminado' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Terminado']
                                                    ];
                                                    $badge = $badges[$contrato->estado] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($contrato->estado)];
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge['class'] }}">
                                                    {{ $badge['text'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('contratos.show', $contrato) }}" class="inline-flex items-center px-3 py-1 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-12">
                                <i class="bi bi-file-earmark-text text-gray-200" style="font-size: 4rem;"></i>
                                <p class="text-gray-500 mt-4">No tienes contratos asignados</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha (35%) -->
                <div class="space-y-6">
                    <!-- Botón Grande: Crear Cuenta de Cobro -->
                    <div class="bg-gradient-to-br from-primary to-accent rounded-xl shadow-md text-white text-center p-8">
                        <i class="bi bi-file-earmark-plus text-6xl opacity-20 mb-4"></i>
                        <h5 class="text-xl font-bold mb-2">Crear Cuenta de Cobro</h5>
                        <p class="text-sm opacity-75 mb-6">Radica tus cuentas de cobro</p>
                        <button class="w-full bg-white text-primary px-6 py-3 rounded-lg font-semibold opacity-50 cursor-not-allowed flex items-center justify-center" disabled>
                            <i class="bi bi-plus-circle mr-2"></i>Próximamente
                        </button>
                    </div>

                    <!-- Resumen Financiero -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h6 class="font-semibold text-primary flex items-center">
                                <i class="bi bi-wallet2 mr-2"></i>Resumen Financiero
                            </h6>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 pb-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Valor Total Contratos:</span>
                                    <span class="font-semibold text-gray-800">${{ number_format($estadisticas['valor_total_contratos'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">A Recibir:</span>
                                    <span class="font-semibold text-green-600">${{ number_format($estadisticas['total_a_recibir'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Pagado este Mes:</span>
                                    <span class="font-semibold text-primary">${{ number_format($estadisticas['pagos_recibidos_mes'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg flex items-start text-sm">
                                <i class="bi bi-info-circle text-blue-500 mr-2 mt-0.5"></i>
                                <span class="text-blue-600">Mantén tus cuentas al día para recibir pagos oportunos</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ayuda y Soporte -->
                    <div class="bg-gray-50 rounded-xl shadow-md p-6">
                        <h6 class="font-semibold text-gray-600 mb-4 flex items-center">
                            <i class="bi bi-question-circle mr-2"></i>¿Necesitas Ayuda?
                        </h6>
                        <p class="text-sm text-gray-500 mb-4">
                            Consulta la guía de usuario o contacta al administrador de tu organización
                        </p>
                        <button class="w-full mb-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center justify-center">
                            <i class="bi bi-book mr-2"></i>Guía de Usuario
                        </button>
                        <button class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center justify-center">
                            <i class="bi bi-headset mr-2"></i>Soporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection