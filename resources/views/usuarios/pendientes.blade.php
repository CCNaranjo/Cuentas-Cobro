@extends('layouts.app-dashboard')

@section('title', 'Usuarios Pendientes - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user-clock text-warning mr-3"></i>
                        Usuarios Pendientes
                    </h1>
                    <p class="text-secondary mt-1">Asigna roles a los usuarios que solicitaron vinculación</p>
                </div>

                <!-- DEBUG: Ver qué roles llegan -->
                <div class="hidden"> <!-- Oculta este div o quítalo después del debug -->
                    <h4>Roles Disponibles ({{ $roles->count() }})</h4>
                    @foreach($roles as $rol)
                        <p>
                            ID: {{ $rol->id }}, 
                            Nombre: {{ $rol->nombre }}, 
                            Org ID: {{ $rol->organizacion_id }},
                            Desc: {{ $rol->descripcion }}
                        </p>
                    @endforeach
                </div>

                <!-- Alert si hay pendientes -->
                @if($pendientes->count() > 0)
                <div class="bg-warning/10 border-l-4 border-warning rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-warning text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $pendientes->count() }} usuario(s) pendiente(s) de asignar rol</p>
                            <p class="text-sm text-secondary mt-1">Revisa y asigna roles a los nuevos usuarios</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Grid de usuarios pendientes -->
                @if($pendientes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($pendientes as $pendiente)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <!-- Header del Card -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white font-bold text-lg mr-3">
                                    {{ substr($pendiente->usuario->nombre, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $pendiente->usuario->nombre }}</h3>
                                    <p class="text-xs text-secondary">{{ $pendiente->usuario->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="space-y-2 mb-4">
                            @if($pendiente->usuario->documento_identidad)
                            <div class="flex items-center text-sm text-secondary">
                                <i class="fas fa-id-card w-5 text-accent"></i>
                                <span>{{ $pendiente->usuario->documento_identidad }}</span>
                            </div>
                            @endif

                            @if($pendiente->usuario->telefono)
                            <div class="flex items-center text-sm text-secondary">
                                <i class="fas fa-phone w-5 text-accent"></i>
                                <span>{{ $pendiente->usuario->telefono }}</span>
                            </div>
                            @endif

                            @if($pendiente->codigo_vinculacion_usado)
                            <div class="flex items-center text-sm text-secondary">
                                <i class="fas fa-key w-5 text-accent"></i>
                                <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $pendiente->codigo_vinculacion_usado }}</code>
                            </div>
                            @endif

                            <div class="flex items-center text-sm text-secondary">
                                <i class="fas fa-clock w-5 text-accent"></i>
                                <span>{{ $pendiente->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <!-- Formulario de asignación -->
                        <form action="{{ route('usuarios.asignar-rol') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="usuario_id" value="{{ $pendiente->usuario_id }}">
                            <input type="hidden" name="organizacion_id" value="{{ $pendiente->organizacion_id }}">
                            
                            <div>
                                <label for="rol_{{ $pendiente->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                    Seleccionar Rol <span class="text-danger">*</span>
                                </label>
                                <select id="rol_{{ $pendiente->id }}" 
                                        name="rol_id" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent text-sm">
                                    <option value="">Selecciona un rol...</option>
                                    @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">
                                        {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                        @if($rol->descripcion)
                                            - {{ $rol->descripcion }}
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="flex-1 bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all text-sm">
                                    <i class="fas fa-user-check mr-2"></i>
                                    Asignar Rol
                                </button>
                                <button type="button" 
                                    onclick="rechazarVinculacion('{{ $pendiente->id }}', '{{ addslashes($pendiente->usuario->nombre) }}')"
                                    class="px-4 py-2 border-2 border-danger text-danger rounded-lg hover:bg-red-50 transition-colors"
                                    title="Rechazar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Estado vacío -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
                    <div class="text-center">
                        <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">¡Todo al día!</h3>
                        <p class="text-secondary">No hay usuarios pendientes de asignar rol</p>
                        <p class="text-sm text-gray-400 mt-2">Los nuevos usuarios aparecerán aquí cuando soliciten vinculación</p>
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</div>

<!-- Modal de Rechazo -->
<div id="modal-rechazo" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                Rechazar Vinculación
            </h3>
            <button onclick="cerrarModalRechazo()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <p class="text-secondary mb-4">
            ¿Estás seguro que deseas rechazar la vinculación de <strong id="nombre-usuario-rechazo"></strong>?
        </p>

        <form id="form-rechazo" method="POST">
            @csrf
            <div class="mb-4">
                <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo del rechazo <span class="text-danger">*</span>
                </label>
                <textarea id="motivo" 
                          name="motivo" 
                          rows="3" 
                          required
                          maxlength="500"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent resize-none"
                          placeholder="Explica el motivo del rechazo..."></textarea>
                <p class="text-xs text-secondary mt-1">Máximo 500 caracteres</p>
            </div>

            <div class="flex space-x-3">
                <button type="button" 
                        onclick="cerrarModalRechazo()"
                        class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 bg-danger text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-ban mr-2"></i>
                    Rechazar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function rechazarVinculacion(vinculacionId, nombreUsuario) {
        const modal = document.getElementById('modal-rechazo');
        const form = document.getElementById('form-rechazo');
        const nombreSpan = document.getElementById('nombre-usuario-rechazo');
        
        nombreSpan.textContent = nombreUsuario;
        form.action = `/usuarios/${vinculacionId}/rechazar`;
        
        modal.classList.remove('hidden');
    }

    function cerrarModalRechazo() {
        const modal = document.getElementById('modal-rechazo');
        const form = document.getElementById('form-rechazo');
        
        modal.classList.add('hidden');
        form.reset();
    }

    // Cerrar modal al presionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalRechazo();
        }
    });

    // Cerrar modal al hacer click fuera
    document.getElementById('modal-rechazo').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalRechazo();
        }
    });
</script>
@endpush
@endsection