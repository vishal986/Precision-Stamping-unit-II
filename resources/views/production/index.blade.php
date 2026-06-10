@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Production Orders</h1>
    <a href="{{ route('production.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> New Order
    </a>
</div>

<div class="card" style="margin-bottom: 1.5rem; padding: 1rem;">
    <form method="GET" action="{{ route('production.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 150px;">
            <label for="search_po" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">PO Number</label>
            <input type="text" id="search_po" name="search_po" value="{{ request('search_po') }}" placeholder="Search PO" class="form-control">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label for="search_client" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Client</label>
            <input type="text" id="search_client" name="search_client" value="{{ request('search_client') }}" placeholder="Search Client" class="form-control">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label for="search_item" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Item to Produce</label>
            <input type="text" id="search_item" name="search_item" value="{{ request('search_item') }}" placeholder="Search Item" class="form-control">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label for="search_po_date" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">PO Date</label>
            <input type="date" id="search_po_date" name="search_po_date" value="{{ request('search_po_date') }}" class="form-control">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label for="search_delivery_week" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Delivery Week</label>
            <input type="text" id="search_delivery_week" name="search_delivery_week" value="{{ request('search_delivery_week') }}" placeholder="Search Week" class="form-control">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                <i class="fa-solid fa-search"></i> Search
            </button>
            <a href="{{ route('production.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; margin-left: 0.5rem; background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-color); text-decoration: none;">
                Clear
            </a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Client</th>
                    <th>Item to Produce</th>
                    <th>Status</th>
                    <th>PO Date</th>
                    <th>Delivery Week</th>
                    <th>Qty Planned</th>
                    <th>Qty Produced</th>
                    <th>Start Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="font-weight: 600;">{{ $order->order_number }}</td>
                    <td>
                        @if($order->client)
                            {{ $order->client->name }} {{ $order->client->company_name ? '('.$order->client->company_name.')' : '' }}
                        @else
                            <span style="color: var(--text-secondary);">-</span>
                        @endif
                    </td>
                    <td>
                        @if($order->items->count() == 1)
                            {{ $order->items->first()->item->name ?? 'Unknown Item' }}
                        @elseif($order->items->count() > 1)
                            <span style="color: var(--primary-color);">{{ $order->items->count() }} Items</span>
                        @else
                            No Items
                        @endif
                    </td>
                    <td>
                        @if($order->status == 'planned')
                            <span style="background: rgba(59, 130, 246, 0.2); color: var(--primary-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">Planned</span>
                        @elseif($order->status == 'in_progress')
                            <span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">In Progress</span>
                        @elseif($order->status == 'completed')
                            <span style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">Completed</span>
                        @else
                            <span style="background: rgba(239, 68, 68, 0.2); color: var(--danger-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">Cancelled</span>
                        @endif
                    </td>
                    <td>{{ $order->po_date ? \Carbon\Carbon::parse($order->po_date)->format('d M, Y') : '-' }}</td>
                    <td>{{ $order->delivery_week ?? '-' }}</td>
                    <td>{{ number_format($order->items->sum('quantity_planned'), 2) }}</td>
                    <td>{{ number_format($order->items->sum('quantity_produced'), 2) }}</td>
                    <td>{{ $order->start_date ?? '-' }}</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('production.show', $order) }}" style="color: var(--primary-color);"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('production.edit', $order) }}" style="color: var(--text-secondary);"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('production.destroy', $order) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--text-secondary); cursor: pointer;" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem;">
                        <div style="color: var(--text-secondary); margin-bottom: 1rem;"><i class="fa-solid fa-industry fa-3x"></i></div>
                        <h4>No Production Orders</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Create a production order to start manufacturing.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
