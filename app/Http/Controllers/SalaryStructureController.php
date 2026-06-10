<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;

class SalaryStructureController extends Controller
{
    public function index()
    {
        $employees = Employee::where('status', 'Active')->with('department')->orderBy('employee_code')->get();
        // Load existing structures and key by employee_id for easy lookup in view
        $structures = SalaryStructure::all()->keyBy('employee_id');
        
        return view('hr.salary_structures.index', compact('employees', 'structures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'structures' => 'required|array',
            'structures.*.basic_salary' => 'required|numeric|min:0',
            'structures.*.hra' => 'required|numeric|min:0',
            'structures.*.medical_allowance' => 'required|numeric|min:0',
            'structures.*.conveyance_allowance' => 'required|numeric|min:0',
            'structures.*.special_allowance' => 'required|numeric|min:0',
            'structures.*.pf_applicable' => 'nullable|boolean',
            'structures.*.esi_applicable' => 'nullable|boolean',
            'structures.*.lwf_applicable' => 'nullable|boolean',
        ]);

        foreach ($validated['structures'] as $empId => $data) {
            // Checkboxes only send value if checked
            $pf = isset($data['pf_applicable']) ? true : false;
            $esi = isset($data['esi_applicable']) ? true : false;
            $lwf = isset($data['lwf_applicable']) ? true : false;
            
            // If the user checked it but the hidden input trick was used, Laravel handles it.
            // But since we might just use normal checkboxes, let's be safe.
            if ($request->has("structures.$empId.pf_hidden")) {
                 $pf = $request->input("structures.$empId.pf_applicable", false);
            }
            if ($request->has("structures.$empId.esi_hidden")) {
                 $esi = $request->input("structures.$empId.esi_applicable", false);
            }
            if ($request->has("structures.$empId.lwf_hidden")) {
                 $lwf = $request->input("structures.$empId.lwf_applicable", false);
            }

            SalaryStructure::updateOrCreate(
                ['employee_id' => $empId],
                [
                    'basic_salary' => $data['basic_salary'],
                    'hra' => $data['hra'],
                    'medical_allowance' => $data['medical_allowance'],
                    'conveyance_allowance' => $data['conveyance_allowance'],
                    'special_allowance' => $data['special_allowance'],
                    'pf_applicable' => $pf,
                    'esi_applicable' => $esi,
                    'lwf_applicable' => $lwf,
                ]
            );
        }

        return redirect()->route('salary_structures.index')->with('success', 'Salary structures updated successfully.');
    }
}
