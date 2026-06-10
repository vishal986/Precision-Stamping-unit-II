@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Hourly Payroll Entry</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Enter total hours worked for employees for the selected month.</p>
    </div>
    
    <div style="display: flex; gap: 1rem; align-items: center;">
        <form action="{{ route('payroll.hourly.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
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
        <a href="{{ route('payroll.index', ['month' => $month, 'year' => $year]) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Register
        </a>
    </div>
</div>

<div class="card">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fa-solid fa-search" style="color: var(--text-secondary);"></i>
        <input type="text" id="employeeSearch" class="form-control" style="max-width: 300px;" placeholder="Search by Employee Code or Name..." onkeyup="filterEmployees()">
    </div>
    <form action="{{ route('payroll.hourly.store') }}" method="POST">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        
        <table class="table">
            <thead>
                <tr>
                    <th>Emp Code</th>
                    <th>Employee Name</th>
                    <th>Daily/Hourly Rate</th>
                    <th>Enter Days</th>
                    <th>Extra Hours</th>
                    <th>Advance</th>
                    <th>Income Tax</th>
                    <th style="width: 150px;">Estimated Net</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    @php
                        $pr = $payrolls->get($employee->id);
                        $struct = $employee->salaryStructure;
                        
                        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
                        $hoursInMonth = $daysInMonth * 8;
                        
                        $rate = 0;
                        if ($struct) {
                            $monthlyGross = $struct->basic_salary + $struct->hra + $struct->medical_allowance + $struct->conveyance_allowance + $struct->special_allowance;
                            $rate = $hoursInMonth > 0 ? round($monthlyGross / $hoursInMonth, 2) : 0;
                        }
                        $dailyRate = round($rate * 8, 2);
                    @endphp
                <tr>
                    <td style="font-weight: 600;">{{ $employee->employee_code }}</td>
                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <span class="rate-val">Day: ₹{{ number_format($dailyRate, 2) }}</span>
                            <span style="font-size: 0.8rem; color: var(--text-secondary);">Hour: ₹{{ number_format($rate, 2) }}</span>
                        </div>
                    </td>
                    <td>
                        <input type="number" step="0.5" name="days[{{ $employee->id }}]" class="form-control day-input" style="width: 100px;" 
                               placeholder="Days" 
                               value="{{ $pr && $pr->present_days > 0 ? floatval($pr->present_days) : '' }}"
                               onkeyup="updateEstimate(this, {{ $rate }})"
                               {{ $pr && $pr->status == 'Paid' ? 'disabled' : '' }}>
                    </td>
                    <td>
                        <input type="number" step="0.5" name="hours[{{ $employee->id }}]" class="form-control hours-input" style="width: 100px;" 
                               value="{{ $pr && $pr->worked_hours > 0 ? floatval($pr->worked_hours) : '' }}" placeholder="0" onkeyup="updateEstimate(this, {{ $rate }})">
                    </td>
                    <td>
                        <input type="number" name="advances[{{ $employee->id }}]" class="form-control advance-input" style="width: 100px;" 
                               value="{{ $pr && $pr->advance_deduction > 0 ? floatval($pr->advance_deduction) : '' }}" placeholder="0" onkeyup="updateEstimate(this, {{ $rate }})">
                    </td>
                    <td>
                        <input type="number" name="income_tax[{{ $employee->id }}]" class="form-control income-tax-input" style="width: 100px;" 
                               value="{{ $pr && $pr->income_tax > 0 ? floatval($pr->income_tax) : '' }}" placeholder="0" onkeyup="updateEstimate(this, {{ $rate }})">
                    </td>
                    <td>
                        <div class="estimate-display" style="font-weight: bold; color: var(--primary-color);">₹0.00</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No active employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($employees->count() > 0)
        <div style="text-align: right; margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                <i class="fa-solid fa-calculator"></i> Generate Hourly Payroll
            </button>
        </div>
        @endif
    </form>
</div>

<script>
    function updateEstimate(element, hourlyRate) {
        const row = element.closest('tr');
        const days = parseFloat(row.querySelector('.day-input').value) || 0;
        const hours = parseFloat(row.querySelector('.hours-input').value) || 0;
        const advance = parseFloat(row.querySelector('.advance-input').value) || 0;
        const incomeTax = parseFloat(row.querySelector('.income-tax-input').value) || 0;
        
        const totalHours = (days * 8) + hours;
        const gross = totalHours * hourlyRate;
        
        const net = Math.max(0, gross - advance - incomeTax);
        row.querySelector('.estimate-display').textContent = '₹' + net.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function filterEmployees() {
        const input = document.getElementById('employeeSearch');
        const filter = input.value.toLowerCase();
        const table = document.querySelector('.table');
        if (!table) return;
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr');

        rows.forEach(row => {
            if (row.cells.length <= 1) return; // Skip empty message row
            const empCode = row.cells[0].textContent || row.cells[0].innerText;
            const empName = row.cells[1].textContent || row.cells[1].innerText;
            
            if (empCode.toLowerCase().includes(filter) || empName.toLowerCase().includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Shift focus to the next input when Enter key is pressed
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const activeElement = document.activeElement;
            if (activeElement && activeElement.tagName === 'INPUT' && activeElement.type === 'number') {
                e.preventDefault(); // Prevent form submission
                
                // Get all visible number inputs in the table
                const inputs = Array.from(document.querySelectorAll('.table tbody tr:not([style*="display: none"]) input[type="number"]:not([disabled])'));
                const currentIndex = inputs.indexOf(activeElement);
                
                if (currentIndex > -1 && currentIndex < inputs.length - 1) {
                    // Focus the next input
                    inputs[currentIndex + 1].focus();
                    inputs[currentIndex + 1].select(); // Optional: select text for easy overwrite
                }
            }
        }
    });
</script>
@endsection
