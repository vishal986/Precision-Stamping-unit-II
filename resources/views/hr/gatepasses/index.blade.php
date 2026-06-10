@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Employee Gatepasses</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Issue and track short-duration movements of employees.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('gatepassModal')">
        <i class="fa-solid fa-plus"></i> Issue New Gatepass
    </button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Emp Code</th>
                <th>Employee Name</th>
                <th>Type</th>
                <th>Date</th>
                <th>Out Time</th>
                <th>In Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gatepasses as $gp)
            <tr>
                <td style="font-weight: 600;">{{ $gp->employee->employee_code }}</td>
                <td>{{ $gp->employee->first_name }} {{ $gp->employee->last_name }}</td>
                <td>
                    <span class="badge" style="background: {{ $gp->type == 'Personal' ? 'rgba(236, 72, 153, 0.2)' : 'rgba(59, 130, 246, 0.2)' }}; color: {{ $gp->type == 'Personal' ? '#ec4899' : '#3b82f6' }};">
                        {{ $gp->type }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($gp->date)->format('d M Y') }}</td>
                <td style="color: var(--danger-color); font-weight: 600;">{{ \Carbon\Carbon::parse($gp->out_time)->format('H:i') }}</td>
                <td style="color: var(--success-color); font-weight: 600;">
                    @if($gp->in_time)
                        {{ \Carbon\Carbon::parse($gp->in_time)->format('H:i') }}
                    @else
                        <button class="btn btn-sm" style="background: rgba(16, 185, 129, 0.2); color: #10b981;" onclick="openInTimeModal({{ $gp->id }})">
                            Mark In
                        </button>
                    @endif
                </td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $gp->reason }}</td>
                <td>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">{{ $gp->status }}</span>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('gatepasses.print', $gp) }}" target="_blank" class="btn btn-sm" style="background: rgba(255,255,255,0.1);" title="Print Slip">
                            <i class="fa-solid fa-print"></i>
                        </a>
                        <form action="{{ route('gatepasses.destroy', $gp) }}" method="POST" onsubmit="return confirm('Delete this gatepass?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-color);">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No gatepass records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1rem;">
        {{ $gatepasses->links() }}
    </div>
</div>

<!-- Issue Gatepass Modal -->
<div id="gatepassModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7);">
    <div style="background: #1e1e2d; margin: 5% auto; padding: 2rem; border-radius: 1rem; width: 500px; box-shadow: var(--card-shadow); border: 1px solid rgba(255,255,255,0.1);">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Issue New Gatepass</h2>
        <form action="{{ route('gatepasses.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-control searchable-select" required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control">
                        <option value="Personal">Personal</option>
                        <option value="Official">Official</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div class="form-group">
                    <label class="form-label">Out Time</label>
                    <input type="time" name="out_time" class="form-control" value="{{ date('H:i') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Expected In Time</label>
                    <input type="time" name="in_time" class="form-control">
                </div>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="2"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn" style="flex: 1; background: rgba(255,255,255,0.1);" onclick="closeModal('gatepassModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Issue Gatepass</button>
            </div>
        </form>
    </div>
</div>

<!-- Mark In Modal -->
<div id="markInModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7);">
    <div style="background: #1e1e2d; margin: 15% auto; padding: 2rem; border-radius: 1rem; width: 400px; box-shadow: var(--card-shadow); border: 1px solid rgba(255,255,255,0.1);">
        <h2 style="margin-bottom: 1.5rem; color: var(--success-color);">Mark Return Time</h2>
        <form id="markInForm" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="Approved">
            <div class="form-group">
                <label class="form-label">Actual In Time</label>
                <input type="time" name="in_time" class="form-control" value="{{ date('H:i') }}" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn" style="flex: 1; background: rgba(255,255,255,0.1);" onclick="closeModal('markInModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; background: var(--success-color);">Save Return</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
        if(id === 'gatepassModal') {
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            document.querySelector('#gatepassModal input[name="out_time"]').value = time;
        }
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    function openInTimeModal(id) {
        document.getElementById('markInForm').action = `/gatepasses/${id}`;
        
        const now = new Date();
        const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        document.querySelector('#markInModal input[name="in_time"]').value = time;
        
        openModal('markInModal');
    }

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
