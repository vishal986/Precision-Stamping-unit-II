@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Biometric Management</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Sync device logs and automate attendance calculations.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Import & Process -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Upload Card -->
        <div class="card">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);"><i class="fa-solid fa-file-import"></i> Import Logs</h3>
            <form action="{{ route('biometric.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">CSV File</label>
                    <input type="file" name="csv_file" class="form-control" required>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.5rem;">Format: EmployeeCode, YYYY-MM-DD HH:mm:ss, DeviceID</p>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fa-solid fa-upload"></i> Upload CSV
                </button>
            </form>
        </div>

        <!-- Process Card -->
        <div class="card">
            <h3 style="margin-bottom: 1.5rem; color: #10b981;"><i class="fa-solid fa-gears"></i> Process Attendance</h3>
            <form action="{{ route('biometric.process') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 1.5rem; background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981;">
                    <i class="fa-solid fa-play"></i> Run Auto-Calculation
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Logs Table -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem;">Recent Punch Logs</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Punch Time</th>
                    <th>Device</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $log->employee->first_name ?? 'Unknown' }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $log->employee_code }}</div>
                    </td>
                    <td>{{ $log->punch_time->format('d M Y, H:i:s') }}</td>
                    <td><span class="badge" style="background: rgba(255,255,255,0.1);">{{ $log->device_id }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No punch logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 1rem;">
            {{ $recentLogs->links() }}
        </div>
    </div>
</div>
@endsection
