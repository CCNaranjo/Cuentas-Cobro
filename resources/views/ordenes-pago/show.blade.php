@extends('layouts.app-dashboard')

@section('title', 'Detalle Orden de Pago - ARCA-D')

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
                                <a href="{{ route('pagos.op.index') }}" class="text-secondary hover:text-primary">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-file-invoice-dollar text-primary mr-3"></i>
                                    Orden de Pago {{ $ordenPago->numero_op }}
                                </h1>
                            </div>
                            <p class="text-secondary ml-9">
                                Detalle de la orden y cuentas asociadas
                            </p>
                        </div>
                        <span class="px-4 py-2 rounded-lg text-sm font-semibold 
                            @if($ordenPago->estado == 'creada') bg-yellow-100 text-yellow-800 
                            @elseif($ordenPago->estado == 'autorizada') bg-blue-100 text-blue-800 
                            @elseif($ordenPago->estado == 'pagada_registrada') bg-green-100 text-green-800 
                            @elseif($ordenPago->estado == 'anulada') bg-red-100 text-red-800 
                            @endif">
                            {{ ucfirst($ordenPago->estado) }}
                        </span>
                    </div>
                </div>

                <!-- Datos Generales -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Datos Generales
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor Total Neto</label>
                            <p class="mt-1 text-lg font-semibold">{{ number_format($ordenPago->valor_total_neto, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha Emisión</label>
                            <p class="mt-1 text-lg">{{ $ordenPago->fecha_emision->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cuenta Origen</label>
                            <p class="mt-1 text-lg">{{ $ordenPago->cuentaOrigen->banco->nombre }} - {{ $ordenPago->cuentaOrigen->numero_cuenta }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ordenador</label>
                            <p class="mt-1 text-lg">{{ $ordenPago->ordenador->nombre ?? 'Pendiente' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cuentas de Cobro Vinculadas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-list text-primary mr-2"></i>
                        Cuentas de Cobro Vinculadas
                    </h2>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Cuenta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contratista</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Neto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($ordenPago->cuentasCobro as $cc)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-primary">{{ $cc->numero_cuenta_cobro }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $cc->contrato->contratista->nombre }}</td>
                                    <td class="px-6 py-4 text-sm">{{ number_format($cc->valor_neto, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $cc->pivot->fecha_pago_efectivo ? $cc->pivot->fecha_pago_efectivo->format('d/m/Y') : 'Pendiente' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $cc->pivot->comprobante_bancario_id ?? 'Pendiente' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection