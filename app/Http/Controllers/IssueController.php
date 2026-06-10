<?php

namespace App\Http\Controllers;

use App\Models\Coil;
use App\Models\Department;
use App\Models\CoilIssue;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class IssueController extends Controller
{
    // Show issue form
    public function create()
    {
        $coils = Coil::orderBy('coil_num', 'ASC')->get(); // fetch all coils (non-deleted)
        $departments = Department::orderBy('name', 'ASC')->get();

        return view('issue.create', compact('coils', 'departments'));
    }

    // Store issued coil
    public function store(Request $request)
    {
        $request->validate([
            'coil_id' => 'required',
            'department_id' => 'required',
            'issued_weight' => 'required|numeric|min:0.001', // in MT
            'issue_date' => 'required|date',
        ]);

        $coil = Coil::findOrFail($request->coil_id);
        $issued_weight_mt = $request->issued_weight;

        if ($issued_weight_mt > $coil->remaining_weight) {
            return back()->with('error', 'Issued quantity is more than available stock!');
        }

        // Deduct stock
        $coil->remaining_weight -= $issued_weight_mt;

        // Soft delete if stock zero
        if ($coil->remaining_weight <= 0) {
            $coil->delete(); // SoftDeletes will mark deleted_at
        } else {
            $coil->save();
        }

        // Save issue
        CoilIssue::create([
            'coil_id' => $request->coil_id,
            'department_id' => $request->department_id,
            'issued_weight' => $issued_weight_mt,
            'issue_date' => $request->issue_date,
            'issued_by' => $request->issued_by,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Coil issued successfully!');
    }

    // Show report
    public function report()
    {
        $issues = CoilIssue::with('coil', 'department')->latest()->get();
        return view('issue.report', compact('issues'));
    }

    // Edit issue
    public function edit($id)
    {
        $issue = CoilIssue::findOrFail($id);
        $coils = Coil::all(); // fetch non-deleted coils
        $departments = Department::all();

        return view('issue.edit', compact('issue', 'coils', 'departments'));
    }

    // Update issue entry
    public function update(Request $request, $id)
    {
        $request->validate([
            'coil_id' => 'required',
            'department_id' => 'required',
            'issued_weight' => 'required|numeric|min:0.001',
            'issue_date' => 'required|date',
        ]);

        $issue = CoilIssue::findOrFail($id);

        // Restore old coil stock (even if soft deleted)
        $old_coil = Coil::withTrashed()->find($issue->coil_id);
        if ($old_coil) {
            $old_coil->remaining_weight += $issue->issued_weight;
            $old_coil->save();
        }

        // Deduct new coil stock
        $new_coil = Coil::findOrFail($request->coil_id);
        $issued_weight_mt = $request->issued_weight;

        if ($issued_weight_mt > $new_coil->remaining_weight) {
            return back()->with('error', 'Issued quantity is more than available stock!');
        }

        $new_coil->remaining_weight -= $issued_weight_mt;

        // Soft delete if stock zero
        if ($new_coil->remaining_weight <= 0) {
            $new_coil->delete();
        } else {
            $new_coil->save();
        }

        // Update issue record
        $issue->update([
            'coil_id' => $request->coil_id,
            'department_id' => $request->department_id,
            'issued_weight' => $issued_weight_mt,
            'issue_date' => $request->issue_date,
            'issued_by' => $request->issued_by,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Issue updated successfully!');
    }

    // Delete issue entry
    public function delete($id)
    {
        $issue = CoilIssue::findOrFail($id);
        $coil = Coil::withTrashed()->find($issue->coil_id);

        if ($coil) {
            $coil->remaining_weight += $issue->issued_weight;
            $coil->save();
        }

        $issue->delete();

        return back()->with('success', 'Issue record deleted!');
    }

    // PDF Report
    public function reportPdf()
    {
        $issues = CoilIssue::with('coil', 'department')->get();
        $pdf = PDF::loadView('issue.report-pdf', compact('issues'));
        return $pdf->download('coil_issue_report.pdf');
    }
}
