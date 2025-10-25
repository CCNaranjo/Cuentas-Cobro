<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra el listado de todas las facturas con filtros
     */
    public function index(Request $request)
    {
        $query = Invoice::with('client');

        // Filtro por búsqueda (número de factura o nombre de cliente)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        // Filtro por estado
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filtro por fecha desde
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        // Ordenar por fecha de creación descendente y paginar
        $invoices = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear una nueva factura
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('invoices.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     * Guarda una nueva factura en la base de datos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:50|unique:invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ], [
            'client_id.required' => 'Debe seleccionar un cliente.',
            'invoice_number.required' => 'El número de factura es obligatorio.',
            'invoice_number.unique' => 'Este número de factura ya existe.',
            'due_date.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de factura.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.min' => 'El monto debe ser mayor o igual a 0.',
        ]);

        // Establecer estado inicial como borrador
        $validated['status'] = 'draft';

        Invoice::create($validated);

        return redirect()->route('invoices.index')
            ->with('success', 'Cuenta de cobro creada exitosamente.');
    }

    /**
     * Display the specified resource.
     * Muestra los detalles de una factura específica
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('client');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar una factura existente
     */
    public function edit(Invoice $invoice)
    {
        $clients = Client::orderBy('name')->get();
        return view('invoices.edit', compact('invoice', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza una factura existente
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $invoice->id,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:draft,sent,paid,cancelled',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ], [
            'client_id.required' => 'Debe seleccionar un cliente.',
            'invoice_number.required' => 'El número de factura es obligatorio.',
            'invoice_number.unique' => 'Este número de factura ya existe.',
            'due_date.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de factura.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.min' => 'El monto debe ser mayor o igual a 0.',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Cuenta de cobro actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina una factura de la base de datos
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Cuenta de cobro eliminada exitosamente.');
    }

    /**
     * Update the status of the invoice.
     * Actualiza solo el estado de una factura
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,cancelled',
        ]);

        $invoice->update(['status' => $validated['status']]);

        // Mensajes personalizados según el estado
        $statusMessages = [
            'draft' => 'Factura marcada como borrador.',
            'sent' => 'Factura marcada como enviada.',
            'paid' => 'Factura marcada como pagada.',
            'cancelled' => 'Factura cancelada.',
        ];

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', $statusMessages[$validated['status']]);
    }
}