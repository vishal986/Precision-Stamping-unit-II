<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Report - {{ date('F', mktime(0,0,0,$month,10)) }} {{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 0; padding: 0; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Precision Stampings - Payroll Report ({{ date('F', mktime(0,0,0,$month,10)) }} {{ $year }})</h2>
    
    <table>
        <thead>
            <tr>
                <th>Emp Code</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Basic</th>
                <th>HRA</th>
                <th>Gross</th>
                <th>PF</th>
                <th>ESI</th>
                <th>LWF</th>
                <th>Advance</th>
                <th>Income Tax</th>
                <th>Total Deductions</th>
                <th>Net Payable</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBasic = 0;
                $totalHra = 0;
                $totalGross = 0;
                $totalPf = 0;
                $totalEsi = 0;
                $totalLwf = 0;
                $totalAdvance = 0;
                $totalIncomeTax = 0;
                $totalDeductions = 0;
                $totalNet = 0;
            @endphp
            @foreach($payrolls as $pr)
                @php
                    $totalBasic += $pr->basic_salary;
                    $totalHra += $pr->hra;
                    $totalGross += $pr->gross_salary;
                    $totalPf += $pr->pf_deduction;
                    $totalEsi += $pr->esi_deduction;
                    $totalLwf += $pr->lwf_deduction;
                    $totalAdvance += $pr->advance_deduction;
                    $totalIncomeTax += $pr->income_tax;
                    $totalDeductions += $pr->total_deduction;
                    $totalNet += $pr->net_payable;
                @endphp
                <tr>
                    <td>{{ $pr->employee->employee_code ?? '' }}</td>
                    <td>{{ $pr->employee->first_name ?? '' }} {{ $pr->employee->last_name ?? '' }}</td>
                    <td>{{ $pr->employee->department->name ?? '' }}</td>
                    <td class="text-right">{{ number_format($pr->basic_salary, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->hra, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->gross_salary, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->pf_deduction, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->esi_deduction, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->lwf_deduction, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->advance_deduction, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->income_tax, 2) }}</td>
                    <td class="text-right">{{ number_format($pr->total_deduction, 2) }}</td>
                    <td class="text-right font-bold">{{ number_format($pr->net_payable, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-bold">
                <td colspan="3" class="text-right">Grand Total:</td>
                <td class="text-right">{{ number_format($totalBasic, 2) }}</td>
                <td class="text-right">{{ number_format($totalHra, 2) }}</td>
                <td class="text-right">{{ number_format($totalGross, 2) }}</td>
                <td class="text-right">{{ number_format($totalPf, 2) }}</td>
                <td class="text-right">{{ number_format($totalEsi, 2) }}</td>
                <td class="text-right">{{ number_format($totalLwf, 2) }}</td>
                <td class="text-right">{{ number_format($totalAdvance, 2) }}</td>
                <td class="text-right">{{ number_format($totalIncomeTax, 2) }}</td>
                <td class="text-right">{{ number_format($totalDeductions, 2) }}</td>
                <td class="text-right">{{ number_format($totalNet, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
