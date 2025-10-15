@extends('layouts.app')

@section('title', 'Registrarse - ARCA-D')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary via-primary-dark to-accent p-4">
    <!-- Patrón de fondo decorativo -->
    <div class="absolute inset-0 overflow-hidden opacity-10">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md animate-fadeIn">
        <!-- Botón de volver -->
        <a 
            href="{{ route('login') }}" 
            class="inline-flex items-center text-white hover:text-accent transition-colors mb-4 group"
        >
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-sm font-medium">Volver al inicio de sesión</span>
        </a>

        <!-- Card principal -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <!-- Header con logo -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary to-accent rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-300">
                        <span class="text-white text-3xl font-bold">A</span>
                        <span class="text-white text-3xl font-bold">D</span>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear cuenta</h1>
                <p class="text-secondary text-sm">Comienza a gestionar tus cuentas por cobrar</p>
            </div>

            <!-- Mensajes de error -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-danger rounded-lg p-4 animate-fadeIn">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-danger mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-danger mb-1">Errores en el formulario</p>
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
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                
                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-primary mr-2"></i>Nombre completo
                        <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        value="{{ old('nombre') }}" 
                        required 
                        autocomplete="name" 
                        autofocus
                        placeholder="Ej: Juan Pérez"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('nombre') border-danger @enderror"
                    >
                    @error('nombre')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-primary mr-2"></i>Correo electrónico
                        <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                        placeholder="usuario@ejemplo.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('email') border-danger @enderror"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-xs text-secondary">
                        <i class="fas fa-info-circle mr-1"></i>
                        Si tu email pertenece a una organización, se detectará automáticamente
                    </p>
                </div>

                <!-- Documento de Identidad (Opcional) -->
                <div>
                    <label for="documento_identidad" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card text-primary mr-2"></i>Documento de Identidad
                    </label>
                    <input 
                        type="text" 
                        id="documento_identidad" 
                        name="documento_identidad" 
                        value="{{ old('documento_identidad') }}" 
                        placeholder="1234567890"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('documento_identidad') border-danger @enderror"
                    >
                    @error('documento_identidad')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Teléfono (Opcional) -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone text-primary mr-2"></i>Teléfono
                    </label>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        value="{{ old('telefono') }}" 
                        placeholder="+57 300 123 4567"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('telefono') border-danger @enderror"
                    >
                    @error('telefono')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Código de Vinculación (Opcional) -->
                <div>
                    <label for="codigo_vinculacion" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key text-primary mr-2"></i>Código de Vinculación
                    </label>
                    <input 
                        type="text" 
                        id="codigo_vinculacion" 
                        name="codigo_vinculacion" 
                        value="{{ old('codigo_vinculacion') }}" 
                        placeholder="ORG-2025-XXXXXX"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('codigo_vinculacion') border-danger @enderror"
                    >
                    @error('codigo_vinculacion')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-xs text-secondary">
                        <i class="fas fa-info-circle mr-1"></i>
                        Si tienes un código de vinculación, ingrésalo aquí
                    </p>
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-primary mr-2"></i>Contraseña
                        <span class="text-danger">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('password') border-danger @enderror"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-primary transition-colors"
                        >
                            <i class="fas fa-eye" id="toggleIconPassword"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                    <!-- Indicador de fortaleza -->
                    <div class="mt-2">
                        <div class="flex gap-1 mb-1">
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength1"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength2"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength3"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength4"></div>
                        </div>
                        <p class="text-xs text-secondary" id="strengthText">
                            Ingresa al menos 8 caracteres
                        </p>
                    </div>
                </div>

                <!-- Confirmar contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-primary mr-2"></i>Confirmar contraseña
                        <span class="text-danger">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="Repite tu contraseña"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-200 @error('password_confirmation') border-danger @enderror"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-primary transition-colors"
                        >
                            <i class="fas fa-eye" id="toggleIconPasswordConfirmation"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-danger flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Botón de registro -->
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-4 rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center group"
                >
                    <i class="fas fa-user-plus mr-2 group-hover:scale-110 transition-transform"></i>
                    Crear cuenta
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-secondary">¿Ya tienes cuenta?</span>
                </div>
            </div>

            <!-- Enlace a login -->
            <a 
                href="{{ route('login') }}" 
                class="block w-full text-center border-2 border-primary text-primary font-semibold py-3 px-4 rounded-lg hover:bg-primary hover:text-white transition-all duration-200 transform hover:-translate-y-0.5"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Iniciar sesión
            </a>
        </div>

        <!-- Footer info -->
        <div class="text-center mt-6 text-white text-sm">
            <p class="opacity-90">
                <i class="fas fa-shield-alt mr-1"></i>
                Tus datos están protegidos y seguros
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const iconId = fieldId === 'password' ? 'toggleIconPassword' : 'toggleIconPasswordConfirmation';
        const toggleIcon = document.getElementById(iconId);
        
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

    // Indicador de fortaleza de contraseña
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strength = calculatePasswordStrength(password);
        updateStrengthIndicator(strength);
    });

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z\d]/.test(password)) strength++;
        return Math.min(strength, 4);
    }

    function updateStrengthIndicator(strength) {
        const indicators = ['strength1', 'strength2', 'strength3', 'strength4'];
        const colors = ['#DC3545', '#FD7E14', '#FD7E14', '#00BCD4'];
        const texts = [
            'Contraseña muy débil',
            'Contraseña débil',
            'Contraseña aceptable',
            'Contraseña fuerte'
        ];

        indicators.forEach((id, index) => {
            const element = document.getElementById(id);
            if (index < strength) {
                element.style.backgroundColor = colors[strength - 1];
            } else {
                element.style.backgroundColor = '#E5E7EB';
            }
        });

        const strengthText = document.getElementById('strengthText');
        if (strength > 0) {
            strengthText.textContent = texts[strength - 1];
            strengthText.style.color = colors[strength - 1];
        } else {
            strengthText.textContent = 'Ingresa al menos 8 caracteres';
            strengthText.style.color = '#6C757D';
        }
    }
</script>
@endpush
@endsection