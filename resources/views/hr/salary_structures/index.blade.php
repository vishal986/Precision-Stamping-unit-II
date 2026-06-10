@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Salary Structures</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Define Basic, HRA, and applicable deductions for all active employees.</p>
    </div>
</div>

<div class="card" style="margin-bottom: 1rem; padding: 1rem; display: flex; gap: 1rem; align-items: center; flex-direction: row;">
    <i class="fa-solid fa-search" style="color: var(--text-secondary);"></i>
    <input type="text" id="searchInput" class="form-control" placeholder="Search employees..." onkeyup="filterTable()" style="max-width: 300px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
</div>

<div class="card" style="overflow-x: auto;">
    <form action="{{ route('salary_structures.store') }}" method="POST">
        @csrf
        
        <table class="table" id="dataTable" style="margin-bottom: 2rem; min-width: 1200px;">
            <thead>
                <tr>
                    <th>Emp Code</th>
                    <th>Employee Name</th>
                    <th>Basic Salary</th>
                    <th>HRA</th>
                    <th>Conveyance</th>
                    <th>Special Allowance</th>
                    <th>Medical</th>
                    <th>Gross</th>
                    <th>Deductions Applicable</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    @php
                        $struct = $structures->get($employee->id);
                    @endphp
                <tr>
                    <td style="font-weight: 600;">{{ $employee->employee_code }}</td>
                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td>
                        <input type="number" step="0.01" name="structures[{{ $employee->id }}][basic_salary]" class="form-control basic-input" style="width: 100px;" value="{{ old("structures.{$employee->id}.basic_salary", $struct->basic_salary ?? '') }}" onkeyup="calcGross(this)" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="structures[{{ $employee->id }}][hra]" class="form-control hra-input" style="width: 100px;" value="{{ old("structures.{$employee->id}.hra", $struct->hra ?? '') }}" onkeyup="calcGross(this)" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="structures[{{ $employee->id }}][conveyance_allowance]" class="form-control conveyance-input" style="width: 100px;" value="{{ old("structures.{$employee->id}.conveyance_allowance", $struct->conveyance_allowance ?? '') }}" onkeyup="calcGross(this)" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="structures[{{ $employee->id }}][special_allowance]" class="form-control sa-input" style="width: 100px;" value="{{ old("structures.{$employee->id}.special_allowance", $struct->special_allowance ?? '') }}" onkeyup="calcGross(this)" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="structures[{{ $employee->id }}][medical_allowance]" class="form-control medical-input" style="width: 100px;" value="{{ old("structures.{$employee->id}.medical_allowance", $struct->medical_allowance ?? '') }}" onkeyup="calcGross(this)" required>
                    </td>
                    <td>
                        <div class="gross-display" style="font-weight: bold; color: var(--primary-color);">0.00</div>
                    </td>
                    <td>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.25rem;">
                                <input type="hidden" name="structures[{{ $employee->id }}][pf_hidden]" value="1">
                                <input type="checkbox" name="structures[{{ $employee->id }}][pf_applicable]" value="1" {{ old("structures.{$employee->id}.pf_applicable", $struct->pf_applicable ?? true) ? 'checked' : '' }}>
                                PF (12%)
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.25rem;">
                                <input type="hidden" name="structures[{{ $employee->id }}][esi_hidden]" value="1">
                                <input type="checkbox" name="structures[{{ $employee->id }}][esi_applicable]" value="1" {{ old("structures.{$employee->id}.esi_applicable", $struct->esi_applicable ?? true) ? 'checked' : '' }}>
                                ESI (0.75%)
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.25rem;">
                                <input type="hidden" name="structures[{{ $employee->id }}][lwf_hidden]" value="1">
                                <input type="checkbox" name="structures[{{ $employee->id }}][lwf_applicable]" value="1" {{ old("structures.{$employee->id}.lwf_applicable", $struct->lwf_applicable ?? true) ? 'checked' : '' }}>
                                LWF (0.2%)
                            </label>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No active employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($employees->count() > 0)
        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                <i class="fa-solid fa-save"></i> Save Salary Structures
            </button>
        </div>
        @endif
    </form>
</div>

<script>
    function calcGross(element) {
        const row = element.closest('tr');
        const basic = parseFloat(row.querySelector('.basic-input').value) || 0;
        const hra = parseFloat(row.querySelector('.hra-input').value) || 0;
        const medical = parseFloat(row.querySelector('.medical-input').value) || 0;
        const conveyance = parseFloat(row.querySelector('.conveyance-input').value) || 0;
        const sa = parseFloat(row.querySelector('.sa-input').value) || 0;
        
        const gross = basic + hra + medical + conveyance + sa;
        row.querySelector('.gross-display').textContent = gross.toFixed(2);

        const esiCheckbox = row.querySelector('input[name*="[esi_applicable]"]');
        if (esiCheckbox) {
            if (gross > 21000) {
                esiCheckbox.checked = false;
                // Highlight to indicate auto-disable
                esiCheckbox.parentElement.style.opacity = '0.6';
            } else {
                esiCheckbox.parentElement.style.opacity = '1';
            }
        }
    }

    // Removed magicBreakdown function as requested

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.basic-input').forEach(input => {
            calcGross(input);
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
