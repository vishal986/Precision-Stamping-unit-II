<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeave;
use App\Models\LeaveType;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = EmployeeLeave::with(['employee', 'leaveType', 'approver'])->orderBy('created_at', 'desc')->get();
        return view('hr.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'Active')->get();
        $leaveTypes = LeaveType::all();
        return view('hr.leaves.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => 'required|numeric|min:0.5',
            'reason' => 'required|string',
        ]);

        EmployeeLeave::create($validated);

        return redirect()->route('leaves.index')->with('success', 'Leave application submitted successfully.');
    }

    public function updateStatus(Request $request, EmployeeLeave $leaf)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'admin_remarks' => 'nullable|string'
        ]);

        $leaf->update([
            'status' => $validated['status'],
            'admin_remarks' => $validated['admin_remarks'] ?? null,
            'approved_by' => auth()->user()->id ?? null, // Will be updated if an employee is logged in instead of admin user
        ]);

        return back()->with('success', 'Leave status updated successfully.');
    }
}
