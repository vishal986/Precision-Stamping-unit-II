<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coil;

class CoilController extends Controller
{
    /**
     * Show the coil form (add + edit)
     */
    public function materialIN()
    {
        return view('material');
    }

    /**
     * Insert or Update coil data
     */
    public function save(Request $request)
    {
        // ---------------------------
        // VALIDATION
        // ---------------------------
        $isEdit = $request->coil_id ? true : false;
        
        $request->validate([
            'material_name' => 'required|string|max:255',
            'coil_num'      => 'required|string|max:255|unique:coils,coil_num' . ($isEdit ? ',' . $request->coil_id : ''),
            'job_size'      => 'required|string|max:255',
            'coil_grade'    => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'weight_value'  => 'required|numeric',
            'weight_unit'   => 'required|in:g,kg,mt,nos',
        ]);
        // ---------------------------
        // WEIGHT CONVERSION TO KG
        // ---------------------------
        $weightValue = $request->weight_value;
        $weightUnit  = $request->weight_unit;

        if ($weightUnit === 'g') {
            $weightInKg = $weightValue / 1000;
        } elseif ($weightUnit === 'mt') {
            $weightInKg = $weightValue * 1000;
        } else {
            $weightInKg = $weightValue; // already kg
        }
        // Check if it is EDIT or INSERT
        $isEdit = $request->coil_id ? true : false;
        // ---------------------------
        // INSERT OR UPDATE
        // ---------------------------
        if ($isEdit) {
            $coil = Coil::findOrFail($request->coil_id);
            
            if ($request->force_reset == 1) {
                $coil->allowReset();
                $coil->remaining_weight = $weightInKg;
            }

            $coil->update([
                'coil_name'    => $request->material_name,
                'coil_num'     => $request->coil_num,
                'job_size'     => $request->job_size,
                'grade'        => $request->coil_grade,
                'quantity'     => $request->quantity,
                'weight_value' => $request->weight_value,
                'weight_unit'  => $request->weight_unit
            ]);
        } else {
            $coil = Coil::create([
                'coil_name'    => $request->material_name,
                'coil_num'     => $request->coil_num,
                'job_size'     => $request->job_size,
                'grade'        => $request->coil_grade,
                'quantity'     => $request->quantity,
                'weight_value' => $request->weight_value,
                'weight_unit'  => $request->weight_unit,
                'remaining_weight' => $weightInKg
            ]);
        }

        // REDIRECT BASED ON ACTION
        // ---------------------------
        if ($isEdit) {
            // After UPDATE → go to listing page
            return redirect('/coil-data')->with('success', 'Coil updated successfully!');
        } else {
            // After INSERT → stay on form
            return redirect()->back()->with('success', 'Coil added successfully!');
        }
    }

    /**
     * Fetch all coil records
     */
    public function viewCoils(Request $request)
    {
        $search = $request->search;

        $coils = Coil::when($search, function ($query) use ($search) {
            $query->where('coil_name', 'LIKE', "%$search%")
                ->orWhere('coil_num', 'LIKE', "%$search%")
                ->orWhere('job_size', 'LIKE', "%$search%")
                ->orWhere('grade', 'LIKE', "%$search%")
                ->orWhere('quantity', 'LIKE', "%$search%")
                ->orWhere('weight_value', 'LIKE', "%$search%")
                ->orWhere('weight_unit', 'LIKE', "%$search%");
        })->get();

        return view('coil-data', compact('coils', 'search'));
    }

    /**
     * Delete record
     */
    public function deleteCoil($id)
    {
        if (Coil::where('id', $id)->delete()) {
            return redirect('/coil-data')->with('success', "Data Deleted successfully");
        }

        return redirect('/coil-data')->with('error', "Something went wrong");
    }

    /**
     * Load a record for editing
     */
    public function editCoil($id)
    {
        $coil = Coil::findOrFail($id);
        return view('material', compact('coil'));
    }
}
