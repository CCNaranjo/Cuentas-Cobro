@extends('layouts.app-dashboard')

@section('title', 'Configuración - ' . $organizacion->nombre_oficial)

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <div class="container mx-auto px-4 py-8 overflow-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-semibold text-primary mb-1 flex items-center">
                            <i class="bi bi-building-gear mr-2"></i>Configuración de Organización
                        </h2>
                        <p class="text-gray-500">{{ $organizacion->nombre_oficial }}</p>
                    </div>
                    <a href="{{ route('dashboard') }}" 
                    class="text-gray-600 hover:text-gray-800 flex items-center">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>

            <!-- Alert de información -->
            <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 mb-6 flex items-start">
                <i class="bi bi-info-circle text-amber-500 text-2xl mr-3"></i>
                <div>
                    <h6 class="font-semibold text-amber-700 mb-1">Configuración Local</h6>
                    <p class="text-sm text-amber-600">Los cambios aquí solo afectan a tu organización. Para cambios globales del sistema, contacta al administrador global.</p>
                </div>
            </div>

            <!-- Tabs de Navegación -->
            <div class="bg-white rounded-t-xl shadow-md">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <a href="?tab=general" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'general' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="bi bi-info-circle mr-2"></i>
                            Información General
                        </a>
                        <a href="?tab=financiero" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'financiero' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="bi bi-currency-dollar mr-2"></i>
                            Parámetros Financieros
                        </a>
                        <a href="?tab=branding" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'branding' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="bi bi-palette mr-2"></i>
                            Branding
                        </a>
                        <a href="?tab=cuentas_bancarias" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'cuentas_bancarias' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="fas fa-bank mr-2"></i>
                            Cuentas Bancarias
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenido de Tabs -->
            <div class="bg-white rounded-b-xl shadow-md p-6">
                
                @if($tab === 'general')
                <!-- TAB: INFORMACIÓN GENERAL -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-building mr-2 text-primary"></i>
                        Información de la Organización
                    </h3>
                    
                    <form action="{{ route('configuracion.actualizar-organizacion') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="seccion" value="general">
                        <!-- Información Básica -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4">Datos de Contacto</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre Oficial <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        name="nombre_oficial" 
                                        value="{{ old('nombre_oficial', $organizacion->nombre_oficial) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Institucional <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                        name="email_institucional" 
                                        value="{{ old('email_institucional', $organizacion->email_institucional) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Teléfono de Contacto <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" 
                                        name="telefono_contacto" 
                                        value="{{ old('telefono_contacto', $organizacion->telefono_contacto) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Dirección <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        name="direccion" 
                                        value="{{ old('direccion', $organizacion->direccion) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Vigencia Fiscal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                        name="vigencia_fiscal" 
                                        value="{{ old('vigencia_fiscal', $configuracionOrg->vigencia_fiscal ?? date('Y')) }}"
                                        min="2020" max="2099"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
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
                
                @if($tab === 'financiero')
                <!-- TAB: PARÁMETROS FINANCIEROS -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-currency-dollar mr-2 text-primary"></i>
                        Parámetros Financieros
                    </h3>
                    
                    <form action="{{ route('configuracion.actualizar-organizacion') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="seccion" value="financiero">
                        
                        <!-- Parámetros Principales -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4">Retenciones y Plazos</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Porcentaje Retención ICA (%) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                        name="porcentaje_retencion_ica" 
                                        value="{{ old('porcentaje_retencion_ica', $configuracionOrg->porcentaje_retencion_ica) }}"
                                        step="0.001" min="0" max="100"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Porcentaje Retención en la Fuente (%) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                        name="porcentaje_retencion_fuente" 
                                        value="{{ old('porcentaje_retencion_fuente', $configuracionOrg->porcentaje_retencion_fuente) }}"
                                        step="0.001" min="0" max="100"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Días de Plazo para Pago <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                        name="dias_plazo_pago" 
                                        value="{{ old('dias_plazo_pago', $configuracionOrg->dias_plazo_pago) }}"
                                        min="1" max="365"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div class="md:col-span-2 flex items-center space-x-3 bg-blue-50 p-4 rounded-lg">
                                    <input type="checkbox" 
                                           id="requiere_paz_y_salvo" 
                                           name="requiere_paz_y_salvo" 
                                           {{ old('requiere_paz_y_salvo', $configuracionOrg->requiere_paz_y_salvo) ? 'checked' : '' }}
                                           class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                    <label for="requiere_paz_y_salvo" class="text-sm text-gray-700 font-medium">
                                        Requerir Paz y Salvo para Contratistas
                                    </label>
                                    <div class="text-xs text-blue-600 flex items-center">
                                        <i class="bi bi-info-circle mr-1"></i>
                                        Obligatorio para radicación de cuentas
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón de Guardar -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                <i class="bi bi-check-circle mr-2"></i>
                                Guardar Parámetros
                            </button>
                        </div>
                    </form>
                </div>
                @endif
                
                @if($tab === 'branding')
                <!-- TAB: BRANDING -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-image mr-2 text-primary"></i>
                        Personalización Visual
                    </h3>
                    
                    <form action="{{ route('configuracion.actualizar-organizacion') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="seccion" value="branding">
                        
                        <!-- Logo Institucional -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4">Logo Institucional</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Vista Previa del Logo Actual -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Logo Actual</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-white flex items-center justify-center" style="min-height: 200px;">
                                        @if($organizacion->logo_path)
                                            <img src="{{ Storage::url($organizacion->logo_path) }}" 
                                                alt="Logo {{ $organizacion->nombre_oficial }}"
                                                class="max-h-40 max-w-full object-contain">
                                        @else
                                            <div class="text-center text-gray-400">
                                                <i class="bi bi-image text-6xl mb-2"></i>
                                                <p class="text-sm">Sin logo cargado</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Upload Nuevo Logo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Subir Nuevo Logo <span class="text-red-500">*</span>
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-white hover:border-primary transition" style="min-height: 200px;">
                                        <div class="text-center">
                                            <i class="bi bi-cloud-upload text-5xl text-gray-400 mb-3"></i>
                                            <div class="mb-4">
                                                <label for="logo" class="cursor-pointer">
                                                    <span class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition inline-block">
                                                        Seleccionar Archivo
                                                    </span>
                                                    <input type="file" 
                                                        id="logo" 
                                                        name="logo" 
                                                        accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                                                        class="hidden"
                                                        onchange="previewImage(this)">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                Formatos: JPG, PNG, SVG<br>
                                                Tamaño máximo: 2MB
                                            </p>
                                        </div>
                                        
                                        <!-- Preview del nuevo logo -->
                                        <div id="imagePreview" class="mt-4 hidden">
                                            <img id="preview" class="max-h-32 mx-auto" alt="Vista previa">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-4 bg-blue-50 rounded-lg flex items-start text-sm">
                                <i class="bi bi-info-circle text-blue-500 mr-2 mt-0.5"></i>
                                <p class="text-blue-700">
                                    El logo se mostrará en documentos oficiales, cuentas de cobro y en el encabezado del sistema para tu organización.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Botón de Guardar -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition font-semibold flex items-center">
                                <i class="bi bi-check-circle mr-2"></i>
                                Actualizar Logo
                            </button>
                        </div>
                    </form>
                </div>
                @endif
                
                @if($tab === 'cuentas_bancarias')
                <!-- TAB: CUENTAS BANCARIAS -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bank mr-2 text-primary"></i>
                        Cuentas Bancarias
                    </h3>
                    
                    <!-- Formulario para añadir nueva cuenta -->
                    <form action="{{ route('configuracion.actualizar-organizacion') }}" method="POST" class="bg-gray-50 rounded-lg p-6 mb-6">
                        @csrf
                        <input type="hidden" name="seccion" value="cuentas_bancarias">
                        
                        <h4 class="font-semibold text-gray-700 mb-4">Añadir Nueva Cuenta</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Banco <span class="text-red-500">*</span>
                                </label>
                                <select name="banco_id" required class="w-full px-4 py-2 border rounded-lg">
                                    <option value="">Seleccione banco</option>
                                    @foreach($bancos as $banco)
                                        <option value="{{ $banco->id }}">{{ $banco->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Número de Cuenta <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="numero_cuenta" required class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Cuenta <span class="text-red-500">*</span>
                                </label>
                                <select name="tipo_cuenta" required class="w-full px-4 py-2 border rounded-lg">
                                    <option value="">Seleccione tipo</option>
                                    <option value="ahorros">Ahorros</option>
                                    <option value="corriente">Corriente</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Titular de la Cuenta <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="titular_cuenta" required class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            
                            <div class="md:col-span-2 flex items-center space-x-3">
                                <input type="checkbox" name="activa" id="activa" checked class="w-5 h-5 text-primary border-gray-300 rounded">
                                <label for="activa" class="text-sm text-gray-700">Cuenta Activa</label>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark flex items-center">
                                <i class="fas fa-plus mr-2"></i> Añadir Cuenta
                            </button>
                        </div>
                    </form>
                    
                    <!-- Lista de Cuentas Existentes -->
                    <div class="bg-white rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-4 p-6 border-b">Cuentas Registradas</h4>
                        
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Banco</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titular</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($cuentasBancarias as $cuenta)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm">{{ $cuenta->banco->nombre }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $cuenta->numero_cuenta }}</td>
                                        <td class="px-6 py-4 text-sm">{{ ucfirst($cuenta->tipo_cuenta) }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $cuenta->titular_cuenta }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                                {{ $cuenta->activa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $cuenta->activa ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($cuentasBancarias->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-bank text-4xl mb-2"></i>
                                <p>No hay cuentas registradas aún</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection