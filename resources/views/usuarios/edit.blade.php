@extends('layouts.app-dashboard')

@section('title', 'Editar Usuario - ARCA-D')

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
                        <a href="{{ route('usuarios.index', ['organizacion_id' => $organizacion->id]) }}" class="hover:text-primary">Usuarios</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-800">Editar</span>
                    </nav>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Usuario</h1>

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

                    <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="organizacion_id" value="{{ $organizacion->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Documento de Identidad</label>
                                <input type="text" name="documento_identidad" value="{{ old('documento_identidad', $usuario->documento_identidad) }}"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                                <select name="estado" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                                    <option value="activo" {{ old('estado', $usuario->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado', $usuario->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspendido" {{ old('estado', $usuario->estado) == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Rol</label>
                                <select name="rol_id" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                                    <option value="">Seleccionar rol...</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}"
                                            {{ old('rol_id', $rolActual->id ?? '') == $rol->id ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('usuarios.index', ['organizacion_id' => $organizacion->id]) }}" class="px-4 py-2 mr-2 border border-secondary text-secondary rounded-lg hover:bg-secondary hover:text-white transition-all">Cancelar</a>
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