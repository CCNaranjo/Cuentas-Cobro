@extends('layouts.app-dashboard')

@section('title', 'Detalle del Contrato')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb y Acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #004AAD;">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('contratos.index') }}" style="color: #004AAD;">Contratos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $contrato->numero_contrato }}</li>
                </ol>
            </nav>
            <h2 class="mb-0" style="color: #004AAD; font-weight: 600;">
                <i class="bi bi-file-earmark-text me-2"></i>
                Contrato {{ $contrato->numero_contrato }}
            </h2>
        </div>
        <div class="d-flex gap-2">
            @if($contrato->estado == 'borrador' && Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
            @endif
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Badge de Estado -->
    <div class="mb-4">
        @php
            $estadoClasses = [
                'borrador' => 'bg-secondary',
                'activo' => 'bg-success',
                'suspendido' => 'bg-warning text-dark',
                'terminado' => 'bg-info text-dark',
                'liquidado' => 'bg-dark'
            ];
            $estadoTextos = [
                'borrador' => 'Borrador',
                'activo' => 'Activo',
                'suspendido' => 'Suspendido',
                'terminado' => 'Terminado',
                'liquidado' => 'Liquidado'
            ];
        @endphp
        <span class="badge {{ $estadoClasses[$contrato->estado] ?? 'bg-secondary' }} fs-6 px-3 py-2">
            <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
            {{ $estadoTextos[$contrato->estado] ?? ucfirst($contrato->estado) }}
        </span>
    </div>

    <div class="row g-4">
        <!-- Columna Principal (70%) -->
        <div class="col-lg-8">
            <!-- Información General -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #004AAD; font-weight: 600;">
                        <i class="bi bi-info-circle me-2"></i>Información General
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Número de Contrato</label>
                            <p class="mb-0 fw-semibold" style="color: #004AAD;">{{ $contrato->numero_contrato }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Organización</label>
                            <p class="mb-0 fw-semibold">{{ $contrato->organizacion->nombre }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small mb-1">Objeto Contractual</label>
                            <p class="mb-0" style="color: #212529; line-height: 1.6;">{{ $contrato->objeto_contractual }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Financiera -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #004AAD; font-weight: 600;">
                        <i class="bi bi-currency-dollar me-2"></i>Información Financiera
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-3 text-center" style="background: linear-gradient(135deg, #004AAD 0%, #0066CC 100%); border-radius: 8px; color: white;">
                                <div class="small mb-1" style="opacity: 0.9;">Valor Total</div>
                                <div class="fs-4 fw-bold">${{ number_format($contrato->valor_total, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 text-center" style="background: linear-gradient(135deg, #00BCD4 0%, #00D9F5 100%); border-radius: 8px; color: white;">
                                <div class="small mb-1" style="opacity: 0.9;">Valor Cobrado</div>
                                <div class="fs-4 fw-bold">${{ number_format($estadisticas['valor_cobrado'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 text-center" style="background: linear-gradient(135deg, #28a745 0%, #34ce57 100%); border-radius: 8px; color: white;">
                                <div class="small mb-1" style="opacity: 0.9;">Valor Disponible</div>
                                <div class="fs-4 fw-bold">${{ number_format($estadisticas['valor_disponible'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de Progreso de Ejecución -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label text-muted small mb-0">Ejecución Financiera</label>
                            <span class="badge" style="background-color: #00BCD4;">{{ number_format($estadisticas['porcentaje_ejecucion'], 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: '{{ $estadisticas['porcentaje_ejecucion'] }}%; background: linear-gradient(90deg, #00BCD4 0%, #004AAD 100%);" 
                                 aria-valuenow="{{ $estadisticas['porcentaje_ejecucion'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Retenciones -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between p-3" style="background-color: #F8F9FA; border-radius: 8px ; border-left:3px solid #FD7E14;">
                                <span class="text-muted">Retención en la Fuente</span>
                                <span class="fw-semibold" style="color: #FD7E14;">{{ $contrato->porcentaje_retencion_fuente }}%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between p-3" style="background-color: #F8F9FA; border-radius: 8px; border-left: 3px solid #DC3545;">
                                <span class="text-muted">Estampilla</span>
                                <span class="fw-semibold" style="color: #DC3545;">{{ $contrato->porcentaje_estampilla }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personas Involucradas -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #004AAD; font-weight: 600;">
                        <i class="bi bi-people me-2"></i>Personas Involucradas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Contratista -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3" style="background-color: #F8F9FA; border-radius: 8px;">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #004AAD 0%, #00BCD4 100%); color: white;">
                                        <i class="bi bi-person-badge fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted mb-1">Contratista</div>
                                    @if($contrato->contratista)
                                        <div class="fw-semibold mb-1" style="color: #004AAD;">{{ $contrato->contratista->nombre }}</div>
                                        <div class="small text-muted">{{ $contrato->contratista->email }}</div>
                                        <div class="small text-muted">{{ $contrato->contratista->documento_identidad }}</div>
                                    @else
                                        <div class="text-muted fst-italic">Sin asignar</div>
                                        @if(Auth::user()->tienePermiso('vincular-contratista', $contrato->organizacion_id))
                                            <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#vincularContratistaModal">
                                                <i class="bi bi-plus-circle me-1"></i>Vincular Contratista
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Supervisor -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3" style="background-color: #F8F9FA; border-radius: 8px;">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #6C757D 0%, #004AAD 100%); color: white;">
                                        <i class="bi bi-eye fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted mb-1">Supervisor</div>
                                    @if($contrato->supervisor)
                                        <div class="fw-semibold mb-1" style="color: #004AAD;">{{ $contrato->supervisor->nombre }}</div>
                                        <div class="small text-muted">{{ $contrato->supervisor->email }}</div>
                                        @if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
                                            <button class="btn btn-sm btn-outline-secondary mt-2" data-bs-toggle="modal" data-bs-target="#cambiarSupervisorModal">
                                                <i class="bi bi-arrow-repeat me-1"></i>Cambiar
                                            </button>
                                        @endif
                                    @else
                                        <div class="text-muted fst-italic">Sin asignar</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral (30%) -->
        <div class="col-lg-4">
            <!-- Fechas del Contrato -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #004AAD; font-weight: 600;">
                        <i class="bi bi-calendar-range me-2"></i>Vigencia
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Fecha de Inicio</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check me-2" style="color: #00BCD4;"></i>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Fecha de Fin</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-x me-2" style="color: #FD7E14;"></i>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <hr class="my-3" style="border-color: #E9ECEF;">
                    <div>
                        <label class="form-label text-muted small mb-1">Duración</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hourglass-split me-2" style="color: #004AAD;"></i>
                            <span class="fw-semibold">
                                @php
                                    $dias = \Carbon\Carbon::parse($contrato->fecha_inicio)->diffInDays($contrato->fecha_fin);
                                    $meses = floor($dias / 30);
                                    $diasRestantes = $dias % 30;
                                @endphp
                                {{ $meses > 0 ? $meses . ' ' . ($meses == 1 ? 'mes' : 'meses') : '' }}
                                {{ $diasRestantes > 0 ? $diasRestantes . ' ' . ($diasRestantes == 1 ? 'día' : 'días') : '' }}
                            </span>
                        </div>
                    </div>

                    @php
                        $hoy = \Carbon\Carbon::now();
                        $fechaFin = \Carbon\Carbon::parse($contrato->fecha_fin);
                        $diasRestantes = $hoy->diffInDays($fechaFin, false);
                    @endphp

                    @if($contrato->estado == 'activo' && $diasRestantes >= 0)
                        <div class="alert alert-warning mt-3 mb-0" style="border-radius: 8px; border-left: 4px solid #FD7E14;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <strong>{{ $diasRestantes }} días restantes</strong>
                                    <div class="small">Hasta la finalización del contrato</div>
                                </div>
                            </div>
                        </div>
                    @elseif($contrato->estado == 'activo' && $diasRestantes < 0)
                        <div class="alert alert-danger mt-3 mb-0" style="border-radius: 8px; border-left: 4px solid #DC3545;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                <div>
                                    <strong>Contrato vencido</strong>
                                    <div class="small">Hace {{ abs($diasRestantes) }} días</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            @if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #004AAD; font-weight: 600;">
                        <i class="bi bi-lightning me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        @if($contrato->estado == 'activo')
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal" data-estado="suspendido">
                                <i class="bi bi-pause-circle me-2"></i>Suspender Contrato
                            </button>
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal" data-estado="terminado">
                                <i class="bi bi-check-circle me-2"></i>Terminar Contrato
                            </button>
                        @elseif($contrato->estado == 'suspendido')
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal" data-estado="activo">
                                <i class="bi bi-play-circle me-2"></i>Reactivar Contrato
                            </button>
                        @elseif($contrato->estado == 'terminado')
                            <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal" data-estado="liquidado">
                                <i class="bi bi-file-earmark-check me-2"></i>Liquidar Contrato
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Información de Auditoría -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #F8F9FA;">
                <div class="card-body p-3">
                    <h6 class="mb-3" style="color: #6C757D; font-size: 0.875rem; font-weight: 600;">
                        <i class="bi bi-clock-history me-1"></i>Información de Registro
                    </h6>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <strong>Creado:</strong> {{ $contrato->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="mb-2">
                            <strong>Actualizado:</strong> {{ $contrato->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @if($contrato->vinculadoPor)
                            <div>
                                <strong>Vinculado por:</strong> {{ $contrato->vinculadoPor->nombre }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Vincular Contratista -->
@if(!$contrato->contratista && Auth::user()->tienePermiso('vincular-contratista', $contrato->organizacion_id))
<div class="modal fade" id="vincularContratistaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #004AAD; font-weight: 600;">
                    <i class="bi bi-person-plus me-2"></i>Vincular Contratista
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('contratos.vincular-contratista', $contrato) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Buscar Usuario</label>
                        <input type="text" class="form-control" id="buscarContratista" placeholder="Nombre, email o documento...">
                        <div id="resultadosBusqueda" class="mt-2"></div>
                    </div>
                    <input type="hidden" name="contratista_id" id="contratistaSeleccionado">
                    <div id="contratistaInfo" class="alert alert-info d-none" style="border-radius: 8px;">
                        <strong>Usuario seleccionado:</strong>
                        <div id="contratistaDetalles"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #004AAD; border-color: #004AAD;" id="btnVincular" disabled>
                        <i class="bi bi-check-circle me-1"></i>Vincular
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal: Cambiar Supervisor -->
@if($contrato->supervisor && Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
<div class="modal fade" id="cambiarSupervisorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #004AAD; font-weight: 600;">
                    <i class="bi bi-arrow-repeat me-2"></i>Cambiar Supervisor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('contratos.cambiar-supervisor', $contrato) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="alert alert-info" style="border-radius: 8px;">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Supervisor actual:</strong> {{ $contrato->supervisor->nombre }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nuevo Supervisor <span class="text-danger">*</span></label>
                        <select name="supervisor_id" class="form-select" required>
                            <option value="">Seleccione un supervisor</option>
                            <!-- Aquí cargarías los supervisores disponibles desde el controlador -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #004AAD; border-color: #004AAD;">
                        <i class="bi bi-check-circle me-1"></i>Cambiar Supervisor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal: Cambiar Estado -->
@if(Auth::user()->tienePermiso('editar-contrato', $contrato->organizacion_id))
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #004AAD; font-weight: 600;">
                    <i class="bi bi-arrow-repeat me-2"></i>Cambiar Estado del Contrato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('contratos.cambiar-estado', $contrato) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <input type="hidden" name="estado" id="nuevoEstado">
                    <div class="alert alert-warning" style="border-radius: 8px;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ¿Está seguro de cambiar el estado del contrato?
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Ingrese las observaciones..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #004AAD; border-color: #004AAD;">
                        <i class="bi bi-check-circle me-1"></i>Confirmar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // Búsqueda de contratista
    document.getElementById('buscarContratista')?.addEventListener('input', function(e) {
        const query = e.target.value;
        if (query.length < 3) {
            document.getElementById('resultadosBusqueda').innerHTML = '';
            return;
        }

        fetch(`/contratos/buscar-contratista?q=${query}`)
            .then(response => response.json())
            .then(data => {
                let html = '<div class="list-group">';
                data.forEach(usuario => {
                    html += `
                        <button type="button" class="list-group-item list-group-item-action" onclick="seleccionarContratista(${usuario.id}, '${usuario.nombre}', '${usuario.email}', '${usuario.documento_identidad}')">
                            <strong>${usuario.nombre}</strong><br>
                            <small class="text-muted">${usuario.email} - ${usuario.documento_identidad}</small>
                        </button>
                    `;
                });
                html += '</div>';
                document.getElementById('resultadosBusqueda').innerHTML = html;
            });
    });

    function seleccionarContratista(id, nombre, email, documento) {
        document.getElementById('contratistaSeleccionado').value = id;
        document.getElementById('contratistaDetalles').innerHTML = `<strong>${nombre}</strong><br><small>${email} - ${documento}</small>`;
        document.getElementById('contratistaInfo').classList.remove('d-none');
        document.getElementById('btnVincular').disabled = false;
        document.getElementById('resultadosBusqueda').innerHTML = '';
        document.getElementById('buscarContratista').value = nombre;
    }

    // Cambiar estado
    const cambiarEstadoModal = document.getElementById('cambiarEstadoModal');
    if (cambiarEstadoModal) {
        cambiarEstadoModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const estado = button.getAttribute('data-estado');
            document.getElementById('nuevoEstado').value = estado;
        });
    }
</script>
@endpush