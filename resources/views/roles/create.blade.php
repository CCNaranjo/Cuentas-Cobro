@extends('layouts.app-dashboard')

@section('title', 'Crear Nuevo Rol - ARCA-D')

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
                                <i class="fas fa-plus-circle text-primary mr-3"></i>
                                Crear Nuevo Rol
                            </h1>
                            <p class="text-secondary mt-1">
                                Define un nuevo rol con sus permisos correspondientes
                            </p>
                        </div>
                        <a href="{{ route('roles.index') }}" 
                           class="bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver
                        </a>
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

                <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Información Básica -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    Información Básica
                                </h2>

                                <!-- Nombre del Rol -->
                                <div class="mb-4">
                                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nombre del Rol <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="nombre"
                                           name="nombre" 
                                           value="{{ old('nombre') }}"
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
                                              required>{{ old('descripcion') }}</textarea>
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
                                        <option value="1" {{ old('nivel_jerarquico') == 1 ? 'selected' : '' }}>Nivel 1 - Administración Global</option>
                                        <option value="2" {{ old('nivel_jerarquico') == 2 ? 'selected' : '' }}>Nivel 2 - Administración Organización</option>
                                        <option value="3" {{ old('nivel_jerarquico') == 3 ? 'selected' : '' }}>Nivel 3 - Gestión Operativa</option>
                                        <option value="4" {{ old('nivel_jerarquico') == 4 ? 'selected' : '' }}>Nivel 4 - Usuarios Especializados</option>
                                        <option value="5" {{ old('nivel_jerarquico') == 5 ? 'selected' : '' }}>Nivel 5 - Usuarios Básicos</option>
                                    </select>
                                </div>

                                <!-- Resumen de Permisos -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-chart-pie mr-2"></i>
                                        Resumen de Permisos
                                    </h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Permisos seleccionados:</span>
                                            <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-full" id="selectedCount">0</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Módulos con permisos:</span>
                                            <span class="bg-accent text-white text-xs font-bold px-2 py-1 rounded-full" id="modulesCount">0</span>
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
                                        Asignar Permisos
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
                                                </h3>
                                                <button type="button" 
                                                        onclick="toggleModulePermissions('modulo-{{ $modulo->id }}')"
                                                        class="text-primary hover:text-primary-dark text-sm font-semibold flex items-center">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Seleccionar módulo
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
                                                       class="mt-1 text-primary focus:ring-primary border-gray-300 rounded permission-checkbox"
                                                       onchange="updatePermissionCount()">
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
                                            Los campos marcados con <span class="text-red-500">*</span> son obligatorios
                                        </div>
                                        <div class="flex space-x-3">
                                            <a href="{{ route('roles.index') }}" 
                                               class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors flex items-center">
                                                <i class="fas fa-times mr-2"></i>
                                                Cancelar
                                            </a>
                                            <button type="submit" 
                                                    class="bg-gradient-to-r from-primary to-primary-dark text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all flex items-center">
                                                <i class="fas fa-save mr-2"></i>
                                                Crear Rol
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
        
        document.querySelectorAll('input[name="permisos[]"]:checked').forEach(checkbox => {
            const moduleDiv = checkbox.closest('[id^="modulo-"]');
            if (moduleDiv) {
                selectedModules.add(moduleDiv.id);
            }
        });
        
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

    // Inicializar contador
    document.addEventListener('DOMContentLoaded', function() {
        updatePermissionCount();
    });
</script>
@endpush
@endsection