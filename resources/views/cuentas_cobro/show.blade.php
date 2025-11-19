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
                                @if ($cuentaCobro->estado == 'certificado_supervisor' && $user->tienePermiso('verificar-legal-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('verificado_contratacion', 'Verificar Documentación Legal')"
                                        class="bg-gradient-to-r from-teal-600 to-teal-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-stamp mr-2"></i>
                                        Verificar Legal
                                    </button>
                                @endif

                                {{-- DEVOLVER CONTRATACIÓN - Certificado Supervisor → En Corrección Contratación --}}
                                @if ($cuentaCobro->estado == 'certificado_supervisor' && $user->tienePermiso('rechazar-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="mostrarModalEstado('en_correccion_contratacion', 'Devolver a Supervisor', 'yellow')"
                                        class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-undo mr-2"></i>
                                        Devolver
                                    </button>
                                @endif

                                {{-- VERIFICAR PRESUPUESTO - Verificado Contratación → Verificado Presupuesto --}}
                                @if ($cuentaCobro->estado == 'verificado_contratacion' && $user->tienePermiso('verificar-presupuesto-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('verificado_presupuesto', 'Verificar Presupuesto')"
                                        class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-wallet mr-2"></i>
                                        Verificar Presupuesto
                                    </button>
                                @endif

                                {{-- APROBAR ORDENADOR - Verificado Presupuesto → Aprobada Ordenador --}}
                                @if ($cuentaCobro->estado == 'verificado_presupuesto' && $user->tienePermiso('aprobar-finalmente', $organizacionId))
                                    <button type="button"
                                        onclick="cambiarEstadoRapido('aprobada_ordenador', 'Aprobar Cuenta')"
                                        class="bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-check-double mr-2"></i>
                                        Aprobar
                                    </button>
                                @endif

                                {{-- GENERAR ORDEN DE PAGO - Aprobada Ordenador → En Proceso Pago --}}
                                @if($cuentaCobro->estado == 'aprobada_ordenador' && $user->tienePermiso('generar-ordenes-pago', $organizacionId))
                                    <a href="{{ route('pagos.op.create', ['cuenta_id' => $cuentaCobro->id]) }}" 
                                    class="bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                                        Generar O.P.
                                    </a>
                                @endif

                                {{-- PROCESAR PAGO - En Proceso Pago → Pagada --}}
                                @if($cuentaCobro->estado == 'en_proceso_pago' && $user->tienePermiso('procesar-pago', $organizacionId))
                                    <a href="{{ route('pagos.op.index') }}" 
                                    class="bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-money-check-alt mr-2"></i>
                                        Procesar Pago
                                    </a>
                                @endif

                                {{-- ANULAR - Cualquier estado except pagada --}}
                                @if ($cuentaCobro->estado != 'pagada' && $user->tienePermiso('anular-cuenta-cobro', $organizacionId))
                                    <button type="button"
                                        onclick="mostrarModalEstado('anulada', 'Anular Cuenta de Cobro', 'red')"
                                        class="bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center">
                                        <i class="fas fa-ban mr-2"></i>
                                        Anular
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Grid Principal -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Información General y Items - Ocupa 2/3 -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Información General -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slideIn">
                                    <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                                        <i class="fas fa-info-circle text-primary mr-2"></i>
                                        Información General
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Contrato
                                            </label>
                                            <p class="text-gray-900 font-medium">
                                                {{ $cuentaCobro->contrato->numero_contrato }}
                                            </p>
                                            <p class="text-sm text-secondary">
                                                Contratista: {{ $cuentaCobro->contrato->contratista->nombre }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Supervisor
                                            </label>
                                            <p class="text-gray-900 font-medium">
                                                {{ $cuentaCobro->contrato->supervisor->nombre ?? 'No asignado' }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Fecha de Radicación
                                            </label>
                                            <p class="text-gray-900 font-medium">
                                                {{ $cuentaCobro->fecha_radicacion->format('d/m/Y') }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Período
                                            </label>
                                            <p class="text-gray-900 font-medium">
                                                {{ $cuentaCobro->periodo_inicio->format('d/m/Y') }} -
                                                {{ $cuentaCobro->periodo_fin->format('d/m/Y') }}
                                            </p>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Observaciones
                                            </label>
                                            <p class="text-gray-900">
                                                {{ $cuentaCobro->observaciones ?? 'Sin observaciones' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Items de la Cuenta -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slideIn">
                                    <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                                        <i class="fas fa-list-ul text-primary mr-2"></i>
                                        Items de la Cuenta
                                    </h2>

                                    @if ($cuentaCobro->items->isNotEmpty())
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Descripción
                                                        </th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Cantidad
                                                        </th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Valor Unitario
                                                        </th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            % Avance
                                                        </th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Subtotal
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach ($cuentaCobro->items as $item)
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $item->descripcion }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ number_format($item->cantidad, 2) }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                ${{ number_format($item->valor_unitario, 0) }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $item->porcentaje_avance ?? 0 }}%
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                                                ${{ number_format($item->cantidad * $item->valor_unitario, 0) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                            <p class="text-secondary">No hay items registrados</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Documentos Adjuntos -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slideIn">
                                    <div class="flex items-center justify-between mb-6">
                                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-paperclip text-primary mr-2"></i>
                                            Documentos Adjuntos
                                        </h2>
                                        @if (in_array($cuentaCobro->estado, ['borrador', 'en_correccion_supervisor', 'en_correccion_contratacion']))
                                            <button type="button"
                                                    onclick="document.getElementById('modalSubirArchivo').classList.remove('hidden')"
                                                    class="bg-gradient-to-r from-accent to-accent text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all flex items-center text-sm no-print">
                                                <i class="fas fa-upload mr-2"></i>
                                                Agregar Documento
                                            </button>
                                        @endif
                                    </div>

                                    @if ($cuentaCobro->archivos->isNotEmpty())
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach ($cuentaCobro->archivos as $archivo)
                                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-3">
                                                            <i class="fas fa-file-alt text-blue-500 text-xl"></i>
                                                            <div>
                                                                <p class="font-medium text-gray-800">{{ $archivo->nombre_original }}</p>
                                                                <p class="text-sm text-secondary">{{ strtoupper($archivo->tipo_archivo) }} - {{ number_format($archivo->tamaño / 1024, 2) }} KB</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex space-x-2 no-print">
                                                            <a href="{{ route('cuentas-cobro.archivos.descargar', [$cuentaCobro->id, $archivo->id]) }}"
                                                               class="text-blue-600 hover:text-blue-800">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            @if (in_array($cuentaCobro->estado, ['borrador', 'en_correccion_supervisor', 'en_correccion_contratacion']))
                                                                <form action="{{ route('cuentas-cobro.archivos.eliminar', [$cuentaCobro->id, $archivo->id]) }}" method="POST"
                                                                      onsubmit="return confirm('¿Está seguro de eliminar este archivo?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <p class="text-sm text-gray-600">{{ $archivo->descripcion ?? 'Sin descripción' }}</p>
                                                    <p class="text-xs text-secondary mt-2">
                                                        Subido por {{ $archivo->subidoPor->nombre }} el {{ $archivo->created_at->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                                            <p class="text-secondary">No hay documentos adjuntos</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Columna Derecha: Resumen Financiero, Historial y Acciones -->
                            <div class="lg:col-span-1 space-y-6">
                                <!-- Resumen Financiero -->
                                <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-sm p-6 text-white animate-slideIn">
                                    <h2 class="text-lg font-bold mb-6 flex items-center">
                                        <i class="fas fa-calculator mr-2"></i>
                                        Resumen Financiero
                                    </h2>

                                    <div class="space-y-4 text-sm">
                                        <div class="flex justify-between">
                                            <span>Valor Bruto:</span>
                                            <span>${{ number_format($cuentaCobro->valor_bruto, 0) }}</span>
                                        </div>

                                        <div class="border-t border-white/20 pt-2">
                                            <div class="flex justify-between">
                                                <span>Retención Fuente:</span>
                                                <span>${{ number_format($cuentaCobro->retencion_fuente, 0) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Estampilla:</span>
                                                <span>${{ number_format($cuentaCobro->estampilla, 0) }}</span>
                                            </div>
                                        </div>

                                        <div class="border-t border-white/20 pt-2">
                                            <div class="flex justify-between font-semibold">
                                                <span>Total Retenciones:</span>
                                                <span>${{ number_format($cuentaCobro->retencion_fuente + $cuentaCobro->estampilla, 0) }}</span>
                                            </div>
                                        </div>

                                        <div class="border-t border-white/20 pt-2">
                                            <div class="flex justify-between text-lg font-bold">
                                                <span>Valor Neto:</span>
                                                <span>${{ number_format($cuentaCobro->valor_neto, 0) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Historial de Cambios -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slideIn">
                                    <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                                        <i class="fas fa-history text-primary mr-2"></i>
                                        Historial de Cambios
                                    </h2>

                                    @if ($cuentaCobro->historial->isNotEmpty())
                                        <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                                            @foreach ($cuentaCobro->historial->sortByDesc('created_at') as $cambio)
                                                <div class="border-l-4 {{ $cambio->estado_nuevo == 'pagada' ? 'border-green-500' : 'border-blue-500' }} pl-4 pb-4">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <p class="font-medium text-gray-800">
                                                            {{ $cambio->usuario->nombre }} cambió a <span class="font-bold">{{ $cambio->estadoNombreNuevo }}</span>
                                                        </p>
                                                        <p class="text-xs text-secondary">
                                                            {{ $cambio->created_at->format('d/m/Y H:i') }}
                                                        </p>
                                                    </div>
                                                    @if ($cambio->comentario)
                                                        <p class="text-sm text-gray-600">
                                                            "{{ $cambio->comentario }}"
                                                        </p>
                                                    @endif
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

        <!-- Modal Subir Archivo -->
        <div id="modalSubirArchivo"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 no-print">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800" id="modalTitulo">Subir Archivo</h3>
                        <button type="button" onclick="document.getElementById('modalSubirArchivo').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('cuentas-cobro.archivos.subir', $cuentaCobro->id) }}" method="POST"
                        id="formSubirArchivo" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Documento <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo_documento" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Seleccione un tipo</option>
                                <option value="cuenta_cobro">Cuenta de Cobro</option>
                                <option value="acta_recibido">Acta de Recibido</option>
                                <option value="informe">Informe</option>
                                <option value="foto_evidencia">Foto Evidencia</option>
                                <option value="planilla">Planilla</option>
                                <option value="soporte_pago">Soporte de Pago</option>
                                <option value="factura">Factura</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Archivo <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="archivo" required
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <p class="text-xs text-gray-500 mt-1">Máximo 10MB</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción
                            </label>
                            <input type="text" name="descripcion"
                                placeholder="Descripción opcional..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="flex items-center space-x-3">
                            <button type="button"
                                    onclick="document.getElementById('modalSubirArchivo').classList.add('hidden')"
                                    class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-2 px-4 rounded-lg hover:shadow-lg transition-all">
                                Subir Archivo
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
                        document.getElementById('modalSubirArchivo').classList.add('hidden');
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