@extends('layouts.app-dashboard')

@section('title', 'Nuevo Contrato - ARCA-D')

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
                        <a href="{{ route('contratos.index') }}" class="hover:text-primary">Contratos</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-800">Nuevo Contrato</span>
                    </nav>
                </div>

                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-contract text-primary mr-3"></i>
                        Nuevo Contrato
                    </h1>
                    <p class="text-secondary mt-1">Registra un nuevo contrato para {{ $organizacion->nombre_oficial }}</p>
                </div>

                <!-- Formulario -->
                <form action="{{ route('contratos.store') }}" method="POST" class="max-w-5xl">
                    @csrf
                    <input type="hidden" name="organizacion_id" value="{{ $organizacion->id }}">

                    <!-- Información del Contrato -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-accent mr-2"></i>
                            Información del Contrato
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Número de Contrato -->
                            <div>
                                <label for="numero_contrato" class="block text-sm font-medium text-gray-700 mb-2">
                                    Número de Contrato <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="numero_contrato" 
                                       name="numero_contrato" 
                                       value="{{ old('numero_contrato') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('numero_contrato') border-danger @enderror"
                                       placeholder="Ej: CONT-2025-001">
                                @error('numero_contrato')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Supervisor -->
                            <div>
                                <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Supervisor <span class="text-danger">*</span>
                                </label>
                                <select id="supervisor_id" 
                                        name="supervisor_id" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('supervisor_id') border-danger @enderror">
                                    <option value="">Seleccionar supervisor...</option>
                                    @foreach($supervisores as $supervisor)
                                    <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-secondary mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    El contratista se asignará posteriormente
                                </p>
                            </div>

                            <!-- Objeto Contractual -->
                            <div class="md:col-span-2">
                                <label for="objeto_contractual" class="block text-sm font-medium text-gray-700 mb-2">
                                    Objeto Contractual <span class="text-danger">*</span>
                                </label>
                                <textarea id="objeto_contractual" 
                                          name="objeto_contractual" 
                                          rows="3"
                                          required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus resize-none @error('objeto_contractual') border-danger @enderror"
                                          placeholder="Describe el objeto del contrato...">{{ old('objeto_contractual') }}</textarea>
                                @error('objeto_contractual')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información Financiera -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-dollar-sign text-accent mr-2"></i>
                            Información Financiera
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Valor Total -->
                            <div>
                                <label for="valor_total" class="block text-sm font-medium text-gray-700 mb-2">
                                    Valor Total <span class="text-danger">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" 
                                           id="valor_total" 
                                           name="valor_total" 
                                           value="{{ old('valor_total') }}"
                                           required
                                           min="0"
                                           step="0.01"
                                           class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('valor_total') border-danger @enderror"
                                           placeholder="0.00">
                                </div>
                                @error('valor_total')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Retención en la Fuente -->
                            <div>
                                <label for="porcentaje_retencion_fuente" class="block text-sm font-medium text-gray-700 mb-2">
                                    Retención Fuente (%) <span class="text-danger">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           id="porcentaje_retencion_fuente" 
                                           name="porcentaje_retencion_fuente" 
                                           value="{{ old('porcentaje_retencion_fuente', '10.00') }}"
                                           required
                                           min="0"
                                           max="100"
                                           step="0.01"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('porcentaje_retencion_fuente') border-danger @enderror"
                                           placeholder="10.00">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                                @error('porcentaje_retencion_fuente')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Estampilla -->
                            <div>
                                <label for="porcentaje_estampilla" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estampilla (%) <span class="text-danger">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           id="porcentaje_estampilla" 
                                           name="porcentaje_estampilla" 
                                           value="{{ old('porcentaje_estampilla', '2.00') }}"
                                           required
                                           min="0"
                                           max="100"
                                           step="0.01"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('porcentaje_estampilla') border-danger @enderror"
                                           placeholder="2.00">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                                @error('porcentaje_estampilla')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Simulador de Retenciones -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Simulador de Retenciones</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-secondary mb-1">Valor Total</p>
                                    <p class="text-lg font-bold text-gray-800" id="sim-total">$0</p>
                                </div>
                                <div>
                                    <p class="text-secondary mb-1">Retención Fuente</p>
                                    <p class="text-lg font-bold text-danger" id="sim-retencion">$0</p>
                                </div>
                                <div>
                                    <p class="text-secondary mb-1">Estampilla</p>
                                    <p class="text-lg font-bold text-warning" id="sim-estampilla">$0</p>
                                </div>
                                <div>
                                    <p class="text-secondary mb-1">Valor Neto</p>
                                    <p class="text-lg font-bold text-accent" id="sim-neto">$0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vigencia del Contrato -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-accent mr-2"></i>
                            Vigencia del Contrato
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha Inicio -->
                            <div>
                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio" 
                                       value="{{ old('fecha_inicio') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('fecha_inicio') border-danger @enderror">
                                @error('fecha_inicio')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha Fin -->
                            <div>
                                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de Finalización <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       id="fecha_fin" 
                                       name="fecha_fin" 
                                       value="{{ old('fecha_fin') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('fecha_fin') border-danger @enderror">
                                @error('fecha_fin')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Duración calculada -->
                        <div class="mt-4 p-3 bg-accent/10 rounded-lg">
                            <p class="text-sm text-secondary">
                                <i class="fas fa-clock text-accent mr-2"></i>
                                <span>Duración del contrato: <strong id="duracion-contrato">0 días</strong></span>
                            </p>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('contratos.index') }}" 
                           class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-8 rounded-lg hover:shadow-lg transition-all flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Crear Contrato
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    // Calculadora de retenciones en tiempo real
    function calcularRetenciones() {
        const valorTotal = parseFloat(document.getElementById('valor_total').value) || 0;
        const porcRetencion = parseFloat(document.getElementById('porcentaje_retencion_fuente').value) || 0;
        const porcEstampilla = parseFloat(document.getElementById('porcentaje_estampilla').value) || 0;

        const retencion = valorTotal * (porcRetencion / 100);
        const estampilla = valorTotal * (porcEstampilla / 100);
        const neto = valorTotal - retencion - estampilla;

        document.getElementById('sim-total').textContent = '$' + valorTotal.toLocaleString('es-CO', {minimumFractionDigits: 0});
        document.getElementById('sim-retencion').textContent = '-$' + retencion.toLocaleString('es-CO', {minimumFractionDigits: 0});
        document.getElementById('sim-estampilla').textContent = '-$' + estampilla.toLocaleString('es-CO', {minimumFractionDigits: 0});
        document.getElementById('sim-neto').textContent = '$' + neto.toLocaleString('es-CO', {minimumFractionDigits: 0});
    }

    // Calcular duración del contrato
    function calcularDuracion() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        if (fechaInicio && fechaFin) {
            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);
            const diff = fin - inicio;
            const dias = Math.ceil(diff / (1000 * 60 * 60 * 24));

            if (dias > 0) {
                const meses = Math.floor(dias / 30);
                const diasRestantes = dias % 30;
                let texto = '';

                if (meses > 0) {
                    texto += meses + (meses === 1 ? ' mes' : ' meses');
                }
                if (diasRestantes > 0) {
                    if (texto) texto += ' y ';
                    texto += diasRestantes + (diasRestantes === 1 ? ' día' : ' días');
                }

                document.getElementById('duracion-contrato').textContent = texto + ' (' + dias + ' días totales)';
            } else {
                document.getElementById('duracion-contrato').textContent = 'Fecha inválida';
            }
        }
    }

    // Event listeners
    document.getElementById('valor_total').addEventListener('input', calcularRetenciones);
    document.getElementById('porcentaje_retencion_fuente').addEventListener('input', calcularRetenciones);
    document.getElementById('porcentaje_estampilla').addEventListener('input', calcularRetenciones);
    document.getElementById('fecha_inicio').addEventListener('change', calcularDuracion);
    document.getElementById('fecha_fin').addEventListener('change', calcularDuracion);

    // Calcular al cargar
    calcularRetenciones();
    calcularDuracion();
</script>
@endpush
@endsection