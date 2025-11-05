@extends('layouts.app-dashboard')

@section('title', 'Editar organización - ' . $organizacion->nombre_oficial)

@section('content')
<div class="flex h-screen bg-gray-50 overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 max-w-4xl mx-auto">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex items-center space-x-2 text-sm text-gray-600">
                        <a href="{{ route('organizaciones.index') }}" class="hover:text-blue-600 transition-colors">Organizaciones</a>
                        <i class="bi bi-chevron-right text-xs"></i>
                        <a href="{{ route('organizaciones.show', $organizacion) }}" class="hover:text-blue-600 transition-colors">{{ $organizacion->nombre_oficial }}</a>
                        <i class="bi bi-chevron-right text-xs"></i>
                        <span class="text-gray-800">Editar</span>
                    </nav>
                </div>

                <div class="space-y-6">
                    <!-- Información de la Organización -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-building text-blue-600 mr-3"></i>
                            Editar Organización
                        </h1>

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="bi bi-exclamation-triangle text-red-500 mr-2"></i>
                                    <span class="text-red-700 font-medium">Errores en el formulario:</span>
                                </div>
                                <ul class="list-disc pl-5 mt-2 text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('organizaciones.update', $organizacion) }}">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre Oficial -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nombre oficial <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nombre_oficial" 
                                           value="{{ old('nombre_oficial', $organizacion->nombre_oficial) }}" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- NIT -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        NIT <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nit" 
                                           value="{{ old('nit', $organizacion->nit) }}" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Email Institucional -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email institucional <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           name="email_institucional" 
                                           value="{{ old('email_institucional', $organizacion->email_institucional) }}" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Departamento -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Departamento <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="departamento" 
                                           value="{{ old('departamento', $organizacion->departamento) }}" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Municipio -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Municipio <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="municipio" 
                                           value="{{ old('municipio', $organizacion->municipio) }}" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Dirección -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Dirección <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="direccion" 
                                           value="{{ old('direccion', $organizacion->direccion) }}" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Teléfono de contacto <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="telefono_contacto" 
                                           value="{{ old('telefono_contacto', $organizacion->telefono_contacto) }}" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Estado <span class="text-red-500">*</span>
                                    </label>
                                    <select name="estado" 
                                            required 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="activa" {{ old('estado', $organizacion->estado) == 'activa' ? 'selected' : '' }}>Activa</option>
                                        <option value="inactiva" {{ old('estado', $organizacion->estado) == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                                        <option value="suspendida" {{ old('estado', $organizacion->estado) == 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                                    </select>
                                </div>

                                <!-- Dominios de Email -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Dominios de email autorizados 
                                        <span class="text-xs text-gray-500">(uno por línea, con @)</span>
                                    </label>
                                    <textarea name="dominios_email[]" 
                                              rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="@dominio1.com&#10;@dominio2.com">{{ old('dominios_email', is_array($organizacion->dominios_email) ? implode("\n", $organizacion->dominios_email) : '') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end space-x-4">
                                <a href="{{ route('organizaciones.show', $organizacion) }}" 
                                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                    Cancelar
                                </a>
                                <button type="submit" 
                                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center">
                                    <i class="bi bi-save mr-2"></i>
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Modificar Administrador -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bi bi-person-gear text-green-600 mr-3"></i>
                            Gestión de Administrador
                        </h2>

                        @php
                            $adminActual = $organizacion->usuarios()
                                ->wherePivot('rol_id', function($query) use ($organizacion) {
                                    $query->select('id')
                                        ->from('roles')
                                        ->where('nombre', 'admin_organizacion')
                                        ->where('organizacion_id', $organizacion->id);
                                })
                                ->wherePivot('estado', 'activo')
                                ->first();
                            
                            $usuariosOrganizacion = $organizacion->usuarios()
                                ->wherePivot('estado', 'activo')
                                ->where('usuarios.id', '!=', $adminActual?->id)
                                ->get();
                        @endphp

                        @if($adminActual)
                            <!-- Formulario para actualizar información del administrador actual -->
                            <form action="{{ route('organizaciones.actualizar-admin', $organizacion) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <!-- Campo oculto para el ID del usuario -->
                                <input type="hidden" name="usuario_id" value="{{ $adminActual->id }}">
                                
                                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                                    <h3 class="font-semibold text-green-800 mb-4 flex items-center">
                                        <i class="bi bi-person-check mr-2"></i>
                                        Administrador Actual
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Nombre -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Nombre completo <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                name="nombre" 
                                                value="{{ old('nombre', $adminActual->nombre) }}" 
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Email <span class="text-red-500">*</span>
                                            </label>
                                            <input type="email" 
                                                name="email" 
                                                value="{{ old('email', $adminActual->email) }}" 
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>

                                        <!-- Teléfono -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Teléfono
                                            </label>
                                            <input type="tel" 
                                                name="telefono" 
                                                value="{{ old('telefono', $adminActual->telefono) }}" 
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="(601) 987-6543">
                                        </div>

                                        <!-- Documento de Identidad -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Documento de identidad
                                            </label>
                                            <input type="text" 
                                                name="documento_identidad" 
                                                value="{{ old('documento_identidad', $adminActual->documento_identidad) }}" 
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="1234567890">
                                        </div>

                                        <!-- Estado -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Estado <span class="text-red-500">*</span>
                                            </label>
                                            <select name="estado" 
                                                    required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="activo" {{ old('estado', $adminActual->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                                <option value="inactivo" {{ old('estado', $adminActual->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                                <option value="pendiente_verificacion" {{ old('estado', $adminActual->estado) == 'pendiente_verificacion' ? 'selected' : '' }}>Pendiente Verificación</option>
                                            </select>
                                        </div>

                                        <!-- Tipo de Vinculación -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Tipo de Vinculación <span class="text-red-500">*</span>
                                            </label>
                                            <select name="tipo_vinculacion" 
                                                    required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="organizacion" {{ old('tipo_vinculacion', $adminActual->tipo_vinculacion) == 'organizacion' ? 'selected' : '' }}>Organización</option>
                                                <option value="contratista" {{ old('tipo_vinculacion', $adminActual->tipo_vinculacion) == 'contratista' ? 'selected' : '' }}>Contratista</option>
                                                <option value="sin_vinculacion" {{ old('tipo_vinculacion', $adminActual->tipo_vinculacion) == 'sin_vinculacion' ? 'selected' : '' }}>Sin Vinculación</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Información de Rol -->
                                    <div class="mt-4 p-3 bg-white rounded border">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">Rol actual:</span>
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                    <i class="bi bi-shield-check mr-1"></i>
                                                    Administrador de Organización
                                                </span>
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                Último acceso: {{ $adminActual->ultimo_acceso ? $adminActual->ultimo_acceso->format('d/m/Y H:i') : 'Nunca' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-4">
                                    <button type="reset" 
                                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                        Restablecer
                                    </button>
                                    <button type="submit" 
                                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center">
                                        <i class="bi bi-person-check mr-2"></i>
                                        Actualizar Administrador
                                    </button>
                                </div>
                            </form>

                            <!-- Sección para cambiar de administrador -->
                            @if($usuariosOrganizacion->count() > 0)
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="bi bi-arrow-left-right text-blue-600 mr-2"></i>
                                    Cambiar Administrador
                                </h4>
                                
                                <form action="{{ route('organizaciones.cambiar-admin', $organizacion) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Seleccionar nuevo administrador
                                            </label>
                                            <select name="nuevo_admin_id" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Seleccione un usuario...</option>
                                                @foreach($usuariosOrganizacion as $usuario)
                                                <option value="{{ $usuario->id }}">
                                                    {{ $usuario->nombre }} ({{ $usuario->email }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <button type="submit" 
                                                    class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center">
                                                <i class="bi bi-arrow-left-right mr-2"></i>
                                                Cambiar
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        El usuario seleccionado será el nuevo administrador de la organización.
                                    </p>
                                </form>
                            </div>
                            @endif

                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <div class="flex items-center">
                                    <i class="bi bi-exclamation-triangle text-yellow-500 mr-3 text-xl"></i>
                                    <div>
                                        <h3 class="font-semibold text-yellow-800">Sin administrador asignado</h3>
                                        <p class="text-yellow-700 text-sm">Esta organización no tiene un administrador asignado.</p>
                                        <a href="{{ route('organizaciones.show', $organizacion) }}#usuarios" 
                                        class="inline-flex items-center mt-2 text-yellow-700 hover:text-yellow-800 font-medium">
                                            <i class="bi bi-arrow-right mr-1"></i>
                                            Asignar administrador desde la vista de detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Información del Código de Vinculación -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bi bi-key text-purple-600 mr-3"></i>
                            Código de Vinculación
                        </h2>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <code class="text-lg font-mono bg-white px-3 py-2 rounded border">{{ $organizacion->codigo_vinculacion }}</code>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Este código permite a los usuarios vincularse a la organización durante el registro.
                                    </p>
                                </div>
                                <button type="button" 
                                        onclick="copiarCodigo('{{ $organizacion->codigo_vinculacion }}')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="bi bi-clipboard mr-2"></i>
                                    Copiar
                                </button>
                            </div>
                        </div>
                    </div>
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

    // Restablecer formulario del administrador
    document.addEventListener('DOMContentLoaded', function() {
        const resetButton = document.querySelector('button[type="reset"]');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                setTimeout(() => {
                    // Mostrar mensaje de restablecimiento
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-4 right-4 z-50';
                    toast.innerHTML = `
                        <div class="bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center">
                            <i class="bi bi-arrow-clockwise mr-2"></i>
                            <span>Formulario restablecido</span>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                }, 100);
            });
        }
    });
</script>
@endpush
@endsection