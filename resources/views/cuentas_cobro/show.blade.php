@extends('layouts.app-dashboard')

@section('title', 'Detalle Cuenta de Cobro - ARCA-D')

@section('content')
    <div class="flex h-screen bg-bg-main overflow-hidden">
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            @include('partials.header')

            <main class="flex-1 overflow-y-auto">
                <div class="p-6">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-3 mb-2">
                                    <a href="{{ route('cuentas-cobro.index') }}"
                                        class="text-secondary hover:text-primary transition-colors">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-file-invoice-dollar text-primary mr-3"></i>
                                        {{ $cuentaCobro->numero_cuenta_cobro }}
                                    </h1>
                                    <!-- Estado Badge -->
                                    @php
                                        $estadosBadge = [
                                            'borrador' => [
                                                'bg' => 'bg-gray-100',
                                                'text' => 'text-gray-800',
                                                'icon' => 'fa-file',
                                            ],
                                            'radicada' => [
                                                'bg' => 'bg-blue-100',
                                                'text' => 'text-blue-800',
                                                'icon' => 'fa-paper-plane',
                                            ],
                                            'en_correccion_supervisor' => [
                                                'bg' => 'bg-orange-100',
                                                'text' => 'text-orange-800',
                                                'icon' => 'fa-exclamation-triangle',
                                            ],
                                            'certificado_supervisor' => [
                                                'bg' => 'bg-cyan-100',
                                                'text' => 'text-cyan-800',
                                                'icon' => 'fa-check-circle',
                                            ],
                                            'en_correccion_contratacion' => [
                                                'bg' => 'bg-yellow-100',
                                                'text' => 'text-yellow-800',
                                                'icon' => 'fa-exclamation-triangle',
                                            ],
                                            'verificado_contratacion' => [
                                                'bg' => 'bg-teal-100',
                                                'text' => 'text-teal-800',
                                                'icon' => 'fa-stamp',
                                            ],
                                            'verificado_presupuesto' => [
                                                'bg' => 'bg-indigo-100',
                                                'text' => 'text-indigo-800',
                                                'icon' => 'fa-wallet',
                                            ],
                                            'aprobada_ordenador' => [
                                                'bg' => 'bg-green-100',
                                                'text' => 'text-green-800',
                                                'icon' => 'fa-check-double',
                                            ],
                                            'en_proceso_pago' => [
                                                'bg' => 'bg-purple-100',
                                                'text' => 'text-purple-800',
                                                'icon' => 'fa-money-check-alt',
                                            ],
                                            'pagada' => [
                                                'bg' => 'bg-green-600',
                                                'text' => 'text-white',
                                                'icon' => 'fa-check-circle',
                                            ],
                                            'anulada' => [
                                                'bg' => 'bg-red-100',
                                                'text' => 'text-red-800',
                                                'icon' => 'fa-ban',
                                            ],
                                        ];
                                        $badge = $estadosBadge[$cuentaCobro->estado] ?? [
                                            'bg' => 'bg-gray-100',
                                            'text' => 'text-gray-800',
                                            'icon' => 'fa-circle',
                                        ];
                                    @endphp
                                    <span
                                        class="px-4 py-2 rounded-full text-sm font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                        <i class="fas {{ $badge['icon'] }} text-xs mr-1"></i>
                                        {{ $cuentaCobro->estadoNombre }}
                                    </span>
                                </div>
                                <p class="text-secondary ml-9">
                                    Creada el {{ $cuentaCobro->created_at->format('d/m/Y H:i') }} por
                                    {{ $cuentaCobro->creador->nombre }}
                                </p>
                            </div>

                            <!-- Botones de Acción Según Permisos y Estado -->
                            <div class="flex items-center space-x-3 no-print">
                                @php
                                    $user = auth()->user();
                                    $organizacionId = $cuentaCobro->contrato->organizacion_id;
                                @endphp

                                {{-- EDITAR - Solo en borrador --}}
                                @if ($cuentaCobro->estado == 'borrador' && $user->tienePermiso('editar-cuenta-cobro', $organizacionId))
                                    <a href="{{ route('cuentas-cobro.edit', $cuentaCobro->id) }}"
                                        class="bg-gradient-to-r from-accent to-accent text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-edit mr-2"></i>
                                        Editar
                                    </a>
                                @endif

                                {{-- RADICAR - Borrador → Radicada --}}
                                @if ($cuentaCobro->estado == 'borrador' && $user->tienePermiso('radicar-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('radicada', 'Radicar Cuenta de Cobro')"
                                        class="bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Radicar
                                    </button>
                                @endif

                                {{-- CERTIFICAR - Radicada → Certificado Supervisor --}}
                                @if ($cuentaCobro->estado == 'radicada' && $user->tienePermiso('revisar-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('certificado_supervisor', 'Certificar Supervisión')"
                                        class="bg-gradient-to-r from-cyan-600 to-cyan-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Certificar
                                    </button>
                                @endif

                                {{-- DEVOLVER SUPERVISOR - Radicada → En Corrección Supervisor --}}
                                @if ($cuentaCobro->estado == 'radicada' && $user->tienePermiso('rechazar-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="mostrarModalEstado('en_correccion_supervisor', 'Devolver a Contratista', 'orange')"
                                        class="bg-gradient-to-r from-orange-600 to-orange-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-undo mr-2"></i>
                                        Devolver
                                    </button>
                                @endif

                                {{-- VERIFICAR LEGAL - Certificado Supervisor → Verificado Contratación --}}
                                @if (
                                    $cuentaCobro->estado == 'certificado_supervisor' &&
                                        $user->tienePermiso('verificar-legal-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('verificado_contratacion', 'Verificar Documentación Legal')"
                                        class="bg-gradient-to-r from-teal-600 to-teal-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-stamp mr-2"></i>
                                        Verificar Legal
                                    </button>
                                @endif

                                {{-- DEVOLVER CONTRATACIÓN --}}
                                @if ($cuentaCobro->estado == 'certificado_supervisor' && $user->tienePermiso('rechazar-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="mostrarModalEstado('en_correccion_contratacion', 'Devolver por Ajustes Legales', 'yellow')"
                                        class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-undo mr-2"></i>
                                        Devolver
                                    </button>
                                @endif

                                {{-- VERIFICAR PRESUPUESTO - Verificado Contratación → Verificado Presupuesto --}}
                                @if (
                                    $cuentaCobro->estado == 'verificado_contratacion' &&
                                        $user->tienePermiso('verificar-presupuesto-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('verificado_presupuesto', 'Verificar CDP/RP')"
                                        class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-wallet mr-2"></i>
                                        Verificar Presupuesto
                                    </button>
                                @endif

                                {{-- APROBAR FINALMENTE - Verificado Presupuesto → Aprobada Ordenador --}}
                                @if ($cuentaCobro->estado == 'verificado_presupuesto' && $user->tienePermiso('aprobar-finalmente', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('aprobada_ordenador', 'Aprobación Final')"
                                        class="bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-check-double mr-2"></i>
                                        Aprobar
                                    </button>
                                @endif

                                {{-- GENERAR ORDEN DE PAGO - Aprobada Ordenador → En Proceso Pago --}}
                                @if ($cuentaCobro->estado == 'aprobada_ordenador' && $user->tienePermiso('generar-ordenes-pago', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('en_proceso_pago', 'Generar Orden de Pago')"
                                        class="bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                                        Generar O.P.
                                    </button>
                                @endif

                                {{-- PROCESAR PAGO - En Proceso Pago → Pagada --}}
                                @if ($cuentaCobro->estado == 'en_proceso_pago' && $user->tienePermiso('procesar-pago', $organizacionId))
                                    <button type="button"
                                        onclick="mostrarModalEstado('pagada', 'Confirmar Pago Ejecutado', 'green')"
                                        class="bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-money-check-alt mr-2"></i>
                                        Confirmar Pago
                                    </button>
                                @endif

                                <button type="button" onclick="window.print()"
                                    class="bg-white border-2 border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-50 transition-all flex items-center no-print">
                                    <i class="fas fa-print mr-2"></i>
                                    Imprimir
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes de Éxito/Error -->
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 animate-slideIn">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                <p class="text-green-800 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 animate-slideIn">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                                <p class="text-red-800 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Alerta para estados de corrección -->
                    @if (in_array($cuentaCobro->estado, ['en_correccion_supervisor', 'en_correccion_contratacion']))
                        <div class="mb-6 bg-orange-50 border-l-4 border-orange-500 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-orange-500 text-xl mr-3 mt-0.5"></i>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-orange-800 mb-1">Cuenta Devuelta para Correcciones</h3>
                                    <p class="text-sm text-orange-700">
                                        Esta cuenta ha sido devuelta. Revise el historial de cambios para ver los
                                        comentarios y realizar las correcciones necesarias.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Columna Principal -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Información del Contrato -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-file-contract text-primary mr-2"></i>
                                    Información del Contrato
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Número de Contrato</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $cuentaCobro->contrato->numero_contrato }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Contratista</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $cuentaCobro->contrato->contratista->nombre }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Cédula/NIT</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $cuentaCobro->contrato->contratista->cedula_nit }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Supervisor</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $cuentaCobro->contrato->supervisor->nombre ?? 'No asignado' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Valor Total Contrato</p>
                                        <p class="font-semibold text-green-600">
                                            ${{ number_format($cuentaCobro->contrato->valor_total, 0, ',', '.') }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Saldo Disponible</p>
                                        <p class="font-semibold text-blue-600">
                                            ${{ number_format($cuentaCobro->contrato->valor_total - $cuentaCobro->contrato->valor_pagado, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información General de la Cuenta -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    Información General
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Fecha de Radicación</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $cuentaCobro->fecha_radicacion ? $cuentaCobro->fecha_radicacion->format('d/m/Y') : 'No radicada' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Período</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $cuentaCobro->periodo_inicio->format('d/m/Y') }} -
                                            {{ $cuentaCobro->periodo_fin->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    @if ($cuentaCobro->pila_verificada)
                                        <div>
                                            <p class="text-sm text-gray-500 mb-1">PILA Verificada</p>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Verificada
                                            </span>
                                        </div>
                                    @endif

                                    @if ($cuentaCobro->observaciones)
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-500 mb-1">Observaciones</p>
                                            <p class="text-gray-800">{{ $cuentaCobro->observaciones }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Items de la Cuenta de Cobro -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-list-ul text-primary mr-2"></i>
                                    Items de la Cuenta de Cobro
                                </h2>

                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                                    #</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                                    Descripción</th>
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                                    Cantidad</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                                    Valor Unit.</th>
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                                    % Avance</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                                    Valor Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach ($cuentaCobro->items as $index => $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->descripcion }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-center text-gray-800">
                                                        {{ number_format($item->cantidad, 2) }}</td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-800">
                                                        ${{ number_format($item->valor_unitario, 0, ',', '.') }}</td>
                                                    <td class="px-4 py-3 text-sm text-center">
                                                        @if ($item->porcentaje_avance)
                                                            <span
                                                                class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                                                {{ number_format($item->porcentaje_avance, 2) }}%
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                                        ${{ number_format($item->valor_total, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Archivos Adjuntos -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-paperclip text-primary mr-2"></i>
                                    Archivos Adjuntos
                                </h2>

                                @if ($cuentaCobro->archivos->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($cuentaCobro->archivos as $archivo)
                                            <div
                                                class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-accent transition-colors">
                                                <div class="flex items-start space-x-3">
                                                    <!-- Icono -->
                                                    <div
                                                        class="w-12 h-12 rounded bg-accent/10 flex items-center justify-center text-accent flex-shrink-0">
                                                        <i class="fas fa-{{ $archivo->icono }} text-lg"></i>
                                                    </div>

                                                    <!-- Info -->
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-semibold text-sm text-gray-800 truncate"
                                                            title="{{ $archivo->nombre_original }}">
                                                            {{ $archivo->nombre_original }}
                                                        </p>
                                                        <p class="text-xs text-secondary mt-1">
                                                            {{ $archivo->tamaño_formateado }}
                                                        </p>
                                                        <span
                                                            class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-semibold bg-primary/10 text-primary">
                                                            {{ $archivo->tipo_documento_nombre }}
                                                        </span>
                                                        @if ($archivo->descripcion)
                                                            <p class="text-xs text-gray-600 mt-1 truncate">
                                                                {{ $archivo->descripcion }}</p>
                                                        @endif
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            Subido por {{ $archivo->subidoPor->nombre ?? 'N/A' }} •
                                                            {{ $archivo->created_at->format('d/m/Y H:i') }}
                                                        </p>
                                                    </div>

                                                    <!-- Acciones -->
                                                    <div class="flex flex-col space-y-2">
                                                        <a href="{{ route('cuentas-cobro.archivos.descargar', ['cuentaCobro' => $cuentaCobro->id, 'archivo' => $archivo->id]) }}"
                                                            class="text-accent hover:text-primary p-1" title="Descargar">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-secondary">No hay archivos adjuntos</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Columna Lateral -->
                        <div class="space-y-6">
                            <!-- Resumen de Valores -->
                            <div
                                class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 text-white">
                                <h2 class="text-lg font-bold mb-6 flex items-center">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Resumen Financiero
                                </h2>

                                <div class="space-y-4">
                                    <!-- Valor Bruto -->
                                    <div class="bg-white/10 rounded-lg p-3">
                                        <p class="text-white/80 text-sm mb-1">Valor Bruto</p>
                                        <p class="text-xl font-bold">
                                            ${{ number_format($cuentaCobro->valor_bruto, 0, ',', '.') }}</p>
                                    </div>

                                    <!-- Retenciones -->
                                    <div class="bg-white/10 rounded-lg p-3">
                                        <p class="text-white/80 text-sm mb-2">Retenciones</p>
                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span>Retención Fuente:</span>
                                                <span class="font-semibold">
                                                    ${{ number_format($cuentaCobro->retencion_fuente, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Estampilla:</span>
                                                <span class="font-semibold">
                                                    ${{ number_format($cuentaCobro->retencion_estampilla, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="border-t border-white/20 pt-1 mt-1">
                                                <div class="flex justify-between font-semibold">
                                                    <span>Total Retenciones:</span>
                                                    <span>
                                                        ${{ number_format($cuentaCobro->retencion_fuente + $cuentaCobro->retencion_estampilla, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Valor Neto -->
                                    <div class="bg-white/10 rounded-lg p-4">
                                        <p class="text-white/80 text-sm mb-1">Valor Neto a Pagar</p>
                                        <p class="text-3xl font-bold">
                                            ${{ number_format($cuentaCobro->valor_neto, 0, ',', '.') }}</p>
                                    </div>

                                    @if ($cuentaCobro->estado == 'pagada' && $cuentaCobro->fecha_pago_real)
                                        <!-- Información de Pago -->
                                        <div class="bg-white/10 rounded-lg p-3">
                                            <p class="text-white/80 text-sm mb-2">Información de Pago</p>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span>Fecha de Pago:</span>
                                                    <span
                                                        class="font-semibold">{{ $cuentaCobro->fecha_pago_real->format('d/m/Y') }}</span>
                                                </div>
                                                @if ($cuentaCobro->numero_comprobante_pago)
                                                    <div class="flex justify-between">
                                                        <span>Comprobante:</span>
                                                        <span
                                                            class="font-semibold">{{ $cuentaCobro->numero_comprobante_pago }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Historial de Estados -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-history text-primary mr-2"></i>
                                    Historial de Estados
                                </h2>

                                @if ($cuentaCobro->historial->count() > 0)
                                    <div class="space-y-4">
                                        @foreach ($cuentaCobro->historial->sortByDesc('created_at') as $cambio)
                                            <div
                                                class="relative pl-6 pb-4 border-l-2 border-gray-200 last:border-0 last:pb-0">
                                                <div
                                                    class="absolute -left-2 top-0 w-4 h-4 bg-primary rounded-full border-2 border-white">
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-800 text-sm">
                                                        {{ ucfirst(str_replace('_', ' ', $cambio->estado_anterior ?? 'Inicio')) }}
                                                        →
                                                        {{ ucfirst(str_replace('_', ' ', $cambio->estado_nuevo)) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Por {{ $cambio->usuario->nombre }} •
                                                        {{ $cambio->created_at->diffForHumans() }}
                                                    </p>
                                                    @if ($cambio->comentario)
                                                        <p class="text-sm text-gray-600 mt-2 bg-gray-50 p-2 rounded">
                                                            "{{ $cambio->comentario }}"
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-secondary text-center py-4">No hay cambios de estado registrados</p>
                                @endif
                            </div>

                            <!-- Acciones Rápidas -->
                            @if ($cuentaCobro->estado == 'borrador' && $user->tienePermiso('editar-cuenta-cobro', $organizacionId))
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 no-print">
                                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-bolt text-primary mr-2"></i>
                                        Acciones Rápidas
                                    </h2>

                                    <div class="space-y-2">
                                        <form action="{{ route('cuentas-cobro.destroy', $cuentaCobro->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Está seguro de eliminar esta cuenta de cobro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-50 text-red-700 font-semibold py-2 px-4 rounded-lg hover:bg-red-100 transition-all flex items-center justify-center">
                                                <i class="fas fa-trash mr-2"></i>
                                                Eliminar Cuenta de Cobro
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Cambiar Estado -->
    <div id="modalCambiarEstado"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 no-print">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800" id="modalTitulo">Cambiar Estado</h3>
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('cuentas-cobro.cambiar-estado', $cuentaCobro->id) }}" method="POST"
                    id="formCambiarEstado">
                    @csrf

                    <input type="hidden" name="nuevo_estado" id="nuevoEstadoInput">

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Comentario <span class="text-red-500" id="comentarioRequerido">*</span>
                        </label>
                        <textarea name="comentario" id="comentarioTextarea" rows="4"
                            placeholder="Describa el motivo o agregue observaciones..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="button"
                            onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')"
                            class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" id="btnConfirmarEstado"
                            class="flex-1 bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Función para cambio rápido de estado (sin comentario obligatorio)
            function cambiarEstadoRapido(nuevoEstado, titulo) {
                document.getElementById('nuevoEstadoInput').value = nuevoEstado;
                document.getElementById('modalTitulo').textContent = titulo;
                document.getElementById('comentarioTextarea').required = false;
                document.getElementById('comentarioRequerido').style.display = 'none';

                // Cambiar color del botón según el estado
                const btnConfirmar = document.getElementById('btnConfirmarEstado');
                const colores = {
                    'radicada': 'from-blue-600 to-blue-700',
                    'certificado_supervisor': 'from-cyan-600 to-cyan-700',
                    'verificado_contratacion': 'from-teal-600 to-teal-700',
                    'verificado_presupuesto': 'from-indigo-600 to-indigo-700',
                    'aprobada_ordenador': 'from-green-600 to-green-700',
                    'en_proceso_pago': 'from-purple-600 to-purple-700',
                    'pagada': 'from-green-600 to-green-700',
                };

                btnConfirmar.className = 'flex-1 bg-gradient-to-r ' + (colores[nuevoEstado] || 'from-primary to-primary-dark') +
                    ' text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all';

                document.getElementById('modalCambiarEstado').classList.remove('hidden');
            }

            // Función para mostrar modal con comentario obligatorio
            function mostrarModalEstado(nuevoEstado, titulo, color) {
                document.getElementById('nuevoEstadoInput').value = nuevoEstado;
                document.getElementById('modalTitulo').textContent = titulo;
                document.getElementById('comentarioTextarea').required = true;
                document.getElementById('comentarioRequerido').style.display = 'inline';

                // Cambiar color del botón
                const btnConfirmar = document.getElementById('btnConfirmarEstado');
                const colores = {
                    'orange': 'from-orange-600 to-orange-700',
                    'yellow': 'from-yellow-600 to-yellow-700',
                    'green': 'from-green-600 to-green-700',
                    'red': 'from-red-600 to-red-700',
                };

                btnConfirmar.className = 'flex-1 bg-gradient-to-r ' + (colores[color] || 'from-primary to-primary-dark') +
                    ' text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all';

                document.getElementById('modalCambiarEstado').classList.remove('hidden');
            }

            // Cerrar modales con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.getElementById('modalCambiarEstado').classList.add('hidden');
                }
            });
        </script>
    @endpush

    @push('styles')
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }

                body {
                    background: white !important;
                }

                .bg-gradient-to-br,
                .bg-gradient-to-r {
                    background: #1e40af !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .bg-white {
                    background: white !important;
                    border: 1px solid #e5e7eb !important;
                }

                .text-primary {
                    color: #1e40af !important;
                }

                .shadow-sm {
                    box-shadow: none !important;
                }
            }

            .animate-slideIn {
                animation: slideIn 0.5s ease-out;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush
@endsection
