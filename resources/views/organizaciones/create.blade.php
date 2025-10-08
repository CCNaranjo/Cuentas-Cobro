@extends('layouts.app-dashboard')

@section('title', 'Nueva Organización - ARCA-D')

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
                        <span class="text-gray-800">Nueva Organización</span>
                    </nav>
                </div>

                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-building text-primary mr-3"></i>
                        Nueva Organización
                    </h1>
                    <p class="text-secondary mt-1">Registra una nueva organización en el sistema</p>
                </div>

                <!-- Formulario -->
                <form action="{{ route('organizaciones.store') }}" method="POST" class="max-w-4xl">
                    @csrf

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-accent mr-2"></i>
                            Información General
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre Oficial -->
                            <div class="md:col-span-2">
                                <label for="nombre_oficial" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre Oficial <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="nombre_oficial" 
                                       name="nombre_oficial" 
                                       value="{{ old('nombre_oficial') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('nombre_oficial') border-danger @enderror"
                                       placeholder="Ej: Alcaldía Municipal de Ejemplo">
                                @error('nombre_oficial')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- NIT -->
                            <div>
                                <label for="nit" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIT <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="nit" 
                                       name="nit" 
                                       value="{{ old('nit') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('nit') border-danger @enderror"
                                       placeholder="123456789-0">
                                @error('nit')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Institucional -->
                            <div>
                                <label for="email_institucional" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Institucional <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       id="email_institucional" 
                                       name="email_institucional" 
                                       value="{{ old('email_institucional') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('email_institucional') border-danger @enderror"
                                       placeholder="contacto@organizacion.gov.co">
                                @error('email_institucional')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="telefono_contacto" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono de Contacto <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       id="telefono_contacto" 
                                       name="telefono_contacto" 
                                       value="{{ old('telefono_contacto') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('telefono_contacto') border-danger @enderror"
                                       placeholder="(601) 123-4567">
                                @error('telefono_contacto')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Departamento -->
                            <div>
                                <label for="departamento" class="block text-sm font-medium text-gray-700 mb-2">
                                    Departamento <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="departamento" 
                                       name="departamento" 
                                       value="{{ old('departamento') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('departamento') border-danger @enderror"
                                       placeholder="Cundinamarca">
                                @error('departamento')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Municipio -->
                            <div>
                                <label for="municipio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Municipio <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="municipio" 
                                       name="municipio" 
                                       value="{{ old('municipio') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('municipio') border-danger @enderror"
                                       placeholder="Bogotá">
                                @error('municipio')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dirección <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="direccion" 
                                       name="direccion" 
                                       value="{{ old('direccion') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('direccion') border-danger @enderror"
                                       placeholder="Calle 123 #45-67">
                                @error('direccion')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dominios de Email -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-at text-accent mr-2"></i>
                            Dominios de Email Autorizados
                        </h2>
                        <p class="text-sm text-secondary mb-4">
                            Los usuarios con estos dominios podrán registrarse automáticamente en esta organización
                        </p>

                        <div id="dominios-container">
                            <div class="dominio-item mb-3">
                                <div class="flex items-center space-x-2">
                                    <input type="text" 
                                           name="dominios_email[]" 
                                           placeholder="@organizacion.gov.co"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus"
                                           pattern="@[a-z0-9.-]+\.[a-z]{2,}$">
                                    <button type="button" 
                                            onclick="eliminarDominio(this)"
                                            class="text-danger hover:bg-red-50 p-3 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" 
                                onclick="agregarDominio()"
                                class="mt-3 text-accent hover:text-primary font-medium flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
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
                                class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-8 rounded-lg hover:shadow-lg transition-all flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Crear Organización
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
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus"
                       pattern="@[a-z0-9.-]+\\.[a-z]{2,}$">
                <button type="button" 
                        onclick="eliminarDominio(this)"
                        class="text-danger hover:bg-red-50 p-3 rounded-lg transition-colors">
                    <i class="fas fa-trash"></i>
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
</script>
@endpush
@endsection