@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ isset($order) ? 'Edit Production Order: ' . $order->order_number : 'Create Production Order' }}</h1>
    <a href="{{ route('production.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <form action="{{ isset($order) ? route('production.update', $order) : route('production.store') }}" method="POST">
        @csrf
        @if(isset($order))
            @method('PUT')
        @endif
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Order Number <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="order_number" class="form-control" required placeholder="e.g. PO-12345" value="{{ old('order_number', $order->order_number ?? '') }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Status <span style="color: var(--danger-color);">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="planned" {{ old('status', $order->status ?? '') == 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="in_progress" {{ old('status', $order->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status', $order->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $order->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label">PO Date <span style="color: var(--danger-color);">*</span></label>
                <input type="date" name="po_date" class="form-control" required value="{{ old('po_date', isset($order) && $order->po_date ? \Carbon\Carbon::parse($order->po_date)->format('Y-m-d') : date('Y-m-d')) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Delivery Week</label>
                <input type="week" name="delivery_week" class="form-control" value="{{ old('delivery_week', $order->delivery_week ?? '') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Client / Customer <span style="color: var(--danger-color);">*</span></label>
                <select name="client_id" id="client_select" class="form-control searchable-select" required onchange="filterItemsByClient(this.value)">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ (isset($order) && $order->client_id == $client->id) ? 'selected' : '' }}>
                            {{ $client->name }} {{ $client->company_name ? '('.$client->company_name.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card" style="margin-bottom: 1.5rem; background: rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem;">Items to Produce</h3>
                <button type="button" class="btn" style="background: var(--primary-color); color: white; padding: 0.25rem 0.75rem;" onclick="addItemRow()">
                    <i class="fa-solid fa-plus"></i> Add Item
                </button>
            </div>
            
            <table class="table" id="itemsTable">
                <thead>
                    <tr>
                        <th>Item <span style="color: var(--danger-color);">*</span></th>
                        <th style="width: 200px;">Planned Qty <span style="color: var(--danger-color);">*</span></th>
                        <th style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    @if(isset($order) && $order->items->count() > 0)
                        @foreach($order->items as $index => $orderItem)
                            <tr>
                                <td>
                                    <select name="items[{{ $index }}][item_id]" class="form-control item-select" required>
                                        <option value="">Select Item</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ $orderItem->item_id == $item->id ? 'selected' : '' }}>{{ $item->item_code }} - {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $index }}][quantity_planned]" class="form-control" required value="{{ $orderItem->quantity_planned }}">
                                </td>
                                <td>
                                    <button type="button" class="btn" style="background: rgba(239,68,68,0.1); color: var(--danger-color); padding: 0.25rem 0.5rem;" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <select name="items[0][item_id]" class="form-control item-select" required>
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][quantity_planned]" class="form-control" required placeholder="0.00" value="">
                            </td>
                            <td>
                                <button type="button" class="btn" style="background: rgba(239,68,68,0.1); color: var(--danger-color); padding: 0.25rem 0.5rem;" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <label class="form-label">Production Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $order->notes ?? '') }}</textarea>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: var(--glass-border); display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('production.index') }}" class="btn" style="background: rgba(255,255,255,0.05); color: var(--text-primary);">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ isset($order) ? 'Update Order' : 'Save Order' }}</button>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ isset($order) ? max($order->items->count(), 1) : 1 }};
    const allItems = @json($items);
    const choiceInstances = {};

    // Initialize Choices.js
    function initChoices(element, id) {
        if (element) {
            const instance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                allowHTML: true
            });
            if (id) choiceInstances[id] = instance;
            return instance;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize client select
        const clientSelect = document.getElementById('client_select');
        new Choices(clientSelect, { searchEnabled: true, itemSelectText: '', allowHTML: true });

        // Initialize existing item selects
        const selects = document.querySelectorAll('.item-select');
        selects.forEach((select, index) => {
            const id = select.id || `item_select_init_${index}`;
            select.id = id;
            initChoices(select, id);
        });

        // If client is already selected (edit mode), filter items
        if (clientSelect.value) {
            filterItemsByClient(clientSelect.value);
        }
    });

    function filterItemsByClient(clientId) {
        const filteredItems = allItems.filter(item => !item.client_id || item.client_id == clientId);
        
        // Update all item selects
        Object.values(choiceInstances).forEach(instance => {
            const currentValue = instance.passedElement.element.value;
            instance.clearChoices();
            instance.setChoices(
                filteredItems.map(item => ({
                    value: item.id,
                    label: `${item.item_code} - ${item.name}`,
                    selected: item.id == currentValue
                })),
                'value',
                'label',
                true
            );
        });
    }

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        const selectId = `item_select_${itemIndex}`;
        const clientId = document.getElementById('client_select').value;
        
        const filteredItems = allItems.filter(item => !item.client_id || item.client_id == clientId);
        
        tr.innerHTML = `
            <td>
                <select name="items[${itemIndex}][item_id]" class="form-control item-select" id="${selectId}" required>
                    <option value="">Select Item</option>
                    ${filteredItems.map(item => `<option value="${item.id}">${item.item_code} - ${item.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][quantity_planned]" class="form-control" required placeholder="0.00" value="">
            </td>
            <td>
                <button type="button" class="btn" style="background: rgba(239,68,68,0.1); color: var(--danger-color); padding: 0.25rem 0.5rem;" onclick="removeItemRow(this, '${selectId}')"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        
        tbody.appendChild(tr);
        
        // Initialize choices on the new select
        setTimeout(() => {
            initChoices(document.getElementById(selectId), selectId);
        }, 10);
        
        itemIndex++;
    }

    function removeItemRow(btn, selectId) {
        btn.closest('tr').remove();
        if (choiceInstances[selectId]) {
            delete choiceInstances[selectId];
        }
    }
</script>
@endsection
