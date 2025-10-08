@extends('layouts.app-dashboard')

@section('title', $organizacion->nombre_oficial . ' - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex items-center space-x-2 text-sm text-secondary">
                        <a href="{{ route('organizaciones.index') }}" class="hover:text-primary">Organizaciones</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-800">{{ $organizacion->nombre_oficial }}</span>
                    </nav>
                </div>

                <!-- Header con acciones -->
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-accent flex items-center justify-center shadow-lg">
                            <i class="fas fa-building text-3xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">{{ $organizacion->nombre_oficial }}</h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-sm text-secondary">
                                    <i class="fas fa-id-card mr-1"></i>
                                    NIT: {{ $organizacion->nit }}
                                </span>
                                @if($organizacion->estado == 'activa')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Inactiva
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('organizaciones.edit', $organizacion) }}" 
                           class="px-4 py-2 border-2 border-primary text-primary font-semibold rounded-lg hover:bg-primary hover:text-white transition-all flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </a>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-accent text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Usuarios Activos</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $estadisticas['usuarios_activos'] }}</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-primary text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Contratos Activos</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $estadisticas['contratos_activos'] }}</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-clock text-warning text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Pendientes</h3>
                        <p class="text-3xl font-bold text-warning">{{ $estadisticas['vinculaciones_pendientes'] }}</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Valor Contratos</h3>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($estadisticas['valor_contratos_activos'], 0) }}</p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{ activeTab: 'info' }">
                    <!-- Tab Headers -->
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6" aria-label="Tabs">
                            <button @click="activeTab = 'info'" 
                                    :class="activeTab === 'info' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información
                            </button>
                            <button @click="activeTab = 'usuarios'" 
                                    :class="activeTab === 'usuarios' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                <i class="fas fa-users mr-2"></i>
                                Usuarios ({{ $organizacion->usuarios->count() }})
                            </button>
                            <button @click="activeTab = 'contratos'" 
                                    :class="activeTab === 'contratos' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                <i class="fas fa-file-contract mr-2"></i>
                                Contratos ({{ $organizacion->contratos->count() }})
                            </button>
                            <button @click="activeTab = 'pendientes'" 
                                    :class="activeTab === 'pendientes' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                Pendientes
                                @if($organizacion->vinculacionesPendientes->count() > 0)
                                    <span class="ml-2 bg-warning text-white text-xs px-2 py-0.5 rounded-full">
                                        {{ $organizacion->vinculacionesPendientes->count() }}
                                    </span>
                                @endif
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Información Tab -->
                        <div x-show="activeTab === 'info'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Ubicación</label>
                                    <p class="mt-1 text-gray-800">
                                        <i class="fas fa-map-marker-alt text-accent mr-2"></i>
                                        {{ $organizacion->direccion }}, {{ $organizacion->municipio }}, {{ $organizacion->departamento }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Email Institucional</label>
                                    <p class="mt-1 text-gray-800">
                                        <i class="fas fa-envelope text-accent mr-2"></i>
                                        {{ $organizacion->email_institucional }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Teléfono</label>
                                    <p class="mt-1 text-gray-800">
                                        <i class="fas fa-phone text-accent mr-2"></i>
                                        {{ $organizacion->telefono_contacto }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Código de Vinculación</label>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <code class="bg-gray-100 px-3 py-2 rounded font-mono text-sm">{{ $organizacion->codigo_vinculacion }}</code>
                                        <button onclick="copiarCodigo('{{ $organizacion->codigo_vinculacion }}')" 
                                                class="text-accent hover:text-primary transition-colors p-2">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                @if($organizacion->dominios_email)
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-gray-700">Dominios Autorizados</label>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($organizacion->dominios_email as $dominio)
                                            <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm font-mono">
                                                {{ $dominio }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Usuarios Tab -->
                        <div x-show="activeTab === 'usuarios'" class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Usuarios</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white rounded-xl shadow-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Nombre</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Email</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Rol</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($organizacion->usuarios as $usuario)
                                        <tr>
                                            <td class="px-4 py-2">{{ $usuario->name }}</td>
                                            <td class="px-4 py-2">{{ $usuario->email }}</td>
                                            <td class="px-4 py-2">{{ $usuario->rol }}</td>
                                            <td class="px-4 py-2">
                                                @if($usuario->activo)
                                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Activo</span>
                                                @else
                                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-2 text-center text-secondary">No hay usuarios registrados.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Contratos Tab -->
                        <div x-show="activeTab === 'contratos'" class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Contratos</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white rounded-xl shadow-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Número</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Objeto</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Valor</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($organizacion->contratos as $contrato)
                                        <tr>
                                            <td class="px-4 py-2">{{ $contrato->numero }}</td>
                                            <td class="px-4 py-2">{{ $contrato->objeto }}</td>
                                            <td class="px-4 py-2">${{ number_format($contrato->valor, 0) }}</td>
                                            <td class="px-4 py-2">
                                                @if($contrato->estado == 'activo')
                                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Activo</span>
                                                @else
                                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-2 text-center text-secondary">No hay contratos registrados.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pendientes Tab -->
                        <div x-show="activeTab === 'pendientes'" class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Vinculaciones Pendientes</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white rounded-xl shadow-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Nombre</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Email</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Fecha Solicitud</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($organizacion->vinculacionesPendientes as $pendiente)
                                        <tr>
                                            <td class="px-4 py-2">{{ $pendiente->nombre }}</td>
                                            <td class="px-4 py-2">{{ $pendiente->email }}</td>
                                            <td class="px-4 py-2">{{ $pendiente->created_at->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2">
                                                <a href="{{ route('vinculaciones.aprobar', $pendiente->id) }}" class="text-green-600 hover:underline mr-2">Aprobar</a>
                                                <a href="{{ route('vinculaciones.rechazar', $pendiente->id) }}" class="text-red-600 hover:underline">Rechazar</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-2 text-center text-secondary">No hay vinculaciones pendientes.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function copiarCodigo(codigo) {
    navigator.clipboard.writeText(codigo);
    alert('Código copiado al portapapeles');
}
</script>
@endsection