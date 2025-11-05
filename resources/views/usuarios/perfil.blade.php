@extends('layouts.app-dashboard')

@section('title', 'Mi Perfil - ' . $usuario->nombre)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-primary mb-1 flex items-center">
                    <i class="bi bi-person-circle mr-2"></i>Mi Perfil
                </h2>
                <p class="text-gray-500">Administra tu información personal y preferencias</p>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="bi bi-arrow-left mr-2"></i>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Izquierda: Info Rápida -->
        <div class="space-y-6">
            <!-- Card de Foto de Perfil -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="text-center">
                    <div class="relative inline-block mb-4">
                        @if($usuario->foto_perfil)
                            <img src="{{ Storage::url($usuario->foto_perfil) }}" 
                                 alt="Foto de {{ $usuario->nombre }}"
                                 class="w-32 h-32 rounded-full object-cover border-4 border-primary">
                        @else
                            <div class="w-32 h-32 rounded-full bg-primary/10 flex items-center justify-center border-4 border-primary">
                                <i class="bi bi-person-fill text-6xl text-primary"></i>
                            </div>
                        @endif
                        
                        <!-- Botón para cambiar foto -->
                        <label for="foto_perfil" 
                               class="absolute bottom-0 right-0 bg-primary text-white p-2 rounded-full cursor-pointer hover:bg-primary-dark transition shadow-lg">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>
                    
                    <form action="{{ route('perfil.subir-foto') }}" method="POST" enctype="multipart/form-data" id="fotoForm">
                        @csrf
                        <input type="file" 
                               id="foto_perfil" 
                               name="foto" 
                               accept="image/jpeg,image/png,image/jpg"
                               class="hidden"
                               onchange="document.getElementById('fotoForm').submit()">
                    </form>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $usuario->nombre }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ $usuario->email }}</p>
                    
                    @if($usuario->roles->isNotEmpty())
                        <span class="inline-block px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-semibold">
                            {{ $usuario->roles->first()->nombre_mostrable }}
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Card de Información Rápida -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-info-circle mr-2 text-primary"></i>
                    Información General
                </h4>
                
                <div class="space-y-3 text-sm">
                    <div class="flex items-start">
                        <i class="bi bi-envelope text-gray-400 mr-3 mt-1"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Email</p>
                            <p class="text-gray-800 font-medium break-all">{{ $usuario->email }}</p>
                        </div>
                    </div>
                    
                    @if($usuario->documento_identidad)
                    <div class="flex items-start">
                        <i class="bi bi-card-text text-gray-400 mr-3 mt-1"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Documento</p>
                            <p class="text-gray-800 font-medium">{{ $usuario->documento_identidad }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($usuario->telefono)
                    <div class="flex items-start">
                        <i class="bi bi-telephone text-gray-400 mr-3 mt-1"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Teléfono</p>
                            <p class="text-gray-800 font-medium">{{ $usuario->telefono }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-start">
                        <i class="bi bi-calendar-check text-gray-400 mr-3 mt-1"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Miembro desde</p>
                            <p class="text-gray-800 font-medium">{{ $usuario->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="bi bi-shield-check text-gray-400 mr-3 mt-1"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Estado</p>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $usuario->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($usuario->estado) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card de Organizaciones -->
            @if($usuario->organizacionesVinculadas->isNotEmpty())
            <div class="bg-white rounded-xl shadow-md p-6">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-building mr-2 text-primary"></i>
                    Mis Organizaciones
                </h4>
                
                <div class="space-y-3">
                    @foreach($usuario->organizacionesVinculadas as $org)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-sm text-gray-800">{{ $org->nombre_oficial }}</p>
                        <p class="text-xs text-gray-500">{{ $org->municipio }}, {{ $org->departamento }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Columna Derecha: Tabs de Edición -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md">
                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs" x-data="{ activeTab: 'informacion' }">
                        <button @click="activeTab = 'informacion'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition"
                                :class="activeTab === 'informacion' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            <i class="bi bi-person mr-2"></i>
                            Información Personal
                        </button>
                        <button @click="activeTab = 'seguridad'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition"
                                :class="activeTab === 'seguridad' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            <i class="bi bi-shield-lock mr-2"></i>
                            Seguridad
                        </button>
                        <button @click="activeTab = 'notificaciones'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition"
                                :class="activeTab === 'notificaciones' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            <i class="bi bi-bell mr-2"></i>
                            Notificaciones
                        </button>
                    </nav>
                </div>
                
                <!-- Contenido de Tabs -->
                <div class="p-6" x-data="{ activeTab: 'informacion' }">
                    
                    <!-- TAB: INFORMACIÓN PERSONAL -->
                    <div x-show="activeTab === 'informacion'" x-transition>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Información Personal</h3>
                        
                        <form action="{{ route('perfil.actualizar') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre Completo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nombre" 
                                           value="{{ old('nombre', $usuario->nombre) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                           required>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email', $usuario->email) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                           required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Teléfono
                                    </label>
                                    <input type="tel" 
                                           name="telefono" 
                                           value="{{ old('telefono', $usuario->telefono) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Documento
                                    </label>
                                    <input type="text" 
                                           value="{{ $usuario->documento_identidad }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                           disabled>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="bi bi-lock-fill mr-1"></i>
                                        Este campo no se puede modificar
                                    </p>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Dirección
                                    </label>
                                    <input type="text" 
                                           name="direccion" 
                                           value="{{ old('direccion', $usuario->direccion) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                            </div>
                            
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button type="submit" 
                                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                    <i class="bi bi-check-circle mr-2"></i>
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- TAB: SEGURIDAD -->
                    <div x-show="activeTab === 'seguridad'" x-transition>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Cambiar Contraseña</h3>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-6 flex items-start">
                            <i class="bi bi-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                            <div class="text-sm text-yellow-700">
                                <strong>Importante:</strong> Por seguridad, deberás iniciar sesión nuevamente después de cambiar tu contraseña.
                            </div>
                        </div>
                        
                        <form action="{{ route('perfil.cambiar-password') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Contraseña Actual <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_actual" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                       required>
                                @error('password_actual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nueva Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_nuevo" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Nueva Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_nuevo_confirmation" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                       required>
                            </div>
                            
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button type="submit" 
                                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                    <i class="bi bi-shield-check mr-2"></i>
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- TAB: NOTIFICACIONES -->
                    <div x-show="activeTab === 'notificaciones'" x-transition>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Preferencias de Notificaciones</h3>
                        
                        <p class="text-gray-600 mb-6">Configura cómo deseas recibir notificaciones del sistema</p>
                        
                        <form action="{{ route('perfil.actualizar-notificaciones') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-4">
                                <label class="flex items-start cursor-pointer p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <input type="checkbox" 
                                           name="notificaciones_email" 
                                           value="1"
                                           {{ old('notificaciones_email', $usuario->preferencias_notificaciones['notificaciones_email'] ?? true) ? 'checked' : '' }}
                                           class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary mt-0.5">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-800">Notificaciones por Email</span>
                                        <p class="text-sm text-gray-500">Recibir notificaciones importantes en tu correo electrónico</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start cursor-pointer p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <input type="checkbox" 
                                           name="notificaciones_sistema" 
                                           value="1"
                                           {{ old('notificaciones_sistema', $usuario->preferencias_notificaciones['notificaciones_sistema'] ?? true) ? 'checked' : '' }}
                                           class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary mt-0.5">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-800">Notificaciones del Sistema</span>
                                        <p class="text-sm text-gray-500">Ver notificaciones dentro de la plataforma</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start cursor-pointer p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <input type="checkbox" 
                                           name="notificaciones_sms" 
                                           value="1"
                                           {{ old('notificaciones_sms', $usuario->preferencias_notificaciones['notificaciones_sms'] ?? false) ? 'checked' : '' }}
                                           class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary mt-0.5">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-800">Notificaciones por SMS</span>
                                        <p class="text-sm text-gray-500">Recibir mensajes de texto para eventos críticos</p>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button type="submit" 
                                        class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                    <i class="bi bi-check-circle mr-2"></i>
                                    Guardar Preferencias
                                </button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection