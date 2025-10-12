@extends('layouts.app-dashboard')

@section('title', 'Contratos - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Contratos</h1>
                        <p class="text-secondary mt-1">Gestiona los contratos de la organización</p>
                    </div>
                    @if(auth()->user()->tienePermiso('crear-contrato', session('organizacion_actual')))
                    <a href="{{ route('contratos.create', ['organizacion_id' => session('organizacion_actual')]) }}" 
                       class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Nuevo Contrato
                    </a>
                    @endif
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <input type="text" 
                                   name="search" 
                                   placeholder="Buscar por número o contratista..." 
                                   value="{{ request('search') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        
                        <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent">
                            <option value="">Todos los estados</option>
                            <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="suspendido" {{ request('estado') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                            <option value="terminado" {{ request('estado') == 'terminado' ? 'selected' : '' }}>Terminado</option>
                            <option value="liquidado" {{ request('estado') == 'liquidado' ? 'selected' : '' }}>Liquidado</option>
                        </select>

                        <select name="contratista_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent">
                            <option value="">Todos los contratistas</option>
                            @foreach($contratistas as $contratista)
                            <option value="{{ $contratista->id }}" {{ request('contratista_id') == $contratista->id ? 'selected' : '' }}>
                                {{ $contratista->nombre }}
                            </option>
                            @endforeach
                        </select>

                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </form>
                </div>

                <!-- Tabla de Contratos -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Número</th>
                                    <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Contratista</th>
                                    <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Supervisor</th>
                                    <th class="text-right py-4 px-6 text-sm font-semibold text-gray-700">Valor Total</th>
                                    <th class="text-center py-4 px-6 text-sm font-semibold text-gray-700">% Ejecución</th>
                                    <th class="text-center py-4 px-6 text-sm font-semibold text-gray-700">Vigencia</th>
                                    <th class="text-center py-4 px-6 text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="text-right py-4 px-6 text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($contratos as $contrato)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div>
                                            <p class="font-mono font-semibold text-gray-800">{{ $contrato->numero_contrato }}</p>
                                            <p class="text-xs text-secondary mt-1 line-clamp-1">{{ $contrato->objeto_contractual }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($contrato->contratista)
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold mr-2">
                                                    {{ substr($contrato->contratista->nombre, 0, 1) }}
                                                </div>
                                                <span class="text-sm text-gray-800">{{ $contrato->contratista->nombre }}</span>
                                            </div>
                                        @else
                                            <span class="text-sm text-secondary italic">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($contrato->supervisor)
                                            <span class="text-sm text-gray-800">{{ $contrato->supervisor->nombre }}</span>
                                        @else
                                            <span class="text-sm text-secondary italic">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <span class="font-bold text-gray-800">${{ number_format($contrato->valor_total, 0) }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center">
                                            <div class="w-24">
                                                <div class="flex items-center justify-between text-xs mb-1">
                                                    <span class="text-secondary">0%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-accent h-2 rounded-full" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-center">
                                            <p class="text-xs text-secondary">{{ $contrato->fecha_inicio->format('d/m/Y') }}</p>
                                            <p class="text-xs text-secondary">{{ $contrato->fecha_fin->format('d/m/Y') }}</p>
                                            @if($contrato->estaActivo())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 mt-1">
                                                    <i class="fas fa-check-circle mr-1"></i>Vigente
                                                </span>
                                            @elseif($contrato->fecha_fin < now())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 mt-1">
                                                    <i class="fas fa-calendar-times mr-1"></i>Vencido
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($contrato->estado == 'activo')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Activo
                                            </span>
                                        @elseif($contrato->estado == 'borrador')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                <i class="fas fa-file mr-1"></i>Borrador
                                            </span>
                                        @elseif($contrato->estado == 'suspendido')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-warning/10 text-warning">
                                                <i class="fas fa-pause-circle mr-1"></i>Suspendido
                                            </span>
                                        @elseif($contrato->estado == 'terminado')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                <i class="fas fa-flag-checkered mr-1"></i>Terminado
                                            </span>
                                        @elseif($contrato->estado == 'liquidado')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                <i class="fas fa-file-invoice-dollar mr-1"></i>Liquidado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('contratos.show', $contrato) }}" 
                                               class="text-accent hover:text-primary transition-colors p-2"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($contrato->estado == 'borrador' && auth()->user()->tienePermiso('editar-contrato', session('organizacion_actual')))
                                            <a href="{{ route('contratos.edit', $contrato) }}" 
                                               class="text-primary hover:text-primary-dark transition-colors p-2"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center">
                                        <i class="fas fa-file-contract text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-secondary font-medium">No hay contratos registrados</p>
                                        <p class="text-sm text-gray-400 mt-2">Crea tu primer contrato</p>
                                        @if(auth()->user()->tienePermiso('crear-contrato', session('organizacion_actual')))
                                        <a href="{{ route('contratos.create', ['organizacion_id' => session('organizacion_actual')]) }}" 
                                           class="inline-flex items-center mt-4 text-primary hover:text-primary-dark font-medium">
                                            <i class="fas fa-plus-circle mr-2"></i>
                                            Nuevo Contrato
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                @if($contratos->hasPages())
                <div class="mt-6">
                    {{ $contratos->links() }}
                </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection