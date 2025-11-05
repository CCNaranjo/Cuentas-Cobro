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
                        <p class="text-secondary mt-1">Registra un nuevo contrato para {{ $organizacion->nombre_oficial }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Columna principal - Formulario -->
                        <div class="lg:col-span-2">
                            <form action="{{ route('contratos.store') }}" method="POST" class="max-w-5xl" id="formContrato"
                                enctype="multipart/form-data">
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
                                            <label for="numero_contrato"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Número de Contrato <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="numero_contrato" name="numero_contrato"
                                                value="{{ old('numero_contrato') }}" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('numero_contrato') border-danger @enderror"
                                                placeholder="Ej: CONT-2025-001">
                                            @error('numero_contrato')
                                                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Estado del Contrato -->
                                        <div>
                                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                                                Estado <span class="text-danger">*</span>
                                            </label>
                                            <select id="estado" name="estado" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('estado') border-danger @enderror">
                                                <option value="borrador" {{ old('estado', 'borrador') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>
                                                    Activo</option>
                                                <option value="suspendido" {{ old('estado') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                            </select>
                                            @error('estado')
                                                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Supervisor -->
                                        <div>
                                            <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                                Supervisor <span class="text-danger">*</span>
                                            </label>
                                            <select id="supervisor_id" name="supervisor_id" required
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
                                        </div>

                                        <!-- Contratista -->
                                        <div>
                                            <label for="contratista_id"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Contratista
                                            </label>
                                            <select id="contratista_id" name="contratista_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('contratista_id') border-danger @enderror">
                                                <option value="">Seleccionar contratista...</option>
                                                @foreach($contratistas as $contratista)
                                                    <option value="{{ $contratista->id }}" {{ old('contratista_id') == $contratista->id ? 'selected' : '' }}>
                                                        {{ $contratista->nombre }} - {{ $contratista->documento_identidad }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('contratista_id')
                                                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                            @enderror
                                            <p class="text-xs text-secondary mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                El contratista se puede asignar ahora o posteriormente
                                            </p>
                                        </div>

                                        <!-- Objeto Contractual -->
                                        <div class="md:col-span-2">
                                            <label for="objeto_contractual"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Objeto Contractual <span class="text-danger">*</span>
                                            </label>
                                            <textarea id="objeto_contractual" name="objeto_contractual" rows="3" required
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
                                                <span
                                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                                <input type="number" id="valor_total" name="valor_total"
                                                    value="{{ old('valor_total') }}" required min="0" step="0.01"
                                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('valor_total') border-danger @enderror"
                                                    placeholder="0.00">
                                            </div>
                                            @error('valor_total')
                                                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Retención en la Fuente -->
                                        <div>
                                            <label for="porcentaje_retencion_fuente"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Retención Fuente (%) <span class="text-danger">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="number" id="porcentaje_retencion_fuente"
                                                    name="porcentaje_retencion_fuente"
                                                    value="{{ old('porcentaje_retencion_fuente', '10.00') }}" required
                                                    min="0" max="100" step="0.01"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('porcentaje_retencion_fuente') border-danger @enderror"
                                                    placeholder="10.00">
                                                <span
                                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                            </div>
                                            @error('porcentaje_retencion_fuente')
                                                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Estampilla -->
                                        <div>
                                            <label for="porcentaje_estampilla"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Estampilla (%) <span class="text-danger">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="number" id="porcentaje_estampilla" name="porcentaje_estampilla"
                                                    value="{{ old('porcentaje_estampilla', '2.00') }}" required min="0"
                                                    max="100" step="0.01"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('porcentaje_estampilla') border-danger @enderror"
                                                    placeholder="2.00">
                                                <span
                                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
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
                                            <input type="date" id="fecha_inicio" name="fecha_inicio"
                                                value="{{ old('fecha_inicio') }}" required
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
                                            <input type="date" id="fecha_fin" name="fecha_fin"
                                                value="{{ old('fecha_fin') }}" required
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
                                            <span>Duración del contrato: <strong id="duracion-contrato">0
                                                    días</strong></span>
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

                        <!-- Columna lateral - Subir Archivos -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-paperclip text-accent mr-2"></i>
                                        Archivos Adjuntos
                                    </h2>
                                    @if(Auth::user()->tienePermiso('subir-archivo-contrato', $organizacion->id))
                                        <button onclick="abrirModalSubirArchivo()"
                                            class="bg-primary text-white p-2 rounded-lg hover:bg-primary-dark transition-colors"
                                            title="Subir archivo">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Lista de archivos a subir -->
                                <div id="listaArchivos" class="space-y-3 max-h-[600px] overflow-y-auto">
                                    <div class="text-center py-6" id="sinArchivos">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-sm text-secondary">No hay archivos para subir</p>
                                        <p class="text-xs text-gray-400 mt-1">Agrega archivos usando el botón superior</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para Subir Archivo -->
    <div id="modalSubirArchivo"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-upload text-primary mr-2"></i>
                        Subir Archivo
                    </h3>
                    <button onclick="cerrarModalSubirArchivo()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form id="formSubirArchivo">
                <div class="p-6 space-y-4">
                    <!-- Tipo de Documento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Documento <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_documento" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Seleccionar tipo...</option>
                            <option value="contrato_firmado">Contrato Firmado</option>
                            <option value="adicion">Adición</option>
                            <option value="suspension">Suspensión</option>
                            <option value="acta_inicio">Acta de Inicio</option>
                            <option value="acta_liquidacion">Acta de Liquidación</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- Archivo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Archivo <span class="text-danger">*</span>
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500">
                                        <span class="font-semibold">Click para subir</span> o arrastra el archivo
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX (Máx. 10MB)</p>
                                </div>
                                <input type="file" name="archivo" accept=".pdf,.doc,.docx,.xls,.xlsx" required
                                    class="hidden" onchange="mostrarNombreArchivo(this)">
                            </label>
                        </div>
                        <p id="nombreArchivoSeleccionado" class="text-sm text-accent mt-2 hidden"></p>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción (Opcional)
                        </label>
                        <textarea name="descripcion" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                            placeholder="Agrega una descripción del archivo..."></textarea>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800 flex items-start">
                            <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                            <span>El archivo se adjuntará al contrato durante la creación.</span>
                        </p>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalSubirArchivo()"
                        class="px-6 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="agregarArchivo()"
                        class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Agregar Archivo
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let archivosParaSubir = [];

            // Calculadora de retenciones en tiempo real
            function calcularRetenciones() {
                const valorTotal = parseFloat(document.getElementById('valor_total').value) || 0;
                const porcRetencion = parseFloat(document.getElementById('porcentaje_retencion_fuente').value) || 0;
                const porcEstampilla = parseFloat(document.getElementById('porcentaje_estampilla').value) || 0;

                const retencion = valorTotal * (porcRetencion / 100);
                const estampilla = valorTotal * (porcEstampilla / 100);
                const neto = valorTotal - retencion - estampilla;

                document.getElementById('sim-total').textContent = '$' + valorTotal.toLocaleString('es-CO', { minimumFractionDigits: 0 });
                document.getElementById('sim-retencion').textContent = '-$' + retencion.toLocaleString('es-CO', { minimumFractionDigits: 0 });
                document.getElementById('sim-estampilla').textContent = '-$' + estampilla.toLocaleString('es-CO', { minimumFractionDigits: 0 });
                document.getElementById('sim-neto').textContent = '$' + neto.toLocaleString('es-CO', { minimumFractionDigits: 0 });
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

            // Funciones para el manejo de archivos
            function abrirModalSubirArchivo() {
                document.getElementById('modalSubirArchivo').classList.remove('hidden');
            }

            function cerrarModalSubirArchivo() {
                document.getElementById('modalSubirArchivo').classList.add('hidden');
                document.getElementById('formSubirArchivo').reset();
                document.getElementById('nombreArchivoSeleccionado').classList.add('hidden');
            }

            function mostrarNombreArchivo(input) {
                const nombreArchivo = input.files[0]?.name;
                const elemento = document.getElementById('nombreArchivoSeleccionado');

                if (nombreArchivo) {
                    elemento.textContent = '📄 ' + nombreArchivo;
                    elemento.classList.remove('hidden');
                } else {
                    elemento.classList.add('hidden');
                }
            }

            function agregarArchivo() {
                const formData = new FormData(document.getElementById('formSubirArchivo'));
                const archivo = formData.get('archivo');
                const tipoDocumento = formData.get('tipo_documento');
                const descripcion = formData.get('descripcion');

                if (!archivo || !tipoDocumento) {
                    alert('Por favor, complete todos los campos requeridos');
                    return;
                }

                // Agregar archivo a la lista
                const archivoData = {
                    archivo: archivo,
                    tipo_documento: tipoDocumento,
                    descripcion: descripcion,
                    nombre_original: archivo.name,
                    id_temporal: Date.now() + Math.random()
                };

                archivosParaSubir.push(archivoData);
                actualizarListaArchivos();
                cerrarModalSubirArchivo();
            }

            function actualizarListaArchivos() {
                const listaArchivos = document.getElementById('listaArchivos');
                const sinArchivos = document.getElementById('sinArchivos');

                if (archivosParaSubir.length > 0) {
                    sinArchivos.style.display = 'none';

                    listaArchivos.innerHTML = archivosParaSubir.map(archivo => `
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-start space-x-2">
                                <!-- Icono -->
                                <div class="w-10 h-10 rounded bg-accent/10 flex items-center justify-center text-accent flex-shrink-0">
                                    <i class="fas fa-file"></i>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm text-gray-800 truncate" title="${archivo.nombre_original}">
                                        ${archivo.nombre_original}
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-semibold bg-primary/10 text-primary">
                                        ${archivo.tipo_documento.replace('_', ' ')}
                                    </span>
                                    ${archivo.descripcion ? `<p class="text-xs text-secondary mt-1">${archivo.descripcion}</p>` : ''}
                                </div>

                                <!-- Acciones -->
                                <div class="flex flex-col space-y-1">
                                    <button onclick="eliminarArchivo(${archivo.id_temporal})" 
                                            class="text-danger hover:text-red-700 p-1"
                                            title="Eliminar">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    sinArchivos.style.display = 'block';
                    listaArchivos.innerHTML = '';
                    listaArchivos.appendChild(sinArchivos);
                }
            }

            function eliminarArchivo(idTemporal) {
                archivosParaSubir = archivosParaSubir.filter(archivo => archivo.id_temporal !== idTemporal);
                actualizarListaArchivos();
            }

            // Modificar el envío del formulario para incluir archivos
            document.getElementById('formContrato').addEventListener('submit', function (e) {
                // Crear un FormData para manejar los archivos correctamente
                const formData = new FormData(this);

                // Limpiar archivos existentes en el FormData
                for (let key of formData.keys()) {
                    if (key.startsWith('archivos[')) {
                        formData.delete(key);
                    }
                }

                // Agregar archivos al FormData en la estructura correcta
                archivosParaSubir.forEach((archivo, index) => {
                    formData.append(`archivos[${index}][archivo]`, archivo.archivo);
                    formData.append(`archivos[${index}][tipo_documento]`, archivo.tipo_documento);
                    if (archivo.descripcion) {
                        formData.append(`archivos[${index}][descripcion]`, archivo.descripcion);
                    }
                });

                // Enviar el formulario manualmente
                e.preventDefault();

                // Mostrar indicador de carga
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creando Contrato...';
                submitBtn.disabled = true;

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Error al crear el contrato');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al crear el contrato: ' + error.message);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });

            // Cerrar modal al hacer click fuera
            document.getElementById('modalSubirArchivo')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    cerrarModalSubirArchivo();
                }
            });

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