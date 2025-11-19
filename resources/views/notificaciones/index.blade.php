@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Notificaciones</h1>
            <p class="text-sm text-gray-600 mt-1">Historial completo de notificaciones</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="marcarTodasLeidas()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                <i class="fas fa-check-double mr-2"></i>
                Marcar todas como leídas
            </button>
            <button onclick="eliminarLeidas()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Eliminar leídas
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        @forelse($notificaciones as $notif)
        <div class="flex items-start p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ !$notif->leida ? 'bg-blue-50' : '' }}">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4 flex-shrink-0
                        {{ $notif->prioridad === 'urgente' ? 'bg-red-100' : '' }}
                        {{ $notif->prioridad === 'alta' ? 'bg-orange-100' : '' }}
                        {{ $notif->prioridad === 'normal' ? 'bg-blue-100' : '' }}
                        {{ $notif->prioridad === 'baja' ? 'bg-gray-100' : '' }}">
                <i class="fas
                    {{ $notif->tipo === 'cuenta_cobro_requiere_correccion' ? 'fa-file-invoice text-red-600' : '' }}
                    {{ $notif->tipo === 'cuenta_cobro_aprobada' ? 'fa-check-circle text-green-600' : '' }}
                    {{ $notif->tipo === 'cuenta_cobro_pagada' ? 'fa-money-bill-wave text-blue-600' : '' }}
                    {{ $notif->tipo === 'cuenta_cobro_radicada' ? 'fa-clock text-orange-600' : '' }}
                    {{ $notif->tipo === 'cuenta_cobro_en_proceso_pago' ? 'fa-credit-card text-teal-600' : '' }}
                    {{ $notif->tipo === 'cuenta_cobro_anulada' ? 'fa-ban text-gray-600' : '' }}
                    {{ !in_array($notif->tipo, ['cuenta_cobro_requiere_correccion', 'cuenta_cobro_aprobada', 'cuenta_cobro_pagada', 'cuenta_cobro_radicada', 'cuenta_cobro_en_proceso_pago', 'cuenta_cobro_anulada']) ? 'fa-file-invoice' : '' }}">
                </i>
            </div>
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $notif->titulo }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $notif->mensaje }}</p>
                        @if($notif->cuenta_cobro_id)
                        <a href="{{ route('cuentas-cobro.show', $notif->cuenta_cobro_id) }}"
                           class="inline-block mt-2 text-xs text-primary hover:text-primary-dark font-medium">
                            <i class="fas fa-arrow-right mr-1"></i>
                            Ver cuenta de cobro
                        </a>
                        @endif
                    </div>
                    <div class="flex flex-col items-end ml-4">
                        <span class="text-xs text-gray-500">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                        @if(!$notif->leida)
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-2"></div>
                        @endif
                    </div>
                </div>
                @if($notif->datos_adicionales)
                <div class="mt-2 text-xs text-gray-500">
                    @if(isset($notif->datos_adicionales['numero_cuenta']))
                    <span class="mr-3">
                        <i class="fas fa-file-invoice mr-1"></i>
                        {{ $notif->datos_adicionales['numero_cuenta'] }}
                    </span>
                    @endif
                    @if(isset($notif->datos_adicionales['numero_contrato']))
                    <span>
                        <i class="fas fa-file-contract mr-1"></i>
                        {{ $notif->datos_adicionales['numero_contrato'] }}
                    </span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No hay notificaciones</h3>
            <p class="text-sm text-gray-500">Cuando recibas notificaciones aparecerán aquí</p>
        </div>
        @endforelse
    </div>

    @if($notificaciones->hasPages())
    <div class="mt-6">
        {{ $notificaciones->links() }}
    </div>
    @endif
</div>

<script>
async function marcarTodasLeidas() {
    try {
        const response = await fetch('{{ route("notificaciones.marcar-todas-leidas") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al marcar como leídas');
    }
}

async function eliminarLeidas() {
    if (!confirm('¿Estás seguro de eliminar todas las notificaciones leídas?')) {
        return;
    }

    try {
        const response = await fetch('{{ route("notificaciones.eliminar-leidas") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar notificaciones');
    }
}
</script>
@endsection
