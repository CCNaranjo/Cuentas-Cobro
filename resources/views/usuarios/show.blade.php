@extends('layouts.app-dashboard')

@section('title', $usuario->nombre . ' - ARCA-D')

@section('content')
<div class="flex h-screen bg-bg-main overflow-hidden">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex items-center space-x-2 text-sm text-secondary">
                        <a href="{{ route('usuarios.index') }}" class="hover:text-primary">Usuarios</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-800">{{ $usuario->nombre }}</span>
                    </nav>
                </div>

                <!-- Header con Perfil -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white text-3xl font-bold mr-6 shadow-lg">
                                {{ substr($usuario->nombre, 0, 1) }}
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-800">{{ $usuario->nombre }}</h1>
                                <p class="text-secondary mt-1 flex items-center">
                                    <i class="fas fa-envelope mr-2"></i>
                                    {{ $usuario->email }}
                                </p>
                                <div class="flex items-center space-x-4 mt-3">
                                    @if($usuario->estado == 'activo')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i>Inactivo
                                        </span>
                                    @endif

                                    @if($usuario->tipo_vinculacion)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            <i class="fas fa-link mr-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $usuario->tipo_vinculacion)) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            @if(auth()->id() == $usuario->id)
                                <a href="#" class="px-4 py-2 border-2 border-primary text-primary font-semibold rounded-lg hover:bg-primary hover:text-white transition-all">
                                    <i class="fas fa-edit mr-2"></i>
                                    Editar Perfil
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Información Personal -->
                    <div class="lg:col-span-2">
                        <!-- Datos Básicos -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-user text-primary mr-2"></i>
                                Información Personal
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm font-semibold text-secondary">Nombre Completo</label>
                                    <p class="mt-1 text-gray-800 font-medium">{{ $usuario->nombre }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-secondary">Email</label>
                                    <p class="mt-1 text-gray-800 font-medium flex items-center">
                                        {{ $usuario->email }}
                                        @if($usuario->email_verificado_en)
                                            <i class="fas fa-check-circle text-green-500 ml-2" title="Email verificado"></i>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-secondary">Documento de Identidad</label>
                                    <p class="mt-1 text-gray-800 font-medium">{{ $usuario->documento_identidad ?? 'No registrado' }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-secondary">Teléfono</label>
                                    <p class="mt-1 text-gray-800 font-medium">{{ $usuario->telefono ?? 'No registrado' }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-secondary">Último Acceso</label>
                                    <p class="mt-1 text-gray-800 font-medium">
                                        {{ $usuario->ultimo_acceso ? $usuario->ultimo_acceso->diffForHumans() : 'Nunca' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-secondary">Fecha de Registro</label>
                                    <p class="mt-1 text-gray-800 font-medium">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Organizaciones Vinculadas -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-building text-accent mr-2"></i>
                                Organizaciones
                            </h2>

                            <div class="space-y-4">
                                @forelse($usuario->organizacionesVinculadas as $org)
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-800">{{ $org->nombre_oficial }}</h3>
                                            <p class="text-sm text-secondary mt-1">{{ $org->municipio }}, {{ $org->departamento }}</p>
                                            
                                            <!-- Rol en esta organización -->
                                            <div class="mt-2 flex items-center space-x-2">
                                                @foreach($usuario->roles as $rol)
                                                    @if($rol->pivot->organizacion_id == $org->id)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                                            <i class="fas fa-user-tag mr-1"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                                                        </span>
                                                        <span class="text-xs text-secondary">
                                                            desde {{ $rol->pivot->created_at->format('d/m/Y') }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold 
                                            {{ $org->pivot->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($org->pivot->estado) }}
                                        </span>
                                    </div>
                                </div>
                                @empty
                                <p class="text-center text-secondary py-4">No está vinculado a ninguna organización</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Contratos -->
                        @if($usuario->contratosComoContratista->count() > 0 || $usuario->contratosComoSupervisor->count() > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-file-contract text-primary mr-2"></i>
                                Contratos Relacionados
                            </h2>

                            <!-- Como Contratista -->
                            @if($usuario->contratosComoContratista->count() > 0)
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-secondary mb-3">Como Contratista</h3>
                                <div class="space-y-2">
                                    @foreach($usuario->contratosComoContratista->take(5) as $contrato)
                                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                        <div>
                                            <p class="font-mono text-sm font-semibold text-gray-800">{{ $contrato->numero_contrato }}</p>
                                            <p class="text-xs text-secondary">Valor: ${{ number_format($contrato->valor_total, 0) }}</p>
                                        </div>
                                        <a href="{{ route('contratos.show', $contrato) }}" 
                                           class="text-primary hover:text-primary-dark">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Como Supervisor -->
                            @if($usuario->contratosComoSupervisor->count() > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-secondary mb-3">Como Supervisor</h3>
                                <div class="space-y-2">
                                    @foreach($usuario->contratosComoSupervisor->take(5) as $contrato)
                                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                        <div>
                                            <p class="font-mono text-sm font-semibold text-gray-800">{{ $contrato->numero_contrato }}</p>
                                            <p class="text-xs text-secondary">Contratista: {{ $contrato->contratista->nombre ?? 'Sin asignar' }}</p>
                                        </div>
                                        <a href="{{ route('contratos.show', $contrato) }}" 
                                           class="text-primary hover:text-primary-dark">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar Derecha -->
                    <div class="lg:col-span-1">
                        <!-- Resumen Rápido -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4">Resumen</h2>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-building text-blue-600 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-800">Organizaciones</span>
                                    </div>
                                    <span class="text-lg font-bold text-blue-600">{{ $usuario->organizacionesVinculadas->count() }}</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-contract text-indigo-600 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-800">Contratos</span>
                                    </div>
                                    <span class="text-lg font-bold text-indigo-600">{{ $usuario->contratosComoContratista->count() }}</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-check text-green-600 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-800">Supervisión</span>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">{{ $usuario->contratosComoSupervisor->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actividad Reciente -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4">Actividad Reciente</h2>
                            
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-sign-in-alt text-green-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 font-medium">Último acceso</p>
                                        <p class="text-xs text-secondary">
                                            {{ $usuario->ultimo_acceso ? $usuario->ultimo_acceso->diffForHumans() : 'Nunca' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-user-plus text-blue-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 font-medium">Registrado</p>
                                        <p class="text-xs text-secondary">{{ $usuario->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                @if($usuario->email_verificado_en)
                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-check-circle text-purple-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 font-medium">Email verificado</p>
                                        <p class="text-xs text-secondary">{{ $usuario->email_verificado_en->diffForHumans() }}</p>
                                    </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection