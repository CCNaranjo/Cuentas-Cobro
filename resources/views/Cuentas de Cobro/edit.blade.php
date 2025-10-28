@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h2>Editar Cuenta de Cobro #{{ $invoice->invoice_number }}</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Información del Cliente -->
                        <div class="form-group mb-3">
                            <label for="client_id" class="form-label">Cliente</label>
                            <select class="form-control @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Selecciona un cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            @selected(old('client_id', $invoice->client_id) == $client->id)>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Número de Factura -->
                        <div class="form-group mb-3">
                            <label for="invoice_number" class="form-label">Número de Factura</label>
                            <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                   id="invoice_number" name="invoice_number" 
                                   value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                            @error('invoice_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label">Fecha de Factura</label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                       id="invoice_date" name="invoice_date" 
                                       value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" 
                                       value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                                @error('due_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="draft" @selected(old('status', $invoice->status) == 'draft')>Borrador</option>
                                <option value="sent" @selected(old('status', $invoice->status) == 'sent')>Enviada</option>
                                <option value="paid" @selected(old('status', $invoice->status) == 'paid')>Pagada</option>
                                <option value="cancelled" @selected(old('status', $invoice->status) == 'cancelled')>Cancelada</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $invoice->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Monto -->
                        <div class="form-group mb-3">
                            <label for="amount" class="form-label">Monto Total</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" step="0.01" 
                                   value="{{ old('amount', $invoice->amount) }}" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Información de Auditoría -->
                        <div class="alert alert-info mb-3">
                            <small>
                                <strong>Creada:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}<br>
                                <strong>Última actualización:</strong> {{ $invoice->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>

                        <!-- Botones -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Actualizar Cuenta de Cobro</button>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-info">Ver</a>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection