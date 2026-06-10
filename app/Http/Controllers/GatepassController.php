<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Gatepass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GatepassController extends Controller
{
    public function index()
    {
        $gatepasses = Gatepass::with(['employee.department'])->orderBy('date', 'desc')->orderBy('out_time', 'desc')->paginate(15);
        $employees = Employee::where('status', 'Active')->get();
        return view('hr.gatepasses.index', compact('gatepasses', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'type' => 'required|in:Personal,Official',
            'out_time' => 'required',
            'in_time' => 'nullable',
            'reason' => 'nullable|string|max:255',
        ]);

        $validated['status'] = 'Approved'; // Default to approved for now as requested
        $validated['approved_by'] = Auth::id();

        Gatepass::create($validated);

        return redirect()->route('gatepasses.index')->with('success', 'Gatepass issued successfully.');
    }

    public function update(Request $request, Gatepass $gatepass)
    {
        $validated = $request->validate([
            'in_time' => 'required',
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        $gatepass->update($validated);

        return redirect()->route('gatepasses.index')->with('success', 'Gatepass updated successfully.');
    }

    public function destroy(Gatepass $gatepass)
    {
        $gatepass->delete();
        return redirect()->route('gatepasses.index')->with('success', 'Gatepass deleted.');
    }

    public function print(Gatepass $gatepass)
    {
        $gatepass->load('employee.department');
        return view('hr.gatepasses.print', compact('gatepass'));
    }
}
