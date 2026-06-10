@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h1 class="page-title">Apply for Leave</h1>
    <a href="{{ route('leaves.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('leaves.store') }}" method="POST">
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
            <label class="form-label">Leave Type <span style="color: var(--danger-color);">*</span></label>
            <select name="leave_type_id" class="form-control" required>
                <option value="">-- Select Leave Type --</option>
                @foreach($leaveTypes as $type)
                    <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }} ({{ $type->code }}) - {{ $type->is_paid ? 'Paid' : 'Unpaid' }}
                    </option>
                @endforeach
            </select>
            @error('leave_type_id') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Start Date <span style="color: var(--danger-color);">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required onchange="calculateDays()">
                @error('start_date') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">End Date <span style="color: var(--danger-color);">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required onchange="calculateDays()">
                @error('end_date') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Total Days <span style="color: var(--danger-color);">*</span></label>
            <input type="number" step="0.5" min="0.5" name="total_days" id="total_days" class="form-control" value="{{ old('total_days') }}" required>
            <small style="color: var(--text-secondary);">You can adjust this for half days manually (e.g. 1.5).</small>
            @error('total_days') <br><span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Reason for Leave <span style="color: var(--danger-color);">*</span></label>
            <textarea name="reason" class="form-control" rows="4" required placeholder="Provide a brief reason for the leave application.">{{ old('reason') }}</textarea>
            @error('reason') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                <i class="fa-solid fa-paper-plane"></i> Submit Application
            </button>
        </div>
    </form>
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

    function calculateDays() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        
        if(start && end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            
            if(endDate >= startDate) {
                // Calculate difference in milliseconds
                const diffTime = Math.abs(endDate - startDate);
                // Convert to days and add 1 (inclusive of start and end date)
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('total_days').value = diffDays;
            } else {
                document.getElementById('total_days').value = '';
            }
        }
    }
</script>
@endsection
