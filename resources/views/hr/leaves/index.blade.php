@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Leave Applications</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Manage employee leave requests and approvals.</p>
    </div>
    <a href="{{ route('leaves.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Apply for Leave
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Total Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $leaf)
            <tr>
                <td>
                    <div style="font-weight: 600;">{{ $leaf->employee->first_name }} {{ $leaf->employee->last_name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $leaf->employee->employee_code }}</div>
                </td>
                <td>
                    <div style="font-weight: 500; color: var(--primary-color);">{{ $leaf->leaveType->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $leaf->leaveType->is_paid ? 'Paid' : 'Unpaid' }}</div>
                </td>
                <td style="font-size: 0.9rem;">
                    {{ \Carbon\Carbon::parse($leaf->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leaf->end_date)->format('d M, Y') }}
                </td>
                <td style="font-weight: bold;">{{ floatval($leaf->total_days) }}</td>
                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $leaf->reason }}">
                    {{ $leaf->reason }}
                </td>
                <td>
                    @php
                        $statusColors = [
                            'Pending' => '#f59e0b',
                            'Approved' => 'var(--success-color)',
                            'Rejected' => 'var(--danger-color)',
                        ];
                        $color = $statusColors[$leaf->status] ?? 'var(--text-secondary)';
                    @endphp
                    <span style="background: {{ $color }}22; color: {{ $color }}; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                        {{ $leaf->status }}
                    </span>
                    @if($leaf->approver)
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">By: {{ $leaf->approver->first_name }}</div>
                    @endif
                </td>
                <td>
                    @if($leaf->status === 'Pending')
                        <div style="display: flex; gap: 0.5rem;">
                            <form action="{{ route('leaves.updateStatus', $leaf) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="btn" style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 0.25rem 0.5rem;" title="Approve">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('leaves.updateStatus', $leaf) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.2); color: var(--danger-color); padding: 0.25rem 0.5rem;" title="Reject">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <span style="color: var(--text-secondary); font-size: 0.85rem;">Processed</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No leave applications found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
