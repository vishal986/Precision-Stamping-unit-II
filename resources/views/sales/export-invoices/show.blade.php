@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="{{ route('export-invoices.index') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">
            <i class="fa-solid fa-arrow-left"></i> Back to Invoices
        </a>
        <h1 class="page-title" style="margin-top: 0.5rem;">Invoice: {{ $exportInvoice->invoice_no }}</h1>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('export-invoices.edit', $exportInvoice) }}" class="btn btn-secondary">
            <i class="fa-solid fa-edit"></i> Edit Invoice
        </a>
        <a href="{{ route('export-invoices.print', $exportInvoice) }}" target="_blank" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> Print Invoice
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <h4 style="color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; margin-bottom: 0.5rem;">Consignee</h4>
                <div style="font-weight: 600; font-size: 1.1rem;">{{ $exportInvoice->customer->company_name }}</div>
                <div style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">
                    {{ $exportInvoice->customer->address }}<br>
                    {{ $exportInvoice->customer->city }}, {{ $exportInvoice->customer->country }}
                </div>
            </div>
            @if(!empty($exportInvoice->buyer_details) && trim($exportInvoice->buyer_details) !== 'Same as consignee')
            <div>
                <h4 style="color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; margin-bottom: 0.5rem;">Buyer</h4>
                <div style="color: var(--text-secondary); font-size: 0.9rem; white-space: pre-line;">
                    {{ $exportInvoice->buyer_details }}
                </div>
            </div>
            @endif
            <div style="text-align: right;">
                <h4 style="color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; margin-bottom: 0.5rem;">Invoice Details</h4>
                <div style="font-weight: 600;">Date: {{ \Carbon\Carbon::parse($exportInvoice->invoice_date)->format('d M Y') }}</div>
                <div style="color: var(--primary-color); font-weight: 600; margin-top: 0.25rem;">Currency: {{ $exportInvoice->currency }}</div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>HS Code</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exportInvoice->items as $item)
                <tr>
                    <td style="font-weight: 600;">{{ $item->item->name }}</td>
                    <td>{{ $item->hs_code }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right; font-weight: 600;">{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: right; font-size: 1.25rem;">Grand Total:</th>
                    <th style="text-align: right; font-size: 1.25rem; color: var(--primary-color);">
                        {{ $exportInvoice->currency == 'EUR' ? '€' : ($exportInvoice->currency == 'USD' ? '$' : '₹') }} 
                        {{ number_format($exportInvoice->total_amount, 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="card" style="border-left: 4px solid #3b82f6;">
            <h3 style="margin-bottom: 1rem; color: #3b82f6;"><i class="fa-solid fa-ship"></i> Logistics Info</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Incoterms:</span>
                    <span style="font-weight: 600;">{{ $exportInvoice->incoterms }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Vessel:</span>
                    <span style="font-weight: 600;">{{ $exportInvoice->vessel_flight_no }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Port of Discharge:</span>
                    <span style="font-weight: 600;">{{ $exportInvoice->port_of_discharge }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1rem; color: var(--success-color);"><i class="fa-solid fa-bank"></i> Bank Account</h3>
            <pre style="font-family: inherit; font-size: 0.85rem; color: var(--text-secondary); background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 4px; white-space: pre-wrap;">{{ $exportInvoice->bank_details }}</pre>
        </div>
    </div>
</div>
@endsection
