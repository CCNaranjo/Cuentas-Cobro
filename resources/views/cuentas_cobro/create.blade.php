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

                    <!-- Layout principal con espaciado -->
                    <div class="space-y-8">
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
                                                <textarea name="observaciones" rows="3"
                                                    placeholder="Notas adicionales..."
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                                                @error('observaciones')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Análisis Financiero - Ocupa 1/3 -->
                                <div class="lg:col-span-1">
                                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 text-white h-full">
                                        <h2 class="text-lg font-bold mb-6 flex items-center">
                                            <i class="fas fa-calculator mr-2"></i>
                                            Análisis Financiero
                                        </h2>

                                        <div class="space-y-4 text-sm">
                                            <div class="flex justify-between">
                                                <span>Valor Bruto:</span>
                                                <span id="displayValorBruto">$0</span>
                                            </div>

                                            <div class="border-t border-white/20 pt-2">
                                                <div class="flex justify-between">
                                                    <span>Retención Fuente:</span>
                                                    <span id="displayRetencionFuente">$0</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Estampilla:</span>
                                                    <span id="displayEstampilla">$0</span>
                                                </div>
                                            </div>

                                            <div class="border-t border-white/20 pt-2">
                                                <div class="flex justify-between font-semibold">
                                                    <span>Total Retenciones:</span>
                                                    <span id="displayRetenciones">$0</span>
                                                </div>
                                            </div>

                                            <div class="border-t border-white/20 pt-2">
                                                <div class="flex justify-between text-lg font-bold">
                                                    <span>Valor Neto:</span>
                                                    <span id="displayValorNeto">$0</span>
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

                            <!-- Documentos Adjuntos -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-paperclip text-primary mr-2"></i>
                                        Documentos Adjuntos
                                    </h2>
                                    <button type="button" onclick="agregarArchivo()"
                                        class="bg-gradient-to-r from-accent to-accent text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center text-sm">
                                        <i class="fas fa-upload mr-2"></i>
                                        Agregar Documento
                                    </button>
                                </div>

                                <!-- Contenedor de Archivos -->
                                <div id="archivosContainer" class="space-y-4">
                                    <!-- Los archivos se agregarán aquí dinámicamente -->
                                </div>

                                <!-- Mensaje si no hay archivos -->
                                <div id="noArchivosMessage" class="text-center py-8 bg-gray-50 rounded-lg">
                                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-secondary">No hay documentos adjuntos</p>
                                    <p class="text-sm text-gray-400 mt-1">Haga clic en "Agregar Documento" para adjuntar</p>
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
                                    Crear Cuenta de Cobro
                                </button>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>

        @push('scripts')
            <script>
                const STORAGE_KEY = 'nuevaCuentaCobroBorrador';

                let itemCounter = 0;
                let archivoCounter = 0;
                let porcentajeRetencion = 0;
                let porcentajeEstampilla = 0;
                let archivosParaSubir = [];

                // Inicializar al cargar
                document.addEventListener('DOMContentLoaded', function() {
                    const contratoSelect = document.getElementById('contrato_id');
                    contratoSelect.addEventListener('change', function() {
                        const option = this.options[this.selectedIndex];
                        porcentajeRetencion = parseFloat(option.dataset.retencion) || 0;
                        porcentajeEstampilla = parseFloat(option.dataset.estampilla) || 0;
                        calcularTotales();
                        guardarBorrador();
                    });

                    // Cargar borrador si existe
                    cargarBorrador();

                    // Listeners para guardar borrador
                    document.querySelector('[name="contrato_id"]').addEventListener('change', guardarBorrador);
                    document.querySelector('[name="fecha_radicacion"]').addEventListener('change', guardarBorrador);
                    document.querySelector('[name="periodo_inicio"]').addEventListener('change', guardarBorrador);
                    document.querySelector('[name="periodo_fin"]').addEventListener('change', guardarBorrador);
                    document.querySelector('[name="observaciones"]').addEventListener('input', guardarBorrador);

                    // Inicializar con un item si no hay
                    if (itemCounter === 0) {
                        agregarItem();
                    }
                });

                function guardarBorrador() {
                    const formData = new FormData(document.getElementById('formCuentaCobro'));
                    const data = Object.fromEntries(formData.entries());
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
                }

                function cargarBorrador() {
                    const savedData = localStorage.getItem(STORAGE_KEY);
                    if (savedData) {
                        const data = JSON.parse(savedData);
                        Object.keys(data).forEach(key => {
                            const input = document.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.value = data[key];
                            }
                        });

                        // Cargar items
                        const items = Object.keys(data).filter(key => key.startsWith('items['));
                        const itemIds = [...new Set(items.map(key => key.match(/items\[(\d+)\]/)[1]))];
                        itemCounter = itemIds.length;
                        itemIds.forEach(id => {
                            agregarItem();
                            const item = document.querySelector(`[data-item="${id}"]`);
                            item.querySelector('.item-descripcion').value = data[`items[${id}][descripcion]`] || '';
                            item.querySelector('.item-cantidad').value = data[`items[${id}][cantidad]`] || '';
                            item.querySelector('.item-valor-unitario').value = data[`items[${id}][valor_unitario]`] || '';
                            item.querySelector('[name="items[${id}][porcentaje_avance]"]').value = data[`items[${id}][porcentaje_avance]`] || '';
                            calcularValorItem(id);
                        });
                    }
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
                                       onchange="calcularValorItem(${itemCounter})"
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
                                       onchange="calcularValorItem(${itemCounter})"
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

            function formatearMoneda(valor) {
                return '$' + Math.round(valor).toLocaleString('es-CO');
            }

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
            
            // Función para agregar archivo
            function agregarArchivo() {
                archivoCounter++;
                const container = document.getElementById('archivosContainer');
                const noArchivosMsg = document.getElementById('noArchivosMessage');

                const archivoHTML = `
                    <div class="archivo-row bg-gray-50 rounded-lg p-4 border border-gray-200" data-archivo="${archivoCounter}">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="font-semibold text-gray-800">Documento #${archivoCounter}</h3>
                            <button type="button"
                                    onclick="eliminarArchivo(${archivoCounter})"
                                    class="text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tipo de Documento -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Documento <span class="text-red-500">*</span>
                                </label>
                                <select id="tipoDocumento${archivoCounter}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Seleccione un tipo</option>
                                    <option value="cuenta_cobro">Cuenta de Cobro</option>
                                    <option value="acta_recibido">Acta de Recibido</option>
                                    <option value="informe">Informe</option>
                                    <option value="foto_evidencia">Foto Evidencia</option>
                                    <option value="planilla">Planilla</option>
                                    <option value="soporte_pago">Soporte de Pago</option>
                                    <option value="factura">Factura</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            <!-- Archivo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Archivo <span class="text-red-500">*</span>
                                </label>
                                <input type="file" id="archivoInput${archivoCounter}"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción
                                </label>
                                <input type="text" id="descripcionArchivo${archivoCounter}"
                                       placeholder="Descripción opcional del documento..."
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', archivoHTML);
                noArchivosMsg.style.display = 'none';

                // Listener para agregar al array al seleccionar archivo
                document.getElementById(`archivoInput${archivoCounter}`).addEventListener('change', function() {
                    const tipo = document.getElementById(`tipoDocumento${archivoCounter}`).value;
                    const descripcion = document.getElementById(`descripcionArchivo${archivoCounter}`).value;
                    if (this.files[0] && tipo) {
                        archivosParaSubir.push({
                            archivo: this.files[0],
                            tipo_documento: tipo,
                            descripcion: descripcion
                        });
                    }
                });
            }

            function eliminarArchivo(archivoId) {
                const archivo = document.querySelector(`[data-archivo="${archivoId}"]`);
                if (archivo) {
                    archivo.remove();
                    // Remover del array if needed, but since new, just remove DOM
                    const container = document.getElementById('archivosContainer');
                    const noArchivosMsg = document.getElementById('noArchivosMessage');
                    if (container.children.length === 0) {
                        noArchivosMsg.style.display = 'block';
                    }
                }
            }
        </script>
    @endpush
@endsection