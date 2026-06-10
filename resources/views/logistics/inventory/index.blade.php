@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Finished Goods (FG) Inventory</h1>
    <p style="color: var(--text-secondary); margin-top: 0.5rem;">Real-time stock of products ready for dispatch.</p>
</div>

<div class="card" style="margin-bottom: 1.5rem; padding: 1rem;">
    <form method="GET" action="{{ route('logistics.inventory') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label for="search_item" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Item Name or Code</label>
            <input type="text" id="search_item" name="search_item" value="{{ request('search_item') }}" placeholder="Search Item" class="form-control">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label for="search_client" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Client Name</label>
            <input type="text" id="search_client" name="search_client" value="{{ request('search_client') }}" placeholder="Search Client" class="form-control">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                <i class="fa-solid fa-search"></i> Search
            </button>
            <a href="{{ route('logistics.inventory') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; margin-left: 0.5rem; background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-color); text-decoration: none;">
                Clear
            </a>
        </div>
    </form>
</div>


<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Article No.</th>
                <th>Item Name</th>
                <th>Client</th>
                <th>Category</th>
                <th>Current FG Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td style="font-weight: 600;">{{ $item->item_code }}</td>
                <td>{{ $item->name }}</td>
                <td style="font-size: 0.85rem; color: var(--text-secondary);">{{ $item->client->company_name ?? '-' }}</td>
                <td>{{ $item->category->name ?? 'N/A' }}</td>
                <td style="font-size: 1.1rem; font-weight: bold; color: var(--primary-color);">
                    {{ number_format($item->current_stock, 0) }} {{ $item->uom }}
                </td>
                <td>
                    @if($item->current_stock > 0)
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">In Stock</span>
                    @else
                        <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-color);">Out of Stock</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('export-invoices.create') }}?item_id={{ $item->id }}" class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-truck-dispatch"></i> Create Invoice
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No Finished Goods found in inventory.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
