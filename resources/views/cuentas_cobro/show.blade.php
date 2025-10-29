@extends('layouts.app-dashboard')

@section('title', 'Detalle Cuenta de Cobro - ARCA-D')

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
                                    {{ $cuentaCobro->numero_cuenta_cobro }}
                                </h1>
                                <!-- Estado Badge -->
                                <span class="px-4 py-2 rounded-full text-sm font-semibold
                                    @if($cuentaCobro->estado == 'borrador') bg-gray-100 text-gray-800
                                    @elseif($cuentaCobro->estado == 'radicada') bg-blue-100 text-blue-800
                                    @elseif($cuentaCobro->estado == 'en_revision') bg-yellow-100 text-yellow-800
                                    @elseif($cuentaCobro->estado == 'aprobada') bg-green-100 text-green-800
                                    @elseif($cuentaCobro->estado == 'rechazada') bg-red-100 text-red-800
                                    @elseif($cuentaCobro->estado == 'pagada') bg-purple-100 text-purple-800
                                    @elseif($cuentaCobro->estado == 'anulada') bg-gray-100 text-gray-600
                                    @endif">
                                    <i class="fas fa-circle text-xs mr-1"></i>
                                    {{ $cuentaCobro->estado_nombre }}
                                </span>
                            </div>
                            <p class="text-secondary ml-9">
                                Creada el {{ $cuentaCobro->created_at->format('d/m/Y H:i') }} por {{ $cuentaCobro->creador->nombre }}
                            </p>
                        </div>
                        
                        <!-- Botones de Acción -->
                        <div class="flex items-center space-x-3">
                            @if($cuentaCobro->estado == 'borrador')
                                <a href="{{ route('cuentas-cobro.edit', $cuentaCobro->id) }}" 
                                   class="bg-gradient-to-r from-accent to-accent text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    Editar
                                </a>
                            @endif
                            
                            <button type="button" 
                                    onclick="document.getElementById('modalCambiarEstado').classList.remove('hidden')"
                                    class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Cambiar Estado
                            </button>
                            
                            <button type="button" 
                                    onclick="window.print()"
                                    class="bg-white border-2 border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-50 transition-all flex items-center">
                                <i class="fas fa-print mr-2"></i>
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de Éxito/Error -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna Principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Información del Contrato -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-file-contract text-primary mr-2"></i>
                                Información del Contrato
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Número de Contrato</p>
                                    <p class="font-semibold text-gray-800">{{ $cuentaCobro->contrato->numero_contrato }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Contratista</p>
                                    <p class="font-semibold text-gray-800">{{ $cuentaCobro->contrato->contratista->nombre }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Cédula/NIT</p>
                                    <p class="font-semibold text-gray-800">{{ $cuentaCobro->contrato->contratista->cedula_nit }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Valor Total Contrato</p>
                                    <p class="font-semibold text-green-600">${{ number_format($cuentaCobro->contrato->valor_total, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información General de la Cuenta -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Información General
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Fecha de Radicación</p>
                                    <p class="font-semibold text-gray-800">{{ $cuentaCobro->fecha_radicacion->format('d/m/Y') }}</p>
                                </div>
                                
                                @if($cuentaCobro->periodo_cobrado)
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Período Cobrado</p>
                                    <p class="font-semibold text-gray-800">{{ $cuentaCobro->periodo_cobrado }}</p>
                                </div>
                                @endif
                                
                                @if($cuentaCobro->observaciones)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500 mb-1">Observaciones</p>
                                    <p class="text-gray-800">{{ $cuentaCobro->observaciones }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Items de la Cuenta de Cobro -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-list-ul text-primary mr-2"></i>
                                Items de la Cuenta de Cobro
                            </h2>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Descripción</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Cantidad</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Valor Unit.</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">% Avance</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Valor Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($cuentaCobro->items as $index => $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-800">{{ $item->descripcion }}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-800">{{ number_format($item->cantidad, 2) }}</td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-800">${{ number_format($item->valor_unitario, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-sm text-center">
                                                @if($item->porcentaje_avance)
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                                        {{ number_format($item->porcentaje_avance, 2) }}%
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                                ${{ number_format($item->valor_total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Documentos Soporte -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-paperclip text-primary mr-2"></i>
                                    Documentos Soporte
                                </h2>
                                @if($cuentaCobro->estado != 'anulada' && $cuentaCobro->estado != 'pagada')
                                <button type="button" 
                                        onclick="document.getElementById('modalSubirDocumento').classList.remove('hidden')"
                                        class="bg-gradient-to-r from-accent to-accent text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center text-sm">
                                    <i class="fas fa-upload mr-2"></i>
                                    Subir Documento
                                </button>
                                @endif
                            </div>
                            
                            @if($cuentaCobro->documentos->count() > 0)
                                <div class="space-y-3">
                                    @foreach($cuentaCobro->documentos as $documento)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3 flex-1">
                                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-{{ $documento->icono }} text-primary text-lg"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-800">{{ $documento->nombre_archivo }}</p>
                                                <div class="flex items-center space-x-3 text-xs text-gray-500 mt-1">
                                                    <span>{{ $documento->getTipoDocumentoNombre() }}</span>
                                                    <span>•</span>
                                                    <span>{{ $documento->getTamanoFormateado() }}</span>
                                                    <span>•</span>
                                                    <span>{{ $documento->uploaded_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ $documento->getUrlDescarga() }}" 
                                               target="_blank"
                                               class="text-primary hover:text-primary-dark transition-colors">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if($cuentaCobro->estado == 'borrador')
                                            <form action="{{ route('cuentas-cobro.eliminar-documento', [$cuentaCobro->id, $documento->id]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('¿Está seguro de eliminar este documento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <i class="fas fa-file text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-secondary">No hay documentos cargados</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Columna Lateral -->
                    <div class="space-y-6">
                        <!-- Resumen de Valores -->
                        <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 text-white">
                            <h2 class="text-lg font-bold mb-6 flex items-center">
                                <i class="fas fa-calculator mr-2"></i>
                                Resumen Financiero
                            </h2>

                            <div class="space-y-4">
                                <!-- Valor Bruto -->
                                <div class="bg-white/10 rounded-lg p-4">
                                    <p class="text-white/80 text-sm mb-1">Valor Bruto</p>
                                    <p class="text-2xl font-bold">${{ number_format($cuentaCobro->valor_bruto, 0, ',', '.') }}</p>
                                </div>

                                <!-- Retenciones -->
                                <div class="bg-white/10 rounded-lg p-4">
                                    <p class="text-white/80 text-sm mb-2">Retenciones</p>
                                    
                                    @if($cuentaCobro->retenciones_calculadas)
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span>Retención Fuente ({{ $cuentaCobro->contrato->porcentaje_retencion_fuente }}%)</span>
                                                <span class="font-semibold">
                                                    ${{ number_format($cuentaCobro->retenciones_calculadas['retencion_fuente'] ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Estampilla ({{ $cuentaCobro->contrato->porcentaje_estampilla }}%)</span>
                                                <span class="font-semibold">
                                                    ${{ number_format($cuentaCobro->retenciones_calculadas['estampilla'] ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="border-t border-white/20 pt-2 mt-2 flex justify-between font-bold">
                                                <span>Total Retenciones</span>
                                                <span>${{ number_format($cuentaCobro->retenciones_calculadas['total'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Valor Neto -->
                                <div class="bg-white/10 rounded-lg p-4">
                                    <p class="text-white/80 text-sm mb-1">Valor Neto a Pagar</p>
                                    <p class="text-3xl font-bold">${{ number_format($cuentaCobro->valor_neto, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Historial de Estados -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-history text-primary mr-2"></i>
                                Historial de Estados
                            </h2>
                            
                            @if($cuentaCobro->historial->count() > 0)
                                <div class="space-y-4">
                                    @foreach($cuentaCobro->historial as $cambio)
                                    <div class="relative pl-6 pb-4 border-l-2 border-gray-200 last:border-0 last:pb-0">
                                        <div class="absolute -left-2 top-0 w-4 h-4 bg-primary rounded-full border-2 border-white"></div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">
                                                {{ $cambio->estado_anterior_nombre }} → {{ $cambio->estado_nuevo_nombre }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Por {{ $cambio->usuario->nombre }} • {{ $cambio->tiempo_cambio }}
                                            </p>
                                            @if($cambio->comentario)
                                            <p class="text-sm text-gray-600 mt-2 bg-gray-50 p-2 rounded">
                                                "{{ $cambio->comentario }}"
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-secondary text-center py-4">No hay cambios de estado registrados</p>
                            @endif
                        </div>

                        <!-- Acciones Rápidas -->
                        @if($cuentaCobro->estado == 'borrador')
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-bolt text-primary mr-2"></i>
                                Acciones Rápidas
                            </h2>
                            
                            <div class="space-y-2">
                                <form action="{{ route('cuentas-cobro.destroy', $cuentaCobro->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('¿Está seguro de eliminar esta cuenta de cobro?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full bg-red-50 text-red-700 font-semibold py-2 px-4 rounded-lg hover:bg-red-100 transition-all flex items-center justify-center">
                                        <i class="fas fa-trash mr-2"></i>
                                        Eliminar Cuenta de Cobro
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Cambiar Estado -->
<div id="modalCambiarEstado" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">Cambiar Estado</h3>
                <button type="button" 
                        onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('cuentas-cobro.cambiar-estado', $cuentaCobro->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nuevo Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="nuevo_estado" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Seleccione un estado</option>
                        <option value="borrador">Borrador</option>
                        <option value="radicada">Radicada</option>
                        <option value="en_revision">En Revisión</option>
                        <option value="aprobada">Aprobada</option>
                        <option value="rechazada">Rechazada</option>
                        <option value="pagada">Pagada</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Comentario
                    </label>
                    <textarea name="comentario" 
                              rows="3"
                              placeholder="Motivo del cambio de estado (opcional)"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')"
                            class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all">
                        Cambiar Estado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Subir Documento -->
<div id="modalSubirDocumento" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">Subir Documento</h3>
                <button type="button" 
                        onclick="document.getElementById('modalSubirDocumento').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('cuentas-cobro.subir-documento', $cuentaCobro->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Documento <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo_documento" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Seleccione un tipo</option>
                        <option value="acta_recibido">Acta de Recibido</option>
                        <option value="informe">Informe</option>
                        <option value="foto_evidencia">Foto de Evidencia</option>
                        <option value="planilla">Planilla</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Archivo <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="archivo" 
                           required
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-gray-500 mt-1">Máximo 10MB. Formatos: PDF, DOC, XLS, Imágenes, ZIP</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('modalSubirDocumento').classList.add('hidden')"
                            class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all">
                        Subir Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background: white !important;
        }
        
        .bg-gradient-to-br,
        .bg-gradient-to-r {
            background: #1e40af !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush
@endsection