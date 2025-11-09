@extends('layouts.app-dashboard')

@section('title', 'Detalles del Rol - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Header Section -->
                <div class="mb-6 animate-slideIn">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                @switch($rol->nombre)
                                    @case('admin_global')
                                        <i class="fas fa-crown text-warning mr-3"></i>
                                        @break
                                    @case('admin_organizacion')
                                        <i class="fas fa-user-shield text-primary mr-3"></i>
                                        @break
                                    @case('ordenador_gasto')
                                        <i class="fas fa-money-check-alt text-green-600 mr-3"></i>
                                        @break
                                    @case('supervisor')
                                        <i class="fas fa-user-check text-blue-600 mr-3"></i>
                                        @break
                                    @case('tesorero')
                                        <i class="fas fa-coins text-yellow-600 mr-3"></i>
                                        @break
                                    @case('contratista')
                                        <i class="fas fa-user-tie text-purple-600 mr-3"></i>
                                        @break
                                    @default
                                        <i class="fas fa-users-cog text-primary mr-3"></i>
                                @endswitch
                                Detalles del Rol: {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                            </h1>
                            <p class="text-secondary mt-1">
                                Información completa y permisos asignados
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('roles.index') }}" 
                               class="bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver
                            </a>
                            @if(!$rol->es_sistema)
                            <a href="{{ route('roles.edit', $rol) }}" 
                               class="bg-gradient-to-r from-warning to-warning-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Editar Rol
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 animate-fadeIn">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-green-800 font-semibold">¡Éxito!</h3>
                            <p class="text-green-600 text-sm mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Información del Rol -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Tarjeta de Información Básica -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Información del Rol
                            </h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-1">ID del Rol</label>
                                    <div class="flex items-center">
                                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-mono">
                                            #{{ $rol->id }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nombre</label>
                                    <p class="text-lg font-semibold text-gray-800 capitalize">
                                        {{ str_replace('_', ' ', $rol->nombre) }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-1">Descripción</label>
                                    <p class="text-gray-700">{{ $rol->descripcion }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nivel Jerárquico</label>
                                    <div class="flex items-center">
                                        <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-semibold">
                                            Nivel {{ $rol->nivel_jerarquico }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            @switch($rol->nivel_jerarquico)
                                                @case(1) (Administración Global) @break
                                                @case(2) (Administración Organización) @break
                                                @case(3) (Gestión Operativa) @break
                                                @case(4) (Usuarios Especializados) @break
                                                @case(5) (Usuarios Básicos) @break
                                            @endswitch
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-1">Tipo</label>
                                    @if($rol->es_sistema)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                        <i class="fas fa-cog mr-2"></i>
                                        Rol del Sistema
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-user mr-2"></i>
                                        Rol Personalizado
                                    </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-1">Usuarios</label>
                                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $rol->usuarios_count ?? $rol->usuarios->count() }} usuarios
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-1">Permisos</label>
                                        <span class="bg-info/10 text-info px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $rol->permisos_count ?? $rol->permisos->count() }} permisos
                                        </span>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Creado</label>
                                            <p class="text-gray-700">{{ $rol->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Actualizado</label>
                                            <p class="text-gray-700">{{ $rol->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones Rápidas -->
                        @if(!$rol->es_sistema)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-bolt text-warning mr-2"></i>
                                Acciones
                            </h2>
                            <div class="space-y-3">
                                <a href="{{ route('roles.edit', $rol) }}" 
                                   class="w-full bg-warning text-white font-semibold py-3 px-4 rounded-lg hover:bg-warning-dark transition-all flex items-center justify-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    Editar Rol
                                </a>
                                
                                @if($rol->usuarios_count == 0)
                                <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full bg-red-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-red-700 transition-all flex items-center justify-center"
                                            onclick="return confirm('¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-trash mr-2"></i>
                                        Eliminar Rol
                                    </button>
                                </form>
                                @else
                                <button disabled
                                        class="w-full bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg flex items-center justify-center cursor-not-allowed"
                                        title="No se puede eliminar un rol con usuarios asignados">
                                    <i class="fas fa-trash mr-2"></i>
                                    Eliminar Rol
                                </button>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Permisos y Usuarios -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Tarjeta de Permisos -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-key text-primary mr-2"></i>
                                    Permisos Asignados
                                    <span class="ml-2 bg-primary/10 text-primary px-2 py-1 rounded-full text-sm">
                                        {{ $rol->permisos_count ?? $rol->permisos->count() }}
                                    </span>
                                </h2>
                            </div>

                            @if($rol->permisos->count() > 0)
                            <div class="space-y-4">
                                @foreach($modulos as $modulo)
                                @php
                                    $moduloPermisos = $rol->permisos->where('modulo_id', $modulo->id);
                                @endphp
                                @if($moduloPermisos->count() > 0)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                        <h3 class="font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-{{ $modulo->icono ?? 'cube' }} text-primary mr-2"></i>
                                            {{ $modulo->nombre }}
                                            <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">
                                                {{ $moduloPermisos->count() }} permisos
                                            </span>
                                        </h3>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            @foreach($moduloPermisos as $permiso)
                                            <div class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 bg-gray-50">
                                                <i class="fas fa-check text-green-500"></i>
                                                <div class="flex-1">
                                                    <span class="block text-sm font-medium text-gray-800">{{ $permiso->nombre }}</span>
                                                    <span class="block text-xs text-gray-500">{{ $permiso->descripcion }}</span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <i class="fas fa-key text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium">No hay permisos asignados</p>
                                <p class="text-sm text-gray-400 mt-1">Este rol no tiene permisos específicos configurados</p>
                                @if(!$rol->es_sistema)
                                <a href="{{ route('roles.edit', $rol) }}" class="mt-4 bg-primary text-white px-4 py-2 rounded-lg inline-flex items-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    Asignar Permisos
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Tarjeta de Usuarios -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-users text-accent mr-2"></i>
                                    Usuarios con este Rol
                                    <span class="ml-2 bg-accent/10 text-accent px-2 py-1 rounded-full text-sm">
                                        {{ $rol->usuarios_count ?? $rol->usuarios->count() }}
                                    </span>
                                </h2>
                            </div>

                            @if($rol->usuarios->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Usuario</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Email</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Organización</th>
                                            <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($rol->usuarios as $usuario)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                                        <i class="fas fa-user text-primary text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-800">{{ $usuario->name }}</p>
                                                        <p class="text-xs text-gray-500">ID: {{ $usuario->id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <p class="text-sm text-gray-800">{{ $usuario->email }}</p>
                                            </td>
                                            <td class="py-4 px-4">
                                                @php
                                                    $organizacion = $usuario->organizacionesVinculadas->first();
                                                @endphp
                                                @if($organizacion)
                                                <span class="text-sm text-gray-700">{{ $organizacion->nombre_oficial }}</span>
                                                @else
                                                <span class="text-xs text-gray-400">Sin organización</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                @if($usuario->estado == 'activo')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>Activo
                                                </span>
                                                @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    Inactivo
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-8">
                                <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium">No hay usuarios asignados</p>
                                <p class="text-sm text-gray-400 mt-1">No hay usuarios con este rol actualmente</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    // Animación de entrada para las tarjetas
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.bg-white');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.4s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endpush
@endsection