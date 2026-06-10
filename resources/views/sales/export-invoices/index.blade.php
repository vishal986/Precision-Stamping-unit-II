@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Export Invoices (Global)</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Tracking international shipments and multi-currency invoicing.</p>
    </div>
    <a href="{{ route('export-invoices.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> New Export Invoice
    </a>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
        <h3 style="font-size: 1.125rem; margin: 0;">Active Period: <span style="color: var(--primary-color);">FY {{ $activeFyLabel }}</span></h3>
        <form action="{{ route('export-invoices.index') }}" method="GET" style="display: flex; gap: 0.75rem; align-items: center; margin: 0;">
            <label style="font-size: 0.85rem; color: var(--text-secondary); white-space: nowrap; margin: 0;"><i class="fa-solid fa-calendar-days"></i> Financial Year:</label>
            <select name="fy" class="form-control" style="width: auto; padding: 0.35rem 2rem 0.35rem 0.75rem; font-weight: 600;" onchange="this.form.submit()">
                @foreach($financialYears as $val => $label)
                    <option value="{{ $val }}" {{ $selectedFy == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Destination</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td style="font-weight: 600; color: var(--primary-color);">{{ $inv->invoice_no }}</td>
                <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d M Y') }}</td>
                <td>
                    <div style="font-weight: 600;">{{ $inv->customer->company_name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $inv->customer->country ?? 'Germany' }}</div>
                </td>
                <td>
                    <div style="font-size: 0.85rem;"><i class="fa-solid fa-ship"></i> {{ $inv->port_of_discharge }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $inv->incoterms }}</div>
                </td>
                <td style="font-weight: 600;">
                    {{ $inv->currency == 'EUR' ? '€' : ($inv->currency == 'USD' ? '$' : '₹') }} 
                    {{ number_format($inv->total_amount, 2) }}
                </td>
                <td>
                    <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">{{ $inv->status }}</span>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('export-invoices.edit', $inv) }}" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        <a href="{{ route('export-invoices.show', $inv) }}" class="btn btn-sm btn-secondary" title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('export-invoices.print', $inv) }}" target="_blank" class="btn btn-sm" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);" title="Print/Export PDF">
                            <i class="fa-solid fa-file-pdf"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No export invoices found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1rem;">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
