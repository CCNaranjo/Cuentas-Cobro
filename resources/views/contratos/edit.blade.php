@extends('layouts.app-dashboard')

@section('title', 'Editar Contrato - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 max-w-4xl mx-auto">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex items-center space-x-2 text-sm text-secondary">
                        <a href="{{ route('contratos.index', ['organizacion_id' => $contrato->organizacion_id]) }}" class="hover:text-primary">Contratos</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <a href="{{ route('contratos.show', $contrato) }}" class="hover:text-primary">{{ $contrato->numero_contrato }}</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-800">Editar</span>
                    </nav>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-edit text-primary mr-2"></i>
                        Editar Contrato
                    </h1>

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center text-red-700 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Por favor corrige los siguientes errores:</strong>
                            </div>
                            <ul class="list-disc pl-5 text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contratos.update', $contrato) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="organizacion_id" value="{{ $contrato->organizacion_id }}">

                        <!-- Información Básica -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Número de Contrato *</label>
                                <input type="text" name="numero_contrato" value="{{ old('numero_contrato', $contrato->numero_contrato) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Estado *</label>
                                <select name="estado" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                    <option value="borrador" {{ old('estado', $contrato->estado) == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                    <option value="activo" {{ old('estado', $contrato->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="terminado" {{ old('estado', $contrato->estado) == 'terminado' ? 'selected' : '' }}>Terminado</option>
                                    <option value="suspendido" {{ old('estado', $contrato->estado) == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Supervisor *</label>
                                <select name="supervisor_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                    <option value="">Seleccionar supervisor...</option>
                                    @foreach($supervisores as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $contrato->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contratista</label>
                                <select name="contratista_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                    <option value="">Seleccionar contratista...</option>
                                    @foreach($contratistas as $contratista)
                                        <option value="{{ $contratista->id }}" {{ old('contratista_id', $contrato->contratista_id) == $contratista->id ? 'selected' : '' }}>
                                            {{ $contratista->nombre }} - {{ $contratista->documento_identidad }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(!$contrato->contratista_id)
                                <p class="text-xs text-secondary mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    El contratista se puede asignar ahora o posteriormente
                                </p>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Objeto Contractual *</label>
                                <textarea name="objeto_contractual" rows="4" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none">{{ old('objeto_contractual', $contrato->objeto_contractual) }}</textarea>
                            </div>
                        </div>

                        <!-- Información Financiera -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Valor Total *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" name="valor_total" value="{{ old('valor_total', $contrato->valor_total) }}" required min="0" step="0.01"
                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">% Retención Fuente *</label>
                                <div class="relative">
                                    <input type="number" name="porcentaje_retencion_fuente" value="{{ old('porcentaje_retencion_fuente', $contrato->porcentaje_retencion_fuente) }}" required min="0" max="100" step="0.01"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">% Estampilla *</label>
                                <div class="relative">
                                    <input type="number" name="porcentaje_estampilla" value="{{ old('porcentaje_estampilla', $contrato->porcentaje_estampilla) }}" required min="0" max="100" step="0.01"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Vigencia -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha de Inicio *</label>
                                <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $contrato->fecha_inicio ? $contrato->fecha_inicio->format('Y-m-d') : '') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha de Fin *</label>
                                <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $contrato->fecha_fin ? $contrato->fecha_fin->format('Y-m-d') : '') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <div>
                                <p class="text-sm text-secondary">
                                    <i class="fas fa-clock mr-1"></i>
                                    Última actualización: {{ $contrato->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('contratos.show', $contrato) }}" 
                                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all flex items-center">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancelar
                                </a>
                                <button type="submit" 
                                        class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-8 rounded-lg hover:shadow-lg transition-all flex items-center">
                                    <i class="fas fa-save mr-2"></i>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection