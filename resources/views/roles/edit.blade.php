@extends('layouts.app-dashboard')

@section('title', 'Editar Rol - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Header Section -->
                <div class="mb-6 animate-slideIn">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-edit text-warning mr-3"></i>
                                Editar Rol: {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                            </h1>
                            <p class="text-secondary mt-1">
                                Modifica la información y permisos del rol
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('roles.show', $rol) }}" 
                               class="bg-gradient-to-r from-info to-info-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                Ver Detalles
                            </a>
                            <a href="{{ route('roles.index') }}" 
                               class="bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver
                            </a>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 animate-fadeIn">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-red-800 font-semibold">Error en el formulario</h3>
                            <ul class="text-red-600 text-sm mt-1">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 animate-fadeIn">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-green-800 font-semibold">¡Éxito!</h3>
                            <p class="text-green-600 text-sm mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Alerta de Usuarios Asignados -->
                @if($rol->usuarios->count() > 0)
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 animate-fadeIn">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-blue-800 font-semibold">Información Importante</h3>
                            <p class="text-blue-600 text-sm mt-1">
                                Este rol tiene <strong>{{ $rol->usuarios->count() }} usuario(s)</strong> asignado(s). 
                                Los cambios que realices afectarán inmediatamente a todos los usuarios con este rol.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('roles.update', $rol) }}" method="POST" id="editRoleForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Información Básica -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    Información del Rol
                                </h2>

                                <!-- ID del Rol -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-600 mb-1">ID del Rol</label>
                                    <div class="flex items-center">
                                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-mono">
                                            #{{ $rol->id }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Nombre del Rol -->
                                <div class="mb-4">
                                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nombre del Rol <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="nombre"
                                           name="nombre" 
                                           value="{{ old('nombre', $rol->nombre) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                           placeholder="Ej: coordinador_contratos"
                                           required
                                           pattern="[a-z_]+"
                                           title="Solo letras minúsculas y guiones bajos">
                                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Solo letras minúsculas y guiones bajos
                                    </p>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Descripción <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="descripcion"
                                              name="descripcion" 
                                              rows="4"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                              placeholder="Describe las funciones y responsabilidades de este rol..."
                                              required>{{ old('descripcion', $rol->descripcion) }}</textarea>
                                </div>

                                <!-- Nivel Jerárquico -->
                                <div class="mb-6">
                                    <label for="nivel_jerarquico" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nivel Jerárquico <span class="text-red-500">*</span>
                                    </label>
                                    <select id="nivel_jerarquico"
                                            name="nivel_jerarquico" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                            required>
                                        <option value="">Seleccione un nivel</option>
                                        <option value="1" {{ old('nivel_jerarquico', $rol->nivel_jerarquico) == 1 ? 'selected' : '' }}>Nivel 1 - Administración Global</option>
                                        <option value="2" {{ old('nivel_jerarquico', $rol->nivel_jerarquico) == 2 ? 'selected' : '' }}>Nivel 2 - Administración Organización</option>
                                        <option value="3" {{ old('nivel_jerarquico', $rol->nivel_jerarquico) == 3 ? 'selected' : '' }}>Nivel 3 - Gestión Operativa</option>
                                        <option value="4" {{ old('nivel_jerarquico', $rol->nivel_jerarquico) == 4 ? 'selected' : '' }}>Nivel 4 - Usuarios Especializados</option>
                                        <option value="5" {{ old('nivel_jerarquico', $rol->nivel_jerarquico) == 5 ? 'selected' : '' }}>Nivel 5 - Usuarios Básicos</option>
                                    </select>
                                </div>

                                <!-- Estadísticas -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-chart-pie mr-2"></i>
                                        Resumen de Permisos
                                    </h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Permisos seleccionados:</span>
                                            <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-full" id="selectedCount">
                                                {{ $rol->permisos->count() }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Módulos con permisos:</span>
                                            <span class="bg-accent text-white text-xs font-bold px-2 py-1 rounded-full" id="modulesCount">
                                                {{ $rol->permisos->groupBy('modulo_id')->count() }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Usuarios afectados:</span>
                                            <span class="bg-warning text-white text-xs font-bold px-2 py-1 rounded-full">
                                                {{ $rol->usuarios->count() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fechas -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Creado</label>
                                            <p class="text-gray-700">{{ $rol->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Actualizado</label>
                                            <p class="text-gray-700">{{ $rol->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permisos por Módulos -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-key text-primary mr-2"></i>
                                        Gestionar Permisos
                                    </h2>
                                    <div class="flex space-x-2">
                                        <button type="button" 
                                                onclick="selectAllPermissions()"
                                                class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition-colors flex items-center">
                                            <i class="fas fa-check-square mr-2"></i>
                                            Seleccionar Todo
                                        </button>
                                        <button type="button" 
                                                onclick="clearAllPermissions()"
                                                class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-600 transition-colors flex items-center">
                                            <i class="fas fa-square mr-2"></i>
                                            Limpiar Todo
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    @foreach($modulos as $modulo)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-semibold text-gray-800 flex items-center">
                                                    <i class="fas fa-{{ $modulo->icono ?? 'cube' }} text-primary mr-2"></i>
                                                    {{ $modulo->nombre }}
                                                    <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs module-count" 
                                                          id="count-modulo-{{ $modulo->id }}">
                                                        {{ $rol->permisos->where('modulo_id', $modulo->id)->count() }} seleccionados
                                                    </span>
                                                </h3>
                                                <button type="button" 
                                                        onclick="toggleModulePermissions('modulo-{{ $modulo->id }}')"
                                                        class="text-primary hover:text-primary-dark text-sm font-semibold flex items-center">
                                                    <i class="fas fa-sync-alt mr-1"></i>
                                                    Alternar módulo
                                                </button>
                                            </div>
                                            @if($modulo->descripcion)
                                            <p class="text-sm text-gray-600 mt-1">{{ $modulo->descripcion }}</p>
                                            @endif
                                        </div>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3" id="modulo-{{ $modulo->id }}">
                                            @foreach($modulo->permisos as $permiso)
                                            <label class="flex items-start space-x-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                                                <input type="checkbox" 
                                                       name="permisos[]" 
                                                       value="{{ $permiso->id }}"
                                                       {{ in_array($permiso->id, old('permisos', $rol->permisos->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       class="mt-1 text-primary focus:ring-primary border-gray-300 rounded permission-checkbox module-{{ $modulo->id }}"
                                                       onchange="updatePermissionCount()"
                                                       data-modulo="{{ $modulo->id }}">
                                                <div class="flex-1">
                                                    <span class="block text-sm font-medium text-gray-800">{{ $permiso->nombre }}</span>
                                                    <span class="block text-xs text-gray-500 mt-1">{{ $permiso->descripcion }}</span>
                                                    <span class="block text-xs text-primary font-mono mt-1">{{ $permiso->slug }}</span>
                                                </div>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Botones de Acción -->
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm text-gray-600 flex items-center">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Los cambios afectarán a {{ $rol->usuarios->count() }} usuario(s) inmediatamente
                                        </div>
                                        <div class="flex space-x-3">
                                            <a href="{{ route('roles.show', $rol) }}" 
                                               class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors flex items-center">
                                                <i class="fas fa-times mr-2"></i>
                                                Cancelar
                                            </a>
                                            <button type="submit" 
                                                    class="bg-gradient-to-r from-warning to-warning-dark text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all flex items-center">
                                                <i class="fas fa-save mr-2"></i>
                                                Actualizar Rol
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    function updatePermissionCount() {
        const selectedPermissions = document.querySelectorAll('input[name="permisos[]"]:checked').length;
        const selectedModules = new Set();
        
        // Actualizar contadores por módulo
        @foreach($modulos as $modulo)
        const module{{ $modulo->id }}Count = document.querySelectorAll('.module-{{ $modulo->id }}:checked').length;
        const module{{ $modulo->id }}Element = document.getElementById('count-modulo-{{ $modulo->id }}');
        if (module{{ $modulo->id }}Element) {
            module{{ $modulo->id }}Element.textContent = module{{ $modulo->id }}Count + ' seleccionados';
        }
        
        if (module{{ $modulo->id }}Count > 0) {
            selectedModules.add({{ $modulo->id }});
        }
        @endforeach
        
        document.getElementById('selectedCount').textContent = selectedPermissions;
        document.getElementById('modulesCount').textContent = selectedModules.size;
    }

    function selectAllPermissions() {
        document.querySelectorAll('input[name="permisos[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
        updatePermissionCount();
    }

    function clearAllPermissions() {
        document.querySelectorAll('input[name="permisos[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        updatePermissionCount();
    }

    function toggleModulePermissions(moduleId) {
        const moduleDiv = document.getElementById(moduleId);
        const checkboxes = moduleDiv.querySelectorAll('input[name="permisos[]"]');
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        updatePermissionCount();
    }

    // Validación del formato del nombre
    document.getElementById('nombre').addEventListener('input', function(e) {
        this.value = this.value.toLowerCase().replace(/[^a-z_]/g, '');
    });

    // Confirmación antes de enviar el formulario
    document.getElementById('editRoleForm').addEventListener('submit', function(e) {
        const userCount = {{ $rol->usuarios->count() }};
        if (userCount > 0) {
            if (!confirm(`⚠️ Este rol tiene ${userCount} usuario(s) asignado(s). Los cambios afectarán inmediatamente a todos ellos. ¿Continuar?`)) {
                e.preventDefault();
                return false;
            }
        }
        
        const selectedPermissions = document.querySelectorAll('input[name="permisos[]"]:checked').length;
        if (selectedPermissions === 0) {
            if (!confirm('⚠️ Está a punto de quitar todos los permisos al rol. ¿Está seguro de continuar?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Inicializar contador
    document.addEventListener('DOMContentLoaded', function() {
        updatePermissionCount();
        
        // Animación de entrada para las tarjetas
        const cards = document.querySelectorAll('.bg-white');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.4s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endpush
@endsection