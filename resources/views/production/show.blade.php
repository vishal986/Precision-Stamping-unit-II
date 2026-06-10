@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Production Order: {{ $production->order_number }}</h1>
    <a href="{{ route('production.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Left Column: Details, History & Consumption -->
    <div>
        <div class="card" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
                <h2 class="card-title" style="margin: 0;">Order Details</h2>
                <span class="badge" style="background: {{ $production->status == 'completed' ? 'var(--success-color)' : 'var(--primary-color)' }};">{{ ucfirst(str_replace('_', ' ', $production->status)) }}</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Client / Customer</span>
                    <div style="font-weight: 500;">
                        @if($production->client)
                            {{ $production->client->name }} {{ $production->client->company_name ? '('.$production->client->company_name.')' : '' }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">PO Date</span>
                    <div style="font-weight: 500;">{{ $production->po_date ? \Carbon\Carbon::parse($production->po_date)->format('d M, Y') : '-' }}</div>
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Delivery Week</span>
                    <div style="font-weight: 500;">{{ $production->delivery_week ?? '-' }}</div>
                </div>
            </div>
            
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Items to Produce</h3>
            <table class="table" style="margin-bottom: 1rem;">
                <thead>
                    <tr>
                        <th>Article No.</th>
                        <th>Item Name</th>
                        <th>Planned Qty</th>
                        <th>Produced Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($production->items as $orderItem)
                    <tr>
                        <td>{{ $orderItem->item->item_code }}</td>
                        <td>{{ $orderItem->item->name }}</td>
                        <td>{{ number_format($orderItem->quantity_planned, 0) }} {{ $orderItem->item->uom }}</td>
                        <td style="color: var(--success-color); font-weight: bold;">{{ number_format($orderItem->quantity_produced, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($production->notes)
            <div style="margin-top: 1.5rem;">
                <p style="color: var(--text-secondary); margin-bottom: 0.25rem;">Notes</p>
                <p>{{ $production->notes }}</p>
            </div>
            @endif
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <h2 class="card-title" style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Production Logs History</h2>
            @php
                $allLogs = collect();
                foreach($production->items as $item) {
                    if ($item->logs) {
                        $allLogs = $allLogs->merge($item->logs);
                    }
                }
                $allLogs = $allLogs->sortByDesc('created_at');
            @endphp

            @if($allLogs->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Quantity Produced</th>
                            <th>Rejected</th>
                            <th>Operator/Machine</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allLogs as $log)
                        <tr>
                            <td>{{ $log->log_date ? $log->log_date->format('d M, Y') : '-' }}</td>
                            <td>{{ $log->productionOrderItem->item->name }}</td>
                            <td style="color: var(--success-color);">+{{ $log->quantity_produced }}</td>
                            <td style="color: var(--danger-color);">+{{ $log->quantity_rejected }}</td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                {{ $log->operator_name ?: '-' }} / {{ $log->machine_name ?: '-' }}
                                @if($log->rejection_reason)
                                <br><span style="color: var(--danger-color);">Reason: {{ $log->rejection_reason }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: var(--text-secondary);">No daily production logs have been recorded yet.</p>
            @endif
        </div>

        <div class="card">
            <h2 class="card-title" style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Material Consumption History</h2>
            @if($production->coilIssues->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Coil No.</th>
                            <th>Weight Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($production->coilIssues as $issue)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M, Y') }}</td>
                            <td>{{ $issue->coil->coil_num }} ({{ $issue->coil->grade }})</td>
                            <td>{{ $issue->issued_weight }} {{ $issue->issue_unit ?? 'kg' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: var(--text-secondary);">No materials have been consumed for this order yet.</p>
            @endif
        </div>
    </div>

    <!-- Right Column: BOM & Actions -->
    <div>
        <div class="card" style="margin-bottom: 2rem; background: rgba(var(--primary-rgb), 0.05); border-color: rgba(var(--primary-rgb), 0.2);">
            <h2 class="card-title" style="margin-bottom: 1rem; color: var(--primary-color);">Total BOM Requirements</h2>
            
            @php
                $totalBom = [];
                foreach($production->items as $orderItem) {
                    foreach($orderItem->item->boms as $bom) {
                        $key = $bom->grade . '|' . $bom->job_size;
                        if (!isset($totalBom[$key])) {
                            $totalBom[$key] = [
                                'grade' => $bom->grade,
                                'job_size' => $bom->job_size,
                                'weight' => 0
                            ];
                        }
                        $totalBom[$key]['weight'] += ($bom->weight_per_unit * $orderItem->quantity_planned);
                    }
                }
            @endphp

            @if(count($totalBom) > 0)
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($totalBom as $req)
                    <li style="padding: 1rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="font-weight: 500; margin-bottom: 0.25rem;">{{ $req['grade'] }} - {{ $req['job_size'] }}</div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">
                            Required: <strong>{{ number_format($req['weight'], 3) }} kg</strong>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <p style="color: var(--text-secondary); font-size: 0.9rem;">No BOM defined for any of the items in this order.</p>
            @endif
        </div>

        @if($production->status !== 'completed' && $production->status !== 'cancelled')
        <div class="card" style="margin-bottom: 2rem;">
            <h2 class="card-title" style="margin-bottom: 1rem;">Log Daily Production (Job Card)</h2>
            <form action="{{ route('production.log', $production) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Item Produced <span style="color: var(--danger-color);">*</span></label>
                    <select name="production_order_item_id" class="form-control" required>
                        <option value="">-- Select Item --</option>
                        @foreach($production->items as $orderItem)
                            @if($orderItem->quantity_produced < $orderItem->quantity_planned)
                                <option value="{{ $orderItem->id }}">{{ $orderItem->item->item_code }} - {{ $orderItem->item->name }} ({{ number_format($orderItem->quantity_planned - $orderItem->quantity_produced, 0) }} left)</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date <span style="color: var(--danger-color);">*</span></label>
                    <input type="date" name="log_date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" style="color: var(--success-color);">Quantity Produced <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" step="1" name="quantity_produced" class="form-control" required value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="color: var(--danger-color);">Rejected Qty</label>
                        <input type="number" step="1" name="quantity_rejected" class="form-control" value="0" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Rejection Reason</label>
                    <input type="text" name="rejection_reason" class="form-control" placeholder="If rejected, why?">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Operator Name</label>
                        <input type="text" name="operator_name" class="form-control" placeholder="e.g. John Doe">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Machine Name</label>
                        <input type="text" name="machine_name" class="form-control" placeholder="e.g. Press #1">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Daily Log</button>
            </form>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <h2 class="card-title" style="margin-bottom: 1rem;">Issue Material</h2>
            <form action="{{ route('production.issue', $production) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Coil / Material</label>
                    <select name="coil_id" class="form-control searchable-select" required>
                        <option value="">-- Choose Material --</option>
                        @foreach(\App\Models\Coil::where('remaining_weight', '>', 0)->get() as $coil)
                            @php
                                $dispWeight = $coil->remaining_weight;
                                if($coil->weight_unit == 'mt') $dispWeight = $coil->remaining_weight / 1000;
                                elseif($coil->weight_unit == 'g') $dispWeight = $coil->remaining_weight * 1000;
                            @endphp
                            <option value="{{ $coil->id }}">{{ $coil->coil_num }} ({{ $coil->grade }}, {{ floatval($dispWeight) }} {{ $coil->weight_unit }} left)</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Qty to Issue</label>
                        <input type="number" step="0.001" name="issued_weight" class="form-control" required min="0.001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit</label>
                        <select name="issue_unit" class="form-control" required>
                            <option value="mt" selected>mt</option>
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="nos">nos</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn" style="width: 100%; background: rgba(255,255,255,0.1); color: var(--text-primary); margin-top: 1rem;">Consume Material</button>
            </form>
        </div>

        <div class="card" style="border-color: var(--success-color);">
            <h2 class="card-title" style="margin-bottom: 1rem; color: var(--success-color);">Close Order</h2>
            <form action="{{ route('production.complete', $production) }}" method="POST" onsubmit="return confirm('Are you sure you want to CLOSE this order? No more logs can be added.');">
                @csrf
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">Once production is fully finished, mark this order as completed.</p>
                <button type="submit" class="btn" style="width: 100%; background: var(--success-color); color: white;">Mark as Completed</button>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.searchable-select');
        selects.forEach(select => {
            new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false
            });
        });
    });
</script>
@endsection
