@extends('layouts.app-dashboard')

@section('title', 'Gestión de Permisos - ARCA-D')

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
                                <i class="fas fa-key text-primary mr-3"></i>
                                Gestión de Permisos
                            </h1>
                            <p class="text-secondary mt-1">
                                Administra los permisos del sistema organizados por módulos
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="toggleCreateForm()" 
                                    class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Nuevo Permiso
                            </button>
                            <a href="{{ route('roles.index') }}" 
                               class="bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver a Roles
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Alerta de Permiso Especial -->
                <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 flex items-start">
                    <i class="fas fa-shield-alt text-amber-500 text-2xl mr-3"></i>
                    <div>
                        <h6 class="font-semibold text-amber-800 mb-1">Acceso Restringido</h6>
                        <p class="text-sm text-amber-700">
                            Solo usuarios con el permiso <code class="bg-amber-100 px-2 py-1 rounded">ver-permisos</code> 
                            (Administradores Globales) pueden ver y gestionar permisos del sistema.
                        </p>
                    </div>
                </div>

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

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-key text-primary text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Total Permisos</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalPermisos }}</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cubes text-accent text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Módulos</h3>
                        <p class="text-3xl font-bold text-accent">{{ $modulos->count() }}</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users-cog text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Roles con Permisos</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $rolesConPermisos }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-layer-group text-white text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-white/80 text-sm font-medium mb-1">Permisos por Nivel</h3>
                        <p class="text-3xl font-bold">{{ number_format($totalPermisos / 5, 0) }}</p>
                    </div>
                </div>

                <!-- Formulario Crear Permiso (Oculto por defecto) -->
                <div id="createForm" class="hidden mb-6 animate-slideIn">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                Crear Nuevo Permiso
                            </h2>
                            <button onclick="toggleCreateForm()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
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

                        <form action="{{ route('permisos.store') }}" method="POST" id="createPermisoForm">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nombre del Permiso <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="nombre"
                                           name="nombre" 
                                           value="{{ old('nombre') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                           placeholder="Ej: Ver Contratos"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Nombre descriptivo del permiso</p>
                                </div>

                                <div>
                                    <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Slug <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="slug"
                                           name="slug" 
                                           value="{{ old('slug') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors font-mono"
                                           placeholder="ver-contratos"
                                           pattern="[a-z\-]+"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Solo letras minúsculas y guiones</p>
                                </div>

                                <div>
                                    <label for="modulo_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Módulo <span class="text-red-500">*</span>
                                    </label>
                                    <select id="modulo_id"
                                            name="modulo_id" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                            required>
                                        <option value="">Seleccione un módulo</option>
                                        @foreach($modulos as $modulo)
                                        <option value="{{ $modulo->id }}" {{ old('modulo_id') == $modulo->id ? 'selected' : '' }}>
                                            {{ $modulo->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="tipo" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Tipo de Permiso <span class="text-red-500">*</span>
                                    </label>
                                    <select id="tipo"
                                            name="tipo" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                            required>
                                        <option value="lectura" {{ old('tipo') == 'lectura' ? 'selected' : '' }}>
                                            <i class="fas fa-eye"></i> Lectura
                                        </option>
                                        <option value="escritura" {{ old('tipo') == 'escritura' ? 'selected' : '' }}>
                                            <i class="fas fa-edit"></i> Escritura
                                        </option>
                                        <option value="eliminacion" {{ old('tipo') == 'eliminacion' ? 'selected' : '' }}>
                                            <i class="fas fa-trash"></i> Eliminación
                                        </option>
                                        <option value="accion" {{ old('tipo') == 'accion' ? 'selected' : '' }}>
                                            <i class="fas fa-bolt"></i> Acción
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Categoría del permiso</p>
                                </div>

                                <div>
                                    <label for="es_organizacion" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Alcance del Permiso <span class="text-red-500">*</span>
                                    </label>
                                    <select id="es_organizacion"
                                            name="es_organizacion" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                            required>
                                        <option value="">Seleccione el alcance</option>
                                        <option value="0" {{ old('es_organizacion') === '0' ? 'selected' : '' }}>
                                            🔒 Sistema (Solo Admin Global)
                                        </option>
                                        <option value="1" {{ old('es_organizacion') === '1' ? 'selected' : '' }}>
                                            🏢 Organización (Admin Org y superiores)
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Define quién puede asignar este permiso a roles
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Descripción
                                    </label>
                                    <textarea id="descripcion"
                                              name="descripcion" 
                                              rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                              placeholder="Describe para qué sirve este permiso...">{{ old('descripcion') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" 
                                        onclick="toggleCreateForm()"
                                        class="bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="bg-gradient-to-r from-primary to-primary-dark text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all flex items-center">
                                    <i class="fas fa-save mr-2"></i>
                                    Crear Permiso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Permisos por Módulo -->
                <div class="space-y-6">
                    @foreach($modulos as $modulo)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-{{ $modulo->icono ?? 'cube' }} text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $modulo->nombre }}</h3>
                                        @if($modulo->descripcion)
                                        <p class="text-sm text-gray-600">{{ $modulo->descripcion }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ $modulo->permisos->count() }} permisos
                                </span>
                            </div>
                        </div>

                        @if($modulo->permisos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left py-3 px-6 text-sm font-semibold text-gray-700">Nombre</th>
                                        <th class="text-left py-3 px-6 text-sm font-semibold text-gray-700">Slug</th>
                                        <th class="text-left py-3 px-6 text-sm font-semibold text-gray-700">Descripción</th>
                                        <th class="text-center py-3 px-6 text-sm font-semibold text-gray-700">Tipo</th>
                                        <th class="text-center py-3 px-6 text-sm font-semibold text-gray-700">Roles Asignados</th>
                                        <th class="text-right py-3 px-6 text-sm font-semibold text-gray-700">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($modulo->permisos as $permiso)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <span class="font-semibold text-gray-800">{{ $permiso->nombre }}</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <code class="bg-gray-100 text-primary px-2 py-1 rounded text-sm font-mono">
                                                {{ $permiso->slug }}
                                            </code>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-sm text-gray-600">{{ $permiso->descripcion ?? 'Sin descripción' }}</span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @php
                                                $tipoBadges = [
                                                    'lectura' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'eye'],
                                                    'escritura' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'edit'],
                                                    'eliminacion' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'trash'],
                                                    'accion' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'bolt'],
                                                ];
                                                $badge = $tipoBadges[$permiso->tipo ?? 'lectura'] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'tag'];
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                                <i class="fas fa-{{ $badge['icon'] }} mr-1"></i>
                                                {{ ucfirst($permiso->tipo ?? 'lectura') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-flex items-center bg-accent/10 text-accent px-3 py-1 rounded-full text-sm font-semibold">
                                                {{ $permiso->roles->count() }} roles
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button onclick="editPermiso('{{ $permiso->id }}', '{{ addslashes($permiso->nombre) }}', '{{ $permiso->slug }}', {{ $permiso->modulo_id }}, '{{ $permiso->tipo ?? 'lectura' }}', '{{ addslashes($permiso->descripcion ?? '') }}')"
                                                        class="text-primary hover:text-primary-dark transition-colors p-2"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if($permiso->roles->count() == 0)
                                                <form action="{{ route('permisos.destroy', $permiso) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este permiso?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 transition-colors p-2"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @else
                                                <span class="text-gray-400 p-2" title="No se puede eliminar (tiene roles asignados)">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-key text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">No hay permisos en este módulo</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Editar Permiso -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-edit text-warning mr-2"></i>
                Editar Permiso
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-4">
                <input type="hidden" id="edit_permiso_id" name="permiso_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombre del Permiso <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="edit_nombre"
                               name="nombre" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="edit_slug"
                               name="slug" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary font-mono"
                               pattern="[a-z\-]+"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Módulo <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_modulo_id"
                                name="modulo_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                required>
                            @foreach($modulos as $modulo)
                            <option value="{{ $modulo->id }}">{{ $modulo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tipo de Permiso <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_tipo"
                                name="tipo" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                required>
                            <option value="lectura">Lectura</option>
                            <option value="escritura">Escritura</option>
                            <option value="eliminacion">Eliminación</option>
                            <option value="accion">Acción</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea id="edit_descripcion"
                                  name="descripcion" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"></textarea>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" 
                        onclick="closeEditModal()"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="bg-gradient-to-r from-warning to-amber-600 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleCreateForm() {
        const form = document.getElementById('createForm');
        form.classList.toggle('hidden');
        
        if (!form.classList.contains('hidden')) {
            document.getElementById('nombre').focus();
        }
    }

    // Auto-generar slug desde el nombre
    document.getElementById('nombre')?.addEventListener('input', function(e) {
        const slug = this.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Eliminar acentos
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        document.getElementById('slug').value = slug;
    });

    function editPermiso(id, nombre, slug, moduloId, tipo, descripcion) {
        document.getElementById('edit_permiso_id').value = id;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_slug').value = slug;
        document.getElementById('edit_modulo_id').value = moduloId;
        document.getElementById('edit_tipo').value = tipo;
        document.getElementById('edit_descripcion').value = descripcion;
        
        const form = document.getElementById('editForm');
        form.action = `/permisos/${id}`;
        
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    });

    // Mostrar form si hay errores
    @if($errors->any())
    document.getElementById('createForm').classList.remove('hidden');
    @endif

    // Validación del slug en tiempo real
    document.getElementById('slug')?.addEventListener('input', function(e) {
        this.value = this.value.toLowerCase().replace(/[^a-z\-]/g, '');
    });

    document.getElementById('edit_slug')?.addEventListener('input', function(e) {
        this.value = this.value.toLowerCase().replace(/[^a-z\-]/g, '');
    });
</script>
@endpush
@endsection