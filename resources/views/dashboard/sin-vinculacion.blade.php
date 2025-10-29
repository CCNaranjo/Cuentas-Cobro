@extends('layouts.app-dashboard')

@section('title', 'Dashboard - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    <!-- Main Content (Sin sidebar para usuarios sin vinculación) -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header Simplificado -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm">
            <div class="flex items-center">
                <div class="w-32">
                    <x-logo primary-color="#004AAD" secondary-color="#00BCD4"></x-logo>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(auth()->user()->nombre, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->nombre }}</span>
                        <i class="fas fa-chevron-down text-gray-600 text-xs"></i>
                    </button>

                    <div 
                        x-show="open"
                        @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                        style="display: none;"
                    >
                        <a 
                            href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors"
                        >
                            <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-4xl mx-auto">
                <!-- Estado Sin Vinculación -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-warning/10 rounded-full mb-4">
                        <i class="fas fa-user-clock text-warning text-5xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        ¡Hola, {{ auth()->user()->nombre }}! 👋
                    </h1>
                    <p class="text-lg text-secondary">
                        Tu cuenta no está vinculada a ninguna organización
                    </p>
                </div>

                @if($vinculacionesPendientes->count() > 0)
                    <!-- Vinculaciones Pendientes -->
                    <div class="bg-blue-50 border-l-4 border-accent rounded-lg p-6 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-accent text-2xl mr-4 mt-1"></i>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 mb-2">Solicitud en proceso</h3>
                                <p class="text-sm text-secondary mb-4">
                                    Tu solicitud de vinculación está siendo revisada por el administrador.
                                </p>
                                
                                @foreach($vinculacionesPendientes as $vinculacion)
                                <div class="bg-white rounded-lg p-4 mb-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $vinculacion->organizacion->nombre_oficial }}</p>
                                            <p class="text-sm text-secondary">Solicitado: {{ $vinculacion->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-warning/10 text-warning">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pendiente
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Opciones para vincularse -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Opción 1: Con Código -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4 mx-auto">
                            <i class="fas fa-key text-primary text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 text-center mb-2">
                            ¿Tienes un código de vinculación?
                        </h3>
                        <p class="text-sm text-secondary text-center mb-4">
                            Si tienes un código proporcionado por una organización, ingrésalo aquí
                        </p>

                        <form action="{{ route('vincular-codigo') }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <input 
                                    type="text" 
                                    name="codigo_vinculacion" 
                                    placeholder="ORG-2025-XXXXXX"
                                    value="{{ old('codigo_vinculacion') }}"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center font-mono @error('codigo_vinculacion') border-red-500 @enderror"
                                >
                                @error('codigo_vinculacion')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button 
                                type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-3 px-4 rounded-lg hover:shadow-lg transition-all"
                            >
                                <i class="bi bi-link-45deg mr-2"></i>
                                Vincular con Código
                            </button>
                        </form>

<!-- Mensajes de éxito -->
@if(session('success'))
<div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
    <div class="flex items-center">
        <i class="bi bi-check-circle text-green-500 mr-2"></i>
        <span class="text-green-700">{{ session('success') }}</span>
        @if(session('organizacion'))
            <strong class="ml-1">{{ session('organizacion') }}</strong>
        @endif
    </div>
</div>
@endif
                    </div>

                    <!-- Opción 2: Email Institucional -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-center w-16 h-16 bg-accent/10 rounded-full mb-4 mx-auto">
                            <i class="fas fa-at text-accent text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 text-center mb-2">
                            Email Institucional
                        </h3>
                        <p class="text-sm text-secondary text-center mb-4">
                            Tu email actual: <strong>{{ auth()->user()->email }}</strong>
                        </p>

                        @if(str_contains(auth()->user()->email, '@'))
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-700">
                                    Si tu email pertenece a una organización, se detectará automáticamente cuando el administrador revise tu solicitud.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl p-6 text-center">
                    <h3 class="text-xl font-bold mb-2">¿Necesitas ayuda?</h3>
                    <p class="mb-4 opacity-90">
                        Contacta con el administrador de tu organización para obtener un código de vinculación
                    </p>
                    <div class="flex justify-center space-x-6 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>soporte@arca-d.com</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>+57 (1) 234-5678</span>
                        </div>
                    </div>
                </div>

                <!-- Instrucciones -->
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-warning mr-2"></i>
                        ¿Cómo funciona el proceso?
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">
                                1
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Solicita vinculación</p>
                                <p class="text-sm text-secondary">Ingresa un código de vinculación o espera a que tu dominio de email sea detectado</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">
                                2
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Revisión del administrador</p>
                                <p class="text-sm text-secondary">El administrador de la organización revisará tu solicitud y asignará tu rol</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">
                                3
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">¡Listo para usar!</p>
                                <p class="text-sm text-secondary">Una vez aprobado, tendrás acceso completo según tu rol asignado</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection