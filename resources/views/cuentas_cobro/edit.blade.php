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

                            <!-- Período Inicio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Período Inicio <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       name="periodo_inicio"
                                       value="{{ old('periodo_inicio', $cuentaCobro->periodo_inicio) }}"
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
                                <input type="date"
                                       name="periodo_fin"
                                       value="{{ old('periodo_fin', $cuentaCobro->periodo_fin) }}"
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
        </main>
    </div>
</div>

@push('scripts')
<script>
    let itemCounter = 0;
    let porcentajeRetencion = 0;
    let porcentajeEstampilla = 0;
    let itemsExistentes = @json($cuentaCobro->items);
    const STORAGE_KEY = 'cuentaCobro_edit_{{ $cuentaCobro->id }}_draft';

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
            periodo_inicio: document.querySelector('[name="periodo_inicio"]').value,
            periodo_fin: document.querySelector('[name="periodo_fin"]').value,
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
            } else {
                document.getElementById('noItemsMessage').style.display = 'block';
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
                <input type="hidden" name="items[${itemId}][id]" value="${itemData.id || ''}">
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