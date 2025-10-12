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
                        <div x-show="activeTab === 'usuarios'">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Usuario</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Email</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Rol</th>
                                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Estado</th>
                                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Fecha Asignación</th>
                                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($organizacion->usuarios as $usuario)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold mr-3">
                                                        {{ substr($usuario->nombre, 0, 1) }}
                                                    </div>
                                                    <span class="font-medium text-gray-800">{{ $usuario->nombre }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-secondary">{{ $usuario->email }}</td>
                                            <td class="py-3 px-4">
                                                @foreach($usuario->roles as $rol)
                                                    @if($rol->pivot->organizacion_id == $organizacion->id)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                                            {{ $rol->nombre }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                @if($usuario->pivot->estado == 'activo')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        Activo
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                        Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-center text-sm text-secondary">
                                                {{ $usuario->pivot->fecha_asignacion ? $usuario->pivot->fecha_asignacion->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <button class="text-primary hover:text-primary-dark p-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-secondary">
                                                No hay usuarios asignados a esta organización
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Contratos Tab -->
                        <div x-show="activeTab === 'contratos'">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Número</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Contratista</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Supervisor</th>
                                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Valor Total</th>
                                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Estado</th>
                                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Vigencia</th>
                                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($organizacion->contratos as $contrato)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <span class="font-mono text-sm font-semibold text-gray-800">{{ $contrato->numero_contrato }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                {{ $contrato->contratista ? $contrato->contratista->nombre : 'Sin asignar' }}
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                {{ $contrato->supervisor ? $contrato->supervisor->nombre : 'Sin asignar' }}
                                            </td>
                                            <td class="py-3 px-4 text-right font-semibold text-gray-800">
                                                ${{ number_format($contrato->valor_total, 0) }}
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                @if($contrato->estado == 'activo')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        Activo
                                                    </span>
                                                @elseif($contrato->estado == 'borrador')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                        Borrador
                                                    </span>
                                                @elseif($contrato->estado == 'terminado')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                        Terminado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-center text-sm text-secondary">
                                                {{ $contrato->fecha_inicio->format('d/m/Y') }} - {{ $contrato->fecha_fin->format('d/m/Y') }}
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <a href="{{ route('contratos.show', $contrato) }}" 
                                                   class="text-accent hover:text-primary p-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-secondary">
                                                No hay contratos registrados
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pendientes Tab -->
                        <div x-show="activeTab === 'pendientes'">
                            @if($organizacion->vinculacionesPendientes->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($organizacion->vinculacionesPendientes as $pendiente)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-full bg-warning/10 flex items-center justify-center text-warning font-bold mr-3">
                                                    {{ substr($pendiente->usuario->nombre, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-800">{{ $pendiente->usuario->nombre }}</h4>
                                                    <p class="text-sm text-secondary">{{ $pendiente->usuario->email }}</p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-secondary">
                                                {{ $pendiente->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        @if($pendiente->codigo_vinculacion_usado)
                                        <p class="text-xs text-secondary mb-3">
                                            <i class="fas fa-key mr-1"></i>
                                            Código usado: <code class="bg-white px-2 py-0.5 rounded">{{ $pendiente->codigo_vinculacion_usado }}</code>
                                        </p>
                                        @endif

                                        <div class="flex space-x-2">
                                            <a href="{{ route('usuarios.pendientes', ['organizacion_id' => $organizacion->id]) }}" 
                                               class="flex-1 bg-primary text-white text-center py-2 rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                                                <i class="fas fa-user-check mr-1"></i>
                                                Asignar Rol
                                            </a>
                                            <button class="px-4 py-2 border border-danger text-danger rounded-lg hover:bg-red-50 transition-colors">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                                    <p class="text-secondary font-medium">No hay vinculaciones pendientes</p>
                                    <p class="text-sm text-gray-400 mt-2">Todas las solicitudes han sido procesadas</p>
                                </div>
                            @endif
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
    function copiarCodigo(codigo) {
        navigator.clipboard.writeText(codigo).then(() => {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slideIn';
            toast.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Código copiado al portapapeles';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    }
</script>
@endpush
@endsection