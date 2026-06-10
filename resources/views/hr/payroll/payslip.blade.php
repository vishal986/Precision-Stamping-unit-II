<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->first_name }} - {{ date('F Y', mktime(0, 0, 0, $payroll->month, 10, $payroll->year)) }}</title>
    <style>
        :root {
            --primary: #8b5cf6;
            --text: #1f2937;
            --border: #e5e7eb;
            --bg-light: #f9fafb;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text);
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .payslip-container {
            width: 100%;
            max-width: 210mm; /* A4 Width */
            margin: 0 auto;
            padding: 20mm;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-info h1 {
            margin: 0;
            font-size: 20px;
            color: #000;
            text-transform: uppercase;
        }
        .company-info p {
            margin: 2px 0 0;
            color: #000;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .payslip-title {
            text-align: right;
        }
        .payslip-title h2 {
            margin: 0;
            font-size: 20px;
            color: #000;
        }
        .info-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-section {
            flex: 1;
        }
        .info-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 13px;
        }
        .info-label {
            color: #4b5563;
        }
        .info-value {
            font-weight: 600;
        }
        .summary-table th, .summary-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #000;
            font-size: 13px;
        }
        /* Vertical line for Earnings Table: Between RATE and EARNED */
        .earnings-table th:nth-child(2), 
        .earnings-table td:nth-child(2) {
            border-right: 1px solid #000;
        }
        /* Vertical line for Deductions Table: Between Deduction and Amount */
        .deductions-table th:first-child, 
        .deductions-table td:first-child {
            border-right: 1px solid #000;
        }
        .summary-table {
            border: 1px solid #000;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table th {
            background: #f3f4f6;
            text-transform: uppercase;
            font-size: 12px;
        }
        .amount-col {
            text-align: right;
            width: 100px;
        }
        .split-tables {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .split-tables > div {
            flex: 1;
        }
        .total-row {
            font-weight: bold;
            background: #f3f4f6;
        }
        .net-payable {
            border: 2px solid #000;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .net-payable h2 {
            margin: 0;
            font-size: 16px;
        }
        .net-payable .amount {
            font-size: 24px;
            font-weight: 800;
        }
        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body { padding: 0; }
            .payslip-container { border: none; box-shadow: none; padding: 15mm; }
            .btn-print-group { display: none !important; }
            .no-print { display: none !important; }
            .net-payable { border: 2px solid #000 !important; -webkit-print-color-adjust: exact; }
        }
        .btn-print-group .btn-print {
            background: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="btn-print-group" style="position: fixed; top: 20px; right: 20px; display: flex; flex-direction: column; gap: 10px; z-index: 1000;">
        <label style="background: #f3f4f6; padding: 10px; border-radius: 6px; border: 1px solid #ccc; cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600;">
            <input type="checkbox" id="toggleEmployer" onchange="toggleEmployerSection()"> Show Employer Contribution
        </label>
        <button class="btn-print" onclick="window.print()" style="position: static; width: 100%;">Print Payslip</button>
    </div>

    <div class="payslip-container">
        <div class="header">
            <div class="company-info">
                <h1>Precision Stampings UNIT-II</h1>
                <p>PLOT NO. 71, SECTOR-25, FARIDABAD</p>
            </div>
            <div class="payslip-title">
                <h2>PAYSLIP</h2>
                <p>{{ date('F Y', mktime(0, 0, 0, $payroll->month, 10, $payroll->year)) }}</p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-section">
                <h3>Employee Details</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Employee Code:</span>
                    <span class="info-value">{{ $payroll->employee->employee_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Department:</span>
                    <span class="info-value">{{ $payroll->employee->department->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Designation:</span>
                    <span class="info-value">{{ $payroll->employee->designation ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-section">
                <h3>Attendance Summary</h3>
                <div class="info-row">
                    <span class="info-label">Total Days in Month:</span>
                    <span class="info-value">{{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->daysInMonth }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Days Worked:</span>
                    <span class="info-value">{{ floatval($payroll->present_days) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Hours:</span>
                    <span class="info-value">{{ floatval($payroll->worked_hours) }} hrs</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ $payroll->status }}</span>
                </div>
            </div>
        </div>

        <div class="split-tables">
            <div>
                @php 
                    $struct = $payroll->employee->salaryStructure; 
                @endphp
                <table class="summary-table earnings-table">
                    <thead>
                        <tr>
                            <th>SALARY HEAD</th>
                            <th class="amount-col">RATE</th>
                            <th class="amount-col">Earned</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="amount-col">{{ number_format($struct->basic_salary ?? 0, 0) }}</td>
                            <td class="amount-col">{{ number_format($payroll->basic_salary, 0) }}</td>
                        </tr>
                        <tr>
                            <td>HRA</td>
                            <td class="amount-col">{{ number_format($struct->hra ?? 0, 0) }}</td>
                            <td class="amount-col">{{ number_format($payroll->hra, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Medical Allowance</td>
                            <td class="amount-col">{{ number_format($struct->medical_allowance ?? 0, 0) }}</td>
                            <td class="amount-col">{{ number_format($payroll->medical_allowance, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Conveyance Allowance</td>
                            <td class="amount-col">{{ number_format($struct->conveyance_allowance ?? 0, 0) }}</td>
                            <td class="amount-col">{{ number_format($payroll->conveyance_allowance, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Special Allowance</td>
                            <td class="amount-col">{{ number_format($struct->special_allowance ?? 0, 0) }}</td>
                            <td class="amount-col">{{ number_format($payroll->special_allowance, 0) }}</td>
                        </tr>
                        @php
                            $actualGross = ($struct->basic_salary ?? 0) + ($struct->hra ?? 0) + ($struct->medical_allowance ?? 0) + ($struct->conveyance_allowance ?? 0) + ($struct->special_allowance ?? 0);
                        @endphp
                        <tr class="total-row">
                            <td>TOTAL SALARY</td>
                            <td class="amount-col">{{ number_format($actualGross, 0) }}</td>
                            <td class="amount-col"></td>
                        </tr>
                        <tr class="total-row">
                            <td>TOTAL EARNING</td>
                            <td class="amount-col"></td>
                            <td class="amount-col">{{ number_format($payroll->gross_salary, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table class="summary-table deductions-table">
                    <thead>
                        <tr>
                            <th>Deductions</th>
                            <th class="amount-col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Provident Fund (PF)</td>
                            <td class="amount-col">{{ number_format($payroll->pf_deduction, 0) }}</td>
                        </tr>
                        <tr>
                            <td>ESIC</td>
                            <td class="amount-col">{{ number_format($payroll->esi_deduction, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Advance</td>
                            <td class="amount-col">{{ number_format($payroll->advance_deduction, 0) }}</td>
                        </tr>
                        <tr>
                            <td>LWF</td>
                            <td class="amount-col">{{ number_format($payroll->lwf_deduction, 0) }}</td>
                        </tr>

                        <tr class="total-row">
                            <td>Total Deductions</td>
                            <td class="amount-col">{{ number_format($payroll->total_deduction, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($payroll->employer_pf > 0 || $payroll->employer_esi > 0)
        <div class="info-section" id="employerSection" style="display: none; margin-top: 20px;">
            <h3>Employer Contributions (CTC Components)</h3>
            <div style="display: flex; gap: 20px;">
                <div class="info-row" style="flex: 1;">
                    <span class="info-label">Employer PF:</span>
                    <span class="info-value">{{ number_format($payroll->employer_pf, 0) }}</span>
                </div>
                <div class="info-row" style="flex: 1;">
                    <span class="info-label">Employer ESI:</span>
                    <span class="info-value">{{ number_format($payroll->employer_esi, 0) }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="net-payable">
            <div>
                <h2>Net Payable</h2>
                <p style="margin: 5px 0 0; font-size: 13px; color: #4b5563;">Transferable to Bank Account</p>
            </div>
            <div class="amount">{{ number_format($payroll->net_payable, 0) }}</div>
        </div>

        <div class="footer">
            <p>This is a computer-generated payslip and does not require a physical signature.</p>
            <p>&copy; {{ date('Y') }} Precision Stampings. All Rights Reserved.</p>
        </div>
    </div>

    <script>
        function toggleEmployerSection() {
            const section = document.getElementById('employerSection');
            const checkbox = document.getElementById('toggleEmployer');
            if (section) {
                section.style.display = checkbox.checked ? 'block' : 'none';
            }
        }
    </script>
</body>
</html>
