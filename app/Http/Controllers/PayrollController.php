<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\Attendance;
use App\Models\EmployeeAdvance;
use App\Models\Holiday;
use App\Models\Gatepass;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $payrolls = Payroll::with('employee')->where('month', $month)->where('year', $year)->get()->sortBy('employee.employee_code');
        
        return view('hr.payroll.index', compact('payrolls', 'month', 'year'));
    }

    public function generate(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Calculate total days in this specific month
        $totalDaysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        
        // Count Holidays in this month
        $holidayCount = Holiday::whereYear('date', $year)->whereMonth('date', $month)->count();

        // Get all active employees with salary structures
        $employees = Employee::where('status', 'Active')->with('salaryStructure')->get();

        $generatedCount = 0;

        foreach ($employees as $employee) {
            if (!$employee->salaryStructure) continue;

            // Prevent regenerating if already PAID
            $existing = Payroll::where('employee_id', $employee->id)->where('month', $month)->where('year', $year)->first();
            if ($existing && $existing->status == 'Paid') continue;

            $structure = $employee->salaryStructure;

            // 1. Calculate Attendance
            // Count Present and Half Days
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $presentCount = 0;
            $lwpCount = 0; // Unpaid absences

            foreach ($attendances as $att) {
                if ($att->status == 'Present') $presentCount += 1;
                else if ($att->status == 'Half Day') $presentCount += 0.5;
                else if ($att->status == 'Absent') $lwpCount += 1;
                // Note: If they are on 'Leave' but it's a paid leave, we don't deduct.
                // For simplicity, we are assuming 'Absent' is LWP. If you have unpaid leaves, they would add to LWP here.
            }

            // Optional: You could calculate $lwpCount differently based on employee_leaves table, 
            // but for now, we use the attendance register's 'Absent' marking.

            // 1.1 Calculate Lost Time (Lates, Early Exits, Personal Gatepasses)
            $attendanceMetrics = Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->selectRaw('SUM(late_minutes) as total_late, SUM(early_exit_minutes) as total_early')
                ->first();

            $totalLateMinutes = $attendanceMetrics->total_late ?? 0;
            $totalEarlyMinutes = $attendanceMetrics->total_early ?? 0;

            $gatepassMinutes = Gatepass::where('employee_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('type', 'Personal')
                ->where('status', 'Approved')
                ->get()
                ->sum(function($gp) {
                    if ($gp->out_time && $gp->in_time) {
                        return Carbon::parse($gp->out_time)->diffInMinutes(Carbon::parse($gp->in_time));
                    }
                    return 0;
                });

            $totalLostMinutes = $totalLateMinutes + $totalEarlyMinutes + $gatepassMinutes;
            
            // Apply 2-hour (120 minutes) relaxation
            $deductibleMinutes = max(0, $totalLostMinutes - 120);

            $shiftMinutes = 480; // Default 8 hours
            if ($employee->shift) {
                $shiftMinutes = Carbon::parse($employee->shift->start_time)->diffInMinutes(Carbon::parse($employee->shift->end_time));
            }
            $lostTimeDaysDeduction = $shiftMinutes > 0 ? ($deductibleMinutes / $shiftMinutes) : 0;

            // 2. Earnings Calculation
            // Pro-rate based on days
            // Paid Days = (Total Days in Month) - (Unpaid Absences) - (Lost Time Days)
            $paidDays = ($totalDaysInMonth - $lwpCount) - $lostTimeDaysDeduction;
            $workingDaysRatio = $paidDays / $totalDaysInMonth;
            
            $basic = round($structure->basic_salary * $workingDaysRatio);
            $hra = round($structure->hra * $workingDaysRatio);
            $medical = round($structure->medical_allowance * $workingDaysRatio);
            $conveyance = round($structure->conveyance_allowance * $workingDaysRatio);
            $special = round($structure->special_allowance * $workingDaysRatio);
            
            $gross = $basic + $hra + $medical + $conveyance + $special;

            // 3. Deductions Calculation
            $totalFixedGross = ($structure->basic_salary + $structure->hra + $structure->medical_allowance + $structure->conveyance_allowance + $structure->special_allowance);
            
            $pf = 0;
            $esi = 0;
            $employerPf = 0;
            $employerEsi = 0;

            if ($structure->pf_applicable) {
                $pfBase = min($basic, 15000); // Cap base at 15000 for max PF of 1800
                $pf = round($pfBase * 0.12);
                $employerPf = round($pfBase * 0.13); // Employer share is 13%
            }
            
            // ESI: 0.75% on Gross (ONLY if Fixed Gross <= 21000 and applicable)
            $esi = 0;
            $employerEsi = 0;
            if ($structure->esi_applicable && $totalFixedGross <= 21000) {
                $esi = round($gross * 0.0075);
                $employerEsi = round($gross * 0.0325);
            }
            
            // LWF: 0.2% on Gross (if applicable), capped at 35
            $lwf = 0;
            if ($structure->lwf_applicable) {
                $lwf = min(round($gross * 0.002), 35);
            }

            // Advance EMI Deduction: Preserve existing manual entry, or default to 0
            $advanceDeduction = $existing ? $existing->advance_deduction : 0;
            
            // Income Tax: Preserve existing manual entry, or default to 0
            $incomeTax = $existing ? $existing->income_tax : 0;

            // LWP Deduction (Basic / Total Days * LWP Days)
            $lwpDeduction = round(($basic / $totalDaysInMonth) * $lwpCount);

            $totalDeduction = $pf + $esi + $lwf + $advanceDeduction + $incomeTax;
            $netPayable = $gross - $totalDeduction;

            // 4. Save Payroll Record
            $payroll = Payroll::updateOrCreate(
                ['employee_id' => $employee->id, 'month' => $month, 'year' => $year],
                [
                    'total_days' => $totalDaysInMonth,
                    'present_days' => $presentCount,
                    'lwp_days' => $lwpCount,
                    'basic_salary' => $basic,
                    'hra' => $hra,
                    'medical_allowance' => $medical,
                    'conveyance_allowance' => $conveyance,
                    'special_allowance' => $special,
                    'gross_salary' => $gross,
                    'pf_deduction' => $pf,
                    'esi_deduction' => $esi,
                    'lwf_deduction' => $lwf,
                    'employer_pf' => $employerPf,
                    'employer_esi' => $employerEsi,
                    'advance_deduction' => $advanceDeduction,
                    'income_tax' => $incomeTax,
                    'lwp_deduction' => $lwpDeduction,
                    'total_deduction' => $totalDeduction,
                    'net_payable' => $netPayable,
                    'status' => 'Generated'
                ]
            );

            $generatedCount++;
        }

        return redirect()->route('payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Payroll generated successfully for {$generatedCount} employees.");
    }

    public function markAsPaid(Request $request)
    {
        $validated = $request->validate([
            'payroll_ids' => 'required|array',
            'payroll_ids.*' => 'exists:payrolls,id'
        ]);

        $payrolls = Payroll::whereIn('id', $validated['payroll_ids'])->get();

        foreach ($payrolls as $payroll) {
            if ($payroll->status == 'Paid') continue;

            $payroll->update([
                'status' => 'Paid',
                'payment_date' => now()->toDateString()
            ]);

            // Deduct from Employee Advance if applicable
            if ($payroll->advance_deduction > 0) {
                $advance = EmployeeAdvance::where('employee_id', $payroll->employee_id)
                    ->where('status', 'Active')
                    ->orderBy('created_at', 'asc') // Deduct from oldest first
                    ->first();
                
                if ($advance) {
                    $advance->remaining_balance = round($advance->remaining_balance - $payroll->advance_deduction);
                    
                    if ($advance->remaining_balance <= 0) {
                        $advance->status = 'Completed';
                        $advance->remaining_balance = 0;
                    }
                    $advance->save();
                }
            }
        }

        return back()->with('success', 'Selected payrolls marked as Paid and advances updated.');
    }

    public function payslip(Payroll $payroll)
    {
        $payroll->load('employee.department');
        return view('hr.payroll.payslip', compact('payroll'));
    }

    public function reports(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $payrolls = Payroll::where('month', $month)
            ->where('year', $year)
            ->with('employee.department')
            ->get()
            ->sortBy('employee.employee_code');

        return view('hr.payroll.reports', compact('payrolls', 'month', 'year'));
    }

    public function exportPfEcr(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $payrolls = Payroll::where('month', $month)->where('year', $year)->with('employee')->get();

        $content = "";
        foreach ($payrolls as $pr) {
            if ($pr->pf_deduction <= 0) continue;

            $uan = $pr->employee->uan_number ?? 'NOTAVAILABLE';
            $name = strtoupper($pr->employee->first_name . ' ' . $pr->employee->last_name);
            
            // EPF Wages is usually Basic Salary (capped at 15000 for EPS, but we'll use actual for EPF)
            $epfWages = $pr->basic_salary;
            $epsWages = min($pr->basic_salary, 15000); 
            $edliWages = $epsWages;
            
            $eeShare = round($pr->pf_deduction);
            $epsShare = round($epsWages * 0.0833);
            $erShare = $eeShare - $epsShare;
            
            $ncpDays = $pr->total_days - $pr->present_days;

            $line = [
                $uan,
                $name,
                round($pr->gross_salary),
                round($epfWages),
                round($epsWages),
                round($edliWages),
                $eeShare,
                $epsShare,
                $erShare,
                $ncpDays,
                0 // Refund of advances
            ];

            $content .= implode("#~#", $line) . "\r\n";
        }

        $filename = "PF_ECR_{$month}_{$year}.txt";
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function exportEsiCsv(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $payrolls = Payroll::where('month', $month)->where('year', $year)->with('employee')->get();

        $filename = "ESI_Monthly_{$month}_{$year}.csv";
        $handle = fopen('php://temp', 'w');
        
        // ESI CSV doesn't usually have headers for the portal upload, but we'll add them if needed.
        // Standard ESIC format: IP Number, IP Name, Days, Wages, Reason, Last Working Day
        foreach ($payrolls as $pr) {
            if ($pr->esi_deduction <= 0) continue;

            fputcsv($handle, [
                $pr->employee->esi_number ?? '',
                strtoupper($pr->employee->first_name . ' ' . $pr->employee->last_name),
                $pr->present_days,
                round($pr->gross_salary),
                0, // Reason for 0 days
                '' // Last working day
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function exportBankStatement(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $payrolls = Payroll::where('month', $month)->where('year', $year)->with('employee')->get();

        $filename = "Bank_Transfer_{$month}_{$year}.csv";
        $handle = fopen('php://temp', 'w');
        
        fputcsv($handle, ['Employee Code', 'Employee Name', 'Bank Name', 'Account Number', 'IFSC Code', 'Net Payable']);

        foreach ($payrolls as $pr) {
            fputcsv($handle, [
                $pr->employee->employee_code,
                $pr->employee->first_name . ' ' . $pr->employee->last_name,
                $pr->employee->bank_name,
                $pr->employee->bank_account,
                $pr->employee->ifsc_code,
                $pr->net_payable
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function hourlyIndex(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $employees = Employee::where('status', 'Active')->with('salaryStructure')->orderBy('employee_code')->get();
        $payrolls = Payroll::where('month', $month)->where('year', $year)->get()->keyBy('employee_id');

        return view('hr.payroll.hourly', compact('employees', 'payrolls', 'month', 'year'));
    }

    public function hourlyStore(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required',
            'year' => 'required',
            'days' => 'nullable|array',
            'days.*' => 'nullable|numeric|min:0',
            'hours' => 'nullable|array',
            'hours.*' => 'nullable|numeric|min:0',
            'advances' => 'nullable|array',
            'advances.*' => 'nullable|numeric|min:0',
            'income_tax' => 'nullable|array',
            'income_tax.*' => 'nullable|numeric|min:0',
        ]);

        $month = $validated['month'];
        $year = $validated['year'];
        $generatedCount = 0;

        // Merge employees from both days and hours input
        $empIds = array_unique(array_merge(
            array_keys($validated['days'] ?? []), 
            array_keys($validated['hours'] ?? []),
            array_keys($validated['advances'] ?? []),
            array_keys($validated['income_tax'] ?? [])
        ));

        foreach ($empIds as $empId) {
            $days = $validated['days'][$empId] ?? 0;
            $hours = $validated['hours'][$empId] ?? 0;
            $advance = $validated['advances'][$empId] ?? 0;
            $incomeTax = $validated['income_tax'][$empId] ?? 0;

            if ($days <= 0 && $hours <= 0 && $advance <= 0 && $incomeTax <= 0) continue;

            $employee = Employee::find($empId);
            if (!$employee || !$employee->salaryStructure) continue;

            $structure = $employee->salaryStructure;
            
            // Dynamic Rate based on total days in this specific month
            $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $monthlyGross = ($structure->basic_salary + $structure->hra + $structure->medical_allowance + $structure->conveyance_allowance + $structure->special_allowance);
            
            // Hourly Rate is strictly calculated as: Monthly Gross / (Days in Month * 8)
            $hourlyRate = ($daysInMonth > 0) ? ($monthlyGross / ($daysInMonth * 8)) : 0;

            // Prevention: If already paid, skip
            $existing = Payroll::where('employee_id', $empId)->where('month', $month)->where('year', $year)->first();
            if ($existing && $existing->status == 'Paid') continue;

            // Calculate Earnings: Gross Monthly Salary * (Days Worked + (Hours Worked / 8)) / Days in Month
            // This is mathematically same as (Days * 8 + Hours) * HourlyRate
            $totalHours = ($days * 8) + $hours;
            $earnedGross = round($totalHours * $hourlyRate);
            
            // Pro-rate components based on salary structure to ensure PF is on Basic only
            $totalFixedGross = ($structure->basic_salary + $structure->hra + $structure->medical_allowance + $structure->conveyance_allowance + $structure->special_allowance);
            $ratio = $totalFixedGross > 0 ? ($earnedGross / $totalFixedGross) : 0;

            $basic = round($structure->basic_salary * $ratio);
            $hra = round($structure->hra * $ratio);
            $medical = round($structure->medical_allowance * $ratio);
            $conveyance = round($structure->conveyance_allowance * $ratio);
            $special = round($structure->special_allowance * $ratio);

            // Recalculate gross to avoid rounding drift
            $earnedGross = $basic + $hra + $medical + $conveyance + $special;

            // Deductions
            $pf = 0;
            $employerPf = 0;
            if ($structure->pf_applicable) {
                // PF always on Basic, capped at 15000 for max PF of 1800
                $pfBase = min($basic, 15000);
                $pf = round($pfBase * 0.12);
                $employerPf = round($pfBase * 0.13); // Employer share is 13%
            }

            $esi = 0;
            $employerEsi = 0;
            if ($structure->esi_applicable && $totalFixedGross <= 21000) {
                // ESI always on Gross
                $esi = round($earnedGross * 0.0075);
                $employerEsi = round($earnedGross * 0.0325);
            }

            $lwf = 0;
            if ($structure->lwf_applicable) {
                // LWF always on Gross, capped at 35
                $lwf = min(round($earnedGross * 0.002), 35);
            }

            $advanceDeduction = round($validated['advances'][$empId] ?? 0);
            $incomeTax = round($validated['income_tax'][$empId] ?? 0);

            $totalDeduction = round($pf + $esi + $lwf + $advanceDeduction + $incomeTax);
            $netPayable = round($earnedGross - $totalDeduction);

            Payroll::updateOrCreate(
                ['employee_id' => $empId, 'month' => $month, 'year' => $year],
                [
                    'total_days' => $daysInMonth, 
                    'present_days' => $days,
                    'worked_hours' => $hours,
                    'basic_salary' => $basic,
                    'hra' => $hra,
                    'medical_allowance' => $medical,
                    'conveyance_allowance' => $conveyance,
                    'special_allowance' => $special,
                    'gross_salary' => $earnedGross,
                    'pf_deduction' => $pf,
                    'esi_deduction' => $esi,
                    'lwf_deduction' => $lwf,
                    'employer_pf' => $employerPf,
                    'employer_esi' => $employerEsi,
                    'advance_deduction' => $advanceDeduction,
                    'income_tax' => $incomeTax,
                    'lwp_deduction' => 0,

                    'total_deduction' => $totalDeduction,
                    'net_payable' => $netPayable,
                    'status' => 'Generated'
                ]
            );

            $generatedCount++;
        }

        return redirect()->route('payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Mixed payroll generated for {$generatedCount} employees.");
    }

    public function exportPdfReport(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $payrolls = Payroll::with(['employee.department'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $pdf = Pdf::loadView('hr.payroll.pdf_report', compact('payrolls', 'month', 'year'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->download("Payroll_Report_{$month}_{$year}.pdf");
    }
}
