@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Payroll Reports & Statutory Compliance</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Generate ECR files, Bank statements, and Monthly summaries.</p>
    </div>
    
    <div style="display: flex; gap: 1rem; align-items: center;">
        <form action="{{ route('payroll.reports') }}" method="GET" style="display: flex; gap: 0.5rem;">
            <select name="month" class="form-control" style="width: auto;" onchange="this.form.submit()">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-control" style="width: auto;" onchange="this.form.submit()">
                @for($y=date('Y')-1; $y<=date('Y')+1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
    <!-- Statutory Exports -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);"><i class="fa-solid fa-building-columns"></i> Statutory Exports</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0;">PF ECR Text File</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0.2rem 0 0 0;">Format: UAN#~#Name... (For EPFO Portal)</p>
                </div>
                <a href="{{ route('payroll.reports.pf_ecr', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa;">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>

            <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0;">ESI Monthly CSV</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0.2rem 0 0 0;">Format: IP#~#Name... (For ESIC Portal)</p>
                </div>
                <a href="{{ route('payroll.reports.esi_csv', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm" style="background: rgba(16, 185, 129, 0.2); color: var(--success-color);">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Financial Reports -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: #ec4899;"><i class="fa-solid fa-file-invoice-dollar"></i> Financial Reports</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0;">Bank Transfer Statement</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0.2rem 0 0 0;">CSV for bulk bank processing</p>
                </div>
                <a href="{{ route('payroll.reports.bank_statement', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>

            <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0;">Salary Register (Excel)</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0.2rem 0 0 0;">Complete breakdown for accounting</p>
                </div>
                <button class="btn btn-sm" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;" onclick="exportTableToExcel()">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </div>

            <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0;">Monthly Payroll Report (PDF)</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0.2rem 0 0 0;">Detailed employee-wise payroll PDF</p>
                </div>
                <a href="{{ route('payroll.reports.pdf', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
        Department-wise Summary: {{ date('F', mktime(0,0,0,$month,10)) }} {{ $year }}
    </h3>
    
    <table class="table" id="summaryTable">
        <thead>
            <tr>
                <th>Department</th>
                <th>Headcount</th>
                <th>Total Basic</th>
                <th>Total Gross</th>
                <th>Employer PF (3.67%+8.33%)</th>
                <th>Employer ESI (3.25%)</th>
                <th>Total Income Tax</th>
                <th>Total CTC</th>
                <th>Total Net Payable</th>
            </tr>
        </thead>
        <tbody>
            @php
                $deptSummary = $payrolls->groupBy(function($pr) {
                    return $pr->employee->department->name ?? 'Unassigned';
                });
                
                $grandTotalCTC = 0;
                $grandTotalNet = 0;
            @endphp
            
            @forelse($deptSummary as $deptName => $items)
                @php
                    $deptBasic = $items->sum('basic_salary');
                    $deptGross = $items->sum('gross_salary');
                    $deptEmployerPF = $items->sum('employer_pf');
                    $deptEmployerESI = $items->sum('employer_esi');
                    $deptIncomeTax = $items->sum('income_tax');
                    $deptNet = $items->sum('net_payable');
                    $deptCTC = $deptGross + $deptEmployerPF + $deptEmployerESI;
                    
                    $grandTotalCTC += $deptCTC;
                    $grandTotalNet += $deptNet;
                @endphp
                <tr>
                    <td style="font-weight: 600;">{{ $deptName }}</td>
                    <td>{{ $items->count() }}</td>
                    <td>₹{{ number_format($deptBasic, 2) }}</td>
                    <td>₹{{ number_format($deptGross, 2) }}</td>
                    <td>₹{{ number_format($deptEmployerPF, 2) }}</td>
                    <td>₹{{ number_format($deptEmployerESI, 2) }}</td>
                    <td>₹{{ number_format($deptIncomeTax, 2) }}</td>
                    <td style="font-weight: 600; color: var(--primary-color);">₹{{ number_format($deptCTC, 2) }}</td>
                    <td style="font-weight: 600; color: var(--success-color);">₹{{ number_format($deptNet, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No data available for this month.</td>
                </tr>
            @endforelse
        </tbody>
        @if($payrolls->count() > 0)
        <tfoot>
            <tr style="border-top: 2px solid rgba(255,255,255,0.1);">
                <th colspan="7" style="text-align: right;">Grand Totals:</th>
                <th style="color: var(--primary-color); font-size: 1.1rem;">₹{{ number_format($grandTotalCTC, 2) }}</th>
                <th style="color: var(--success-color); font-size: 1.1rem;">₹{{ number_format($grandTotalNet, 2) }}</th>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<script>
    function exportTableToExcel() {
        let table = document.getElementById("summaryTable");
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let link = document.createElement("a");
        link.download = "Salary_Summary_{{ $month }}_{{ $year }}.xls";
        link.href = url;
        link.click();
    }
</script>
@endsection
