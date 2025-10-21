@extends('layouts.app-dashboard')

@section('title', 'Nueva Organización - ARCA-D')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex items-center space-x-2 text-sm text-gray-600">
                        <a href="{{ route('organizaciones.index') }}" class="hover:text-blue-600 transition-colors">Organizaciones</a>
                        <i class="bi bi-chevron-right text-xs"></i>
                        <span class="text-gray-800">Nueva Organización</span>
                    </nav>
                </div>

                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="bi bi-building text-blue-600 mr-3"></i>
                        Nueva Organización
                    </h1>
                    <p class="text-gray-600 mt-1">Registra una nueva organización en el sistema</p>
                </div>

                <!-- Formulario -->
                <form action="{{ route('organizaciones.store') }}" method="POST" class="max-w-4xl">
                    @csrf

                    <!-- Información de la Organización -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bi bi-info-circle text-blue-500 mr-2"></i>
                            Información de la Organización
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre Oficial -->
                            <div class="md:col-span-2">
                                <label for="nombre_oficial" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre Oficial <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nombre_oficial" 
                                       name="nombre_oficial" 
                                       value="{{ old('nombre_oficial') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nombre_oficial') border-red-500 @enderror"
                                       placeholder="Ej: Alcaldía Municipal de Ejemplo">
                                @error('nombre_oficial')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- NIT -->
                            <div>
                                <label for="nit" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIT <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nit" 
                                       name="nit" 
                                       value="{{ old('nit') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nit') border-red-500 @enderror"
                                       placeholder="123456789-0">
                                @error('nit')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Institucional -->
                            <div>
                                <label for="email_institucional" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Institucional <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       id="email_institucional" 
                                       name="email_institucional" 
                                       value="{{ old('email_institucional') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email_institucional') border-red-500 @enderror"
                                       placeholder="contacto@organizacion.gov.co">
                                @error('email_institucional')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="telefono_contacto" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono de Contacto <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       id="telefono_contacto" 
                                       name="telefono_contacto" 
                                       value="{{ old('telefono_contacto') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telefono_contacto') border-red-500 @enderror"
                                       placeholder="(601) 123-4567">
                                @error('telefono_contacto')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Departamento -->
                            <div>
                                <label for="departamento" class="block text-sm font-medium text-gray-700 mb-2">
                                    Departamento <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="departamento" 
                                       name="departamento" 
                                       value="{{ old('departamento') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('departamento') border-red-500 @enderror"
                                       placeholder="Cundinamarca">
                                @error('departamento')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Municipio -->
                            <div>
                                <label for="municipio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Municipio <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="municipio" 
                                       name="municipio" 
                                       value="{{ old('municipio') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('municipio') border-red-500 @enderror"
                                       placeholder="Bogotá">
                                @error('municipio')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dirección <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="direccion" 
                                       name="direccion" 
                                       value="{{ old('direccion') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('direccion') border-red-500 @enderror"
                                       placeholder="Calle 123 #45-67">
                                @error('direccion')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Administrador de la Organización -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bi bi-person-badge text-green-600 mr-2"></i>
                            Administrador de la Organización
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">
                            Crea la cuenta del administrador que gestionará esta organización
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre del Administrador -->
                            <div class="md:col-span-2">
                                <label for="admin_nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre Completo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="admin_nombre" 
                                       name="admin_nombre" 
                                       value="{{ old('admin_nombre') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_nombre') border-red-500 @enderror"
                                       placeholder="Ej: Juan Pérez García">
                                @error('admin_nombre')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email del Administrador -->
                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       id="admin_email" 
                                       name="admin_email" 
                                       value="{{ old('admin_email') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_email') border-red-500 @enderror"
                                       placeholder="admin@organizacion.gov.co">
                                @error('admin_email')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Documento de Identidad -->
                            <div>
                                <label for="admin_documento" class="block text-sm font-medium text-gray-700 mb-2">
                                    Documento de Identidad
                                </label>
                                <input type="text" 
                                       id="admin_documento" 
                                       name="admin_documento" 
                                       value="{{ old('admin_documento') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                            @error('admin_documento') border-red-500 @enderror"
                                       placeholder="1234567890">
                                @error('admin_documento')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Teléfono del Administrador -->
                            <div>
                                <label for="admin_telefono" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono
                                </label>
                                <input type="tel" 
                                       id="admin_telefono" 
                                       name="admin_telefono" 
                                       value="{{ old('admin_telefono') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_telefono') border-red-500 @enderror"
                                       placeholder="(601) 987-6543">
                                @error('admin_telefono')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div>
                                <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       id="admin_password" 
                                       name="admin_password" 
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_password') border-red-500 @enderror"
                                       placeholder="Mínimo 8 caracteres">
                                @error('admin_password')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div>
                                <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       id="admin_password_confirmation" 
                                       name="admin_password_confirmation" 
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Repite la contraseña">
                            </div>
                        </div>
                    </div>

                    <!-- Dominios de Email -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                            <i class="bi bi-at text-blue-500 mr-2"></i>
                            Dominios de Email Autorizados
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">
                            Los usuarios con estos dominios podrán registrarse automáticamente en esta organización
                        </p>

                        <div id="dominios-container">
                            <div class="dominio-item mb-3">
                                <div class="flex items-center space-x-2">
                                    <input type="text" 
                                           name="dominios_email[]" 
                                           placeholder="@organizacion.gov.co"
                                           value="{{ old('dominios_email.0') }}"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           pattern="@[a-z0-9.-]+\.[a-z]{2,}$">
                                    <button type="button" 
                                            onclick="eliminarDominio(this)"
                                            class="text-red-600 hover:bg-red-50 p-3 rounded-lg transition-colors">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" 
                                onclick="agregarDominio()"
                                class="mt-3 text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors">
                            <i class="bi bi-plus-circle mr-2"></i>
                            Agregar otro dominio
                        </button>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('organizaciones.index') }}" 
                           class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg hover:shadow-lg transition-all flex items-center">
                            <i class="bi bi-save mr-2"></i>
                            Crear Organización y Administrador
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    function agregarDominio() {
        const container = document.getElementById('dominios-container');
        const newItem = document.createElement('div');
        newItem.className = 'dominio-item mb-3';
        newItem.innerHTML = `
            <div class="flex items-center space-x-2">
                <input type="text" 
                       name="dominios_email[]" 
                       placeholder="@organizacion.gov.co"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       pattern="@[a-z0-9.-]+\\.[a-z]{2,}$">
                <button type="button" 
                        onclick="eliminarDominio(this)"
                        class="text-red-600 hover:bg-red-50 p-3 rounded-lg transition-colors">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newItem);
    }

    function eliminarDominio(button) {
        const container = document.getElementById('dominios-container');
        if (container.children.length > 1) {
            button.closest('.dominio-item').remove();
        } else {
            alert('Debe mantener al menos un dominio');
        }
    }

    // Validación de contraseña en tiempo real
    document.getElementById('admin_password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('admin_password').value;
        const confirmPassword = this.value;
        
        if (password !== confirmPassword) {
            this.classList.add('border-red-500');
            this.classList.remove('border-green-500');
        } else {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        }
    });
</script>
@endpush
@endsection