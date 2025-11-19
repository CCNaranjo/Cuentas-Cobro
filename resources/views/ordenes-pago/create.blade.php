@extends('layouts.app-dashboard')

@section('title', 'Crear Orden de Pago - ARCA-D')

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
                                    Nueva Orden de Pago
                                </h1>
                            </div>
                            <p class="text-secondary ml-9">
                                Seleccione cuentas de cobro aprobadas y cuenta de origen
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pagos.op.store') }}" method="POST">
                    @csrf

                    <!-- Seleccionar Cuenta Origen -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-bank text-primary mr-2"></i>
                            Cuenta de Origen
                        </h2>
                        <select name="cuenta_origen_id" required class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Seleccione cuenta</option>
                            @foreach($cuentasOrigen as $cuenta)
                                <option value="{{ $cuenta->id }}">
                                    {{ $cuenta->banco->nombre }} - {{ $cuenta->numero_cuenta }} ({{ ucfirst($cuenta->tipo_cuenta) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Listado de Cuentas de Cobro -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-list text-primary mr-2"></i>
                            Cuentas de Cobro Aprobadas Pendientes
                        </h2>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Seleccionar</th>
                                    <th class="px-6 py-3 text-left">No. Cuenta</th>
                                    <th class="px-6 py-3 text-left">Contratista</th>
                                    <th class="px-6 py-3 text-left">Valor Neto</th>
                                    <th class="px-6 py-3 text-left">Fecha Radicación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($cuentasCobro as $cc)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="cuentas_cobro[]" value="{{ $cc->id }}">
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-primary">{{ $cc->numero_cuenta_cobro }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $cc->contrato->contratista->nombre }}</td>
                                        <td class="px-6 py-4 text-sm">{{ number_format($cc->valor_neto, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $cc->fecha_radicacion->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('pagos.op.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg">
                            Generar Orden de Pago
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection