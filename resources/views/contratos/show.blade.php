@extends('layouts.app-dashboard')

@section('title', 'Detalle del Contrato')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Breadcrumb y Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <nav class="flex items-center space-x-2 text-sm text-secondary mb-2">
                            <a href="{{ route('dashboard') }}" class="hover:text-primary">Dashboard</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <a href="{{ route('contratos.index') }}" class="hover:text-primary">Contratos</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <span class="text-gray-800">{{ $contrato->numero_contrato }}</span>
                        </nav>
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-contract text-primary mr-3"></i>
                            Contrato {{ $contrato->numero_contrato }}
                        </h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($contrato->estado == 'borrador' && Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                            <a href="{{ route('contratos.edit', $contrato) }}" 
                               class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Editar
                            </a>
                        @endif
                        <button onclick="window.print()" 
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center">
                            <i class="fas fa-print mr-2"></i>
                            Imprimir
                        </button>
                    </div>
                </div>

                <!-- Badge de Estado -->
                <div class="mb-6">
                    @php
                        $estadoStyles = [
                            'borrador' => 'bg-gray-100 text-gray-800',
                            'activo' => 'bg-green-100 text-green-800',
                            'suspendido' => 'bg-yellow-100 text-yellow-800',
                            'terminado' => 'bg-blue-100 text-blue-800',
                            'liquidado' => 'bg-purple-100 text-purple-800'
                        ];
                        $estadoTextos = [
                            'borrador' => 'Borrador',
                            'activo' => 'Activo',
                            'suspendido' => 'Suspendido',
                            'terminado' => 'Terminado',
                            'liquidado' => 'Liquidado'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $estadoStyles[$contrato->estado] ?? 'bg-gray-100 text-gray-800' }}">
                        <i class="fas fa-circle mr-2" style="font-size: 0.5rem;"></i>
                        {{ $estadoTextos[$contrato->estado] ?? ucfirst($contrato->estado) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna Principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Información General -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-accent mr-2"></i>
                                Información General
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Número de Contrato</label>
                                    <p class="text-gray-800 font-semibold">{{ $contrato->numero_contrato }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Organización</label>
                                    <p class="text-gray-800 font-semibold">{{ $contrato->organizacion->nombre }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm text-secondary mb-1">Objeto Contractual</label>
                                    <p class="text-gray-800 leading-relaxed">{{ $contrato->objeto_contractual }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información Financiera -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-dollar-sign text-accent mr-2"></i>
                                Información Financiera
                            </h2>
                            
                            <!-- Tarjetas de Valores -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gradient-to-br from-primary to-primary-dark text-white p-4 rounded-lg text-center">
                                    <div class="text-sm opacity-90 mb-1">Valor Total</div>
                                    <div class="text-2xl font-bold">${{ number_format($contrato->valor_total, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-accent to-blue-400 text-white p-4 rounded-lg text-center">
                                    <div class="text-sm opacity-90 mb-1">Valor Cobrado</div>
                                    <div class="text-2xl font-bold">${{ number_format($estadisticas['valor_cobrado'], 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg text-center">
                                    <div class="text-sm opacity-90 mb-1">Valor Disponible</div>
                                    <div class="text-2xl font-bold">${{ number_format($estadisticas['valor_disponible'], 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Barra de Progreso -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-sm text-secondary">Ejecución Financiera</label>
                                    <span class="bg-accent text-white text-xs px-2 py-1 rounded-full">
                                        {{ number_format($estadisticas['porcentaje_ejecucion'], 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-accent to-primary h-3 rounded-full" 
                                         style="width: {{ $estadisticas['porcentaje_ejecucion'] }}%"></div>
                                </div>
                            </div>

                            <!-- Retenciones -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-warning">
                                    <div class="flex justify-between items-center">
                                        <span class="text-secondary">Retención en la Fuente</span>
                                        <span class="font-semibold text-warning">{{ $contrato->porcentaje_retencion_fuente }}%</span>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-danger">
                                    <div class="flex justify-between items-center">
                                        <span class="text-secondary">Estampilla</span>
                                        <span class="font-semibold text-danger">{{ $contrato->porcentaje_estampilla }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personas Involucradas -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-users text-accent mr-2"></i>
                                Personas Involucradas
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Contratista -->
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm text-secondary mb-1">Contratista</div>
                                        @if($contrato->contratista)
                                            <div class="font-semibold text-gray-800 mb-1">{{ $contrato->contratista->nombre }}</div>
                                            <div class="text-sm text-secondary">{{ $contrato->contratista->email }}</div>
                                            <div class="text-sm text-secondary">{{ $contrato->contratista->documento_identidad }}</div>
                                        @else
                                            <div class="text-secondary italic">Sin asignar</div>
                                            @if(Auth::user()->tienePermiso('vincular-contratista', $contrato->organizacion_id))
                                                <button class="mt-2 text-primary hover:text-primary-dark text-sm font-medium flex items-center"
                                                        onclick="abrirModalVincular()">
                                                    <i class="fas fa-plus-circle mr-1"></i>
                                                    Vincular Contratista
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Supervisor -->
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-500 to-primary flex items-center justify-center text-white">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm text-secondary mb-1">Supervisor</div>
                                        @if($contrato->supervisor)
                                            <div class="font-semibold text-gray-800 mb-1">{{ $contrato->supervisor->nombre }}</div>
                                            <div class="text-sm text-secondary">{{ $contrato->supervisor->email }}</div>
                                            @if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                                                <button class="mt-2 text-secondary hover:text-gray-800 text-sm font-medium flex items-center"
                                                        onclick="abrirModalSupervisor()">
                                                    <i class="fas fa-sync-alt mr-1"></i>
                                                    Cambiar
                                                </button>
                                            @endif
                                        @else
                                            <div class="text-secondary italic">Sin asignar</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Archivos del Contrato -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-paperclip text-accent mr-2"></i>
                                    Archivos del Contrato
                                </h2>
                                @if(Auth::user()->tienePermiso('subir-archivo-contrato', $contrato->organizacion_id))
                                <button onclick="abrirModalSubirArchivo()" 
                                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors flex items-center text-sm">
                                    <i class="fas fa-upload mr-2"></i>
                                    Subir Archivo
                                </button>
                                @endif
                            </div>

                            @if($contrato->archivos->count() > 0)
                            <div class="space-y-3">
                                @foreach($contrato->archivos as $archivo)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-accent transition-colors">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <!-- Icono según tipo de archivo -->
                                        <div class="w-12 h-12 rounded-lg bg-accent/10 flex items-center justify-center text-accent flex-shrink-0">
                                            @if($archivo->tipo_archivo == 'pdf')
                                                <i class="fas fa-file-pdf text-2xl"></i>
                                            @elseif(in_array($archivo->tipo_archivo, ['doc', 'docx']))
                                                <i class="fas fa-file-word text-2xl"></i>
                                            @elseif(in_array($archivo->tipo_archivo, ['xls', 'xlsx']))
                                                <i class="fas fa-file-excel text-2xl"></i>
                                            @else
                                                <i class="fas fa-file text-2xl"></i>
                                            @endif
                                        </div>

                                        <!-- Información del archivo -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <p class="font-semibold text-gray-800 truncate">{{ $archivo->nombre_original }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-primary/10 text-primary whitespace-nowrap">
                                                    {{ str_replace('_', ' ', ucfirst($archivo->tipo_documento)) }}
                                                </span>
                                            </div>
                                            <div class="flex items-center flex-wrap gap-x-4 gap-y-1 text-sm text-secondary">
                                                <span><i class="fas fa-weight-hanging mr-1"></i>{{ $archivo->tamaño_formateado }}</span>
                                                <span><i class="fas fa-calendar mr-1"></i>{{ $archivo->created_at->format('d/m/Y H:i') }}</span>
                                                <span><i class="fas fa-user mr-1"></i>{{ $archivo->subidoPor->nombre }}</span>
                                            </div>
                                            @if($archivo->descripcion)
                                            <p class="text-sm text-gray-600 mt-1">{{ $archivo->descripcion }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="flex items-center space-x-2 ml-3">
                                        <a href="{{ route('contratos.archivos.descargar', $archivo) }}" 
                                           class="text-accent hover:text-primary transition-colors p-2"
                                           title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if(Auth::user()->tienePermiso('eliminar-archivo-contrato', $contrato->organizacion_id))
                                        <form action="{{ route('contratos.archivos.eliminar', $archivo) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar este archivo?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-danger hover:text-red-700 transition-colors p-2"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <i class="fas fa-folder-open text-6xl text-gray-300 mb-3"></i>
                                <p class="text-secondary font-medium">No hay archivos adjuntos</p>
                                <p class="text-sm text-gray-400 mt-1">Sube el primer archivo del contrato</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Columna Lateral -->
                    <div class="space-y-6">
                        <!-- Fechas del Contrato -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-accent mr-2"></i>
                                Vigencia
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Fecha de Inicio</label>
                                    <div class="flex items-center text-gray-800 font-semibold">
                                        <i class="fas fa-calendar-check text-accent mr-2"></i>
                                        {{ $contrato->fecha_inicio->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm text-secondary mb-1">Fecha de Fin</label>
                                    <div class="flex items-center text-gray-800 font-semibold">
                                        <i class="fas fa-calendar-times text-warning mr-2"></i>
                                        {{ $contrato->fecha_fin->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <label class="block text-sm text-secondary mb-1">Duración</label>
                                    <div class="flex items-center text-gray-800 font-semibold">
                                        <i class="fas fa-hourglass-half text-primary mr-2"></i>
                                        @php
                                            $dias = $contrato->fecha_inicio->diffInDays($contrato->fecha_fin);
                                            $meses = floor($dias / 30);
                                            $diasRestantes = $dias % 30;
                                        @endphp
                                        {{ $meses > 0 ? $meses . ' ' . ($meses == 1 ? 'mes' : 'meses') : '' }}
                                        {{ $diasRestantes > 0 ? $diasRestantes . ' ' . ($diasRestantes == 1 ? 'día' : 'días') : '' }}
                                    </div>
                                </div>

                                @php
                                    $hoy = now();
                                    $diasRestantes = $hoy->diffInDays($contrato->fecha_fin, false);
                                @endphp

                                @if($contrato->estado == 'activo' && $diasRestantes >= 0)
                                    <div class="mt-4 p-3 bg-warning/10 border border-warning/20 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                            <div>
                                                <div class="font-semibold text-warning">{{ $diasRestantes }} días restantes</div>
                                                <div class="text-sm text-secondary">Hasta la finalización del contrato</div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($contrato->estado == 'activo' && $diasRestantes < 0)
                                    <div class="mt-4 p-3 bg-danger/10 border border-danger/20 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-times-circle text-danger mr-2"></i>
                                            <div>
                                                <div class="font-semibold text-danger">Contrato vencido</div>
                                                <div class="text-sm text-secondary">Hace {{ abs($diasRestantes) }} días</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Acciones Rápidas -->
                        @if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-bolt text-accent mr-2"></i>
                                Acciones Rápidas
                            </h2>
                            <div class="space-y-2">
                                @if($contrato->estado == 'activo')
                                    <button class="w-full text-left p-3 border border-warning text-warning rounded-lg hover:bg-warning/5 transition-colors flex items-center"
                                            onclick="cambiarEstado('suspendido')">
                                        <i class="fas fa-pause-circle mr-2"></i>
                                        Suspender Contrato
                                    </button>
                                    <button class="w-full text-left p-3 border border-accent text-accent rounded-lg hover:bg-accent/5 transition-colors flex items-center"
                                            onclick="cambiarEstado('terminado')">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Terminar Contrato
                                    </button>
                                @elseif($contrato->estado == 'suspendido')
                                    <button class="w-full text-left p-3 border border-green-500 text-green-500 rounded-lg hover:bg-green-50 transition-colors flex items-center"
                                            onclick="cambiarEstado('activo')">
                                        <i class="fas fa-play-circle mr-2"></i>
                                        Reactivar Contrato
                                    </button>
                                @elseif($contrato->estado == 'terminado')
                                    <button class="w-full text-left p-3 border border-gray-700 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center"
                                            onclick="cambiarEstado('liquidado')">
                                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                                        Liquidar Contrato
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Información de Auditoría -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-sm font-semibold text-gray-600 mb-3 flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                Información de Registro
                            </h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div>
                                    <strong>Creado:</strong> {{ $contrato->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <strong>Actualizado:</strong> {{ $contrato->updated_at->format('d/m/Y H:i') }}
                                </div>
                                @if($contrato->vinculadoPor)
                                    <div>
                                        <strong>Vinculado por:</strong> {{ $contrato->vinculadoPor->nombre }}
                                    </div>
                                @endif
                            </div>
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

@endsection

@push('scripts')
<script>
    // Modal de subir archivo
    function abrirModalSubirArchivo() {
        document.getElementById('modalSubirArchivo').classList.remove('hidden');
    }

    function cerrarModalSubirArchivo() {
        document.getElementById('modalSubirArchivo').classList.add('hidden');
        // Limpiar formulario
        document.querySelector('#modalSubirArchivo form').reset();
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
    document.getElementById('modalSubirArchivo')?.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalSubirArchivo();
        }
    });

    // Funciones para otros modales
    function abrirModalVincular() {
        // Lógica para abrir modal de vincular contratista
        console.log('Abrir modal vincular contratista');
    }

    function abrirModalSupervisor() {
        // Lógica para abrir modal de cambiar supervisor
        console.log('Abrir modal cambiar supervisor');
    }

    function cambiarEstado(estado) {
        // Lógica para cambiar estado
        console.log('Cambiar estado a:', estado);
    }
</script>
@endpush