@extends('layouts.app-dashboard')

@section('title', 'Organizaciones - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Organizaciones</h1>
                        <p class="text-secondary mt-1">Gestiona las organizaciones del sistema</p>
                    </div>
                    <a href="{{ route('organizaciones.create') }}" 
                       class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Nueva Organización
                    </a>
                </div>

                <!-- Alerta de Organización Actual -->
                @if(session('organizacion_actual'))
                    @php
                        $orgActual = \App\Models\Organizacion::find(session('organizacion_actual'));
                    @endphp
                    @if($orgActual)
                    <div class="flex items-center p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 rounded-lg rounded-l-none shadow-sm">
                        <i class="bi bi-info-circle text-2xl text-blue-500 mr-3"></i>
                        <div class="flex-grow">
                            <strong class="text-blue-900">Trabajando con:</strong> 
                            <span class="text-blue-700">{{ $orgActual->nombre_oficial }}</span>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('usuarios.index') }}" class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors text-sm">
                                <i class="bi bi-people mr-1.5"></i>Usuarios
                            </a>
                            <a href="{{ route('contratos.index') }}" class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors text-sm">
                                <i class="bi bi-file-earmark-text mr-1.5"></i>Contratos
                            </a>
                        </div>
                    </div>
                    @endif
                @endif

                <!-- Filtros -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <form method="GET" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-64">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Buscar por nombre, NIT..." 
                                   value="{{ request('search') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent">
                            <option value="">Todos los estados</option>
                            <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                            <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactivas</option>
                            <option value="suspendida" {{ request('estado') == 'suspendida' ? 'selected' : '' }}>Suspendidas</option>
                        </select>
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </form>
                </div>

                <!-- Tabla -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Organización</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">NIT</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Ubicación</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Código Vinculación</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-700">Usuarios</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-700">Contratos</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-700">Estado</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-700">Seleccionar</th>
                                <th class="text-right py-4 px-6 text-sm font-semibold text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($organizaciones as $org)
                            @php
                                $organizacionActual = session('organizacion_actual');
                                $esOrganizacionActual = $organizacionActual == $org->id;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $esOrganizacionActual ? 'bg-primary/5 border-l-4 border-primary' : '' }}">
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mr-3">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 flex items-center">
                                                {{ $org->nombre_oficial }}
                                                @if($esOrganizacionActual)
                                                <span class="ml-2 bg-primary text-white text-xs px-2 py-1 rounded-full flex items-center">
                                                    <i class="fas fa-check mr-1"></i> Actual
                                                </span>
                                                @endif
                                            </p>
                                            <p class="text-sm text-secondary">{{ $org->email_institucional }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-mono text-sm">{{ $org->nit }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div>
                                        <p class="text-sm text-gray-800">{{ $org->municipio }}</p>
                                        <p class="text-xs text-secondary">{{ $org->departamento }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <code class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{{ $org->codigo_vinculacion }}</code>
                                        <button onclick="copiarCodigo('{{ $org->codigo_vinculacion }}')" 
                                                class="text-accent hover:text-primary transition-colors p-1"
                                                title="Copiar código">
                                            <i class="fas fa-copy text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center justify-center bg-accent/10 text-accent px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ $org->usuarios_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center justify-center bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ $org->contratos_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($org->estado == 'activa')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Activa
                                        </span>
                                    @elseif($org->estado == 'inactiva')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactiva
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-warning/10 text-warning">
                                            <i class="fas fa-pause-circle mr-1"></i> Suspendida
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if(!$esOrganizacionActual)
                                    <form action="{{ route('organizaciones.seleccionar', $org) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-gradient-to-r from-accent to-cyan-500 text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all flex items-center text-sm"
                                                title="Seleccionar esta organización">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Seleccionar
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-sm text-primary font-semibold flex items-center justify-center">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Seleccionada
                                    </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('organizaciones.show', $org) }}" 
                                        class="text-accent hover:text-primary transition-colors p-2"
                                        title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organizaciones.edit', $org) }}" 
                                        class="text-primary hover:text-primary-dark transition-colors p-2"
                                        title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center">
                                    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-secondary font-medium">No hay organizaciones registradas</p>
                                    <p class="text-sm text-gray-400 mt-2">Crea tu primera organización</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-6">
                    {{ $organizaciones->links() }}
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    function copiarCodigo(codigo) {
        navigator.clipboard.writeText(codigo).then(() => {
            // Toast notification
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