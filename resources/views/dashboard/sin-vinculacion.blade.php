@extends('layouts.app-dashboard')

@section('title', 'Bienvenido a ARCA-D')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">
    @include('partials.header')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Hero Section -->
            <div class="bg-gradient-to-br from-primary to-accent rounded-xl shadow-lg text-white text-center p-12 mb-6">
                <i class="bi bi-shield-check text-8xl opacity-30 mb-6"></i>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">¡Bienvenido a ARCA-D!</h1>
                <p class="text-xl md:text-2xl mb-6 font-light">Sistema de Administración y Registro de Contratos y Cuentas</p>
                <p class="opacity-90 flex items-center justify-center">
                    <i class="bi bi-info-circle mr-2"></i>
                    Para comenzar, vincula tu cuenta a una organización
                </p>
            </div>

            @if($vinculacionesPendientes->count() > 0)
            <!-- Vinculaciones Pendientes -->
            <div class="bg-white rounded-xl shadow-md mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h5 class="text-xl font-semibold text-warning flex items-center">
                        <i class="bi bi-clock-history mr-2"></i>Vinculaciones Pendientes
                    </h5>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($vinculacionesPendientes as $vinculacion)
                    <div class="bg-yellow-50 border-l-4 border-warning rounded-lg p-4 flex items-start">
                        <i class="bi bi-hourglass-split text-warning text-3xl mr-4"></i>
                        <div class="flex-1">
                            <h6 class="font-semibold text-gray-800 mb-1">Solicitud en revisión</h6>
                            <p class="text-sm text-gray-600">
                                {{ $vinculacion->organizacion->nombre_oficial }} - Enviada {{ $vinculacion->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                    <p class="text-gray-500 text-sm flex items-start mt-4">
                        <i class="bi bi-info-circle mr-2 mt-0.5"></i>
                        El administrador de la organización revisará tu solicitud pronto
                    </p>
                </div>
            </div>
            @endif

            <!-- Opciones de Vinculación -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-md hover-lift">
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-key-fill text-4xl text-primary"></i>
                        </div>
                        <h5 class="text-xl font-semibold text-primary mb-3">Tengo un Código</h5>
                        <p class="text-gray-500 mb-6">
                            Si tienes un código de vinculación proporcionado por una organización, ingrésalo aquí
                        </p>
                        <button 
                            x-data 
                            @click="$dispatch('open-modal', 'codigo')" 
                            class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold inline-flex items-center">
                            <i class="bi bi-arrow-right-circle mr-2"></i>Ingresar Código
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md hover-lift">
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-search text-4xl text-accent"></i>
                        </div>
                        <h5 class="text-xl font-semibold text-accent mb-3">Buscar Organización</h5>
                        <p class="text-gray-500 mb-6">
                            Explora las organizaciones disponibles en el sistema
                        </p>
                        <button 
                            onclick="document.getElementById('listaOrganizaciones').scrollIntoView({behavior: 'smooth'})" 
                            class="border border-primary text-primary px-6 py-3 rounded-lg hover:bg-primary hover:text-white transition font-semibold inline-flex items-center">
                            <i class="bi bi-list-ul mr-2"></i>Ver Lista
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Organizaciones -->
            <div class="bg-white rounded-xl shadow-md" id="listaOrganizaciones">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h5 class="text-xl font-semibold text-primary flex items-center">
                        <i class="bi bi-building mr-2"></i>Organizaciones Disponibles
                    </h5>
                </div>
                <div>
                    @if($organizacionesDisponibles->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($organizacionesDisponibles as $org)
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mr-4">
                                    <i class="bi bi-building text-primary text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h6 class="font-semibold text-gray-800 mb-1">{{ $org->nombre_oficial }}</h6>
                                    <small class="text-gray-500 flex items-center">
                                        <i class="bi bi-geo-alt mr-1"></i>
                                        {{ $org->municipio }}, {{ $org->departamento }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <code class="bg-gray-100 text-gray-800 px-3 py-1.5 rounded-lg text-sm font-mono">
                                        {{ $org->telefono_contacto}}
                                    </code>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-16">
                        <i class="bi bi-building text-gray-200" style="font-size: 4rem;"></i>
                        <p class="text-gray-500 mt-4">No hay organizaciones disponibles</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Ingresar Código -->
    <div 
        x-data="{ open: false }"
        @open-modal.window="open = ($event.detail === 'codigo')"
        @keydown.escape.window="open = false"
        x-show="open"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
            @click="open = false">
        </div>

        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
                class="bg-white rounded-xl shadow-xl max-w-md w-full relative z-10">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h5 class="text-xl font-semibold text-primary flex items-center">
                            <i class="bi bi-key mr-2"></i>Vincular con Código
                        </h5>
                        <button 
                            @click="open = false" 
                            class="text-gray-400 hover:text-gray-600 transition">
                            <i class="bi bi-x-lg text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <form action="{{ route('vincular-codigo') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6 flex items-start">
                            <i class="bi bi-info-circle text-blue-500 mr-3 mt-0.5"></i>
                            <span class="text-blue-700 text-sm">Ingresa el código proporcionado por la organización</span>
                        </div>
                        
                        <div class="mb-6">
                            <label for="codigo_vinculacion" class="block text-sm font-medium text-gray-700 mb-2">
                                Código de Vinculación <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="w-full px-4 py-3 text-center text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition font-mono tracking-wider"
                                id="codigo_vinculacion" 
                                name="codigo_vinculacion" 
                                placeholder="ORG-2025-XXXXXX"
                                required>
                            <p class="mt-2 text-sm text-gray-500">Formato: ORG-YYYY-XXXXXX</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="open = false"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-semibold">
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition font-semibold inline-flex items-center">
                            <i class="bi bi-check-circle mr-2"></i>Vincular
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Alternativa sin Alpine.js - usando Bootstrap modal style
    document.addEventListener('DOMContentLoaded', function() {
        const modalTrigger = document.querySelector('[x-data]');
        if (modalTrigger) {
            modalTrigger.addEventListener('click', function() {
                // Implementación simple sin Alpine.js
                console.log('Modal trigger clicked');
            });
        }
    });
</script>
@endpush
@endsection