<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with(['category', 'client']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('lfe', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        $items = $query->latest()->get();
        $clients = \App\Models\Contact::all();

        return view('items.index', compact('items', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\ItemCategory::all();
        $clients = \App\Models\Contact::all();
        return view('items.create', compact('categories', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|unique:items,item_code',
            'name' => 'required|string|max:255',
            'lfe' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'item_category_id' => 'nullable|exists:item_categories,id',
            'client_id' => 'nullable|exists:contacts,id',
            'uom' => 'required|string|max:50',
            'unit_price' => 'numeric|min:0',
            'cost_price' => 'numeric|min:0',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');

        Item::create($validated);
        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = \App\Models\ItemCategory::all();
        $clients = \App\Models\Contact::all();
        return view('items.edit', compact('item', 'categories', 'clients'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_code' => 'required|unique:items,item_code,' . $item->id,
            'name' => 'required|string|max:255',
            'lfe' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'item_category_id' => 'nullable|exists:item_categories,id',
            'client_id' => 'nullable|exists:contacts,id',
            'uom' => 'required|string|max:50',
            'unit_price' => 'numeric|min:0',
            'cost_price' => 'numeric|min:0',
        ]);
        
        $validated['is_active'] = $request->has('is_active');

        $item->update($validated);
        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

    public function exportExcel(Request $request)
    {
        $query = Item::with(['category', 'client']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('lfe', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        $items = $query->latest()->get();
        $filename = "items_inventory_" . date('Ymd_His') . ".xls";

        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        $xml .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        $xml .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="s1"><Font ss:Bold="1"/></Style>' . "\n";
        $xml .= '</Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Inventory">' . "\n";
        $xml .= '<Table>' . "\n";
        
        // Header Row
        $headers = ['ID', 'Article No / Item Code', 'Item Name', 'LFe', 'Category', 'Client / Customer', 'Current Stock', 'Unit', 'Description', 'Created Date'];
        $xml .= '<Row>' . "\n";
        foreach ($headers as $h) {
            $xml .= '<Cell ss:StyleID="s1"><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
        }
        $xml .= '</Row>' . "\n";

        // Data Rows
        foreach ($items as $item) {
            $xml .= '<Row>' . "\n";
            $data = [
                $item->id,
                $item->item_code,
                $item->name,
                $item->lfe,
                $item->category ? $item->category->name : '',
                $item->client ? ($item->client->company_name ?? $item->client->name) : '',
                $item->current_stock,
                $item->uom,
                $item->description,
                $item->created_at ? $item->created_at->format('Y-m-d') : ''
            ];
            foreach ($data as $val) {
                $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$val) . '</Data></Cell>' . "\n";
            }
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return response($xml, 200, [
            "Content-Type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
