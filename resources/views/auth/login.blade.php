@extends('layouts.app')

@section('title', 'Iniciar Sesión - ARCA-D')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary via-primary-dark to-accent p-4">
    <!-- Patrón de fondo decorativo -->
    <div class="absolute inset-0 overflow-hidden opacity-10">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md animate-fadeIn">
        <!-- Card principal -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <!-- Header con logo -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-300">
                        <x-logo primary-color="#1B3A6B" secondary-color="#00E5CC" ></x-logo>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">ARCA-D</h1>
                <p class="text-secondary text-sm">Sistema de Gestión de Cuentas por Cobrar</p>
            </div>

            <!-- Mensajes de error -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-danger rounded-lg p-4 animate-fadeIn">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-danger mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-danger mb-1">Error al iniciar sesión</p>
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-primary mr-2"></i>Correo electrónico
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus
                        placeholder="usuario@ejemplo.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('email') border-danger @enderror"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-primary mr-2"></i>Contraseña
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('password') border-danger @enderror"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-primary transition-colors"
                        >
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Recordarme -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer group">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember"
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-accent focus:ring-2 cursor-pointer"
                        >
                        <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">
                            Recordarme
                        </span>
                    </label>
                    
                    <a href="#" class="text-sm text-primary hover:text-accent transition-colors font-medium">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Botón de login -->
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-4 rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center group"
                >
                    <i class="fas fa-sign-in-alt mr-2 group-hover:translate-x-1 transition-transform"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-secondary">¿Nuevo en ARCA-D?</span>
                </div>
            </div>

            <!-- Enlace a registro -->
            <a 
                href="{{ route('register') }}" 
                class="block w-full text-center border-2 border-primary text-primary font-semibold py-3 px-4 rounded-lg hover:bg-primary hover:text-white transition-all duration-200 transform hover:-translate-y-0.5"
            >
                <i class="fas fa-user-plus mr-2"></i>
                Crear cuenta nueva
            </a>
        </div>

        <!-- Footer info -->
        <div class="text-center mt-6 text-white text-sm">
            <p class="opacity-90">
                <i class="fas fa-shield-alt mr-1"></i>
                Conexión segura y cifrada
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection