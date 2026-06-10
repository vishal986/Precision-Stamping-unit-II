<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'manager'])->orderBy('employee_code')->get();
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $designations = Designation::where('is_active', true)->get();
        $shifts = Shift::where('is_active', true)->get();
        $managers = Employee::where('status', 'Active')->get();

        return view('hr.employees.create', compact('departments', 'designations', 'shifts', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|unique:employees,employee_code',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'joining_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'nullable|string|max:255',
            'shift' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
            'grade' => 'nullable|string|max:50',
            'status' => 'required|in:Active,Inactive,Terminated,Resigned',
            'aadhaar_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'uan_number' => 'nullable|string|max:50',
            'esi_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'aadhaar_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('aadhaar_file')) {
            $validated['aadhaar_file_path'] = $request->file('aadhaar_file')->store('employees/documents', 'public');
        }
        if ($request->hasFile('pan_file')) {
            $validated['pan_file_path'] = $request->file('pan_file')->store('employees/documents', 'public');
        }
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('employees/photos', 'public');
        }

        // Unset file inputs from array before mass assignment
        unset($validated['aadhaar_file'], $validated['pan_file'], $validated['photo']);

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'manager']);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $designations = Designation::where('is_active', true)->get();
        $shifts = Shift::where('is_active', true)->get();
        $managers = Employee::where('status', 'Active')->where('id', '!=', $employee->id)->get();

        return view('hr.employees.create', compact('employee', 'departments', 'designations', 'shifts', 'managers'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,' . $employee->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'joining_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'nullable|string|max:255',
            'shift' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
            'grade' => 'nullable|string|max:50',
            'status' => 'required|in:Active,Inactive,Terminated,Resigned',
            'aadhaar_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'uan_number' => 'nullable|string|max:50',
            'esi_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'aadhaar_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('aadhaar_file')) {
            if ($employee->aadhaar_file_path) Storage::disk('public')->delete($employee->aadhaar_file_path);
            $validated['aadhaar_file_path'] = $request->file('aadhaar_file')->store('employees/documents', 'public');
        }
        if ($request->hasFile('pan_file')) {
            if ($employee->pan_file_path) Storage::disk('public')->delete($employee->pan_file_path);
            $validated['pan_file_path'] = $request->file('pan_file')->store('employees/documents', 'public');
        }
        if ($request->hasFile('photo')) {
            if ($employee->photo_path) Storage::disk('public')->delete($employee->photo_path);
            $validated['photo_path'] = $request->file('photo')->store('employees/photos', 'public');
        }

        // Unset file inputs from array before mass assignment
        unset($validated['aadhaar_file'], $validated['pan_file'], $validated['photo']);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->photo_path) Storage::disk('public')->delete($employee->photo_path);
        if ($employee->aadhaar_file_path) Storage::disk('public')->delete($employee->aadhaar_file_path);
        if ($employee->pan_file_path) Storage::disk('public')->delete($employee->pan_file_path);
        
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
