@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Daily Attendance Register</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Mark daily attendance and log overtime hours.</p>
    </div>
    
    <form action="{{ route('attendance.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center;">
        <label style="color: var(--text-secondary); font-weight: 500;">Select Date:</label>
        <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()" style="width: auto;">
    </form>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
        <h3 style="margin: 0; color: var(--primary-color);">Attendance for: {{ \Carbon\Carbon::parse($date)->format('d F Y (l)') }}</h3>
        <div>
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: var(--success-color); margin-right: 0.25rem;"></span> Present
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: var(--danger-color); margin-right: 0.25rem; margin-left: 1rem;"></span> Absent
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; margin-right: 0.25rem; margin-left: 1rem;"></span> Half Day
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #8b5cf6; margin-right: 0.25rem; margin-left: 1rem;"></span> Leave
        </div>
    </div>

    @if($holiday)
        <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid #3b82f6; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1.5rem;">
            <div style="font-size: 2.5rem; color: #3b82f6;"><i class="fa-solid fa-umbrella-beach"></i></div>
            <div>
                <h3 style="margin: 0; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px;">Today is a Holiday: {{ $holiday->name }}</h3>
                <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary);">{{ $holiday->description ?? 'No description provided.' }} - This day is automatically counted as a paid day for all employees.</p>
            </div>
        </div>
    @endif

    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        
        <table class="table" style="margin-bottom: 2rem;">
            <thead>
                <tr>
                    <th>Emp Code</th>
                    <th>Employee Name</th>
                    <th>Shift Details</th>
                    <th>Status</th>
                    <th>Punch In</th>
                    <th>Punch Out</th>
                    <th>Late Min</th>
                    <th>Gatepass</th>
                    <th>OT Hours</th>
                    <th>Auto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    @php
                        $att = $attendances->get($employee->id);
                        $status = old("attendance.{$employee->id}.status", $att->status ?? 'Present');
                        $punch_in = old("attendance.{$employee->id}.punch_in", $att && $att->punch_in ? \Carbon\Carbon::parse($att->punch_in)->format('H:i') : '');
                        $punch_out = old("attendance.{$employee->id}.punch_out", $att && $att->punch_out ? \Carbon\Carbon::parse($att->punch_out)->format('H:i') : '');
                        $ot_hours = old("attendance.{$employee->id}.ot_hours", $att->ot_hours ?? 0);
                    @endphp
                <tr>
                    <td style="font-weight: 600;">{{ $employee->employee_code }}</td>
                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td style="font-size: 0.85rem; color: var(--text-secondary);">
                        {{ $employee->shift ?? 'No Shift' }}
                    </td>
                    <td>
                        <select name="attendance[{{ $employee->id }}][status]" class="form-control status-select" style="min-width: 120px;" onchange="updateColors(this)">
                            <option value="Present" {{ $status == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ $status == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Half Day" {{ $status == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                            <option value="Leave" {{ $status == 'Leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                    </td>
                    <td>
                        <input type="time" name="attendance[{{ $employee->id }}][punch_in]" class="form-control" value="{{ $punch_in }}">
                    </td>
                    <td>
                        <input type="time" name="attendance[{{ $employee->id }}][punch_out]" class="form-control" value="{{ $punch_out }}">
                    </td>
                    <td style="color: {{ ($att->late_minutes ?? 0) > 0 ? 'var(--danger-color)' : 'var(--text-secondary)' }}; font-weight: {{ ($att->late_minutes ?? 0) > 0 ? 'bold' : 'normal' }};">
                        {{ $att->late_minutes ?? 0 }}
                    </td>
                    <td>
                        @if(isset($gatepasses[$employee->id]))
                            @php
                                $totalAway = 0;
                                foreach($gatepasses[$employee->id] as $gp) {
                                    if($gp->in_time && $gp->out_time) {
                                        $totalAway += \Carbon\Carbon::parse($gp->out_time)->diffInMinutes(\Carbon\Carbon::parse($gp->in_time));
                                    }
                                }
                            @endphp
                            @if($totalAway > 0)
                                <span class="badge" style="background: rgba(236, 72, 153, 0.1); color: #ec4899; font-size: 0.75rem;">
                                    <i class="fa-solid fa-door-open"></i> {{ $totalAway }}m
                                </span>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.8rem;">Pending In</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <input type="number" step="0.5" min="0" max="12" name="attendance[{{ $employee->id }}][ot_hours]" class="form-control" style="width: 80px;" value="{{ floatval($ot_hours) }}">
                    </td>
                    <td>
                        @if($att->is_auto_calculated ?? false)
                            <i class="fa-solid fa-robot" title="Auto-Calculated from Biometric" style="color: #10b981;"></i>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No active employees found to mark attendance.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($employees->count() > 0)
        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                <i class="fa-solid fa-save"></i> Save Attendance
            </button>
        </div>
        @endif
    </form>
</div>

<script>
    function updateColors(selectElement) {
        const value = selectElement.value;
        let color = '';
        if(value === 'Present') color = 'var(--success-color)';
        else if(value === 'Absent') color = 'var(--danger-color)';
        else if(value === 'Half Day') color = '#f59e0b';
        else if(value === 'Leave') color = '#8b5cf6';
        
        selectElement.style.borderLeft = `4px solid ${color}`;
        selectElement.style.color = color;
    }

    // Initialize colors on load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.status-select').forEach(function(select) {
            updateColors(select);
        });
    });
</script>
@endsection
