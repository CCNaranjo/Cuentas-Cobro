@extends('layouts.app-dashboard')

@section('title', 'Editar organización - ' . $organizacion->nombre_oficial)

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
                        <a href="{{ route('organizaciones.index') }}" class="hover:text-primary">Organizaciones</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <a href="{{ route('organizaciones.show', $organizacion) }}" class="hover:text-primary">{{ $organizacion->nombre_oficial }}</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-800">Editar</span>
                    </nav>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Organización</h1>

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

                    <form method="POST" action="{{ route('organizaciones.update', $organizacion) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre oficial</label>
                                <input type="text" name="nombre_oficial" value="{{ old('nombre_oficial', $organizacion->nombre_oficial) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">NIT</label>
                                <input type="text" name="nit" value="{{ old('nit', $organizacion->nit) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Departamento</label>
                                <input type="text" name="departamento" value="{{ old('departamento', $organizacion->departamento) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Municipio</label>
                                <input type="text" name="municipio" value="{{ old('municipio', $organizacion->municipio) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Dirección</label>
                                <input type="text" name="direccion" value="{{ old('direccion', $organizacion->direccion) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono de contacto</label>
                                <input type="text" name="telefono_contacto" value="{{ old('telefono_contacto', $organizacion->telefono_contacto) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email institucional</label>
                                <input type="email" name="email_institucional" value="{{ old('email_institucional', $organizacion->email_institucional) }}" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                                <select name="estado" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary">
                                    <option value="activa" {{ old('estado', $organizacion->estado) == 'activa' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactiva" {{ old('estado', $organizacion->estado) == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                                    <option value="suspendida" {{ old('estado', $organizacion->estado) == 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Dominios de email autorizados <span class="text-xs text-secondary">(uno por línea, con @)</span></label>
                                <textarea name="dominios_email[]" rows="3"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-primary"
                                    placeholder="@dominio1.com&#10;@dominio2.com">{{ old('dominios_email', is_array($organizacion->dominios_email) ? implode("\n", $organizacion->dominios_email) : '') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('organizaciones.show', $organizacion) }}" class="px-4 py-2 mr-2 border border-secondary text-secondary rounded-lg hover:bg-secondary hover:text-white transition-all">Cancelar</a>
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