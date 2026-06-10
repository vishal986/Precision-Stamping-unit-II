<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\ProductionOrder::with(['items.item', 'client']);

        if ($request->filled('search_po')) {
            $query->where('order_number', 'like', '%' . $request->search_po . '%');
        }

        if ($request->filled('search_client')) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_client . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search_client . '%');
            });
        }

        if ($request->filled('search_item')) {
            $query->whereHas('items.item', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_item . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search_item . '%');
            });
        }

        if ($request->filled('search_po_date')) {
            $query->whereDate('po_date', $request->search_po_date);
        }

        if ($request->filled('search_delivery_week')) {
            $query->where('delivery_week', 'like', '%' . $request->search_delivery_week . '%');
        }

        $orders = $query->latest()->get();
        return view('production.index', compact('orders'));
    }

    public function create()
    {
        $items = \App\Models\Item::where('is_active', true)->get();
        $clients = \App\Models\Contact::where('type', 'customer')->get();
        return view('production.create', compact('items', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|unique:production_orders,order_number',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'po_date' => 'nullable|date',
            'delivery_week' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:contacts,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_planned' => 'required|numeric|min:0.01',
        ]);

        $order = \App\Models\ProductionOrder::create([
            'order_number' => $validated['order_number'],
            'status' => $validated['status'],
            'po_date' => $validated['po_date'] ?? null,
            'delivery_week' => $validated['delivery_week'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
        ]);

        foreach ($validated['items'] as $itemData) {
            $order->items()->create([
                'item_id' => $itemData['item_id'],
                'quantity_planned' => $itemData['quantity_planned'],
                'quantity_produced' => 0
            ]);
        }

        return redirect()->route('production.index')->with('success', 'Production Order created successfully.');
    }

    public function show(\App\Models\ProductionOrder $production)
    {
        $production->load('items.item.boms', 'coilIssues.coil', 'client');
        return view('production.show', compact('production'));
    }

    public function edit(\App\Models\ProductionOrder $production)
    {
        $items = \App\Models\Item::where('is_active', true)->get();
        $clients = \App\Models\Contact::where('type', 'customer')->get();
        return view('production.create', ['order' => $production, 'items' => $items, 'clients' => $clients]);
    }

    public function update(Request $request, \App\Models\ProductionOrder $production)
    {
        $validated = $request->validate([
            'order_number' => 'required|unique:production_orders,order_number,' . $production->id,
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'po_date' => 'nullable|date',
            'delivery_week' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:contacts,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_planned' => 'required|numeric|min:0.01',
        ]);

        $production->update([
            'order_number' => $validated['order_number'],
            'status' => $validated['status'],
            'po_date' => $validated['po_date'] ?? null,
            'delivery_week' => $validated['delivery_week'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
        ]);

        // Delete old items and recreate
        $production->items()->delete();
        foreach ($validated['items'] as $itemData) {
            $production->items()->create([
                'item_id' => $itemData['item_id'],
                'quantity_planned' => $itemData['quantity_planned'],
                // Try to keep produced quantity if exists, but for simplicity here we reset or just rely on completing.
                // In a robust system we'd sync or update existing.
                'quantity_produced' => $itemData['quantity_produced'] ?? 0
            ]);
        }

        return redirect()->route('production.index')->with('success', 'Production Order updated successfully.');
    }

    public function destroy(\App\Models\ProductionOrder $production)
    {
        $production->delete();
        return redirect()->route('production.index')->with('success', 'Production Order deleted successfully.');
    }

    public function issueMaterial(Request $request, \App\Models\ProductionOrder $production)
    {
        $validated = $request->validate([
            'coil_id' => 'required|exists:coils,id',
            'issued_weight' => 'required|numeric|min:0.001',
            'issue_unit' => 'required|in:g,kg,mt,nos',
        ]);

        $coil = \App\Models\Coil::findOrFail($validated['coil_id']);

        // Convert issued weight to base unit (KG / Nos) for remaining_weight calculation
        $weightInBaseUnit = $validated['issued_weight'];
        if ($validated['issue_unit'] === 'g') {
            $weightInBaseUnit = $validated['issued_weight'] / 1000;
        } elseif ($validated['issue_unit'] === 'mt') {
            $weightInBaseUnit = $validated['issued_weight'] * 1000;
        }

        if ($coil->remaining_weight < $weightInBaseUnit) {
            return back()->with('error', 'Not enough material remaining in this coil.');
        }

        $coil->remaining_weight -= $weightInBaseUnit;
        $coil->save();

        $department = \App\Models\Department::first();
        if (!$department) {
            $department = \App\Models\Department::create([
                'name' => 'Production Floor',
                'code' => 'PROD-01'
            ]);
        }

        \App\Models\CoilIssue::create([
            'coil_id' => $coil->id,
            'department_id' => $department->id,
            'production_order_id' => $production->id,
            'issued_weight' => $validated['issued_weight'],
            'issue_unit' => $validated['issue_unit'],
            'issue_date' => now()->toDateString(),
            'issued_by' => auth()->user()?->name ?? 'System',
        ]);

        if ($production->status == 'planned') {
            $production->update(['status' => 'in_progress', 'start_date' => now()->toDateString()]);
        }

        return back()->with('success', 'Material consumed successfully.');
    }

    public function storeLog(Request $request, \App\Models\ProductionOrder $production)
    {
        $validated = $request->validate([
            'production_order_item_id' => 'required|exists:production_order_items,id',
            'log_date' => 'required|date',
            'quantity_produced' => 'required|numeric|min:0',
            'quantity_rejected' => 'required|numeric|min:0',
            'rejection_reason' => 'nullable|string',
            'operator_name' => 'nullable|string',
            'machine_name' => 'nullable|string',
        ]);

        if ($validated['quantity_produced'] == 0 && $validated['quantity_rejected'] == 0) {
            return back()->with('error', 'Must log at least some produced or rejected quantity.');
        }

        // Verify the item belongs to this order
        $orderItem = ProductionOrderItem::where('id', $validated['production_order_item_id'])
            ->where('production_order_id', $production->id)
            ->firstOrFail();

        // Create the log
        \App\Models\ProductionLog::create($validated);

        // Update the order item quantities
        $orderItem->quantity_produced += $validated['quantity_produced'];
        $orderItem->quantity_rejected += $validated['quantity_rejected'];
        $orderItem->save();

        // Update Finished Item Stock
        $item = $orderItem->item;
        if ($item) {
            $item->current_stock += $validated['quantity_produced'];
            $item->save();
        }

        // Update status to in_progress if planned
        if ($production->status == 'planned') {
            $production->update(['status' => 'in_progress', 'start_date' => now()->toDateString()]);
        }

        return back()->with('success', 'Production log added successfully. Stock updated.');
    }

    public function completeOrder(Request $request, \App\Models\ProductionOrder $production)
    {
        $production->update([
            'status' => 'completed',
            'end_date' => now()->toDateString()
        ]);

        return back()->with('success', 'Production Order has been marked as Completed.');
    }
}
