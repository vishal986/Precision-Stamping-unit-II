@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Monthly Payroll Processing</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Generate payroll, view payslips, and mark salaries as paid.</p>
    </div>
    
    <div style="display: flex; gap: 1rem; align-items: center;">
        <form action="{{ route('payroll.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
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

        <form action="{{ route('payroll.generate') }}" method="POST">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Generate payroll for {{ date('F', mktime(0,0,0,$month,10)) }} {{ $year }}? This will recalculate any Draft/Generated records.');">
                <i class="fa-solid fa-gears"></i> Generate Auto Payroll
            </button>
        </form>
        <a href="{{ route('payroll.hourly.index', ['month' => $month, 'year' => $year]) }}" class="btn" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa; border: 1px solid #a78bfa;">
            <i class="fa-solid fa-clock"></i> Hourly Payroll
        </a>
    </div>
</div>

<div class="card" style="margin-bottom: 1rem; padding: 1rem; display: flex; gap: 1rem; align-items: center; flex-direction: row;">
    <i class="fa-solid fa-search" style="color: var(--text-secondary);"></i>
    <input type="text" id="searchInput" class="form-control" placeholder="Search employees..." onkeyup="filterTable()" style="max-width: 300px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
</div>

<div class="card">
    <form action="{{ route('payroll.markPaid') }}" method="POST" id="markPaidForm">
        @csrf
        
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: var(--primary-color);">Salary Register: {{ date('F', mktime(0,0,0,$month,10)) }} {{ $year }}</h3>
            <button type="submit" class="btn" style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); border: 1px solid var(--success-color);" onclick="return confirm('Mark selected as Paid? This will deduct EMI from active advances.');">
                <i class="fa-solid fa-check-double"></i> Mark Selected as Paid
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="table" id="dataTable" style="white-space: nowrap;">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                        <th>Emp Code</th>
                        <th>Employee Name</th>
                        <th>Days (W/P)</th>
                        <th>Basic</th>
                        <th>HRA</th>
                        <th>Medical</th>
                        <th>Conveyance</th>
                        <th>Special</th>
                        <th>Gross Earned</th>
                        <th>PF (Emp)</th>
                        <th>ESI (Emp)</th>
                        <th>LWF (Emp)</th>
                        <th>PF (Comp)</th>
                        <th>ESI (Comp)</th>
                        <th>Advance EMI</th>
                        <th>Income Tax</th>
                        <th>Net Payable</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalGross = 0;
                        $totalNet = 0; 
                    @endphp
                    @forelse($payrolls as $pr)
                        @php 
                            $totalGross += $pr->gross_salary;
                            $totalNet += $pr->net_payable;
                        @endphp
                    <tr>
                        <td>
                            @if($pr->status !== 'Paid')
                                <input type="checkbox" name="payroll_ids[]" value="{{ $pr->id }}" class="row-checkbox">
                            @endif
                        </td>
                        <td style="font-weight: 600;">{{ $pr->employee->employee_code }}</td>
                        <td>{{ $pr->employee->first_name }} {{ $pr->employee->last_name }}</td>
                        <td>
                            @php
                                $displayTotalDays = floatval($pr->total_days);
                                if ($displayTotalDays <= 0) {
                                    $displayTotalDays = \Carbon\Carbon::createFromDate($pr->year, $pr->month, 1)->daysInMonth;
                                }
                            @endphp
                            {{ $displayTotalDays }} / 
                            <span style="color: var(--success-color); font-weight: bold;">{{ floatval($pr->present_days) }}d</span>
                            @if($pr->worked_hours > 0)
                                <small style="color: #a78bfa;">+{{ floatval($pr->worked_hours) }}h</small>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->basic_salary, 0) }}</td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->hra, 0) }}</td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->medical_allowance, 0) }}</td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->conveyance_allowance, 0) }}</td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->special_allowance, 0) }}</td>
                        <td style="color: #fff; font-weight: 600;">₹{{ number_format($pr->gross_salary, 0) }}</td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->pf_deduction, 0) }}</td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->esi_deduction, 0) }}</td>
                        <td style="color: var(--text-secondary);">₹{{ number_format($pr->lwf_deduction, 0) }}</td>
                        <td style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">₹{{ number_format($pr->employer_pf, 0) }}</td>
                        <td style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">₹{{ number_format($pr->employer_esi, 0) }}</td>
                        <td style="color: var(--danger-color);">₹{{ number_format($pr->advance_deduction, 0) }}</td>
                        <td style="color: var(--danger-color);">₹{{ number_format($pr->income_tax ?? 0, 0) }}</td>
                        <td style="font-weight: bold; color: var(--primary-color); font-size: 1.1rem;">₹{{ number_format($pr->net_payable, 0) }}</td>
                        <td>
                            @if($pr->status == 'Draft' || $pr->status == 'Generated')
                                <span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Generated</span>
                            @else
                                <span style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Paid</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('payroll.payslip', $pr) }}" target="_blank" class="btn" style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.25rem 0.5rem;" title="View Payslip">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="20" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No payroll records generated for this month. Click Generate Payroll.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($payrolls->count() > 0)
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align: right; border-top: 2px solid rgba(255,255,255,0.1);">Totals:</th>
                        <th colspan="5" style="border-top: 2px solid rgba(255,255,255,0.1);"></th>
                        <th style="border-top: 2px solid rgba(255,255,255,0.1); color: #fff;">₹{{ number_format($totalGross, 0) }}</th>
                        <th colspan="5" style="border-top: 2px solid rgba(255,255,255,0.1);"></th>
                        <th style="border-top: 2px solid rgba(255,255,255,0.1); color: var(--primary-color); font-size: 1.1rem;">₹{{ number_format($totalNet, 0) }}</th>
                        <th colspan="2" style="border-top: 2px solid rgba(255,255,255,0.1);"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </form>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
        });
    });

    function filterTable() {
        const filter = document.getElementById("searchInput").value.toLowerCase();
        const tr = document.getElementById("dataTable").getElementsByTagName("tbody")[0].getElementsByTagName("tr");
        for (let i = 0; i < tr.length; i++) {
            if (tr[i].getElementsByTagName("td").length < 2) continue;
            let text = tr[i].textContent || tr[i].innerText;
            tr[i].style.display = text.toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
</script>
@endsection
