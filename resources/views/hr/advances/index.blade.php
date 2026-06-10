@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Advances & Loans</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Manage employee salary advances and monthly EMI deductions.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: 2rem;">
    <!-- Issue New Advance Form -->
    <div class="card" style="align-self: start;">
        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: var(--primary-color);">Issue New Advance</h3>
        
        <form action="{{ route('advances.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Employee <span style="color: var(--danger-color);">*</span></label>
                <select name="employee_id" class="form-control searchable-select" required>
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_code }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Date Given <span style="color: var(--danger-color);">*</span></label>
                <input type="date" name="date_given" class="form-control" value="{{ old('date_given', date('Y-m-d')) }}" required>
                @error('date_given') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Total Amount Given (₹) <span style="color: var(--danger-color);">*</span></label>
                <input type="number" step="0.01" min="1" name="amount_given" id="amount_given" class="form-control" value="{{ old('amount_given') }}" required>
                @error('amount_given') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">EMI Deduction per Month (₹) <span style="color: var(--danger-color);">*</span></label>
                <input type="number" step="0.01" min="1" name="emi_amount" class="form-control" value="{{ old('emi_amount') }}" required>
                <small style="color: var(--text-secondary);">This amount will be automatically deducted during monthly payroll.</small>
                @error('emi_amount') <br><span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes">{{ old('remarks') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
                <i class="fa-solid fa-plus"></i> Issue Advance
            </button>
        </form>
    </div>

    <!-- Active Advances List -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Total Amount</th>
                    <th>EMI / Month</th>
                    <th>Balance Remaining</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advances as $advance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($advance->date_given)->format('d M, Y') }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $advance->employee->first_name }} {{ $advance->employee->last_name }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $advance->employee->employee_code }}</div>
                    </td>
                    <td style="font-weight: bold; color: #fff;">₹{{ number_format($advance->amount_given, 0) }}</td>
                    <td style="color: var(--danger-color);">₹{{ number_format($advance->emi_amount, 0) }}</td>
                    <td style="font-weight: bold; color: var(--primary-color);">₹{{ number_format($advance->remaining_balance, 0) }}</td>
                    <td>
                        @if($advance->status == 'Active')
                            <span style="background: rgba(139, 92, 246, 0.2); color: #8b5cf6; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Active</span>
                        @else
                            <span style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Completed</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('advances.destroy', $advance) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record? This should only be done to correct mistakes.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-color); padding: 0.25rem 0.5rem;" title="Delete Record">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No advance records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.searchable-select');
        selects.forEach(select => {
            new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false
            });
        });
    });
</script>
@endsection
