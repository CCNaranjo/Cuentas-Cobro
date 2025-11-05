@extends('layouts.app-dashboard')

@section('title', 'Dashboard - ' . $organizacion->nombre_oficial)

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <div class="container mx-auto px-4 py-8 overflow-auto">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-semibold text-primary mb-1 flex items-center">
                    <i class="bi bi-speedometer2 mr-2"></i>Dashboard - {{ $organizacion->nombre_oficial }}
                </h2>
                <p class="text-gray-500">Panel de control y gestión operativa</p>
            </div>

            <!-- KPIs Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Usuarios Activos -->
                <div class="bg-white rounded-xl shadow-md hover-lift border-l-4 border-accent">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center">
                                <i class="bi bi-people-fill text-2xl text-accent"></i>
                            </div>
                            @if(isset($estadisticas['usuarios_pendientes']) && $estadisticas['usuarios_pendientes'] > 0)
                            <span class="badge-warning">
                                {{ $estadisticas['usuarios_pendientes'] }} pendientes
                            </span>
                            @endif
                        </div>
                        <h6 class="text-sm text-gray-500 mb-2">Usuarios Activos</h6>
                        <h3 class="text-3xl font-bold text-primary">{{ $estadisticas['usuarios_activos'] }}</h3>
                    </div>
                </div>

                <!-- Contratos Activos -->
                <div class="bg-white rounded-xl shadow-md hover-lift border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                                <i class="bi bi-file-earmark-text-fill text-2xl text-primary"></i>
                            </div>
                            @if(isset($estadisticas['contratos_por_vencer']) && $estadisticas['contratos_por_vencer'] > 0)
                            <span class="badge-danger">
                                {{ $estadisticas['contratos_por_vencer'] }} por vencer
                            </span>
                            @endif
                        </div>
                        <h6 class="text-sm text-gray-500 mb-2">Contratos Activos</h6>
                        <h3 class="text-3xl font-bold text-primary">{{ $estadisticas['contratos_activos'] }}</h3>
                    </div>
                </div>

                <!-- Valor Contratos -->
                <div class="bg-white rounded-xl shadow-md hover-lift border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                                <i class="bi bi-currency-dollar text-2xl text-green-500"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-500 mb-2">Valor Contratos</h6>
                        <h3 class="text-3xl font-bold text-green-500">
                            ${{ number_format($estadisticas['valor_contratos'], 0, ',', '.') }}
                        </h3>
                    </div>
                </div>

                <!-- Cuentas Pendientes -->
                <div class="bg-white rounded-xl shadow-md hover-lift border-l-4 border-warning">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center">
                                <i class="bi bi-clock-history text-2xl text-warning"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-500 mb-2">Cuentas Pendientes</h6>
                        <h3 class="text-3xl font-bold text-warning">
                            {{ $estadisticas['cuentas_pendientes'] ?? 0 }}
                        </h3>
                        <small class="text-gray-500">Próximamente</small>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Columna Izquierda (60%) -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Tareas Pendientes (Solo Admin Organización) -->
                    @if(isset($usuariosPendientes) && $usuariosPendientes->count() > 0)
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h5 class="text-xl font-semibold text-warning flex items-center">
                                <i class="bi bi-exclamation-triangle mr-2"></i>Tareas Pendientes
                            </h5>
                            <span class="bg-warning text-white px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $usuariosPendientes->count() }}
                            </span>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($usuariosPendientes as $pendiente)
                            <div class="p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-warning/10 rounded-full flex items-center justify-center text-warning mr-4">
                                        <i class="bi bi-person-plus"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="font-semibold text-gray-800 mb-1">Usuario pendiente de asignación</h6>
                                        <small class="text-gray-500">
                                            {{ $pendiente->usuario->nombre }} - {{ $pendiente->usuario->email }}
                                        </small>
                                    </div>
                                    <a href="{{ route('usuarios.pendientes') }}" class="px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-dark transition">
                                        Asignar Rol
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Contratos Recientes -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h5 class="text-xl font-semibold text-primary flex items-center">
                                <i class="bi bi-file-earmark-text mr-2"></i>Contratos Recientes
                            </h5>
                            <a href="{{ route('contratos.index') }}" class="px-4 py-2 border border-primary text-primary text-sm rounded-lg hover:bg-primary hover:text-white transition">
                                Ver Todos
                            </a>
                        </div>
                        <div class="overflow-hidden">
                            @if(isset($contratosRecientes) && $contratosRecientes->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contratista</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($contratosRecientes as $contrato)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('contratos.show', $contrato) }}" class="text-primary font-semibold hover:text-primary-dark">
                                                    {{ $contrato->numero_contrato }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">{{ $contrato->contratista->nombre ?? 'Sin asignar' }}</td>
                                            <td class="px-6 py-4">{{ $contrato->supervisor->nombre ?? 'Sin asignar' }}</td>
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
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-12">
                                <i class="bi bi-file-earmark-text text-gray-200" style="font-size: 4rem;"></i>
                                <p class="text-gray-500 mt-4">No hay contratos registrados</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contratos por Vencer -->
                    @if(isset($contratosPorVencer) && $contratosPorVencer->count() > 0)
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-red-600 flex items-center">
                                <i class="bi bi-exclamation-circle mr-2"></i>Alertas: Contratos por Vencer
                            </h5>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($contratosPorVencer as $contrato)
                            @php
                                $diasRestantes = now()->diffInDays($contrato->fecha_fin, false);
                            @endphp
                            <div class="p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-500 mr-4">
                                        <i class="bi bi-calendar-x"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="font-semibold text-gray-800 mb-1">{{ $contrato->numero_contrato }}</h6>
                                        <small class="text-gray-500">
                                            {{ $contrato->contratista->nombre ?? 'Sin contratista' }} - 
                                            Vence en {{ $diasRestantes }} días
                                        </small>
                                    </div>
                                    <a href="{{ route('contratos.show', $contrato) }}" class="px-4 py-2 border border-red-500 text-red-500 text-sm rounded-lg hover:bg-red-500 hover:text-white transition">
                                        Ver Detalle
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Columna Derecha (40%) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Acciones Rápidas -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h5 class="text-xl font-semibold text-primary flex items-center">
                                <i class="bi bi-lightning mr-2"></i>Acciones Rápidas
                            </h5>
                        </div>
                        <div class="p-6 space-y-3">
                            @if(auth()->user()->tienePermiso('crear-contrato', $organizacion->id))
                            <a href="{{ route('contratos.create') }}" class="block w-full bg-primary text-white text-center px-4 py-3 rounded-lg hover:bg-primary-dark transition font-semibold">
                                <i class="bi bi-plus-circle mr-2"></i>Nuevo Contrato
                            </a>
                            @endif

                            @if(auth()->user()->tienePermiso('ver-usuarios', $organizacion->id))
                            <a href="{{ route('usuarios.index') }}" class="block w-full border border-primary text-primary text-center px-4 py-3 rounded-lg hover:bg-primary hover:text-white transition font-semibold">
                                <i class="bi bi-people mr-2"></i>Gestionar Usuarios
                            </a>
                            @endif

                            @if(auth()->user()->tienePermiso('asignar-rol', $organizacion->id))
                            <a href="{{ route('usuarios.pendientes') }}" class="block w-full border border-warning text-warning text-center px-4 py-3 rounded-lg hover:bg-warning hover:text-white transition font-semibold relative">
                                <i class="bi bi-clock-history mr-2"></i>Usuarios Pendientes
                                @if(isset($estadisticas['usuarios_pendientes']) && $estadisticas['usuarios_pendientes'] > 0)
                                <span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-0.5 rounded-full text-xs">
                                    {{ $estadisticas['usuarios_pendientes'] }}
                                </span>
                                @endif
                            </a>
                            @endif

                            <a href="{{ route('contratos.index') }}" class="block w-full border border-gray-300 text-gray-700 text-center px-4 py-3 rounded-lg hover:bg-gray-50 transition font-semibold">
                                <i class="bi bi-list-ul mr-2"></i>Ver Contratos
                            </a>
                        </div>
                    </div>

                    <!-- Información de la Organización -->
                    <div class="bg-gradient-to-br from-primary to-accent rounded-xl shadow-md text-white p-6">
                        <h6 class="font-semibold mb-4 flex items-center">
                            <i class="bi bi-building mr-2"></i>{{ $organizacion->nombre_oficial }}
                        </h6>
                        <div class="space-y-3 text-sm mb-4">
                            <div class="flex items-start">
                                <i class="bi bi-geo-alt mr-3 mt-0.5"></i>
                                <span>{{ $organizacion->municipio }}, {{ $organizacion->departamento }}</span>
                            </div>
                            <div class="flex items-start">
                                <i class="bi bi-envelope mr-3 mt-0.5"></i>
                                <span class="break-all">{{ $organizacion->email_institucional }}</span>
                            </div>
                            <div class="flex items-start">
                                <i class="bi bi-telephone mr-3 mt-0.5"></i>
                                <span>{{ $organizacion->telefono_contacto }}</span>
                            </div>
                        </div>
                        <a href="{{ route('organizaciones.show', $organizacion) }}" class="block w-full bg-white text-primary text-center px-4 py-2 rounded-lg hover:bg-gray-100 transition font-semibold">
                            <i class="bi bi-eye mr-2"></i>Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection