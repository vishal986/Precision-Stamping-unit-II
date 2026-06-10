<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\QualityLog;
use App\Models\Item;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    public function qcIndex()
    {
        $orders = ProductionOrder::with(['items.item', 'qualityLogs'])
            ->where('status', 'completed')
            ->get()
            ->map(function($order) {
                $order->items->each(function($item) use ($order) {
                    $checked = QualityLog::where('production_order_item_id', $item->id)->sum(\DB::raw('ok_qty + rejected_qty'));
                    $item->remaining_qc = max(0, $item->quantity_produced - $checked);
                });
                return $order;
            });

        $qualityLogs = QualityLog::with(['productionOrder', 'productionOrderItem.item'])->orderBy('created_at', 'desc')->paginate(20);
        
        return view('logistics.qc.index', compact('orders', 'qualityLogs'));
    }

    public function qcStore(Request $request)
    {
        $validated = $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'production_order_item_id' => 'required|exists:production_order_items,id',
            'ok_qty' => 'required|numeric|min:0',
            'rejected_qty' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $orderItem = \App\Models\ProductionOrderItem::with('item')->findOrFail($validated['production_order_item_id']);
        
        // Calculate remaining to check for THIS specific item
        $totalProduced = $orderItem->quantity_produced;
        $alreadyChecked = QualityLog::where('production_order_item_id', $orderItem->id)->sum(\DB::raw('ok_qty + rejected_qty'));
        
        $remainingToCheck = $totalProduced - $alreadyChecked;
        $enteringNow = $validated['ok_qty'] + $validated['rejected_qty'];

        if ($enteringNow > $remainingToCheck) {
            return back()->with('error', "QC quantity for this item cannot exceed produced quantity. Remaining: $remainingToCheck.");
        }

        $log = QualityLog::create($validated);

        // Update Item Stock (Finished Goods) - ONLY for the selected item
        if ($orderItem->item) {
            $orderItem->item->increment('current_stock', $validated['ok_qty']);
        }

        // Check if EVERYTHING in the order is now QC'd
        $order = ProductionOrder::with('items', 'qualityLogs')->findOrFail($validated['production_order_id']);
        $allProduced = $order->items->sum('quantity_produced');
        $allChecked = QualityLog::where('production_order_id', $order->id)->sum(\DB::raw('ok_qty + rejected_qty'));

        if ($allChecked >= $allProduced && $allProduced > 0) {
            $order->update(['status' => 'QC Completed']);
        }

        return redirect()->route('logistics.qc.index')->with('success', 'Quality Log updated for ' . $orderItem->item->name . ' and stock increased.');
    }

    public function inventory(Request $request)
    {
        $query = Item::with(['category', 'client'])
            ->where('is_active', true)
            ->where('current_stock', '>', 0);
            
        if ($request->filled('search_item')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_item . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search_item . '%');
            });
        }
        
        if ($request->filled('search_client')) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_client . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search_client . '%');
            });
        }

        $items = $query->get();
        return view('logistics.inventory.index', compact('items'));
    }
}
