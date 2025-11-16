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

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Columna principal - Formulario -->
                        <div class="lg:col-span-2">
                            <form action="{{ route('cuentas-cobro.store') }}" method="POST" id="formCuentaCobro"
                                enctype="multipart/form-data">
                                @csrf

                                <!-- Información General -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
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

                                        <!-- Período Cobrado -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Período Cobrado
                                            </label>
                                            <input type="text" name="periodo_cobrado"
                                                value="{{ old('periodo_cobrado') }}"
                                                placeholder="Ej: Enero 2025, Semana 1-4, etc."
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('periodo_cobrado') border-red-500 @enderror">
                                            @error('periodo_cobrado')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Observaciones -->
                                        <div>
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

                                <!-- Items de la Cuenta de Cobro -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
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

                                <!-- Resumen de Valores -->
                                <div
                                    class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 mb-6 text-white">
                                    <h2 class="text-lg font-bold mb-6 flex items-center">
                                        <i class="fas fa-calculator mr-2"></i>
                                        Resumen de Valores
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                                <!-- Botones de Acción -->
                                <div class="flex items-center justify-end space-x-4">
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

                        <!-- Columna lateral - Subir Archivos -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-paperclip text-accent mr-2"></i>
                                        Archivos Adjuntos
                                    </h2>
                                    <button type="button" onclick="abrirModalSubirArchivo()"
                                        class="bg-primary text-white p-2 rounded-lg hover:bg-primary-dark transition-colors"
                                        title="Subir archivo">
                                        <i class="fas fa-upload"></i>
                                    </button>
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
                                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, XLS, Imágenes, ZIP (Máx. 10MB)</p>
                                </div>
                                <input type="file" name="archivo"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip" required class="hidden"
                                    onchange="mostrarNombreArchivo(this)">
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
            const STORAGE_KEY = 'cuentaCobro_create_draft';
            let archivosParaSubir = [];

            // ==================== FUNCIONES PARA EL MANEJO DE ARCHIVOS ====================

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

                    listaArchivos.innerHTML = archivosParaSubir.map(archivo => {
                        const tiposNombres = {
                            'cuenta_cobro': 'Cuenta de Cobro',
                            'acta_recibido': 'Acta de Recibido',
                            'informe': 'Informe',
                            'foto_evidencia': 'Foto de Evidencia',
                            'planilla': 'Planilla',
                            'soporte_pago': 'Soporte de Pago',
                            'factura': 'Factura',
                            'otro': 'Otro'
                        };

                        return `
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
                                            ${tiposNombres[archivo.tipo_documento] || archivo.tipo_documento}
                                        </span>
                                        ${archivo.descripcion ? `<p class="text-xs text-secondary mt-1">${archivo.descripcion}</p>` : ''}
                                    </div>

                                    <!-- Acciones -->
                                    <div class="flex flex-col space-y-1">
                                        <button type="button" onclick="eliminarArchivo(${archivo.id_temporal})" 
                                                class="text-danger hover:text-red-700 p-1"
                                                title="Eliminar">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
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

            // Cerrar modal al hacer click fuera
            document.getElementById('modalSubirArchivo')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalSubirArchivo();
                }
            });

            // ==================== FUNCIONES PARA ITEMS ====================

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

            // Guardar borrador en localStorage
            function guardarBorrador() {
                const datos = {
                    contrato_id: document.querySelector('[name="contrato_id"]').value,
                    fecha_radicacion: document.querySelector('[name="fecha_radicacion"]').value,
                    periodo_cobrado: document.querySelector('[name="periodo_cobrado"]').value,
                    observaciones: document.querySelector('[name="observaciones"]').value,
                    items: [],
                    itemCounter: itemCounter,
                    porcentajeRetencion: porcentajeRetencion,
                    porcentajeEstampilla: porcentajeEstampilla,
                    timestamp: new Date().toISOString()
                };

                // Guardar items
                document.querySelectorAll('.item-row').forEach((itemRow, index) => {
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

            // Restaurar datos persistidos
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

                        // Cargar porcentajes del contrato
                        const option = contratoSelect.options[contratoSelect.selectedIndex];
                        porcentajeRetencion = parseFloat(option.dataset.retencion) || 0;
                        porcentajeEstampilla = parseFloat(option.dataset.estampilla) || 0;
                    }

                    if (datos.fecha_radicacion) {
                        document.querySelector('[name="fecha_radicacion"]').value = datos.fecha_radicacion;
                    }

                    if (datos.periodo_cobrado) {
                        document.querySelector('[name="periodo_cobrado"]').value = datos.periodo_cobrado;
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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-hashtag text-primary mr-2 text-sm"></i>
                            Item ${itemId}
                        </h3>
                        <button type="button"
                                onclick="eliminarItem(${itemId})"
                                class="text-red-600 hover:text-red-800 transition-colors p-2"
                                title="Eliminar item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Descripción (ancho completo) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea name="items[${itemId}][descripcion]"
                                      rows="2"
                                      required
                                      placeholder="Descripción del trabajo realizado..."
                                      oninput="guardarBorrador()"
                                      class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none">${itemData.descripcion || ''}</textarea>
                        </div>

                        <!-- Fila de campos numéricos -->
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <!-- Cantidad -->
                            <div>
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
                                       class="item-cantidad w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-center">
                            </div>

                            <!-- Valor Unitario -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Valor Unit. <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="items[${itemId}][valor_unitario]"
                                       step="1"
                                       min="0"
                                       value="${itemData.valor_unitario || ''}"
                                       placeholder="0"
                                       required
                                       onchange="calcularValorItem(${itemId}); guardarBorrador()"
                                       oninput="calcularValorItem(${itemId})"
                                       class="item-valor-unitario w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-right">
                            </div>

                            <!-- % Avance -->
                            <div>
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
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-center">
                            </div>

                            <!-- Valor Total -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Valor Total
                                </label>
                                <input type="text"
                                       id="valorTotal${itemId}"
                                       readonly
                                       value="$0"
                                       class="item-valor-total w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-green-600 text-right">
                            </div>
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
                document.querySelector('[name="periodo_cobrado"]').addEventListener('input', guardarBorrador);
                document.querySelector('[name="observaciones"]').addEventListener('input', guardarBorrador);
            }

            function agregarItem() {
                itemCounter++;
                const container = document.getElementById('itemsContainer');
                const noItemsMsg = document.getElementById('noItemsMessage');

                const itemHTML = `
                <div class="item-row bg-gray-50 rounded-lg p-4 border border-gray-200" data-item="${itemCounter}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-hashtag text-primary mr-2 text-sm"></i>
                            Item ${itemCounter}
                        </h3>
                        <button type="button"
                                onclick="eliminarItem(${itemCounter})"
                                class="text-red-600 hover:text-red-800 transition-colors p-2"
                                title="Eliminar item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Descripción (ancho completo) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea name="items[${itemCounter}][descripcion]"
                                      rows="2"
                                      required
                                      placeholder="Descripción del trabajo realizado..."
                                      oninput="guardarBorrador()"
                                      class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"></textarea>
                        </div>

                        <!-- Fila de campos numéricos -->
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <!-- Cantidad -->
                            <div>
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
                                       class="item-cantidad w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-center">
                            </div>

                            <!-- Valor Unitario -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Valor Unit. <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="items[${itemCounter}][valor_unitario]"
                                       step="1"
                                       min="0"
                                       required
                                       placeholder="0"
                                       onchange="calcularValorItem(${itemCounter}); guardarBorrador()"
                                       oninput="calcularValorItem(${itemCounter})"
                                       class="item-valor-unitario w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-right">
                            </div>

                            <!-- % Avance -->
                            <div>
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
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-center">
                            </div>

                            <!-- Valor Total -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Valor Total
                                </label>
                                <input type="text"
                                       id="valorTotal${itemCounter}"
                                       readonly
                                       value="$0"
                                       class="item-valor-total w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-green-600 text-right">
                            </div>
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

            // ==================== ENVÍO DEL FORMULARIO ====================

            // Validación y envío con archivos
            document.getElementById('formCuentaCobro').addEventListener('submit', function(e) {
                e.preventDefault();

                const itemsCount = document.querySelectorAll('.item-row').length;

                if (itemsCount === 0) {
                    alert('Debe agregar al menos un item a la cuenta de cobro');
                    return false;
                }

                // Crear un FormData para manejar los archivos correctamente
                const formData = new FormData(this);

                // Agregar archivos al FormData en la estructura correcta
                archivosParaSubir.forEach((archivo, index) => {
                    formData.append(`archivos[${index}][archivo]`, archivo.archivo);
                    formData.append(`archivos[${index}][tipo_documento]`, archivo.tipo_documento);
                    if (archivo.descripcion) {
                        formData.append(`archivos[${index}][descripcion]`, archivo.descripcion);
                    }
                });

                // Mostrar indicador de carga
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creando Cuenta de Cobro...';
                submitBtn.disabled = true;

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => {
                        if (response.redirected) {
                            // Limpiar el borrador al enviar exitosamente
                            localStorage.removeItem(STORAGE_KEY);
                            window.location.href = response.url;
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Error al crear la cuenta de cobro');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al crear la cuenta de cobro: ' + error.message);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        </script>
    @endpush
@endsection
