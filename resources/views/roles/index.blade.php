@extends('layouts.app-dashboard')

@section('title', 'Gestión de Roles - ARCA-D')

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
                                <i class="fas fa-users-cog text-primary mr-3"></i>
                                Gestión de Roles
                            </h1>
                            <p class="text-secondary mt-1">
                                Administra los roles y permisos del sistema
                            </p>
                        </div>
                        <a href="{{ route('roles.create') }}" 
                           class="bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-all flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Nuevo Rol
                        </a>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Roles -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users-cog text-primary text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                {{ $roles->where('es_sistema', false)->count() }} personalizados
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Total de Roles</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $roles->count() }}</p>
                    </div>

                    <!-- Usuarios con Rol -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-accent text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                Activos
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Usuarios con Rol</h3>
                        <p class="text-3xl font-bold text-accent">{{ $roles->sum('usuarios_count') }}</p>
                    </div>

                    <!-- Roles del Sistema -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cogs text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                Fijos
                            </span>
                        </div>
                        <h3 class="text-secondary text-sm font-medium mb-1">Roles del Sistema</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $roles->where('es_sistema', true)->count() }}</p>
                    </div>

                    <!-- Niveles Jerárquicos -->
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sitemap text-white text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">
                                5 niveles
                            </span>
                        </div>
                        <h3 class="text-white/80 text-sm font-medium mb-1">Niveles Jerárquicos</h3>
                        <p class="text-3xl font-bold">1-5</p>
                    </div>
                </div>

                <!-- Tabla de Roles -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-list text-primary mr-2"></i>
                            Lista de Roles
                        </h2>
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <input type="text" placeholder="Buscar rol..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    @if($roles->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Rol</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Descripción</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Nivel</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Usuarios</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Permisos</th>
                                    <th class="text-center py-3 px-4 text-sm font-semibold text-secondary">Tipo</th>
                                    <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($roles as $role)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mr-3">
                                                @switch($role->nombre)
                                                    @case('admin_global')
                                                        <i class="fas fa-crown text-primary"></i>
                                                        @break
                                                    @case('admin_organizacion')
                                                        <i class="fas fa-user-shield text-primary"></i>
                                                        @break
                                                    @case('ordenador_gasto')
                                                        <i class="fas fa-money-check-alt text-green-600"></i>
                                                        @break
                                                    @case('supervisor')
                                                        <i class="fas fa-user-check text-blue-600"></i>
                                                        @break
                                                    @case('tesorero')
                                                        <i class="fas fa-coins text-yellow-600"></i>
                                                        @break
                                                    @case('contratista')
                                                        <i class="fas fa-user-tie text-purple-600"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-user text-gray-600"></i>
                                                @endswitch
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 capitalize">
                                                    {{ str_replace('_', ' ', $role->nombre) }}
                                                </p>
                                                <p class="text-xs text-secondary">ID: {{ $role->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <p class="text-sm text-gray-800">{{ $role->descripcion }}</p>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-semibold">
                                            Nivel {{ $role->nivel_jerarquico }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-accent/10 text-accent px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $role->usuarios_count }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-info/10 text-info px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $role->permisos_count ?? $role->permisos->count() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($role->es_sistema)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                <i class="fas fa-cog mr-1"></i>Sistema
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-user mr-1"></i>Personalizado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('roles.show', $role) }}" 
                                               class="text-accent hover:text-primary transition-colors p-2"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!($role->nombre === 'admin_global'))
                                            <a href="{{ route('roles.edit', $role) }}" 
                                               class="text-primary hover:text-primary-dark transition-colors p-2"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            @if(!$role->es_sistema)
                                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 transition-colors p-2"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este rol?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-users-cog text-6xl text-gray-300 mb-4"></i>
                        <p class="text-secondary font-medium">No hay roles registrados</p>
                        <p class="text-sm text-gray-400 mt-1">Comienza creando el primer rol del sistema</p>
                        <a href="{{ route('roles.create') }}" class="mt-4 bg-primary text-white px-6 py-2 rounded-lg inline-flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Crear Primer Rol
                        </a>
                    </div>
                    @endif
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