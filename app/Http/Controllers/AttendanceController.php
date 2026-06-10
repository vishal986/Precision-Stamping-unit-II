<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Gatepass;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $employees = Employee::where('status', 'Active')->with(['shift', 'department'])->get();
        
        // Fetch existing attendance for this date
        $attendances = Attendance::where('date', $date)->get()->keyBy('employee_id');

        // Fetch gatepasses for this date
        $gatepasses = Gatepass::where('date', $date)
            ->where('status', 'Approved')
            ->get()
            ->groupBy('employee_id');

        $holiday = Holiday::where('date', $date)->first();

        return view('hr.attendance.index', compact('employees', 'attendances', 'gatepasses', 'date', 'holiday'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Present,Absent,Half Day,Leave',
            'attendance.*.punch_in' => 'nullable|date_format:H:i',
            'attendance.*.punch_out' => 'nullable|date_format:H:i',
            'attendance.*.ot_hours' => 'nullable|numeric|min:0',
        ]);

        $date = $validated['date'];

        foreach ($validated['attendance'] as $employeeId => $data) {
            Attendance::updateOrCreate(
                ['employee_id' => $employeeId, 'date' => $date],
                [
                    'status' => $data['status'],
                    'punch_in' => $data['punch_in'] ?? null,
                    'punch_out' => $data['punch_out'] ?? null,
                    'ot_hours' => $data['ot_hours'] ?? 0,
                ]
            );
        }

        return redirect()->route('attendance.index', ['date' => $date])->with('success', 'Attendance records saved successfully.');
    }
}
