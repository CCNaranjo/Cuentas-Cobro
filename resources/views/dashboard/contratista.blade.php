@extends('layouts.app-dashboard')

@section('title', 'Mi Dashboard - Contratista')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <div class="container mx-auto px-4 py-8 overflow-auto">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-semibold text-primary mb-1 flex items-center">
                    <i class="bi bi-briefcase mr-2"></i>Mi Panel de Contratista
                </h2>
                <p class="text-gray-500">Gestiona tus contratos y cuentas de cobro</p>
            </div>

            <!-- Mensajes -->
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
                        <h6 class="text-sm text-gray-500 mb-2">Pendientes de Acción</h6>
                        <h3 class="text-3xl font-bold text-warning mb-1">{{ $estadisticas['cuentas_pendientes'] }}</h3>
                        <small class="text-gray-500">Borradores y correcciones</small>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna Principal (65%) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- ⚠️ ALERTA: Cuentas Devueltas -->
                    @if($cuentasDevueltas->count() > 0)
                    <div class="bg-white rounded-xl shadow-md border-l-4 border-red-500">
                        <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
                            <h5 class="text-xl font-semibold text-red-600 flex items-center">
                                <i class="bi bi-exclamation-triangle-fill mr-2 animate-pulse"></i>
                                ¡Atención! Cuentas Devueltas para Corrección
                            </h5>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($cuentasDevueltas as $cuenta)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <span class="font-mono font-semibold text-red-600 mr-3">
                                                {{ $cuenta->numero_cuenta_cobro }}
                                            </span>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                {{ ucfirst(str_replace('_', ' ', $cuenta->estado)) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Contrato:</strong> {{ $cuenta->contrato->numero_contrato }}
                                        </p>
                                        @if($cuenta->historial->first())
                                        <div class="bg-red-50 p-3 rounded-lg mt-2">
                                            <p class="text-xs text-red-800 font-semibold mb-1">
                                                <i class="bi bi-chat-left-text mr-1"></i>Comentario de revisión:
                                            </p>
                                            <p class="text-sm text-red-700">
                                                "{{ $cuenta->historial->first()->comentario ?? 'Sin comentarios' }}"
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" 
                                           class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition flex items-center">
                                            <i class="bi bi-pencil mr-1"></i>Corregir
                                        </a>
                                        <a href="{{ route('cuentas-cobro.show', $cuenta->id) }}" 
                                           class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Borradores Sin Terminar -->
                    @if($cuentasBorrador->count() > 0)
                    <div class="bg-white rounded-xl shadow-md border-l-4 border-yellow-500">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-yellow-600 flex items-center">
                                <i class="bi bi-file-earmark mr-2"></i>
                                Borradores Pendientes
                            </h5>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($cuentasBorrador as $cuenta)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-1">
                                            <span class="font-mono font-semibold text-gray-800 mr-3">
                                                {{ $cuenta->numero_cuenta_cobro }}
                                            </span>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                Borrador
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            {{ $cuenta->contrato->numero_contrato }} • 
                                            ${{ number_format($cuenta->valor_neto, 0, ',', '.') }} •
                                            {{ $cuenta->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" 
                                           class="px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-dark transition">
                                            <i class="bi bi-pencil mr-1"></i>Completar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- ✅ Contratos Listos para Cobrar -->
                    @if($contratosListosParaCobro->count() > 0)
                    <div class="bg-white rounded-xl shadow-md border-l-4 border-green-500">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-green-600 flex items-center">
                                <i class="bi bi-check-circle mr-2"></i>Contratos Listos para Crear Cuenta de Cobro
                            </h5>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-500 mb-4">Tienes {{ $contratosListosParaCobro->count() }} contrato(s) activo(s)</p>
                            <div class="space-y-3">
                                @foreach($contratosListosParaCobro->take(3) as $contrato)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div>
                                        <h6 class="font-semibold text-gray-800 mb-1">{{ $contrato->numero_contrato }}</h6>
                                        <div class="text-xs text-gray-500 space-y-1">
                                            <p>{{ $contrato->organizacion->nombre_oficial }}</p>
                                            <p>Valor: ${{ number_format($contrato->valor_total, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('cuentas-cobro.create') }}?contrato_id={{ $contrato->id }}" 
                                       class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition flex items-center">
                                        <i class="bi bi-plus-circle mr-1"></i>Crear Cuenta
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Cuentas en Proceso -->
                    @if($cuentasEnProceso->count() > 0)
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-blue-600 flex items-center">
                                <i class="bi bi-hourglass-split mr-2"></i>Cuentas en Revisión
                            </h5>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($cuentasEnProceso as $cuenta)
                            @php
                                $estadosBadge = [
                                    'radicada' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Radicada'],
                                    'certificado_supervisor' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-800', 'label' => 'Certificada'],
                                    'verificado_contratacion' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'label' => 'Verificada (Legal)'],
                                    'verificado_presupuesto' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Verificada (Presupuesto)'],
                                ];
                                $badge = $estadosBadge[$cuenta->estado] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'En proceso'];
                            @endphp
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-1">
                                            <span class="font-mono font-semibold text-primary mr-3">
                                                {{ $cuenta->numero_cuenta_cobro }}
                                            </span>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            {{ $cuenta->contrato->numero_contrato }} • 
                                            ${{ number_format($cuenta->valor_neto, 0, ',', '.') }} •
                                            {{ $cuenta->updated_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <a href="{{ route('cuentas-cobro.show', $cuenta->id) }}" 
                                       class="px-4 py-2 border border-primary text-primary text-sm rounded-lg hover:bg-primary hover:text-white transition">
                                        Ver Detalle
                                    </a>
                                </div>
                            </div>
                            @endforeach
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
                                <div class="p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition"
                                     onclick="window.location='{{ route('cuentas-cobro.index') }}?estado=borrador'">
                                    <i class="bi bi-file-earmark text-5xl text-gray-400 mb-2"></i>
                                    <h4 class="text-2xl font-bold text-gray-800 mb-1">{{ $trazabilidadCuentas['borradores'] }}</h4>
                                    <small class="text-gray-500">Borradores</small>
                                </div>
                                <div class="p-4 bg-accent/10 rounded-lg cursor-pointer hover:bg-accent/20 transition"
                                     onclick="window.location='{{ route('cuentas-cobro.index') }}?estado=radicada'">
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
                                <div class="p-4 bg-primary/10 rounded-lg cursor-pointer hover:bg-primary/20 transition"
                                     onclick="window.location='{{ route('cuentas-cobro.index') }}?estado=pagada'">
                                    <i class="bi bi-cash-coin text-5xl text-primary mb-2"></i>
                                    <h4 class="text-2xl font-bold text-primary mb-1">{{ $trazabilidadCuentas['pagadas'] }}</h4>
                                    <small class="text-gray-500">Pagadas</small>
                                </div>
                                <div class="p-4 bg-red-50 rounded-lg">
                                    <i class="bi bi-x-circle text-5xl text-red-500 mb-2"></i>
                                    <h4 class="text-2xl font-bold text-red-500 mb-1">{{ $trazabilidadCuentas['rechazadas'] }}</h4>
                                    <small class="text-gray-500">Devueltas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mis Contratos -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h5 class="text-xl font-semibold text-primary flex items-center">
                                <i class="bi bi-file-earmark-text mr-2"></i>Mis Contratos
                            </h5>
                            <a href="{{ route('contratos.index') }}" class="text-primary hover:text-primary-dark text-sm font-semibold">
                                Ver todos →
                            </a>
                        </div>
                        <div class="overflow-hidden">
                            @if($misContratos->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organización</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($misContratos->take(5) as $contrato)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('contratos.show', $contrato) }}" class="text-primary font-semibold hover:text-primary-dark">
                                                    {{ $contrato->numero_contrato }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $contrato->organizacion->nombre_oficial }}</p>
                                                    <p class="text-xs text-gray-500">Supervisor: {{ $contrato->supervisor->nombre ?? 'Sin asignar' }}</p>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right font-semibold">${{ number_format($contrato->valor_total, 0, ',', '.') }}</td>
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
                        <p class="text-sm opacity-75 mb-6">Radica tus cuentas de cobro de forma rápida</p>
                        <a href="{{ route('cuentas-cobro.create') }}" 
                           class="block w-full bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center justify-center">
                            <i class="bi bi-plus-circle mr-2"></i>Nueva Cuenta
                        </a>
                    </div>

                    <!-- Accesos Rápidos -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h6 class="font-semibold text-primary flex items-center">
                                <i class="bi bi-lightning mr-2"></i>Accesos Rápidos
                            </h6>
                        </div>
                        <div class="p-4 space-y-2">
                            <a href="{{ route('cuentas-cobro.index') }}" 
                               class="block w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-between group">
                                <span><i class="bi bi-list-ul mr-2"></i>Mis Cuentas de Cobro</span>
                                <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="{{ route('contratos.index') }}" 
                               class="block w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-between group">
                                <span><i class="bi bi-file-earmark-text mr-2"></i>Mis Contratos</span>
                                <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
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
                        <div class="space-y-2">
                            <button class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center justify-center">
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
</div>

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