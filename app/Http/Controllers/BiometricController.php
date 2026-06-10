<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BiometricLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BiometricController extends Controller
{
    public function index()
    {
        $recentLogs = BiometricLog::with('employee')->orderBy('punch_time', 'desc')->paginate(20);
        return view('hr.biometric.index', compact('recentLogs'));
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header if exists
        $header = fgetcsv($handle); 
        
        $count = 0;
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Assuming CSV format: EmployeeCode, PunchTime (YYYY-MM-DD HH:mm:ss), DeviceID
            if (count($data) < 2) continue;

            BiometricLog::create([
                'employee_code' => $data[0],
                'punch_time' => Carbon::parse($data[1]),
                'device_id' => $data[2] ?? 'CSV_IMPORT',
            ]);
            $count++;
        }
        fclose($handle);

        return back()->with('success', "$count logs imported successfully.");
    }

    public function process(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', Carbon::today()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', Carbon::today()->toDateString()));

        $employees = Employee::where('status', 'Active')->with('shift')->get();

        $processedCount = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();

            foreach ($employees as $employee) {
                $logs = BiometricLog::where('employee_code', $employee->employee_code)
                    ->whereDate('punch_time', $dateString)
                    ->orderBy('punch_time', 'asc')
                    ->get();

                if ($logs->isEmpty()) {
                    // Check if already marked as Leave/Holiday - if not, we can mark absent
                    // But for now, we just skip or mark absent
                    continue; 
                }

                $punchIn = $logs->first()->punch_time;
                $punchOut = $logs->count() > 1 ? $logs->last()->punch_time : null;

                $status = 'Present';
                $lateMinutes = 0;
                $earlyExitMinutes = 0;
                $otHours = 0;

                if ($employee->shift) {
                    $shiftStart = Carbon::parse($dateString . ' ' . $employee->shift->start_time);
                    $shiftEnd = Carbon::parse($dateString . ' ' . $employee->shift->end_time);

                    // Late In Calculation
                    if ($punchIn->gt($shiftStart)) {
                        $diff = $punchIn->diffInMinutes($shiftStart);
                        if ($diff > ($employee->shift->grace_period ?? 15)) {
                            $lateMinutes = $diff;
                        }
                    }

                    // Early Exit & OT Calculation (Only if punch out exists)
                    if ($punchOut) {
                        if ($punchOut->lt($shiftEnd)) {
                            $earlyExitMinutes = $shiftEnd->diffInMinutes($punchOut);
                        }

                        // OT Calculation
                        $totalWorkMinutes = $punchOut->diffInMinutes($punchIn);
                        $shiftDuration = $shiftEnd->diffInMinutes($shiftStart);
                        
                        if ($totalWorkMinutes > $shiftDuration) {
                            $extraMinutes = $totalWorkMinutes - $shiftDuration;
                            if ($extraMinutes >= ($employee->shift->min_ot_minutes ?? 60)) {
                                $otHours = round($extraMinutes / 60, 2);
                            }
                        }
                    }
                }

                Attendance::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $dateString],
                    [
                        'status' => $status,
                        'punch_in' => $punchIn->format('H:i'),
                        'punch_out' => $punchOut ? $punchOut->format('H:i') : null,
                        'late_minutes' => $lateMinutes,
                        'early_exit_minutes' => $earlyExitMinutes,
                        'ot_hours' => $otHours,
                        'is_auto_calculated' => true,
                    ]
                );
                $processedCount++;
            }
        }

        return back()->with('success', "Attendance processed for $processedCount employee-days.");
    }
}
