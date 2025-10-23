@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Cuentas de Cobro</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Cuenta de Cobro
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('invoices.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Buscar por factura o cliente" 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="status">
                        <option value="">Todos los estados</option>
                        <option value="draft" @selected(request('status') == 'draft')>Borrador</option>
                        <option value="sent" @selected(request('status') == 'sent')>Enviada</option>
                        <option value="paid" @selected(request('status') == 'paid')>Pagada</option>
                        <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="from_date" 
                           placeholder="Desde" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">Filtrar</button>
                </div>
                @if (request('search') || request('status') || request('from_date'))
                    <div class="col-md-1">
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if ($invoices->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Vencimiento</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>
                                    <strong>#{{ $invoice->invoice_number }}</strong>
                                </td>
                                <td>{{ $invoice->client->name ?? 'N/A' }}</td>
                                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                <td>
                                    {{ $invoice->due_date->format('d/m/Y') }}
                                    @if ($invoice->due_date < now() && $invoice->status !== 'paid')
                                        <span class="badge bg-danger">Vencida</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>${{ number_format($invoice->amount, 2, ',', '.') }}</strong>
                                </td>
                                <td>
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
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('invoices.show', $invoice->id) }}" 
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.edit', $invoice->id) }}" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $invoice->id }}"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de Confirmación de Eliminación -->
                                    <div class="modal fade" id="deleteModal{{ $invoice->id }}" 
                                         tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close" 
                                                            data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Estás seguro de que deseas eliminar la factura 
                                                    <strong>#{{ $invoice->invoice_number }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" 
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('invoices.destroy', $invoice->id) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $invoices->links() }}
        </div>
    @else
        <div class="alert alert-warning text-center">
            <h5>No hay cuentas de cobro registradas</h5>
            <p class="mb-0">
                <a href="{{ route('invoices.create') }}" class="alert-link">Crear una nueva cuenta de cobro</a>
            </p>
        </div>
    @endif
</div>
@endsection