@extends('layouts.app-dashboard')

@section('title', 'Configuración - ' . $organizacion->nombre_oficial)

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <div class="container mx-auto px-4 py-8">
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
                            </div>
                        </div>
                        
                        <!-- Vigencia Fiscal -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-calendar-event mr-2"></i>
                                Vigencia Fiscal
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        NIT Organización
                                    </label>
                                    <input type="text" 
                                        name="nit_organizacion" 
                                        value="{{ old('nit_organizacion', $configuracionOrg->nit_organizacion ?? $organizacion->nit) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vigencia Fiscal -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-calendar-event mr-2"></i>
                                Vigencia Fiscal
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Inicio <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                        name="vigencia_fiscal_fecha_inicio" 
                                        value="{{ old('vigencia_fiscal_fecha_inicio', $configuracionOrg->vigencia_fiscal_fecha_inicio?->format('Y-m-d') ?? date('Y') . '-01-01') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Fin <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                        name="vigencia_fiscal_fecha_fin" 
                                        value="{{ old('vigencia_fiscal_fecha_fin', $configuracionOrg->vigencia_fiscal_fecha_fin?->format('Y-m-d') ?? date('Y') . '-12-31') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Define el período fiscal activo para contratos y pagos</p>
                        </div>
                        
                        <!-- Información de Ubicación (Solo lectura) -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4">Ubicación Geográfica</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Departamento:</span>
                                    <p class="font-semibold text-gray-800">{{ $organizacion->departamento }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Municipio:</span>
                                    <p class="font-semibold text-gray-800">{{ $organizacion->municipio }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">NIT:</span>
                                    <p class="font-semibold text-gray-800">{{ $organizacion->nit }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-3">
                                <i class="bi bi-lock-fill mr-1"></i>
                                Estos campos son de solo lectura. Contacta al administrador global para modificarlos.
                            </p>
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
                        <i class="bi bi-calculator mr-2 text-primary"></i>
                        Configuración Financiera Local
                    </h3>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-6 flex items-start">
                        <i class="bi bi-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                        <div class="text-sm text-yellow-700">
                            <strong>Importante:</strong> Estos parámetros afectan el cálculo de retenciones y pagos. Verifica con el área financiera antes de modificar.
                        </div>
                    </div>
                    
                    <form action="{{ route('configuracion.actualizar-organizacion') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="seccion" value="financiero">
                        
                        <!-- Tasas de Retención -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-percent mr-2"></i>
                                Tasas de Retención
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Retención ICA (%) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                            name="porcentaje_retencion_ica" 
                                            value="{{ old('porcentaje_retencion_ica', $configuracionOrg->porcentaje_retencion_ica) }}"
                                            step="0.001"
                                            min="0"
                                            max="100"
                                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                            required>
                                        <span class="absolute right-3 top-2.5 text-gray-500">%</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Porcentaje de retención de Industria y Comercio</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Retención en la Fuente (%) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                            name="porcentaje_retencion_fuente" 
                                            value="{{ old('porcentaje_retencion_fuente', $configuracionOrg->porcentaje_retencion_fuente) }}"
                                            step="0.1"
                                            min="0"
                                            max="100"
                                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                            required>
                                        <span class="absolute right-3 top-2.5 text-gray-500">%</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Porcentaje de retención en la fuente aplicable</p>
                                </div>
                            </div>
                            
                            <!-- Ejemplo de Cálculo -->
                            <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200">
                                <h5 class="text-sm font-semibold text-gray-700 mb-2">Ejemplo de Cálculo</h5>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <p>Valor Bruto del Contrato: <span class="font-semibold">$10,000,000</span></p>
                                    <p>Retención ICA ({{ $configuracionOrg->porcentaje_retencion_ica }}%): <span class="text-red-600 font-semibold">-${{ number_format(10000000 * $configuracionOrg->porcentaje_retencion_ica / 100, 0, ',', '.') }}</span></p>
                                    <p>Retención Fuente ({{ $configuracionOrg->porcentaje_retencion_fuente }}%): <span class="text-red-600 font-semibold">-${{ number_format(10000000 * $configuracionOrg->porcentaje_retencion_fuente / 100, 0, ',', '.') }}</span></p>
                                    <p class="border-t border-gray-200 pt-1 mt-1">Valor Neto a Pagar: <span class="text-green-600 font-bold">${{ number_format(10000000 - (10000000 * $configuracionOrg->porcentaje_retencion_ica / 100) - (10000000 * $configuracionOrg->porcentaje_retencion_fuente / 100), 0, ',', '.') }}</span></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Plazos de Pago -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="bi bi-clock-history mr-2"></i>
                                Plazos de Pago
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Días de Plazo para Pago <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                        name="dias_plazo_pago" 
                                        value="{{ old('dias_plazo_pago', $configuracionOrg->dias_plazo_pago) }}"
                                        min="1"
                                        max="365"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">Días hábiles desde la radicación de la cuenta de cobro</p>
                                </div>
                                
                                <div class="flex items-center">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" 
                                            name="requiere_paz_y_salvo" 
                                            value="1"
                                            {{ old('requiere_paz_y_salvo', $configuracionOrg->requiere_paz_y_salvo) ? 'checked' : '' }}
                                            class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary mt-0.5">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-700">Requiere Paz y Salvo</span>
                                            <p class="text-xs text-gray-500">Exigir paz y salvo antes de aprobar cuentas</p>
                                        </div>
                                    </label>
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