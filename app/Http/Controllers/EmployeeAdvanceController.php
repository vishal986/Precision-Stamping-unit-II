<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAdvance;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeAdvanceController extends Controller
{
    public function index()
    {
        $advances = EmployeeAdvance::with('employee')->orderBy('created_at', 'desc')->get();
        $employees = Employee::where('status', 'Active')->get();
        return view('hr.advances.index', compact('advances', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date_given' => 'required|date',
            'amount_given' => 'required|numeric|min:1',
            'emi_amount' => 'required|numeric|min:1|lte:amount_given',
            'remarks' => 'nullable|string',
        ]);

        $validated['remaining_balance'] = $validated['amount_given'];
        $validated['status'] = 'Active';

        EmployeeAdvance::create($validated);

        return redirect()->route('advances.index')->with('success', 'Advance added successfully.');
    }

    // Optional: a method to mark as fully paid early or delete if mistake
    public function destroy(EmployeeAdvance $advance)
    {
        $advance->delete();
        return redirect()->route('advances.index')->with('success', 'Advance record removed.');
    }
}
