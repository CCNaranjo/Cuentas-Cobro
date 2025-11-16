@extends('layouts.app-dashboard')

@section('title', 'Editar Cuenta de Cobro - ARCA-D')

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
                                    <a href="{{ route('cuentas-cobro.show', $cuentaCobro->id) }}"
                                       class="text-secondary hover:text-primary transition-colors">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-edit text-primary mr-3"></i>
                                        Editar Cuenta de Cobro
                                    </h1>
                                </div>
                                <p class="text-secondary ml-9">
                                    Modificar los datos de la cuenta de cobro {{ $cuentaCobro->numero_cuenta_cobro }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-4 py-2 rounded-lg text-sm font-semibold
                                    @if($cuentaCobro->estado === 'borrador') bg-gray-100 text-gray-700
                                    @endif">
                                    <i class="fas fa-circle text-xs mr-1"></i>
                                    {{ ucfirst($cuentaCobro->estado) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Alerta de Estado -->
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-500 text-xl mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <h3 class="font-semibold text-yellow-800 mb-1">Edición de Cuenta de Cobro</h3>
                                <p class="text-sm text-yellow-700">
                                    Solo puedes editar cuentas de cobro en estado <strong>borrador</strong>.
                                    Una vez radicada o en otro estado, no se podrán realizar modificaciones.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Errores de Validación -->
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-red-800 mb-2">Hay errores en el formulario:</h3>
                                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Columna Principal - Formulario -->
                        <div class="lg:col-span-2">
                            <form action="{{ route('cuentas-cobro.update', $cuentaCobro->id) }}" method="POST" id="formCuentaCobro">
                                @csrf
                                @method('PUT')

                                <!-- Información General -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                                    <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                                        <i class="fas fa-info-circle text-primary mr-2"></i>
                                        Información General
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Número de Cuenta de Cobro (Solo lectura) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Número de Cuenta de Cobro
                                            </label>
                                            <input type="text"
                                                   value="{{ $cuentaCobro->numero_cuenta_cobro }}"
                                                   readonly
                                                   class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                        </div>

                                        <!-- Seleccionar Contrato -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Contrato <span class="text-red-500">*</span>
                                            </label>
                                            <select name="contrato_id"
                                                    id="contrato_id"
                                                    required
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('contrato_id') border-red-500 @enderror">
                                                <option value="">Seleccione un contrato</option>
                                                @foreach($contratos as $contrato)
                                                    <option value="{{ $contrato->id }}"
                                                            {{ (old('contrato_id', $cuentaCobro->contrato_id) == $contrato->id) ? 'selected' : '' }}
                                                            data-valor="{{ $contrato->valor_total }}"
                                                            data-retencion="{{ $contrato->porcentaje_retencion_fuente ?? 0 }}"
                                                            data-estampilla="{{ $contrato->porcentaje_estampilla ?? 0 }}">
                                                        {{ $contrato->numero_contrato }} - {{ $contrato->contratista->nombre ?? 'Sin contratista' }}
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
                                            <input type="date"
                                                   name="fecha_radicacion"
                                                   value="{{ old('fecha_radicacion', $cuentaCobro->fecha_radicacion) }}"
                                                   required
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
                                            <input type="text"
                                                   name="periodo_cobrado"
                                                   value="{{ old('periodo_cobrado', $cuentaCobro->periodo_cobrado) }}"
                                                   placeholder="Ej: Enero 2025, Semana 1-4, etc."
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('periodo_cobrado') border-red-500 @enderror">
                                            @error('periodo_cobrado')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Observaciones -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Observaciones
                                            </label>
                                            <textarea name="observaciones"
                                                      rows="3"
                                                      placeholder="Notas adicionales..."
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $cuentaCobro->observaciones) }}</textarea>
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
                                        <button type="button"
                                                onclick="agregarItem()"
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
                                    <div id="noItemsMessage" class="text-center py-8 bg-gray-50 rounded-lg" style="display: none;">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-secondary">No hay items agregados</p>
                                        <p class="text-sm text-gray-400 mt-1">Haga clic en "Agregar Item" para comenzar</p>
                                    </div>
                                </div>

                                <!-- Resumen de Valores -->
                                <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 mb-6 text-white">
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
                                    <a href="{{ route('cuentas-cobro.show', $cuentaCobro->id) }}"
                                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                                        <i class="fas fa-times mr-2"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit"
                                            class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-save mr-2"></i>
                                        Actualizar Cuenta de Cobro
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Columna Lateral - Archivos -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-paperclip text-accent mr-2"></i>
                                        Archivos
                                    </h2>
                                    <button type="button" onclick="abrirModalSubirArchivo()"
                                        class="bg-primary text-white p-2 rounded-lg hover:bg-primary-dark transition-colors"
                                        title="Subir archivo">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                </div>

                                @if($cuentaCobro->archivos->count() > 0)
                                    <div class="space-y-3 max-h-[600px] overflow-y-auto">
                                        @foreach($cuentaCobro->archivos as $archivo)
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-accent transition-colors">
                                                <div class="flex items-start space-x-2">
                                                    <!-- Icono -->
                                                    <div
                                                        class="w-10 h-10 rounded bg-accent/10 flex items-center justify-center text-accent flex-shrink-0">
                                                        @if($archivo->tipo_archivo == 'pdf')
                                                            <i class="fas fa-file-pdf"></i>
                                                        @elseif(in_array($archivo->tipo_archivo, ['doc', 'docx']))
                                                            <i class="fas fa-file-word"></i>
                                                        @elseif(in_array($archivo->tipo_archivo, ['xls', 'xlsx']))
                                                            <i class="fas fa-file-excel"></i>
                                                        @elseif(in_array($archivo->tipo_archivo, ['jpg', 'jpeg', 'png']))
                                                            <i class="fas fa-file-image"></i>
                                                        @else
                                                            <i class="fas fa-file"></i>
                                                        @endif
                                                    </div>

                                                    <!-- Info -->
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-semibold text-sm text-gray-800 truncate"
                                                            title="{{ $archivo->nombre_original }}">
                                                            {{ $archivo->nombre_original }}
                                                        </p>
                                                        <p class="text-xs text-secondary mt-1">
                                                            {{ $archivo->tamaño_formateado }}
                                                        </p>
                                                        <span
                                                            class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-semibold bg-primary/10 text-primary">
                                                            {{ $archivo->tipo_documento_nombre }}
                                                        </span>
                                                    </div>

                                                    <!-- Acciones -->
                                                    <div class="flex flex-col space-y-1">
                                                        <a href="{{ route('cuentas-cobro.archivos.descargar', $archivo->id) }}"
                                                            class="text-accent hover:text-primary p-1" title="Descargar">
                                                            <i class="fas fa-download text-sm"></i>
                                                        </a>
                                                        @if($cuentaCobro->estado == 'borrador')
                                                            <form
                                                                action="{{ route('cuentas-cobro.archivos.eliminar', [$cuentaCobro->id, $archivo->id]) }}"
                                                                method="POST" onsubmit="return confirm('¿Eliminar este archivo?')" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-danger hover:text-red-700 p-1" title="Eliminar">
                                                                    <i class="fas fa-trash text-sm"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-sm text-secondary">No hay archivos adjuntos</p>
                                    </div>
                                @endif
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

            <form action="{{ route('cuentas-cobro.archivos.subir', $cuentaCobro->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
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
                                <input type="file" name="archivo" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
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
                            <span>El archivo se subirá de forma segura al servidor FTP configurado.</span>
                        </p>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalSubirArchivo()"
                        class="px-6 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                        <i class="fas fa-upload mr-2"></i>
                        Subir Archivo
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
        let itemsExistentes = @json($cuentaCobro->items);
        const STORAGE_KEY = 'cuentaCobro_edit_{{ $cuentaCobro->id }}_draft';

        // Funciones para el manejo de archivos
        function abrirModalSubirArchivo() {
            document.getElementById('modalSubirArchivo').classList.remove('hidden');
        }

        function cerrarModalSubirArchivo() {
            document.getElementById('modalSubirArchivo').classList.add('hidden');
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

        // Cerrar modal al hacer click fuera
        document.getElementById('modalSubirArchivo')?.addEventListener('click', function (e) {
            if (e.target === this) {
                cerrarModalSubirArchivo();
            }
        });

        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            // Intentar restaurar datos guardados
            const datosRestaurados = restaurarDatosPersistidos();

            // Si no se restauraron datos, cargar items existentes
            if (!datosRestaurados) {
                if (itemsExistentes && itemsExistentes.length > 0) {
                    itemsExistentes.forEach(item => {
                        agregarItemExistente(item);
                    });
                } else {
                    // Si no hay items, agregar uno vacío
                    agregarItem();
                }
            }

            // Listener para cambio de contrato
            const contratoSelect = document.getElementById('contrato_id');
            contratoSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                porcentajeRetencion = parseFloat(option.dataset.retencion) || 0;
                porcentajeEstampilla = parseFloat(option.dataset.estampilla) || 0;
                calcularTotales();
                guardarBorrador();
            });

            // Cargar porcentajes del contrato seleccionado
            const selectedOption = contratoSelect.options[contratoSelect.selectedIndex];
            porcentajeRetencion = parseFloat(selectedOption.dataset.retencion) || 0;
            porcentajeEstampilla = parseFloat(selectedOption.dataset.estampilla) || 0;

            // Calcular totales iniciales
            calcularTotales();

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
                const hiddenId = itemRow.querySelector('[name*="[id]"]');
                datos.items.push({
                    itemId: itemId,
                    id: hiddenId ? hiddenId.value : null,
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
                return false;
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
                return true;
            } catch (error) {
                console.error('Error al restaurar datos:', error);
                return false;
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

            // Insertar después de la alerta de estado
            const alertaEstado = document.querySelector('.bg-yellow-50');
            if (alertaEstado) {
                alertaEstado.after(notificacion);
            }
        }

        // Limpiar borrador
        function limpiarBorrador() {
            if (confirm('¿Está seguro de que desea descartar el borrador guardado y restaurar los datos originales?')) {
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
                    ${itemData.id ? `<input type="hidden" name="items[${itemId}][id]" value="${itemData.id}">` : ''}
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
                                      oninput="guardarBorrador()"
                                      class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">${itemData.descripcion || ''}</textarea>
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
            document.querySelector('[name="periodo_cobrado"]').addEventListener('input', guardarBorrador);
            document.querySelector('[name="observaciones"]').addEventListener('input', guardarBorrador);
        }

        function agregarItemExistente(item) {
            itemCounter++;
            const container = document.getElementById('itemsContainer');
            const noItemsMsg = document.getElementById('noItemsMessage');

            const itemHTML = `
                <div class="item-row bg-gray-50 rounded-lg p-4 border border-gray-200" data-item="${itemCounter}">
                    <input type="hidden" name="items[${itemCounter}][id]" value="${item.id}">
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
                                      class="item-descripcion w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">${item.descripcion}</textarea>
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
                                   value="${item.cantidad}"
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
                                   value="${item.valor_unitario}"
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
                                   value="${item.porcentaje_avance || ''}"
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

            // Calcular valor inicial del item
            calcularValorItem(itemCounter);
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
    </script>
    @endpush
@endsection