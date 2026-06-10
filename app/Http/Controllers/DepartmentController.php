<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('id', 'DESC')->get();
        return view('hr.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
        ]);

        Department::create($request->all());

        return redirect()->route('departments.index')->with('success', 'Department added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $id,
        ]);

        Department::findOrFail($id)->update($request->all());

        return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully!');
    }
}
