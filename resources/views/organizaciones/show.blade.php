@extends('layouts.app-dashboard')

@section('title', $organizacion->nombre_oficial . ' - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <main class="flex-1 overflow-y-auto">
            <div class="container-fluid py-6 gap-4 px-4">
                <!-- Breadcrumb -->
                <nav class="flex mb-6" aria-label="breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                Dashboard
                            </a>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-400 mx-2">/</span>
                            <a href="{{ route('organizaciones.index') }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                Organizaciones
                            </a>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-400 mx-2">/</span>
                            <span class="text-gray-600">{{ $organizacion->nombre_oficial }}</span>
                        </li>
                    </ol>
                </nav>

                <!-- Header con Badge de Organización Actual -->
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-start">
                        <div class="mr-4">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center">
                                <i class="fas fa-building text-2xl text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <h2 class="text-2xl font-bold text-blue-900">{{ $organizacion->nombre_oficial }}</h2>
                                @if(session('organizacion_actual') == $organizacion->id)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-br from-cyan-500 to-cyan-600 text-white">
                                        <i class="bi bi-check-circle mr-1"></i>Organización Actual
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-gray-600">
                                    <i class="bi bi-card-text mr-1"></i>
                                    NIT: {{ $organizacion->nit }}
                                </span>
                                @php
                                    $estadoBadges = [
                                        'activa' => ['bg' => 'bg-green-100 text-green-800', 'icon' => 'check-circle-fill'],
                                        'inactiva' => ['bg' => 'bg-gray-100 text-gray-800', 'icon' => 'x-circle-fill'],
                                        'suspendida' => ['bg' => 'bg-orange-100 text-orange-800', 'icon' => 'pause-circle-fill']
                                    ];
                                    $badge = $estadoBadges[$organizacion->estado] ?? $estadoBadges['activa'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['bg'] }}">
                                    <i class="bi bi-{{ $badge['icon'] }} mr-1"></i>{{ ucfirst($organizacion->estado) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex gap-2">
                        <a href="{{ route('organizaciones.edit', $organizacion) }}" 
                        class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                            <i class="bi bi-pencil mr-2"></i>Editar
                        </a>
                        
                        @if(session('organizacion_actual') != $organizacion->id)
                        <a href="{{ route('organizaciones.seleccionar', $organizacion) }}" 
                        class="inline-flex items-center px-4 py-2 bg-cyan-500 hover:bg-cyan-600 border border-cyan-500 text-white rounded-lg transition-colors">
                            <i class="bi bi-cursor mr-2"></i>Seleccionar
                        </a>
                        @endif
                    </div>
                </div>

                <!-- KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Usuarios Activos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-full bg-cyan-50 flex items-center justify-center">
                                <i class="bi bi-people-fill text-xl text-cyan-500"></i>
                            </div>
                        </div>
                        <h6 class="text-gray-600 text-sm font-medium mb-2">Usuarios Activos</h6>
                        <h3 class="text-2xl font-bold text-blue-900">{{ $estadisticas['usuarios_activos'] }}</h3>
                    </div>

                    <!-- Contratos Activos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                                <i class="bi bi-file-earmark-text-fill text-xl text-blue-600"></i>
                            </div>
                        </div>
                        <h6 class="text-gray-600 text-sm font-medium mb-2">Contratos Activos</h6>
                        <h3 class="text-2xl font-bold text-blue-900">{{ $estadisticas['contratos_activos'] }}</h3>
                    </div>

                    <!-- Pendientes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center">
                                <i class="bi bi-clock-history text-xl text-orange-500"></i>
                            </div>
                        </div>
                        <h6 class="text-gray-600 text-sm font-medium mb-2">Pendientes</h6>
                        <h3 class="text-2xl font-bold text-orange-500">{{ $estadisticas['vinculaciones_pendientes'] }}</h3>
                    </div>

                    <!-- Valor Contratos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                                <i class="bi bi-currency-dollar text-xl text-green-600"></i>
                            </div>
                        </div>
                        <h6 class="text-gray-600 text-sm font-medium mb-2">Valor Contratos</h6>
                        <h3 class="text-2xl font-bold text-green-600">${{ number_format($estadisticas['valor_contratos_activos'], 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <!-- Tabs Header -->
                    <div class="border-b border-gray-200 px-6 pt-4">
                        <div class="flex space-x-8" role="tablist">
                            <button class="tab-button active pb-3 border-b-2 border-blue-600 text-blue-900 font-medium transition-colors"
                                    data-tab="info"
                                    type="button"
                                    role="tab">
                                <i class="bi bi-info-circle mr-2"></i>Información
                            </button>
                            <button class="tab-button pb-3 border-b-2 border-transparent text-gray-600 hover:text-blue-900 transition-colors"
                                    data-tab="usuarios"
                                    type="button"
                                    role="tab">
                                <i class="bi bi-people mr-2"></i>Usuarios ({{ $organizacion->usuarios->count() }})
                            </button>
                            <button class="tab-button pb-3 border-b-2 border-transparent text-gray-600 hover:text-blue-900 transition-colors"
                                    data-tab="contratos"
                                    type="button"
                                    role="tab">
                                <i class="bi bi-file-earmark-text mr-2"></i>Contratos ({{ $organizacion->contratos->count() }})
                            </button>
                            <button class="tab-button pb-3 border-b-2 border-transparent text-gray-600 hover:text-blue-900 transition-colors"
                                    data-tab="pendientes"
                                    type="button"
                                    role="tab">
                                <i class="bi bi-clock mr-2"></i>Pendientes
                                @if($estadisticas['vinculaciones_pendientes'] > 0)
                                    <span class="ml-1 bg-yellow-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                                        {{ $estadisticas['vinculaciones_pendientes'] }}
                                    </span>
                                @endif
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="tab-content">
                            <!-- Tab Información -->
                            <div class="tab-pane active" id="info" role="tabpanel">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Ubicación</label>
                                        <p class="flex items-center text-gray-900">
                                            <i class="bi bi-geo-alt mr-2 text-cyan-500"></i>
                                            {{ $organizacion->direccion }}, {{ $organizacion->municipio }}, {{ $organizacion->departamento }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Email Institucional</label>
                                        <p class="flex items-center text-gray-900">
                                            <i class="bi bi-envelope mr-2 text-cyan-500"></i>
                                            {{ $organizacion->email_institucional }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Teléfono</label>
                                        <p class="flex items-center text-gray-900">
                                            <i class="bi bi-telephone mr-2 text-cyan-500"></i>
                                            {{ $organizacion->telefono_contacto }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Código de Vinculación</label>
                                        <div class="flex items-center gap-2">
                                            <code class="px-3 py-2 bg-gray-50 rounded-lg font-mono text-sm border">
                                                {{ $organizacion->codigo_vinculacion }}
                                            </code>
                                            <button onclick="copiarCodigo('{{ $organizacion->codigo_vinculacion }}')" 
                                                    class="inline-flex items-center p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                                    title="Copiar código">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>

                                    @if($organizacion->dominios_email && count($organizacion->dominios_email) > 0)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Dominios Autorizados</label>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($organizacion->dominios_email as $dominio)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-cyan-50 text-cyan-700 font-mono">
                                                    {{ $dominio }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tab Usuarios -->
                            <div class="tab-pane hidden" id="usuarios" role="tabpanel">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-lg font-semibold text-blue-900">Usuarios de la Organización</h5>
                                    <a href="{{ route('usuarios.index') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                                        <i class="bi bi-gear mr-2"></i>Gestionar Usuarios
                                    </a>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Usuario</th>
                                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Rol</th>
                                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Estado</th>
                                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Fecha Asignación</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($organizacion->usuarios as $usuario)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-white font-semibold mr-3">
                                                            {{ substr($usuario->nombre, 0, 1) }}
                                                        </div>
                                                        <span class="text-gray-900">{{ $usuario->nombre }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                                                <td class="px-4 py-3">
                                                    @foreach($usuario->roles as $rol)
                                                        @if($rol->pivot->organizacion_id == $organizacion->id)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-600 text-white">
                                                                {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($usuario->pivot->estado == 'activo')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Activo
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Inactivo
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-500 text-sm">
                                                    {{ $usuario->pivot->fecha_asignacion ? \Carbon\Carbon::parse($usuario->pivot->fecha_asignacion)->format('d/m/Y') : '-' }}
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-8 text-center">
                                                    <i class="bi bi-people text-4xl text-gray-300 mb-2"></i>
                                                    <p class="text-gray-500">No hay usuarios asignados</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Contratos -->
                            <div class="tab-pane hidden" id="contratos" role="tabpanel">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-lg font-semibold text-blue-900">Contratos de la Organización</h5>
                                    <a href="{{ route('contratos.index') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                                        <i class="bi bi-file-earmark-plus mr-2"></i>Gestionar Contratos
                                    </a>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Número</th>
                                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Contratista</th>
                                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Supervisor</th>
                                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-600">Valor</th>
                                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Estado</th>
                                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Vigencia</th>
                                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-600">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($organizacion->contratos as $contrato)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3"><code class="text-sm">{{ $contrato->numero_contrato }}</code></td>
                                                <td class="px-4 py-3 text-gray-900">{{ $contrato->contratista->nombre ?? 'Sin asignar' }}</td>
                                                <td class="px-4 py-3 text-gray-900">{{ $contrato->supervisor->nombre ?? 'Sin asignar' }}</td>
                                                <td class="px-4 py-3 text-right font-semibold text-gray-900">${{ number_format($contrato->valor_total, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @php
                                                        $estadoBadges = [
                                                            'activo' => ['bg' => 'bg-green-100 text-green-800', 'text' => 'Activo'],
                                                            'borrador' => ['bg' => 'bg-gray-100 text-gray-800', 'text' => 'Borrador'],
                                                            'terminado' => ['bg' => 'bg-blue-100 text-blue-800', 'text' => 'Terminado'],
                                                            'suspendido' => ['bg' => 'bg-orange-100 text-orange-800', 'text' => 'Suspendido']
                                                        ];
                                                        $badge = $estadoBadges[$contrato->estado] ?? ['bg' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($contrato->estado)];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['bg'] }}">
                                                        {{ $badge['text'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }} - 
                                                    {{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <a href="{{ route('contratos.show', $contrato) }}" class="inline-flex items-center p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="px-4 py-8 text-center">
                                                    <i class="bi bi-file-earmark-text text-4xl text-gray-300 mb-2"></i>
                                                    <p class="text-gray-500">No hay contratos registrados</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Pendientes -->
                            <div class="tab-pane hidden" id="pendientes" role="tabpanel">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-lg font-semibold text-blue-900">Vinculaciones Pendientes</h5>
                                    <a href="{{ route('usuarios.pendientes') }}" class="inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors text-sm">
                                        <i class="bi bi-clock-history mr-2"></i>Gestionar Pendientes
                                    </a>
                                </div>

                                @if($organizacion->vinculacionesPendientes->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($organizacion->vinculacionesPendientes as $pendiente)
                                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-orange-500">
                                            <div class="p-4">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div class="flex items-center">
                                                        <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 font-semibold mr-3">
                                                            {{ substr($pendiente->usuario->nombre, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <h6 class="font-semibold text-gray-900 mb-0">{{ $pendiente->usuario->nombre }}</h6>
                                                            <small class="text-gray-500">{{ $pendiente->usuario->email }}</small>
                                                        </div>
                                                    </div>
                                                    <small class="text-gray-400">{{ $pendiente->created_at->diffForHumans() }}</small>
                                                </div>
                                                
                                                @if($pendiente->codigo_vinculacion_usado)
                                                <div class="mb-3 p-2 bg-gray-50 rounded-lg">
                                                    <small class="text-gray-600">
                                                        <i class="bi bi-key mr-1"></i>Código usado: 
                                                        <code class="ml-1 font-mono">{{ $pendiente->codigo_vinculacion_usado }}</code>
                                                    </small>
                                                </div>
                                                @endif

                                                <div class="flex gap-2">
                                                    <a href="{{ route('usuarios.pendientes') }}" 
                                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                                                        <i class="bi bi-check-circle mr-2"></i>Asignar Rol
                                                    </a>
                                                    <button type="button" 
                                                            class="inline-flex items-center p-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                            onclick="rechazarModal('{{ $pendiente->id }}', '{{ $pendiente->usuario->nombre }}')">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i class="bi bi-check-circle text-5xl text-green-500 mb-3"></i>
                                        <h5 class="text-lg font-semibold text-green-600 mb-2">¡Todo al día!</h5>
                                        <p class="text-gray-500">No hay vinculaciones pendientes por revisar</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Rechazar Vinculación -->
            <div class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="modalRechazar">
                <div class="relative top-20 mx-auto p-4 border w-full max-w-md shadow-lg rounded-xl bg-white">
                    <div class="modal-content rounded-xl border-none">
                        <div class="modal-header border-b-0 pb-0">
                            <h5 class="modal-title text-xl font-bold text-red-600">
                                <i class="bi bi-exclamation-triangle mr-2"></i>Rechazar Vinculación
                            </h5>
                            <button type="button" class="modal-close absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <form id="formRechazar" method="POST">
                            @csrf
                            <div class="modal-body p-6">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <div class="flex">
                                        <i class="bi bi-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                                        <div>
                                            <span class="text-yellow-700">¿Estás seguro de rechazar la vinculación de <strong id="usuarioNombreRechazar"></strong>?</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="motivoRechazo" class="block text-sm font-medium text-gray-700 mb-2">
                                        Motivo del Rechazo <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="motivoRechazo" 
                                            name="motivo" 
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            rows="3" 
                                            placeholder="Explica brevemente el motivo..."
                                            required></textarea>
                                    <div class="mt-1 text-sm text-gray-500">El usuario recibirá este mensaje.</div>
                                </div>
                            </div>
                            
                            <div class="modal-footer border-t border-gray-200 pt-4 flex justify-end space-x-3">
                                <button type="button" class="modal-cancel px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center">
                                    <i class="bi bi-x-circle mr-2"></i>Confirmar Rechazo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>  
</div>

@endsection

@push('scripts')
<script>
    // Sistema de tabs personalizado
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tabs
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Actualizar botones
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-600', 'text-blue-900');
                    btn.classList.add('text-gray-600', 'border-transparent');
                });
                this.classList.add('active', 'border-blue-600', 'text-blue-900');
                this.classList.remove('text-gray-600', 'border-transparent');
                
                // Actualizar paneles
                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                    pane.classList.remove('active');
                });
                
                const targetPane = document.getElementById(targetTab);
                targetPane.classList.remove('hidden');
                targetPane.classList.add('active');
            });
        });

        // Sistema de modales personalizado
        const modals = document.querySelectorAll('.modal');
        const modalCloseButtons = document.querySelectorAll('.modal-close, .modal-cancel');
        
        modalCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                modal.classList.add('hidden');
            });
        });
        
        // Cerrar modal al hacer clic fuera
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    });

    // Copiar código de vinculación
    function copiarCodigo(codigo) {
        navigator.clipboard.writeText(codigo).then(() => {
            // Toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 z-50';
            toast.innerHTML = `
                <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center">
                    <i class="bi bi-check-circle mr-2"></i>
                    <span>Código copiado al portapapeles</span>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }).catch(err => {
            alert('Error al copiar: ' + err);
        });
    }

    // Modal rechazar vinculación
    function rechazarModal(id, nombre) {
        const modal = document.getElementById('modalRechazar');
        const form = document.getElementById('formRechazar');
        const nombreSpan = document.getElementById('usuarioNombreRechazar');
        
        nombreSpan.textContent = nombre;
        form.action = `/usuarios/${id}/rechazar`;
        document.getElementById('motivoRechazo').value = '';
        
        modal.classList.remove('hidden');
    }
</script>
@endpush