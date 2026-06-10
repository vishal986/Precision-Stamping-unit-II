<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bom;
use App\Models\Item;

class BomController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $validated = $request->validate([
            'grade' => 'required|string|max:255',
            'job_size' => 'required|string|max:255',
            'weight_per_unit' => 'required|numeric|min:0.001',
        ]);

        $item->boms()->create($validated);

        return back()->with('success', 'BOM entry added successfully.');
    }

    public function destroy(Bom $bom)
    {
        $bom->delete();
        return back()->with('success', 'BOM entry removed.');
    }
}
