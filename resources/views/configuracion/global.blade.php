@extends('layouts.app-dashboard')

@section('title', 'Configuración Global - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <div class="container mx-auto px-4 py-8 overflow-auto">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-semibold text-primary mb-1 flex items-center">
                    <i class="bi bi-gear-fill mr-2"></i>Configuración Global del Sistema
                </h2>
                <p class="text-gray-500">Administración de parámetros globales de ARCA-D</p>
            </div>

            <!-- Alert de permisos -->
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6 flex items-start">
                <i class="bi bi-shield-lock text-blue-500 text-2xl mr-3"></i>
                <div>
                    <h6 class="font-semibold text-blue-700 mb-1">Acceso Restringido</h6>
                    <p class="text-sm text-blue-600">Los cambios aquí afectan a toda la plataforma ARCA-D. Solo administradores globales pueden modificar estos parámetros.</p>
                </div>
            </div>

            <!-- Tabs de Navegación -->
            <div class="bg-white rounded-t-xl shadow-md">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <a href="?tab=seguridad" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'seguridad' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="bi bi-shield-check mr-2"></i>
                            Seguridad
                        </a>
                        <a href="?tab=integraciones" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'integraciones' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="bi bi-plugin mr-2"></i>
                            Integraciones API
                        </a>
                        <a href="?tab=plantillas" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'plantillas' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="bi bi-envelope mr-2"></i>
                            Plantillas Email
                        </a>
                        <a href="?tab=logs" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'logs' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="bi bi-file-text mr-2"></i>
                            Logs y Monitoreo
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenido de Tabs -->
            <div class="bg-white rounded-b-xl shadow-md p-6">
                
                @if($tab === 'seguridad')
                <!-- TAB: SEGURIDAD -->
                <div x-data="{ showAdvanced: false }">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-lock mr-2 text-primary"></i>
                        Políticas de Contraseña y Seguridad
                    </h3>
                    
                    <form action="{{ route('configuracion.actualizar-global') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="seccion" value="seguridad">
                        
                        <!-- Políticas de Contraseña -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-key mr-2"></i>
                                Políticas de Contraseña
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Longitud Mínima <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                        name="min_longitud_password" 
                                        value="{{ old('min_longitud_password', $configuracion->min_longitud_password) }}"
                                        min="6" 
                                        max="32"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">Caracteres mínimos requeridos (6-32)</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Intentos Máximos de Login <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                        name="intentos_maximos_login" 
                                        value="{{ old('intentos_maximos_login', $configuracion->intentos_maximos_login) }}"
                                        min="3" 
                                        max="10"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">Antes de bloquear la cuenta (3-10)</p>
                                </div>
                            </div>
                            
                            <!-- Checkboxes de Requisitos -->
                            <div class="mt-6 space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        name="requiere_mayuscula" 
                                        value="1"
                                        {{ old('requiere_mayuscula', $configuracion->requiere_mayuscula) ? 'checked' : '' }}
                                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <span class="ml-3 text-sm text-gray-700">Requiere al menos una letra mayúscula</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        name="requiere_numero" 
                                        value="1"
                                        {{ old('requiere_numero', $configuracion->requiere_numero) ? 'checked' : '' }}
                                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <span class="ml-3 text-sm text-gray-700">Requiere al menos un número</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        name="requiere_caracter_especial" 
                                        value="1"
                                        {{ old('requiere_caracter_especial', $configuracion->requiere_caracter_especial) ? 'checked' : '' }}
                                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <span class="ml-3 text-sm text-gray-700">Requiere carácter especial (@, #, $, etc.)</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Autenticación de Dos Factores -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-phone mr-2"></i>
                                Autenticación de Dos Factores (2FA)
                            </h4>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                    name="habilitar_2fa" 
                                    value="1"
                                    {{ old('habilitar_2fa', $configuracion->habilitar_2fa) ? 'checked' : '' }}
                                    class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-gray-700">Habilitar 2FA para todos los usuarios</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-2 ml-7">Los usuarios deberán configurar un segundo factor de autenticación</p>
                        </div>
                        
                        <!-- Configuración Avanzada -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <button type="button" 
                                    @click="showAdvanced = !showAdvanced"
                                    class="flex items-center text-sm font-medium text-primary hover:text-primary-dark transition">
                                <i class="bi mr-2" :class="showAdvanced ? 'bi-chevron-down' : 'bi-chevron-right'"></i>
                                Configuración Avanzada
                            </button>
                            
                            <div x-show="showAdvanced" 
                                x-transition
                                class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Días para Expiración de Contraseña
                                </label>
                                <input type="number" 
                                    name="dias_expiracion_password" 
                                    value="{{ old('dias_expiracion_password', $configuracion->dias_expiracion_password) }}"
                                    min="0" 
                                    max="365"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                <p class="text-xs text-gray-500 mt-1">0 = Sin expiración</p>
                            </div>
                        </div>
                        
                        <!-- Botón de Guardar -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                <i class="bi bi-check-circle mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
                @endif
                
                @if($tab === 'integraciones')
                <!-- TAB: INTEGRACIONES API -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-cloud mr-2 text-primary"></i>
                        Configuración de Integraciones Externas
                    </h3>
                    
                    <form action="{{ route('configuracion.actualizar-global') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="seccion" value="integraciones">
                        
                        <!-- SMTP (Email) -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-envelope-at mr-2"></i>
                                Servidor SMTP (Correo Electrónico)
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Host SMTP</label>
                                    <input type="text" 
                                        name="smtp_host" 
                                        value="{{ old('smtp_host', $configuracion->integraciones_api['smtp_host'] ?? '') }}"
                                        placeholder="smtp.gmail.com"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Puerto</label>
                                    <input type="number" 
                                        name="smtp_port" 
                                        value="{{ old('smtp_port', $configuracion->integraciones_api['smtp_port'] ?? '587') }}"
                                        placeholder="587"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                                    <input type="text" 
                                        name="smtp_user" 
                                        value="{{ old('smtp_user', $configuracion->integraciones_api['smtp_user'] ?? '') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                                    <input type="password" 
                                        name="smtp_password" 
                                        value="{{ old('smtp_password') }}"
                                        placeholder="••••••••"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                            </div>
                        </div>
                        
                        <!-- SMS Provider -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-chat-dots mr-2"></i>
                                Proveedor de SMS
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                                    <select name="sms_provider" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                        <option value="">Seleccionar...</option>
                                        <option value="twilio">Twilio</option>
                                        <option value="nexmo">Nexmo</option>
                                        <option value="local">Proveedor Local</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                                    <input type="text" 
                                        name="sms_api_key" 
                                        value="{{ old('sms_api_key', $configuracion->integraciones_api['sms_api_key'] ?? '') }}"
                                        placeholder="sk_..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón de Guardar -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                <i class="bi bi-check-circle mr-2"></i>
                                Guardar Integraciones
                            </button>
                        </div>
                    </form>
                </div>
                @endif
                
                @if($tab === 'plantillas')
                <!-- TAB: PLANTILLAS EMAIL -->
                <div x-data="{ selectedTemplate: 'bienvenida' }">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-file-earmark-text mr-2 text-primary"></i>
                        Plantillas de Correo Electrónico
                    </h3>
                    
                    <!-- Selector de Plantilla -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Plantilla</label>
                        <select x-model="selectedTemplate" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                            <option value="bienvenida">Bienvenida al Sistema</option>
                            <option value="recuperacion_password">Recuperación de Contraseña</option>
                            <option value="cuenta_aprobada">Cuenta de Cobro Aprobada</option>
                            <option value="contrato_asignado">Contrato Asignado</option>
                        </select>
                    </div>
                    
                    <form action="{{ route('configuracion.actualizar-global') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="seccion" value="plantillas">
                        <input type="hidden" name="tipo_plantilla" x-model="selectedTemplate">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Asunto del Correo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                name="asunto" 
                                placeholder="Ej: Bienvenido a ARCA-D"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contenido del Email <span class="text-red-500">*</span>
                            </label>
                            <textarea name="contenido" 
                                    rows="10"
                                    placeholder="Escribe el contenido del email aquí..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition font-mono text-sm"
                                    required></textarea>
                            <p class="text-xs text-gray-500 mt-2">
                                Variables disponibles: {{nombre}}, {{email}}, {{organizacion}}, {{enlace}}
                            </p>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <button type="button" 
                                    class="text-primary hover:text-primary-dark font-medium flex items-center">
                                <i class="bi bi-eye mr-2"></i>
                                Vista Previa
                            </button>
                            <button type="submit" 
                                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                <i class="bi bi-check-circle mr-2"></i>
                                Guardar Plantilla
                            </button>
                        </div>
                    </form>
                </div>
                @endif
                
                @if($tab === 'logs')
                <!-- TAB: LOGS Y MONITOREO -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-activity mr-2 text-primary"></i>
                        Logs del Sistema y Monitoreo
                    </h3>
                    
                    <!-- Filtros -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                <option>Todos los niveles</option>
                                <option>ERROR</option>
                                <option>WARNING</option>
                                <option>INFO</option>
                            </select>
                            
                            <input type="date" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        
                        <button class="bg-accent text-white px-4 py-2 rounded-lg hover:bg-cyan-600 transition text-sm font-semibold flex items-center">
                            <i class="bi bi-download mr-2"></i>
                            Exportar Logs
                        </button>
                    </div>
                    
                    <!-- Tabla de Logs -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nivel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mensaje</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $log['fecha']->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badges = [
                                                'ERROR' => 'bg-red-100 text-red-800',
                                                'WARNING' => 'bg-yellow-100 text-yellow-800',
                                                'INFO' => 'bg-blue-100 text-blue-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badges[$log['nivel']] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $log['nivel'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        {{ $log['mensaje'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $log['usuario'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
            </div>
        </div>
    </div>      
</div>
@endsection