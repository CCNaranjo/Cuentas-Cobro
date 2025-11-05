@extends('layouts.app-dashboard')

@section('title', 'Editar Contrato - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 max-w-6xl mx-auto">
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

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna principal - Formulario -->
                    <div class="lg:col-span-2">
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
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                        <i class="fas fa-info-circle text-accent mr-2"></i>
                                        Información del Contrato
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Objeto Contractual *</label>
                                            <textarea name="objeto_contractual" rows="4" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none">{{ old('objeto_contractual', $contrato->objeto_contractual) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Financiera -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                        <i class="fas fa-dollar-sign text-accent mr-2"></i>
                                        Información Financiera
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                </div>

                                <!-- Vigencia -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                        <i class="fas fa-calendar-alt text-accent mr-2"></i>
                                        Vigencia
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                    <!-- Columna lateral - Archivos del Contrato -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-paperclip text-accent mr-2"></i>
                                    Archivos
                                </h2>
                                @if(Auth::user()->tienePermiso('subir-archivo-contrato', $contrato->organizacion_id))
                                <button onclick="abrirModalSubirArchivo()" 
                                        class="bg-primary text-white p-2 rounded-lg hover:bg-primary-dark transition-colors"
                                        title="Subir archivo">
                                    <i class="fas fa-upload"></i>
                                </button>
                                @endif
                            </div>

                            @if($contrato->archivos->count() > 0)
                            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                                @foreach($contrato->archivos as $archivo)
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-accent transition-colors">
                                    <div class="flex items-start space-x-2">
                                        <!-- Icono -->
                                        <div class="w-10 h-10 rounded bg-accent/10 flex items-center justify-center text-accent flex-shrink-0">
                                            @if($archivo->tipo_archivo == 'pdf')
                                                <i class="fas fa-file-pdf"></i>
                                            @elseif(in_array($archivo->tipo_archivo, ['doc', 'docx']))
                                                <i class="fas fa-file-word"></i>
                                            @elseif(in_array($archivo->tipo_archivo, ['xls', 'xlsx']))
                                                <i class="fas fa-file-excel"></i>
                                            @else
                                                <i class="fas fa-file"></i>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-gray-800 truncate" title="{{ $archivo->nombre_original }}">
                                                {{ $archivo->nombre_original }}
                                            </p>
                                            <p class="text-xs text-secondary mt-1">
                                                {{ $archivo->tamaño_formateado }}
                                            </p>
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-semibold bg-primary/10 text-primary">
                                                {{ str_replace('_', ' ', ucfirst($archivo->tipo_documento)) }}
                                            </span>
                                        </div>

                                        <!-- Acciones -->
                                        <div class="flex flex-col space-y-1">
                                            <a href="{{ route('contratos.archivos.descargar', $archivo) }}" 
                                               class="text-accent hover:text-primary p-1"
                                               title="Descargar">
                                                <i class="fas fa-download text-sm"></i>
                                            </a>
                                            @if(Auth::user()->tienePermiso('eliminar-archivo-contrato', $contrato->organizacion_id))
                                            <form action="{{ route('contratos.archivos.eliminar', $archivo) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('¿Eliminar este archivo?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-danger hover:text-red-700 p-1"
                                                        title="Eliminar">
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
<div id="modalSubirArchivo" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
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

        <form action="{{ route('contratos.archivos.subir', $contrato) }}" method="POST" enctype="multipart/form-data">
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
                        <option value="contrato_firmado">Contrato Firmado</option>
                        <option value="adicion">Adición</option>
                        <option value="suspension">Suspensión</option>
                        <option value="acta_inicio">Acta de Inicio</option>
                        <option value="acta_liquidacion">Acta de Liquidación</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <!-- Archivo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Archivo <span class="text-danger">*</span>
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">
                                    <span class="font-semibold">Click para subir</span> o arrastra el archivo
                                </p>
                                <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX (Máx. 10MB)</p>
                            </div>
                            <input type="file" name="archivo" accept=".pdf,.doc,.docx,.xls,.xlsx" required class="hidden" 
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
                        <span>El archivo se subirá de forma segura al servidor FTP configurado.</span>
                    </p>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" 
                        onclick="cerrarModalSubirArchivo()"
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
function abrirModalSubirArchivo() {
    document.getElementById('modalSubirArchivo').classList.remove('hidden');
}

function cerrarModalSubirArchivo() {
    document.getElementById('modalSubirArchivo').classList.add('hidden');
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
document.getElementById('modalSubirArchivo')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalSubirArchivo();
    }
});
</script>
@endpush
@endsection