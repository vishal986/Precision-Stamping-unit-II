@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Items & Inventory</h1>
    <a href="{{ route('items.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> New Item
    </a>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.125rem;">Inventory Master</h3>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('items.export', request()->query()) }}" class="btn btn-secondary" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color); border: 1px solid rgba(16, 185, 129, 0.2);">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('items.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem; background: rgba(255, 255, 255, 0.02); padding: 1rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05);">
        <!-- General Keyword Search -->
        <div style="flex: 1; min-width: 250px; display: flex; gap: 0.5rem; align-items: center;">
            <label style="font-size: 0.85rem; color: var(--text-secondary); white-space: nowrap; margin: 0;"><i class="fa-solid fa-magnifying-glass"></i> Item Search:</label>
            <input type="text" name="search" class="form-control" placeholder="Search by Article No, Name, LFe, etc..." value="{{ request('search') }}" style="margin: 0; padding: 0.50rem 0.75rem;">
        </div>

        <!-- Party/Client Selector -->
        <div style="width: 300px; display: flex; gap: 0.5rem; align-items: center;">
            <label style="font-size: 0.85rem; color: var(--text-secondary); white-space: nowrap; margin: 0;"><i class="fa-solid fa-user-tie"></i> Party Wise:</label>
            <select name="client_id" class="form-control" style="margin: 0; padding: 0.50rem 0.75rem;" onchange="this.form.submit()">
                <option value="">-- All Parties / Clients --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->company_name ?? $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="submit" class="btn btn-primary" style="padding: 0.50rem 1.25rem;"><i class="fa-solid fa-filter"></i> Filter</button>
            @if(request()->filled('search') || request()->filled('client_id'))
                <a href="{{ route('items.index') }}" class="btn" style="background: rgba(255, 255, 255, 0.05); color: var(--text-primary); text-decoration: none; padding: 0.50rem 1.25rem; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-xmark"></i> Clear</a>
            @endif
        </div>
    </form>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Article No.</th>
                    <th>Name</th>
                    <th>LFe</th>
                    <th>Client</th>
                    <th>Category</th>
                    <th>UOM</th>
                    <th>Unit Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td style="font-weight: 600;">{{ $item->item_code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->lfe ?? '-' }}</td>
                    <td style="font-size: 0.85rem;">{{ $item->client->company_name ?? '-' }}</td>
                    <td>{{ $item->category->name ?? 'Uncategorized' }}</td>
                    <td>{{ $item->uom }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>
                        @if($item->is_active)
                        <span style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">Active</span>
                        @else
                        <span style="background: rgba(239, 68, 68, 0.2); color: var(--danger-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('items.edit', $item) }}" style="color: var(--text-secondary); hover: color: var(--primary-color);"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--text-secondary); cursor: pointer;" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash hover:text-danger"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem;">
                        <div style="color: var(--text-secondary); margin-bottom: 1rem;"><i class="fa-solid fa-box-open fa-3x"></i></div>
                        <h4>No Items Found</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Get started by creating a new inventory item.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
