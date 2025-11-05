@extends('layouts.app-dashboard')

@section('title', 'Usuarios - ' . $organizacion->nombre_oficial)

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')
    <!-- Header -->
    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')
        <!--
        <div class="flex gap-2">
            <a href="{{ route('usuarios.pendientes') }}" 
               class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                <i class="bi bi-clock-history mr-2"></i>
                Pendientes
                @if($organizacion->vinculacionesPendientes()->where('estado', 'pendiente')->count() > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        {{ $organizacion->vinculacionesPendientes()->where('estado', 'pendiente')->count() }}
                    </span>
                @endif
            </a>
        </div>
        -->
        <main class="flex-1 overflow-y-auto gap-4 px-4 md:px-6 py-6">
            <!-- Card Principal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <!-- Tabs -->
                <div class="border-b border-gray-200 px-6 pt-4">
                    <div class="flex space-x-8" role="tablist">
                        <button class="tab-button active pb-3 border-b-2 border-transparent text-gray-600 hover:text-blue-900 transition-colors"
                                data-tab="activos"
                                type="button"
                                role="tab">
                            <i class="bi bi-check-circle mr-2"></i>
                            Activos ({{ $usuarios->where('estado', 'activo')->count() }})
                        </button>
                        <button class="tab-button pb-3 border-b-2 border-transparent text-gray-600 hover:text-blue-900 transition-colors"
                                data-tab="inactivos"
                                type="button"
                                role="tab">
                            <i class="bi bi-x-circle mr-2"></i>
                            Inactivos
                        </button>
                        <!-- Nueva Tab de Pendientes -->
                        <button class="tab-button pb-3 border-b-2 border-transparent text-gray-600 hover:text-blue-900 transition-colors relative"
                                data-tab="pendientes"
                                type="button"
                                role="tab">
                            <i class="bi bi-clock-history mr-2"></i>
                            Pendientes
                            @if($organizacion->vinculacionesPendientes()->where('estado', 'pendiente')->count() > 0)
                                <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                                    {{ $organizacion->vinculacionesPendientes()->where('estado', 'pendiente')->count() }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <form method="GET" action="{{ route('usuarios.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <input type="hidden" name="tab" id="currentTab" value="{{ request('tab', 'activos') }}">
                        
                        <div class="md:col-span-5">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                    name="search" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Buscar por nombre, email o documento..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        
                        <div class="md:col-span-3">
                            <select name="rol_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $rol)
                                <option value="{{ $rol->id }}" {{ request('rol_id') == $rol->id ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                <i class="bi bi-funnel mr-2"></i>Filtrar
                            </button>
                        </div>
                        
                        <div class="md:col-span-2">
                            <a href="{{ route('usuarios.index') }}" class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                <i class="bi bi-x-circle mr-2"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Usuarios -->
                <div class="tab-content">
                    <!-- Tab Activos -->
                    <div class="tab-pane active" id="activos" role="tabpanel">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Usuario</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Rol</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Documento</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Teléfono</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Fecha Asignación</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Estado</th>
                                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($usuarios->where('estado', 'activo') as $usuario)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-white font-semibold mr-3">
                                                    {{ substr($usuario->nombre, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-blue-900">{{ $usuario->nombre }}</div>
                                                    <div class="text-sm text-gray-500">{{ $usuario->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $rolUsuario = $usuario->roles->first(function($rol) use ($organizacion) {
                                                    return $rol->pivot->organizacion_id == $organizacion->id;
                                                });
                                                
                                                $badgeColors = [
                                                    'admin_organizacion' => 'bg-gradient-to-br from-purple-600 to-purple-800 text-white',
                                                    'ordenador_gasto' => 'bg-gradient-to-br from-blue-600 to-blue-800 text-white',
                                                    'supervisor' => 'bg-gradient-to-br from-green-600 to-green-700 text-white',
                                                    'tesorero' => 'bg-gradient-to-br from-orange-500 to-orange-600 text-white',
                                                    'contratista' => 'bg-gradient-to-br from-gray-600 to-gray-700 text-white'
                                                ];
                                            @endphp
                                            
                                            @if($rolUsuario)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $badgeColors[$rolUsuario->nombre] ?? 'bg-gray-600 text-white' }}">
                                                    <i class="bi bi-shield-check mr-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $rolUsuario->nombre)) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-700">
                                                    Sin rol
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-500">
                                            {{ $usuario->documento_identidad ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-500">
                                            {{ $usuario->telefono ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-500 text-sm">
                                            {{ $usuario->fecha_asignacion ? \Carbon\Carbon::parse($usuario->fecha_asignacion)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="bi bi-check-circle-fill mr-1"></i>Activo
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('usuarios.show', $usuario->id) }}" 
                                                class="inline-flex items-center p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                                title="Ver perfil">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(auth()->user()->tienePermiso('editar-usuario', $organizacion->id))
                                                <a href="{{ route('usuarios.edit', $usuario->id) }}" 
                                                class="inline-flex items-center p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                                title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                                
                                                @if(auth()->user()->tienePermiso('editar-usuario', $organizacion->id) 
                                                && auth()->user()->id !== $usuario->id)
                                                <button onclick="openEditRolModal('{{ $usuario->id }}', '{{ $usuario->nombre }}', '{{ $rolUsuario ? $rolUsuario->id : null }}')" 
                                                        class="inline-flex items-center p-2 border border-gray-300 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors"
                                                        title="Cambiar rol">
                                                    <i class="bi bi-person-gear"></i>
                                                </button>
                                                @endif

                                                @if(auth()->user()->tienePermiso('cambiar-estado-usuario', $organizacion->id)
                                                && auth()->user()->id !== $usuario->id)
                                                <button onclick="openEstadoModal('{{ $usuario->id }}', '{{ $usuario->nombre }}', '{{ $usuario->estado }}')" 
                                                        class="inline-flex items-center p-2 border border-gray-300 rounded-lg text-yellow-600 hover:bg-yellow-50 transition-colors"
                                                        title="Cambiar estado">
                                                    <i class="bi bi-toggle-on"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <i class="bi bi-people text-6xl text-gray-200 mb-3"></i>
                                            <p class="text-gray-500">No hay usuarios activos</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Inactivos -->
                    <div class="tab-pane hidden" id="inactivos" role="tabpanel">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Usuario</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Rol</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Documento</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Estado</th>
                                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($usuarios->where('estado', '!=', 'activo') as $usuario)
                                    <tr class="opacity-70 hover:opacity-100 transition-opacity">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold mr-3">
                                                    {{ substr($usuario->nombre, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold">{{ $usuario->nombre }}</div>
                                                    <div class="text-sm text-gray-500">{{ $usuario->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $rolUsuario = $usuario->roles->first(function($rol) use ($organizacion) {
                                                    return $rol->pivot->organizacion_id == $organizacion->id;
                                                });
                                            @endphp
                                            @if($rolUsuario)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-700">
                                                    {{ ucfirst(str_replace('_', ' ', $rolUsuario->nombre)) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-500">
                                            {{ $usuario->documento_identidad ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($usuario->estado == 'suspendido')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="bi bi-pause-circle-fill mr-1"></i>Suspendido
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="bi bi-x-circle-fill mr-1"></i>Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if(auth()->user()->tienePermiso('cambiar-estado-usuario', $organizacion->id))
                                            <button onclick="openEstadoModal('{{ $usuario->id }}', '{{ $usuario->nombre }}', '{{ $usuario->estado }}')" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm"
                                                    title="Reactivar usuario">
                                                <i class="bi bi-arrow-clockwise mr-1"></i>Reactivar
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <i class="bi bi-people text-6xl text-gray-200 mb-3"></i>
                                            <p class="text-gray-500">No hay usuarios inactivos</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Pendientes -->
                    <div class="tab-pane hidden m-4 " id="pendientes" role="tabpanel">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-semibold text-blue-900">Vinculaciones Pendientes</h5>
                            <a href="{{ route('usuarios.pendientes') }}" class="inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors text-sm">
                                <i class="bi bi-clock-history mr-2"></i>Gestionar Pendientes
                            </a>
                        </div>

                        @if($organizacion->vinculacionesPendientes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($organizacion->vinculacionesPendientes as $pendiente)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-orange-500">
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 font-semibold mr-3">
                                                    {{ substr($pendiente->usuario->nombre, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="font-semibold text-gray-900 mb-0">{{ $pendiente->usuario->nombre }}</h6>
                                                    <small class="text-gray-500">{{ $pendiente->usuario->email }}</small>
                                                </div>
                                            </div>
                                            <small class="text-gray-400">{{ $pendiente->created_at->diffForHumans() }}</small>
                                        </div>
                                        
                                        @if($pendiente->codigo_vinculacion_usado)
                                        <div class="mb-3 p-2 bg-gray-50 rounded-lg">
                                            <small class="text-gray-600">
                                                <i class="bi bi-key mr-1"></i>Código usado: 
                                                <code class="ml-1 font-mono">{{ $pendiente->codigo_vinculacion_usado }}</code>
                                            </small>
                                        </div>
                                        @endif

                                        <div class="flex gap-2">
                                            <a href="{{ route('usuarios.pendientes') }}" 
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                                                <i class="bi bi-check-circle mr-2"></i>Asignar Rol
                                            </a>
                                            <button type="button" 
                                                    class="inline-flex items-center p-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                    onclick="rechazarModal('{{ $pendiente->id }}', '{{ $pendiente->usuario->nombre }}')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="bi bi-check-circle text-5xl text-green-500 mb-3"></i>
                                <h5 class="text-lg font-semibold text-green-600 mb-2">¡Todo al día!</h5>
                                <p class="text-gray-500">No hay vinculaciones pendientes por revisar</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Paginación -->
                @if($usuarios->hasPages())
                <div class="border-t border-gray-200 px-6 py-4 bg-white">
                    {{ $usuarios->links() }}
                </div>
                @endif
            </div>
        </main>
    </div>
    
</div>

<!-- Modal: Cambiar Rol -->
<div class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="modalCambiarRol">
    <div class="relative top-20 mx-auto p-4 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="modal-content rounded-xl border-none">
            <div class="modal-header border-b-0 pb-0">
                <h5 class="modal-title text-xl font-bold text-blue-900">
                    <i class="bi bi-person-gear mr-2"></i>Cambiar Rol de Usuario
                </h5>
                <button type="button" class="modal-close absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form id="formCambiarRol" method="POST" action="{{ route('usuarios.cambiar-rol', $usuario->id) }}">
                @csrf
                <input type="hidden" name="organizacion_id" value="{{ $organizacion->id }}">
                
                <div class="modal-body p-6">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-4">
                        <div class="flex">
                            <i class="bi bi-info-circle text-blue-400 mr-2 mt-0.5"></i>
                            <div>
                                <span class="text-blue-700">Cambiar rol de: <strong id="usuarioNombreRol"></strong></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="nuevoRolId" class="block text-sm font-medium text-gray-700 mb-2">
                            Nuevo Rol <span class="text-red-500">*</span>
                        </label>
                        <select id="nuevoRolId" name="rol_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Seleccionar rol...</option>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->id }}">
                                {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                <span class="text-gray-500">(Nivel {{ $rol->nivel_jerarquico }})</span>
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer border-t border-gray-200 pt-4 flex justify-end space-x-3">
                    <button type="button" class="modal-cancel px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                        <i class="bi bi-check-circle mr-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cambiar Estado -->
<div class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="modalCambiarEstado">
    <div class="relative top-20 mx-auto p-4 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="modal-content rounded-xl border-none">
            <div class="modal-header border-b-0 pb-0">
                <h5 class="modal-title text-xl font-bold text-blue-900">
                    <i class="bi bi-toggle-on mr-2"></i>Cambiar Estado de Usuario
                </h5>
                <button type="button" class="modal-close absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form id="formCambiarEstado" method="POST" action="{{ route('usuarios.cambiar-estado', $usuario->id) }}">
                @csrf                
                <div class="modal-body p-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-4">
                        <div class="flex">
                            <i class="bi bi-exclamation-triangle text-yellow-400 mr-2 mt-0.5"></i>
                            <div>
                                <span class="text-yellow-700">Cambiar estado de: <strong id="usuarioNombreEstado"></strong></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="nuevoEstado" class="block text-sm font-medium text-gray-700 mb-2">
                            Nuevo Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="nuevoEstado" name="estado" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="activo">
                                <i class="bi bi-check-circle"></i> Activo
                            </option>
                            <option value="inactivo">
                                <i class="bi bi-x-circle"></i> Inactivo
                            </option>
                            <option value="suspendido">
                                <i class="bi bi-pause-circle"></i> Suspendido
                            </option>
                        </select>
                        <div class="mt-2 text-sm text-gray-500">
                            <p><strong>Activo:</strong> Usuario con acceso completo</p>
                            <p><strong>Suspendido:</strong> Usuario temporalmente sin acceso</p>
                            <p><strong>Inactivo:</strong> Usuario sin acceso permanente</p>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-t border-gray-200 pt-4 flex justify-end space-x-3">
                    <button type="button" class="modal-cancel px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors flex items-center">
                        <i class="bi bi-arrow-repeat mr-2"></i>Cambiar Estado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Sistema de tabs personalizado
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tabs
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Actualizar botones
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-600', 'text-blue-900');
                    btn.classList.add('text-gray-600');
                });
                this.classList.add('active', 'border-blue-600', 'text-blue-900');
                this.classList.remove('text-gray-600');
                
                // Actualizar paneles
                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                    pane.classList.remove('active');
                });
                
                const targetPane = document.getElementById(targetTab);
                targetPane.classList.remove('hidden');
                targetPane.classList.add('active');
                
                // Actualizar input hidden para filtros
                document.getElementById('currentTab').value = targetTab;
            });
        });
        
        // Establecer tab activo inicial
        const activeTab = '{{ request("tab", "activos") }}';
        if (activeTab === 'inactivos') {
            document.querySelector('[data-tab="inactivos"]').click();
        }
        
        // Sistema de modales personalizado
        const modals = document.querySelectorAll('.modal');
        const modalCloseButtons = document.querySelectorAll('.modal-close, .modal-cancel');
        
        modalCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                modal.classList.add('hidden');
            });
        });
        
        // Cerrar modal al hacer clic fuera
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    });

    // Función para abrir modal de cambiar rol
    function openEditRolModal(usuarioId, nombre, rolActualId) {
        const modal = document.getElementById('modalCambiarRol');
        const form = document.getElementById('formCambiarRol');
        const nombreSpan = document.getElementById('usuarioNombreRol');
        const selectRol = document.getElementById('nuevoRolId');
        
        nombreSpan.textContent = nombre;
        form.action = `/usuarios/${usuarioId}/cambiar-rol`;
        selectRol.value = rolActualId || '';
        
        modal.classList.remove('hidden');
    }

    // Función para abrir modal de cambiar estado
    function openEstadoModal(usuarioId, nombre, estadoActual) {
        const modal = document.getElementById('modalCambiarEstado');
        const form = document.getElementById('formCambiarEstado');
        const nombreSpan = document.getElementById('usuarioNombreEstado');
        const selectEstado = document.getElementById('nuevoEstado');
        
        nombreSpan.textContent = nombre;
        form.action = `/usuarios/${usuarioId}/cambiar-estado`;
        selectEstado.value = estadoActual;
        
        modal.classList.remove('hidden');
    }

    // Función para abrir modal de rechazar vinculación
    function rechazarModal(vinculacionId, nombreUsuario) {
        if (confirm(`¿Estás seguro de que deseas rechazar la vinculación de ${nombreUsuario}?`)) {
            // Aquí puedes hacer una petición AJAX o redireccionar
            fetch(`/vinculaciones/${vinculacionId}/rechazar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
</script>
@endpush