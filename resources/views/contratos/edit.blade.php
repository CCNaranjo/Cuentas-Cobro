{{-- filepath: c:\xampp\cuentasCobro\resources\views\contratos\edit.blade.php --}}
@extends('layouts.app-dashboard')

@section('title', 'Editar Contrato - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 max-w-3xl mx-auto">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex items-center space-x-2 text-sm text-secondary">
                        <a href="{{ route('contratos.index', ['organizacion_id' => $contrato->organizacion_id]) }}" class="hover:text-primary">Contratos</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-800">Editar</span>
                    </nav>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Contrato</h1>

                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="bg-red-100 text-red-700 px-4 py-2 rounded">
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contratos.update', $contrato) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="organizacion_id" value="{{ $contrato->organizacion_id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Número de Contrato</label>
                                <input type="text" name="numero_contrato" value="{{ old('numero_contrato', $contrato->numero_contrato) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Supervisor</label>
                                <select name="supervisor_id" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                                    <option value="">Seleccionar supervisor...</option>
                                    @foreach($supervisores as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $contrato->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Objeto Contractual</label>
                                <textarea name="objeto_contractual" rows="3" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">{{ old('objeto_contractual', $contrato->objeto_contractual) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor Total</label>
                                <input type="number" name="valor_total" value="{{ old('valor_total', $contrato->valor_total) }}" required min="0"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $contrato->fecha_inicio ? $contrato->fecha_inicio->format('Y-m-d') : '') }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Fin</label>
                                <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $contrato->fecha_fin ? $contrato->fecha_fin->format('Y-m-d') : '') }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">% Retención en la Fuente</label>
                                <input type="number" name="porcentaje_retencion_fuente" value="{{ old('porcentaje_retencion_fuente', $contrato->porcentaje_retencion_fuente) }}" required min="0" max="100" step="0.01"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">% Estampilla</label>
                                <input type="number" name="porcentaje_estampilla" value="{{ old('porcentaje_estampilla', $contrato->porcentaje_estampilla) }}" required min="0" max="100" step="0.01"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('contratos.show', $contrato) }}" class="px-4 py-2 mr-2 border border-secondary text-secondary rounded-lg hover:bg-secondary hover:text-white transition-all">Cancelar</a>
                            <button type="submit" class="px-6 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-all">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection