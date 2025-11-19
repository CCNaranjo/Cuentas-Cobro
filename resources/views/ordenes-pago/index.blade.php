@extends('layouts.app-dashboard')

@section('title', 'Órdenes de Pago - ARCA-D')

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
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-invoice-dollar text-primary mr-3"></i>
                                Órdenes de Pago
                            </h1>
                            <p class="text-secondary mt-1">
                                Listado de órdenes de pago generadas
                            </p>
                        </div>
                        <a href="{{ route('pagos.op.create') }}" 
                           class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nueva Orden de Pago
                        </a>
                    </div>
                </div>

                <!-- Tabla de Órdenes de Pago -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. OP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuentas Asociadas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($ordenesPago as $op)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary">
                                        <a href="{{ route('pagos.op.show', $op->id) }}">{{ $op->numero_op }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($op->valor_total_neto, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $op->cuentasCobro->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                                            @if($op->estado == 'creada') bg-yellow-100 text-yellow-800 
                                            @elseif($op->estado == 'autorizada') bg-blue-100 text-blue-800 
                                            @elseif($op->estado == 'pagada_registrada') bg-green-100 text-green-800 
                                            @elseif($op->estado == 'anulada') bg-red-100 text-red-800 
                                            @endif">
                                            {{ ucfirst($op->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $op->fecha_emision->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($op->estado == 'creada' && Auth::user()->tieneRol('ordenador_gasto'))
                                            <form action="{{ route('pagos.op.autorizar', $op->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-check mr-1"></i> Autorizar
                                                </button>
                                            </form>
                                        @endif
                                        @if($op->estado == 'autorizada' && Auth::user()->tieneRol('tesorero'))
                                            <button onclick="abrirModalRegistrarPago('{{ $op->id }}')" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-money-check mr-1"></i> Registrar Pago
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $ordenesPago->links() }}
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal para Registrar Pago -->
<div id="modalRegistrarPago" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form action="" method="POST" id="formRegistrarPago">
            @csrf
            @method('PUT')
            <h3 class="text-lg font-bold mb-4">Registrar Pago</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Fecha Pago Efectivo</label>
                <input type="date" name="fecha_pago_efectivo" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Comprobante Bancario ID</label>
                <input type="text" name="comprobante_bancario_id" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="cerrarModal()" class="px-4 py-2 border text-gray-700 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Registrar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function abrirModalRegistrarPago(opId) {
        document.getElementById('formRegistrarPago').action = `/pagos/ordenes-pago/${opId}/pagar`;
        document.getElementById('modalRegistrarPago').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('modalRegistrarPago').classList.add('hidden');
    }
</script>
@endpush
@endsection