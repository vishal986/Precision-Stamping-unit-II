<?php

namespace App\Http\Controllers;

use App\Models\ExportInvoice;
use App\Models\ExportInvoiceItem;
use App\Models\Contact;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $now = \Carbon\Carbon::now();
        $currentStartYear = $now->month >= 4 ? $now->year : $now->year - 1;

        $selectedFy = $request->query('fy', "{$currentStartYear}-" . ($currentStartYear + 1));
        $parts = explode('-', $selectedFy);
        $startYear = (int) $parts[0];

        $startDate = \Carbon\Carbon::create($startYear, 4, 1)->startOfDay();
        $endDate = \Carbon\Carbon::create($startYear + 1, 3, 31)->endOfDay();

        $activeFyLabel = $startYear . "-" . substr($startYear + 1, -2);

        // Get min year for dropdown
        $minDate = ExportInvoice::min('invoice_date');
        $minYear = $minDate ? \Carbon\Carbon::parse($minDate)->year : 2025;
        if ($minYear > $currentStartYear) $minYear = $currentStartYear;
        if ($minYear < 2024) $minYear = 2024; // Ensure reasonable range

        $financialYears = [];
        for ($y = $minYear; $y <= $currentStartYear + 1; $y++) {
            $financialYears["{$y}-" . ($y + 1)] = "FY " . $y . "-" . substr($y + 1, -2);
        }

        $invoices = ExportInvoice::with('customer')
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('sales.export-invoices.index', compact('invoices', 'financialYears', 'selectedFy', 'activeFyLabel'));
    }

    public function create()
    {
        $customers = Contact::where('type', 'customer')->get();
        
        // Fetch items with their production order details to link order number/date
        $latestOrders = DB::table('production_orders')
            ->join('production_order_items', 'production_orders.id', '=', 'production_order_items.production_order_id')
            ->where('production_orders.status', 'QC Completed')
            ->orderBy('production_orders.created_at', 'desc')
            ->select('production_order_items.item_id', 'production_orders.order_number', 'production_orders.po_date')
            ->get()
            ->unique('item_id')
            ->keyBy('item_id');

        $items = Item::where('is_active', true)->with(['client'])->get()->map(function($item) use ($latestOrders) {
            $latestOrder = $latestOrders->get($item->id);
            $item->suggested_order_no = $latestOrder->order_number ?? '';
            $item->suggested_order_date = $latestOrder->po_date ?? '';
            return $item;
        });

        return view('sales.export-invoices.create', compact('customers', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|unique:export_invoices,invoice_no',
            'invoice_date' => 'required|date',
            'customer_id' => 'required|exists:contacts,id',
            'buyer_details' => 'nullable|string',
            'currency' => 'required|in:EUR,USD,INR',
            'exchange_rate' => 'nullable|numeric|min:0',
            'incoterms' => 'required|string',
            'vessel_flight_no' => 'nullable|string',
            'container_no' => 'nullable|string',
            'port_of_loading' => 'nullable|string',
            'port_of_discharge' => 'nullable|string',
            'final_destination' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'exporter_ref' => 'nullable|string',
            'buyer_order_no' => 'nullable|string',
            'eori_no' => 'nullable|string',
            'pre_carriage_by' => 'nullable|string',
            'place_of_receipt' => 'nullable|string',
            'country_of_origin' => 'nullable|string',
            'country_of_final_destination' => 'nullable|string',
            'marks_and_nos' => 'nullable|string',
            'no_and_kind_of_pkgs' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.hs_code' => 'nullable|string',
            'items.*.order_number' => 'nullable|string',
            'items.*.order_date' => 'nullable|date',
        ]);

        return DB::transaction(function() use ($request, $validated) {
            $totalAmount = 0;
            foreach($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $invoice = ExportInvoice::create(array_merge($validated, ['total_amount' => $totalAmount]));

            foreach($request->items as $itemData) {
                ExportInvoiceItem::create([
                    'export_invoice_id' => $invoice->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'hs_code' => $itemData['hs_code'],
                    'order_number' => $itemData['order_number'] ?? null,
                    'order_date' => $itemData['order_date'] ?? null,
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                ]);

                // Deduct from Stock
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    $item->decrement('current_stock', $itemData['quantity']);
                }
            }

            return redirect()->route('export-invoices.index')->with('success', 'Export Invoice generated successfully and Stock updated.');
        });
    }

    public function edit(ExportInvoice $exportInvoice)
    {
        $exportInvoice->load('items');
        $customers = Contact::where('type', 'customer')->get();
        
        // Fetch items with their production order details to link order number/date
        $latestOrders = DB::table('production_orders')
            ->join('production_order_items', 'production_orders.id', '=', 'production_order_items.production_order_id')
            ->where('production_orders.status', 'QC Completed')
            ->orderBy('production_orders.created_at', 'desc')
            ->select('production_order_items.item_id', 'production_orders.order_number', 'production_orders.po_date')
            ->get()
            ->unique('item_id')
            ->keyBy('item_id');

        $items = Item::where('is_active', true)->with(['client'])->get()->map(function($item) use ($latestOrders) {
            $latestOrder = $latestOrders->get($item->id);
            $item->suggested_order_no = $latestOrder->order_number ?? '';
            $item->suggested_order_date = $latestOrder->po_date ?? '';
            return $item;
        });

        return view('sales.export-invoices.edit', compact('exportInvoice', 'customers', 'items'));
    }

    public function update(Request $request, ExportInvoice $exportInvoice)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|unique:export_invoices,invoice_no,' . $exportInvoice->id,
            'invoice_date' => 'required|date',
            'customer_id' => 'required|exists:contacts,id',
            'buyer_details' => 'nullable|string',
            'currency' => 'required|in:EUR,USD,INR',
            'exchange_rate' => 'nullable|numeric|min:0',
            'incoterms' => 'required|string',
            'vessel_flight_no' => 'nullable|string',
            'container_no' => 'nullable|string',
            'port_of_loading' => 'nullable|string',
            'port_of_discharge' => 'nullable|string',
            'final_destination' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'exporter_ref' => 'nullable|string',
            'buyer_order_no' => 'nullable|string',
            'eori_no' => 'nullable|string',
            'pre_carriage_by' => 'nullable|string',
            'place_of_receipt' => 'nullable|string',
            'country_of_origin' => 'nullable|string',
            'country_of_final_destination' => 'nullable|string',
            'marks_and_nos' => 'nullable|string',
            'no_and_kind_of_pkgs' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.hs_code' => 'nullable|string',
            'items.*.order_number' => 'nullable|string',
            'items.*.order_date' => 'nullable|date',
        ]);

        return DB::transaction(function() use ($request, $validated, $exportInvoice) {
            // Revert stock for old items
            foreach($exportInvoice->items as $oldItem) {
                $itemModel = Item::find($oldItem->item_id);
                if ($itemModel) {
                    $itemModel->increment('current_stock', $oldItem->quantity);
                }
            }

            // Delete old items
            $exportInvoice->items()->delete();

            // Calculate new total amount
            $totalAmount = 0;
            foreach($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Update invoice
            $exportInvoice->update(array_merge($validated, ['total_amount' => $totalAmount]));

            // Create new items and deduct stock
            foreach($request->items as $itemData) {
                ExportInvoiceItem::create([
                    'export_invoice_id' => $exportInvoice->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'hs_code' => $itemData['hs_code'],
                    'order_number' => $itemData['order_number'] ?? null,
                    'order_date' => $itemData['order_date'] ?? null,
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                ]);

                // Deduct from Stock
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    $item->decrement('current_stock', $itemData['quantity']);
                }
            }

            return redirect()->route('export-invoices.show', $exportInvoice)->with('success', 'Export Invoice updated successfully and Stock adjusted.');
        });
    }

    public function show(ExportInvoice $exportInvoice)
    {
        $exportInvoice->load('customer', 'items.item');
        return view('sales.export-invoices.show', compact('exportInvoice'));
    }

    public function print(ExportInvoice $exportInvoice)
    {
        $exportInvoice->load('customer', 'items.item');
        return view('sales.export-invoices.print', compact('exportInvoice'));
    }
}
