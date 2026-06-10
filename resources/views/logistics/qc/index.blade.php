@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Quality Control (QC) Inspection</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Verify production output before moving to Finished Goods stock.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Recent QC Logs -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Pending Inspection</h3>
        <ul style="list-style: none; padding: 0;">
            @forelse($orders as $order)
            @php
                $produced = $order->items->sum('quantity_produced');
                $checked = \App\Models\QualityLog::where('production_order_id', $order->id)->sum(\DB::raw('ok_qty + rejected_qty'));
                $remaining = $produced - $checked;
            @endphp
            <li style="background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid var(--primary-color);">
                <div style="font-weight: 600;">{{ $order->order_number }}</div>
                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                    @foreach($order->items as $oi)
                        {{ $oi->item->name }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary);">Total Produced: <strong>{{ number_format($produced, 0) }}</strong></div>
                <div style="font-size: 0.85rem; color: var(--success-color); font-weight: bold;">Remaining to Check: {{ number_format($remaining, 0) }}</div>
                
                <button class="btn btn-sm" style="margin-top: 1rem; width: 100%; background: var(--primary-color);" 
                    onclick='setupQCModal({{ $order->id }}, "{{ $order->order_number }}", @json($order->items))'>
                    Inspect Items
                </button>
            </li>
            @empty
            <p style="color: var(--text-secondary);">No orders pending for QC.</p>
            @endforelse
        </ul>
    </div>

    <!-- QC History -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--success-color);">QC History & Logs</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Order No</th>
                    <th>Item</th>
                    <th>OK Qty</th>
                    <th>Rejected</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($qualityLogs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M Y') }}</td>
                    <td style="font-weight: 600;">{{ $log->productionOrder->order_number }}</td>
                    <td>{{ $log->productionOrderItem->item->name ?? 'Deleted Item' }}</td>
                    <td style="color: var(--success-color); font-weight: bold;">{{ number_format($log->ok_qty, 0) }}</td>
                    <td style="color: var(--danger-color);">{{ number_format($log->rejected_qty, 0) }}</td>
                    <td><span class="badge" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">Passed</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No QC history found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 1rem;">
            {{ $qualityLogs->links() }}
        </div>
    </div>
</div>

<!-- QC Entry Modal -->
<div id="qcModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7);">
    <div style="background: #1e1e2d; margin: 5% auto; padding: 2rem; border-radius: 1rem; width: 500px; box-shadow: var(--card-shadow); border: 1px solid rgba(255,255,255,0.1);">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Quality Inspection</h2>
        <form action="{{ route('logistics.qc.store') }}" method="POST">
            @csrf
            <input type="hidden" name="production_order_id" id="modal_order_id">
            
            <div class="form-group">
                <label class="form-label">Order Reference</label>
                <input type="text" id="modal_order_no" class="form-control" readonly style="background: rgba(255,255,255,0.05);">
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Select Item to Inspect <span style="color: var(--danger-color);">*</span></label>
                <select name="production_order_item_id" id="modal_item_select" class="form-control" required onchange="updateItemDetails()">
                    <!-- Options populated via JS -->
                </select>
            </div>

            <div id="item_detail_section" style="display: none; background: rgba(59, 130, 246, 0.05); padding: 1rem; border-radius: 8px; margin-top: 1rem; border: 1px dashed rgba(59, 130, 246, 0.3);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: var(--text-secondary); font-size: 0.85rem;">Produced Qty:</span>
                    <span id="modal_produced_qty" style="font-weight: 600;">0</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary); font-size: 0.85rem;">Remaining to Check:</span>
                    <span id="modal_remaining_qty" style="color: var(--primary-color); font-weight: bold;">0</span>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" style="color: var(--success-color);">OK Quantity</label>
                    <input type="number" step="1" name="ok_qty" id="modal_ok_input" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" style="color: var(--danger-color);">Rejected Qty</label>
                    <input type="number" step="1" name="rejected_qty" id="modal_rej_input" class="form-control" value="0">
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Remarks / Observation</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Any defects or notes?"></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn" style="flex: 1; background: rgba(255,255,255,0.1);" onclick="closeModal('qcModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Approve & Log</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentOrderItems = [];

    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function setupQCModal(orderId, orderNo, items) {
        document.getElementById('modal_order_id').value = orderId;
        document.getElementById('modal_order_no').value = orderNo;
        currentOrderItems = items;

        const select = document.getElementById('modal_item_select');
        select.innerHTML = '<option value="">-- Choose Item --</option>';
        
        items.forEach(oi => {
            const option = document.createElement('option');
            option.value = oi.id;
            option.textContent = oi.item.item_code + ' - ' + oi.item.name;
            select.appendChild(option);
        });

        // Reset details
        document.getElementById('item_detail_section').style.display = 'none';
        document.getElementById('modal_ok_input').value = '';
        document.getElementById('modal_rej_input').value = 0;

        openModal('qcModal');
    }

    function updateItemDetails() {
        const itemId = document.getElementById('modal_item_select').value;
        const section = document.getElementById('item_detail_section');
        
        if (!itemId) {
            section.style.display = 'none';
            return;
        }

        const orderItem = currentOrderItems.find(oi => oi.id == itemId);
        if (orderItem) {
            section.style.display = 'block';
            document.getElementById('modal_produced_qty').textContent = Math.round(orderItem.quantity_produced);
            
            // Use the remaining_qc calculated in the controller
            const remaining = Math.round(orderItem.remaining_qc || 0);
            document.getElementById('modal_remaining_qty').textContent = remaining;
            document.getElementById('modal_ok_input').value = remaining;
            document.getElementById('modal_ok_input').max = remaining;
        }
    }
</script>
@endsection
