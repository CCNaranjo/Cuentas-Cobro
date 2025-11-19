@extends('layouts.app-dashboard')

@section('title', 'Nueva Cuenta de Cobro - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

            <main class="flex-1 overflow-y-auto">
                <div class="p-6">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-3 mb-2">
                                    <a href="{{ route('cuentas-cobro.index') }}"
                                        class="text-secondary hover:text-primary transition-colors">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-file-invoice-dollar text-primary mr-3"></i>
                                        Nueva Cuenta de Cobro
                                    </h1>
                                </div>
                                <p class="text-secondary ml-9">
                                    Complete los datos para registrar una nueva cuenta de cobro
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                `;

                <!-- Errores de Validación -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <h3 class="font-semibold text-red-800 mb-2">Hay errores en el formulario:</h3>
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Layout principal en una sola columna -->
                <div class="space-y-6">
                    <form action="{{ route('cuentas-cobro.store') }}" method="POST" id="formCuentaCobro"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Grid de 2 columnas para Información General y Análisis -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Información General - Ocupa 2/3 -->
                            <div class="lg:col-span-2">
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                    <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                                        <i class="fas fa-info-circle text-primary mr-2"></i>
                                        Información General
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Seleccionar Contrato -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Contrato <span class="text-red-500">*</span>
                                            </label>
                                            <select name="contrato_id" id="contrato_id" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('contrato_id') border-red-500 @enderror">
                                                <option value="">Seleccione un contrato</option>
                                                @foreach ($contratos as $contrato)
                                                    <option value="{{ $contrato->id }}"
                                                        {{ old('contrato_id') == $contrato->id ? 'selected' : '' }}
                                                        data-valor="{{ $contrato->valor_total }}"
                                                        data-retencion="{{ $contrato->porcentaje_retencion_fuente ?? 0 }}"
                                                        data-estampilla="{{ $contrato->porcentaje_estampilla ?? 0 }}">
                                                        {{ $contrato->numero_contrato }} -
                                                        {{ $contrato->contratista->nombre ?? 'Sin contratista' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('contrato_id')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Fecha de Radicación -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Fecha de Radicación <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" name="fecha_radicacion"
                                                value="{{ old('fecha_radicacion', date('Y-m-d')) }}" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('fecha_radicacion') border-red-500 @enderror">
                                            @error('fecha_radicacion')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Período Inicio -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Período Inicio <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" name="periodo_inicio" value="{{ old('periodo_inicio') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('periodo_inicio') border-red-500 @enderror">
                                            @error('periodo_inicio')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Período Fin -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Período Fin <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" name="periodo_fin" value="{{ old('periodo_fin') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('periodo_fin') border-red-500 @enderror">
                                            @error('periodo_fin')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Observaciones -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Observaciones
                                            </label>
                                            <textarea name="observaciones" rows="3" placeholder="Notas adicionales..."
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                                            @error('observaciones')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Análisis - Ocupa 1/3 -->
                            <div class="lg:col-span-1">
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-full">
                                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-chart-bar text-accent mr-2"></i>
                                        ANÁLISIS
                                    </h2>

                                    <div class="space-y-4">
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                            <p class="text-sm text-blue-800 font-semibold mb-2">Informe - Próximo</p>
                                            <p class="text-xs text-blue-600">Análisis de rendimiento disponible pronto
                                            </p>
                                        </div>

                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-semibold">
                                                    JD
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800">John Doe</p>
                                                    <p class="text-xs text-gray-600">Admin Global</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items de la Cuenta de Cobro -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-list-ul text-primary mr-2"></i>
                                    Items de la Cuenta de Cobro
                                </h2>
                                <button type="button" onclick="agregarItem()"
                                    class="bg-gradient-to-r from-accent to-accent text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center text-sm">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Agregar Item
                                </button>
                            </div>

                            <!-- Contenedor de Items -->
                            <div id="itemsContainer" class="space-y-4">
                                <!-- Los items se agregarán aquí dinámicamente -->
                            </div>

                            <!-- Mensaje si no hay items -->
                            <div id="noItemsMessage" class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                <p class="text-secondary">No hay items agregados</p>
                                <p class="text-sm text-gray-400 mt-1">Haga clic en "Agregar Item" para comenzar</p>
                            </div>
                        </div>

                        <!-- Resumen de Valores y Archivos en 2 columnas -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Resumen de Valores -->
                            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 text-white">
                                <h2 class="text-lg font-bold mb-6 flex items-center">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Resumen de Valores
                                </h2>

                                <div class="space-y-4">
                                    <!-- Valor Bruto -->
                                    <div class="bg-white/10 rounded-lg p-4">
                                        <p class="text-white/80 text-sm mb-1">Valor Bruto</p>
                                        <p class="text-2xl font-bold" id="displayValorBruto">$0</p>
                                    </div>

                                    <!-- Total Retenciones -->
                                    <div class="bg-white/10 rounded-lg p-4">
                                        <p class="text-white/80 text-sm mb-1">Total Retenciones</p>
                                        <p class="text-2xl font-bold" id="displayRetenciones">$0</p>
                                        <div class="text-xs text-white/70 mt-2 space-y-1">
                                            <div class="flex justify-between">
                                                <span>Retención Fuente:</span>
                                                <span id="displayRetencionFuente">$0</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Estampilla:</span>
                                                <span id="displayEstampilla">$0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Valor Neto -->
                                    <div class="bg-white/10 rounded-lg p-4">
                                        <p class="text-white/80 text-sm mb-1">Valor Neto a Pagar</p>
                                        <p class="text-3xl font-bold" id="displayValorNeto">$0</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Archivos Adjuntos -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-paperclip text-accent mr-2"></i>
                                        Archivos Adjuntos
                                    </h2>
                                    @if (Auth::user()->tienePermiso('cargar-documentos', session('organizacion_actual')))
                                        <button type="button" onclick="abrirModalSubirArchivo()"
                                            class="bg-primary text-white p-2 rounded-lg hover:bg-primary-dark transition-colors"
                                            title="Subir archivo">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Lista de archivos a subir -->
                                <div id="listaArchivos" class="space-y-3 max-h-[300px] overflow-y-auto">
                                    <div class="text-center py-6" id="sinArchivos">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-sm text-secondary">No hay archivos para subir</p>
                                        <p class="text-xs text-gray-400 mt-1">Agrega archivos usando el botón superior
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end space-x-4 pt-6">
                            <a href="{{ route('cuentas-cobro.index') }}"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cuenta de Cobro
                            </button>
                        </div>
                    </form>
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
                            <option value="cuenta_cobro">Cuenta de Cobro</option>
                            <option value="acta_recibido">Acta de Recibido</option>
                            <option value="informe">Informe</option>
                            <option value="foto_evidencia">Foto de Evidencia</option>
                            <option value="planilla">Planilla</option>
                            <option value="soporte_pago">Soporte de Pago</option>
                            <option value="factura">Factura</option>
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
                                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Máx. 10MB)
                                    </p>
                                </div>
                                <input type="file" name="archivo" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                    required class="hidden" onchange="mostrarNombreArchivo(this)">
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
                            <span>El archivo se adjuntará a la cuenta de cobro durante la creación.</span>
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
            let itemCounter = 0;
            let porcentajeRetencion = 0;
            let porcentajeEstampilla = 0;
            let archivosParaSubir = [];
            const STORAGE_KEY = 'cuentaCobro_create_draft';

            // Agregar primer item al cargar
            document.addEventListener('DOMContentLoaded', function() {
                // Intentar restaurar datos guardados
                restaurarDatosPersistidos();

                // Si no hay items restaurados, agregar uno vacío
                if (document.querySelectorAll('.item-row').length === 0) {
                    agregarItem();
                }

                // Listener para cambio de contrato
                document.getElementById('contrato_id').addEventListener('change', function() {
                    const option = this.options[this.selectedIndex];
                    porcentajeRetencion = parseFloat(option.dataset.retencion) || 0;
                    porcentajeEstampilla = parseFloat(option.dataset.estampilla) || 0;
                    calcularTotales();
                    guardarBorrador();
                });

                // Guardar automáticamente cuando se modifican campos
                configurarAutoguardado();
            });

            // ========================================
            // FUNCIONES DE ARCHIVOS
            // ========================================
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

                // Validar tamaño del archivo (10MB máximo)
                if (archivo.size > 10 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
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
                guardarBorrador();
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
                                    type="button"
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
                guardarBorrador();
            }

            // ========================================
            // FUNCIONES DE BORRADOR
            // ========================================
            function guardarBorrador() {
                const datos = {
                    contrato_id: document.querySelector('[name="contrato_id"]').value,
                    fecha_radicacion: document.querySelector('[name="fecha_radicacion"]').value,
                    periodo_inicio: document.querySelector('[name="periodo_inicio"]').value,
                    periodo_fin: document.querySelector('[name="periodo_fin"]').value,
                    observaciones: document.querySelector('[name="observaciones"]').value,
                    items: [],
                    archivos: archivosParaSubir.map(a => ({
                        tipo_documento: a.tipo_documento,
                        descripcion: a.descripcion,
                        nombre_original: a.nombre_original,
                        id_temporal: a.id_temporal
                    })),
                    itemCounter: itemCounter,
                    porcentajeRetencion: porcentajeRetencion,
                    porcentajeEstampilla: porcentajeEstampilla,
                    timestamp: new Date().toISOString()
                };

                // Guardar items
                document.querySelectorAll('.item-row').forEach((itemRow) => {
                    const itemId = itemRow.getAttribute('data-item');
                    datos.items.push({
                        itemId: itemId,
                        descripcion: itemRow.querySelector('.item-descripcion').value,
                        cantidad: itemRow.querySelector('.item-cantidad').value,
                        valor_unitario: itemRow.querySelector('.item-valor-unitario').value,
                        porcentaje_avance: itemRow.querySelector('[name*="[porcentaje_avance]"]').value
                    });
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(datos));
                console.log('Borrador guardado automáticamente');
            }

            function restaurarDatosPersistidos() {
                const datosGuardados = localStorage.getItem(STORAGE_KEY);

                if (!datosGuardados) {
                    return;
                }

                try {
                    const datos = JSON.parse(datosGuardados);

                    // Mostrar notificación de restauración
                    mostrarNotificacionRestauracion(datos.timestamp);

                    // Restaurar campos generales
                    if (datos.contrato_id) {
                        const contratoSelect = document.querySelector('[name="contrato_id"]');
                        contratoSelect.value = datos.contrato_id;

                        const option = contratoSelect.options[contratoSelect.selectedIndex];
                        porcentajeRetencion = parseFloat(option.dataset.retencion) || 0;
                        porcentajeEstampilla = parseFloat(option.dataset.estampilla) || 0;
                    }

                    if (datos.fecha_radicacion) {
                        document.querySelector('[name="fecha_radicacion"]').value = datos.fecha_radicacion;
                    }

                    if (datos.periodo_inicio) {
                        document.querySelector('[name="periodo_inicio"]').value = datos.periodo_inicio;
                    }

                    if (datos.periodo_fin) {
                        document.querySelector('[name="periodo_fin"]').value = datos.periodo_fin;
                    }

                    if (datos.observaciones) {
                        document.querySelector('[name="observaciones"]').value = datos.observaciones;
                    }

                    // Restaurar counter
                    itemCounter = datos.itemCounter || 0;

                    // Restaurar items
                    if (datos.items && datos.items.length > 0) {
                        datos.items.forEach(item => {
                            agregarItemRestaurado(item);
                        });
                    }

                    // Restaurar archivos (solo metadata, no los archivos reales)
                    if (datos.archivos && datos.archivos.length > 0) {
                        // Mostrar nota de que los archivos no se pueden restaurar
                        const notaArchivos = document.createElement('div');
                        notaArchivos.className = 'mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg';
                        notaArchivos.innerHTML = `
                    <p class="text-sm text-yellow-800 flex items-start">
                        <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                        <span>Se encontraron ${datos.archivos.length} archivo(s) en el borrador. Por seguridad, deberás volver a agregarlos.</span>
                    </p>
                `;
                        document.querySelector('#listaArchivos').parentNode.insertBefore(notaArchivos, document.getElementById(
                            'listaArchivos'));
                    }

                    calcularTotales();
                } catch (error) {
                    console.error('Error al restaurar datos:', error);
                }
            }

            function mostrarNotificacionRestauracion(timestamp) {
                const fecha = new Date(timestamp);
                const fechaFormateada = fecha.toLocaleString('es-CO');

                const notificacion = document.createElement('div');
                notificacion.className = 'mb-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4';
                notificacion.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-800 mb-1">Borrador restaurado</h3>
                        <p class="text-sm text-blue-700">
                            Se han restaurado los datos guardados el ${fechaFormateada}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="limpiarBorrador()" class="text-blue-600 hover:text-blue-800 text-sm font-semibold ml-4">
                    Descartar borrador
                </button>
            </div>
        `;

                const header = document.querySelector('.mb-6');
                header.after(notificacion);
            }

            function limpiarBorrador() {
                if (confirm('¿Está seguro de que desea descartar el borrador guardado y empezar de nuevo?')) {
                    localStorage.removeItem(STORAGE_KEY);
                    location.reload();
                }
            }

            function agregarItemRestaurado(itemData) {
                const container = document.getElementById('itemsContainer');
                const noItemsMsg = document.getElementById('noItemsMessage');
                const itemId = itemData.itemId;

                const itemHTML = `
            <div class="item-row bg-gray-50 rounded-lg p-4 border border-gray-200" data-item="${itemId}">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Item #${itemId}</h3>
                    <button type="button"
                            onclick="eliminarItem(${itemId})"
                            class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <textarea name="items[${itemId}][descripcion]"
                                  rows="2"
                                  required
                                  placeholder="Descripción del trabajo realizado..."
                                  class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                  oninput="guardarBorrador()">${itemData.descripcion || ''}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemId}][cantidad]"
                               step="0.01"
                               min="0"
                               value="${itemData.cantidad || 1}"
                               required
                               onchange="calcularValorItem(${itemId}); guardarBorrador()"
                               class="item-cantidad w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Unitario <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemId}][valor_unitario]"
                               step="0.01"
                               min="0"
                               value="${itemData.valor_unitario || ''}"
                               required
                               onchange="calcularValorItem(${itemId}); guardarBorrador()"
                               class="item-valor-unitario w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            % Avance
                        </label>
                        <input type="number"
                               name="items[${itemId}][porcentaje_avance]"
                               step="0.01"
                               min="0"
                               max="100"
                               value="${itemData.porcentaje_avance || ''}"
                               placeholder="0"
                               oninput="guardarBorrador()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Total
                        </label>
                        <input type="text"
                               id="valorTotal${itemId}"
                               readonly
                               class="item-valor-total w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-green-600">
                    </div>
                </div>
            </div>
        `;

                container.insertAdjacentHTML('beforeend', itemHTML);
                noItemsMsg.style.display = 'none';
                calcularValorItem(itemId);
            }

            function configurarAutoguardado() {
                document.querySelector('[name="contrato_id"]').addEventListener('change', guardarBorrador);
                document.querySelector('[name="fecha_radicacion"]').addEventListener('change', guardarBorrador);
                document.querySelector('[name="periodo_inicio"]').addEventListener('change', guardarBorrador);
                document.querySelector('[name="periodo_fin"]').addEventListener('change', guardarBorrador);
                document.querySelector('[name="observaciones"]').addEventListener('input', guardarBorrador);
            }

            // ========================================
            // FUNCIONES DE ITEMS
            // ========================================
            function agregarItem() {
                itemCounter++;
                const container = document.getElementById('itemsContainer');
                const noItemsMsg = document.getElementById('noItemsMessage');

                const itemHTML = `
            <div class="item-row bg-gray-50 rounded-lg p-4 border border-gray-200" data-item="${itemCounter}">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Item #${itemCounter}</h3>
                    <button type="button"
                            onclick="eliminarItem(${itemCounter})"
                            class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <textarea name="items[${itemCounter}][descripcion]"
                                  rows="2"
                                  required
                                  placeholder="Descripción del trabajo realizado..."
                                  oninput="guardarBorrador()"
                                  class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemCounter}][cantidad]"
                               step="0.01"
                               min="0"
                               value="1"
                               required
                               onchange="calcularValorItem(${itemCounter}); guardarBorrador()"
                               class="item-cantidad w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Unitario <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemCounter}][valor_unitario]"
                               step="0.01"
                               min="0"
                               required
                               onchange="calcularValorItem(${itemCounter}); guardarBorrador()"
                               class="item-valor-unitario w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            % Avance
                        </label>
                        <input type="number"
                               name="items[${itemCounter}][porcentaje_avance]"
                               step="0.01"
                               min="0"
                               max="100"
                               placeholder="0"
                               oninput="guardarBorrador()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Total
                        </label>
                        <input type="text"
                               id="valorTotal${itemCounter}"
                               readonly
                               class="item-valor-total w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-green-600">
                    </div>
                </div>
            </div>
        `;

            if (datos.fecha_radicacion) {
                document.querySelector('[name="fecha_radicacion"]').value = datos.fecha_radicacion;
            }

            function eliminarItem(itemId) {
                const item = document.querySelector(`[data-item="${itemId}"]`);
                if (item) {
                    item.remove();
                    calcularTotales();

                    const container = document.getElementById('itemsContainer');
                    const noItemsMsg = document.getElementById('noItemsMessage');
                    if (container.children.length === 0) {
                        noItemsMsg.style.display = 'block';
                    }

                    guardarBorrador();
                }
            }

            if (datos.observaciones) {
                document.querySelector('[name="observaciones"]').value = datos.observaciones;
            }

            // Restaurar counter
            itemCounter = datos.itemCounter || 0;

                document.querySelectorAll('.item-row').forEach(item => {
                    const cantidad = parseFloat(item.querySelector('.item-cantidad').value) || 0;
                    const valorUnitario = parseFloat(item.querySelector('.item-valor-unitario').value) || 0;
                    valorBruto += cantidad * valorUnitario;
                });

                const retencionFuente = valorBruto * (porcentajeRetencion / 100);
                const estampilla = valorBruto * (porcentajeEstampilla / 100);
                const totalRetenciones = retencionFuente + estampilla;
                const valorNeto = valorBruto - totalRetenciones;

                document.getElementById('displayValorBruto').textContent = formatearMoneda(valorBruto);
                document.getElementById('displayRetencionFuente').textContent = formatearMoneda(retencionFuente);
                document.getElementById('displayEstampilla').textContent = formatearMoneda(estampilla);
                document.getElementById('displayRetenciones').textContent = formatearMoneda(totalRetenciones);
                document.getElementById('displayValorNeto').textContent = formatearMoneda(valorNeto);
            }

            calcularTotales();
        } catch (error) {
            console.error('Error al restaurar datos:', error);
        }
    }

    // Mostrar notificación de restauración
    function mostrarNotificacionRestauracion(timestamp) {
        const fecha = new Date(timestamp);
        const fechaFormateada = fecha.toLocaleString('es-CO');

        const notificacion = document.createElement('div');
        notificacion.className = 'mb-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4';
        notificacion.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-800 mb-1">Borrador restaurado</h3>
                        <p class="text-sm text-blue-700">
                            Se han restaurado los datos guardados el ${fechaFormateada}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="limpiarBorrador()" class="text-blue-600 hover:text-blue-800 text-sm font-semibold ml-4">
                    Descartar borrador
                </button>
            </div>
        `;

        // Insertar después del header
        const header = document.querySelector('.mb-6');
        header.after(notificacion);
    }

    // Limpiar borrador
    function limpiarBorrador() {
        if (confirm('¿Está seguro de que desea descartar el borrador guardado y empezar de nuevo?')) {
            localStorage.removeItem(STORAGE_KEY);
            location.reload();
        }
    }

    // Agregar item restaurado
    function agregarItemRestaurado(itemData) {
        const container = document.getElementById('itemsContainer');
        const noItemsMsg = document.getElementById('noItemsMessage');
        const itemId = itemData.itemId;

        const itemHTML = `
            <div class="item-row bg-gray-50 rounded-lg p-4 border border-gray-200" data-item="${itemId}">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Item #${itemId}</h3>
                    <button type="button"
                            onclick="eliminarItem(${itemId})"
                            class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Descripción -->
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <textarea name="items[${itemId}][descripcion]"
                                  rows="2"
                                  required
                                  placeholder="Descripción del trabajo realizado..."
                                  class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                  oninput="guardarBorrador()">${itemData.descripcion || ''}</textarea>
                    </div>

                    <!-- Cantidad -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemId}][cantidad]"
                               step="0.01"
                               min="0"
                               value="${itemData.cantidad || 1}"
                               required
                               onchange="calcularValorItem(${itemId}); guardarBorrador()"
                               class="item-cantidad w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- Valor Unitario -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Unitario <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemId}][valor_unitario]"
                               step="0.01"
                               min="0"
                               value="${itemData.valor_unitario || ''}"
                               required
                               onchange="calcularValorItem(${itemId}); guardarBorrador()"
                               class="item-valor-unitario w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- % Avance -->
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            % Avance
                        </label>
                        <input type="number"
                               name="items[${itemId}][porcentaje_avance]"
                               step="0.01"
                               min="0"
                               max="100"
                               value="${itemData.porcentaje_avance || ''}"
                               placeholder="0"
                               oninput="guardarBorrador()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- Valor Total -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Total
                        </label>
                        <input type="text"
                               id="valorTotal${itemId}"
                               readonly
                               class="item-valor-total w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-green-600">
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', itemHTML);
        noItemsMsg.style.display = 'none';

        // Calcular valor del item
        calcularValorItem(itemId);
    }

    // Configurar autoguardado
    function configurarAutoguardado() {
        // Guardar al cambiar campos generales
        document.querySelector('[name="contrato_id"]').addEventListener('change', guardarBorrador);
        document.querySelector('[name="fecha_radicacion"]').addEventListener('change', guardarBorrador);
        document.querySelector('[name="periodo_inicio"]').addEventListener('change', guardarBorrador);
        document.querySelector('[name="periodo_fin"]').addEventListener('change', guardarBorrador);
        document.querySelector('[name="observaciones"]').addEventListener('input', guardarBorrador);
    }

    function agregarItem() {
        itemCounter++;
        const container = document.getElementById('itemsContainer');
        const noItemsMsg = document.getElementById('noItemsMessage');

        const itemHTML = `
            <div class="item-row bg-gray-50 rounded-lg p-4 border border-gray-200" data-item="${itemCounter}">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Item #${itemCounter}</h3>
                    <button type="button"
                            onclick="eliminarItem(${itemCounter})"
                            class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Descripción -->
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <textarea name="items[${itemCounter}][descripcion]"
                                  rows="2"
                                  required
                                  placeholder="Descripción del trabajo realizado..."
                                  oninput="guardarBorrador()"
                                  class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>

                    <!-- Cantidad -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemCounter}][cantidad]"
                               step="0.01"
                               min="0"
                               value="1"
                               required
                               onchange="calcularValorItem(${itemCounter}); guardarBorrador()"
                               class="item-cantidad w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- Valor Unitario -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Unitario <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="items[${itemCounter}][valor_unitario]"
                               step="0.01"
                               min="0"
                               required
                               onchange="calcularValorItem(${itemCounter}); guardarBorrador()"
                               class="item-valor-unitario w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- % Avance -->
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            % Avance
                        </label>
                        <input type="number"
                               name="items[${itemCounter}][porcentaje_avance]"
                               step="0.01"
                               min="0"
                               max="100"
                               placeholder="0"
                               oninput="guardarBorrador()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- Valor Total -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Total
                        </label>
                        <input type="text"
                               id="valorTotal${itemCounter}"
                               readonly
                               class="item-valor-total w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-green-600">
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', itemHTML);
        noItemsMsg.style.display = 'none';
        guardarBorrador();
    }

    function eliminarItem(itemId) {
        const item = document.querySelector(`[data-item="${itemId}"]`);
        if (item) {
            item.remove();
            calcularTotales();

            // Mostrar mensaje si no hay items
            const container = document.getElementById('itemsContainer');
            const noItemsMsg = document.getElementById('noItemsMessage');
            if (container.children.length === 0) {
                noItemsMsg.style.display = 'block';
            }

            guardarBorrador();
        }
    }

    function calcularValorItem(itemId) {
        const item = document.querySelector(`[data-item="${itemId}"]`);
        const cantidad = parseFloat(item.querySelector('.item-cantidad').value) || 0;
        const valorUnitario = parseFloat(item.querySelector('.item-valor-unitario').value) || 0;
        const valorTotal = cantidad * valorUnitario;
        
        document.getElementById(`valorTotal${itemId}`).value = formatearMoneda(valorTotal);
        
        calcularTotales();
    }

    function calcularTotales() {
        let valorBruto = 0;
        
        // Sumar todos los items
        document.querySelectorAll('.item-row').forEach(item => {
            const cantidad = parseFloat(item.querySelector('.item-cantidad').value) || 0;
            const valorUnitario = parseFloat(item.querySelector('.item-valor-unitario').value) || 0;
            valorBruto += cantidad * valorUnitario;
        });
        
        // Calcular retenciones
        const retencionFuente = valorBruto * (porcentajeRetencion / 100);
        const estampilla = valorBruto * (porcentajeEstampilla / 100);
        const totalRetenciones = retencionFuente + estampilla;
        const valorNeto = valorBruto - totalRetenciones;
        
        // Actualizar display
        document.getElementById('displayValorBruto').textContent = formatearMoneda(valorBruto);
        document.getElementById('displayRetencionFuente').textContent = formatearMoneda(retencionFuente);
        document.getElementById('displayEstampilla').textContent = formatearMoneda(estampilla);
        document.getElementById('displayRetenciones').textContent = formatearMoneda(totalRetenciones);
        document.getElementById('displayValorNeto').textContent = formatearMoneda(valorNeto);
    }

    function formatearMoneda(valor) {
        return '$' + Math.round(valor).toLocaleString('es-CO');
    }

    // Validación antes de enviar
    document.getElementById('formCuentaCobro').addEventListener('submit', function(e) {
        const itemsCount = document.querySelectorAll('.item-row').length;

        if (itemsCount === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un item a la cuenta de cobro');
            return false;
        }

        // Limpiar el borrador al enviar exitosamente
        localStorage.removeItem(STORAGE_KEY);
    });
    // ========================================
    // ENVÍO DEL FORMULARIO
    // ========================================
    document.getElementById('formCuentaCobro').addEventListener('submit', function(e) {
        const itemsCount = document.querySelectorAll('.item-row').length;

        if (itemsCount === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un item a la cuenta de cobro');
            return false;
        }

        // Crear FormData correctamente
        const formData = new FormData(this);

        // Agregar archivos al FormData de manera correcta
        archivosParaSubir.forEach((archivo, index) => {
            formData.append(`archivos[${index}][archivo]`, archivo.archivo);
            formData.append(`archivos[${index}][tipo_documento]`, archivo.tipo_documento);
            formData.append(`archivos[${index}][descripcion]`, archivo.descripcion || '');
        });

        // Mostrar loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creando Cuenta de Cobro...';
        submitBtn.disabled = true;

        // Enviar con fetch
        fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error del servidor');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.redirect) {
                    localStorage.removeItem(STORAGE_KEY);
                    window.location.href = data.redirect;
                } else {
                    throw new Error('Respuesta inesperada del servidor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear la cuenta de cobro: ' + error.message);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });

        e.preventDefault();
    });
</script>
@endpush
@endsection

