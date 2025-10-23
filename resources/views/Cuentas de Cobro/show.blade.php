@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Cuenta de Cobro #{{ $invoice->invoice_number }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información de la Factura</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Número de Factura:</strong>
                            <p>#{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong>
                            <p>
                                @switch($invoice->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">Borrador</span>
                                        @break
                                    @case('sent')
                                        <span class="badge bg-info">Enviada</span>
                                        @break
                                    @case('paid')
                                        <span class="badge bg-success">Pagada</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-warning">Cancelada</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fecha de Factura:</strong>
                            <p>{{ $invoice->invoice_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Fecha de Vencimiento:</strong>
                            <p>
                                {{ $invoice->due_date->format('d/m/Y') }}
                                @if ($invoice->due_date < now() && $invoice->status !== 'paid')
                                    <span class="badge bg-danger">Vencida</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Descripción:</strong>
                            <p>{{ $invoice->description ?? 'Sin descripción' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Nombre:</strong>
                            <p>{{ $invoice->client->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Email:</strong>
                            <p>
                                @if ($invoice->client->email)
                                    <a href="mailto:{{ $invoice->client->email }}">
                                        {{ $invoice->client->email }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Teléfono:</strong>
                            <p>{{ $invoice->client->phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Documento:</strong>
                            <p>{{ $invoice->client->document ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Dirección:</strong>
                            <p>{{ $invoice->client->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de Montos -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>${{ number_format($invoice->amount, 2, ',', '.') }}</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span>IVA (0%):</span>
                            <strong>$0,00</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="h5">Total:</span>
                            <strong class="h5 text-primary">
                                ${{ number_format($invoice->amount, 2, ',', '.') }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    @if ($invoice->status === 'draft')
                        <form action="{{ route('invoices.status', $invoice->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="sent">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-send"></i> Marcar como Enviada
                            </button>
                        </form>
                    @endif

                    @if ($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                        <form action="{{ route('invoices.status', $invoice->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="paid">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Marcar como Pagada
                            </button>
                        </form>
                    @endif

                    <button class="btn btn-secondary w-100">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>

                    <button class="btn btn-outline-secondary w-100">
                        <i class="bi bi-download"></i> Descargar PDF
                    </button>

                    @if ($invoice->status !== 'cancelled')
                        <button type="button" class="btn btn-danger w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle"></i> Cancelar Factura
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Auditoría -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Creada:</strong> {{ $invoice->created_at->format('d/m/Y H:i:s') }}<br>
                        <strong>Última actualización:</strong> {{ $invoice->updated_at->format('d/m/Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Cancelación -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancelar Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas cancelar esta factura?
                    <br><strong>#{{ $invoice->invoice_number }}</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        No, Mantener
                    </button>
                    <form action="{{ route('invoices.status', $invoice->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger">
                            Sí, Cancelar Factura
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection