@extends('layouts.app-dashboard')

@section('title', 'Nuevo CuentadeCobro - ARCA-D')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h2>Crear Nueva Cuenta de Cobro</h2>
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

                    <form action="{{ route('invoices.store') }}" method="POST">
                        @csrf

                        <!-- Información del Cliente -->
                        <div class="form-group mb-3">
                            <label for="client_id" class="form-label">Cliente</label>
                            <select class="form-control @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Selecciona un cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            @selected(old('client_id') == $client->id)>
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
                                   value="{{ old('invoice_number') }}" required>
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
                                       value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" 
                                       value="{{ old('due_date') }}" required>
                                @error('due_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Monto -->
                        <div class="form-group mb-3">
                            <label for="amount" class="form-label">Monto Total</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" step="0.01" 
                                   value="{{ old('amount') }}" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Guardar Cuenta de Cobro</button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection