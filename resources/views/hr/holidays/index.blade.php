@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Holiday Calendar</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Define public and company holidays for payroll and attendance.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('holidayModal')">
        <i class="fa-solid fa-calendar-plus"></i> Add Holiday
    </button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Holiday Name</th>
                <th>Date</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($holidays as $holiday)
            <tr>
                <td style="font-weight: 600;">{{ $holiday->name }}</td>
                <td>
                    <div style="font-weight: 600; color: var(--primary-color);">{{ \Carbon\Carbon::parse($holiday->date)->format('d F Y') }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ \Carbon\Carbon::parse($holiday->date)->format('l') }}</div>
                </td>
                <td>{{ $holiday->description }}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <button class="btn btn-sm" style="background: rgba(255,255,255,0.1);" onclick="openEditModal({{ $holiday->id }}, '{{ $holiday->name }}', '{{ $holiday->date }}', '{{ $holiday->description }}')">
                            <i class="fa-solid fa-edit"></i>
                        </button>
                        <form action="{{ route('holidays.index') }}/{{ $holiday->id }}" method="POST" onsubmit="return confirm('Delete this holiday?')">
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
                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No holidays defined.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1rem;">
        {{ $holidays->links() }}
    </div>
</div>

<!-- Add Holiday Modal -->
<div id="holidayModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7);">
    <div style="background: #1e1e2d; margin: 10% auto; padding: 2rem; border-radius: 1rem; width: 450px; box-shadow: var(--card-shadow); border: 1px solid rgba(255,255,255,0.1);">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Add New Holiday</h2>
        <form action="{{ route('holidays.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Holiday Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Republic Day" required>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn" style="flex: 1; background: rgba(255,255,255,0.1);" onclick="closeModal('holidayModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Holiday</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Holiday Modal -->
<div id="editHolidayModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7);">
    <div style="background: #1e1e2d; margin: 10% auto; padding: 2rem; border-radius: 1rem; width: 450px; box-shadow: var(--card-shadow); border: 1px solid rgba(255,255,255,0.1);">
        <h2 style="margin-bottom: 1.5rem; color: #fbbf24;">Edit Holiday</h2>
        <form id="editHolidayForm" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Holiday Name</label>
                <input type="text" id="edit_name" name="name" class="form-control" required>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Date</label>
                <input type="date" id="edit_date" name="date" class="form-control" required>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Description (Optional)</label>
                <textarea id="edit_description" name="description" class="form-control" rows="2"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn" style="flex: 1; background: rgba(255,255,255,0.1);" onclick="closeModal('editHolidayModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; background: #fbbf24; border-color: #fbbf24;">Update Holiday</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    function openEditModal(id, name, date, description) {
        document.getElementById('editHolidayForm').action = `{{ route('holidays.index') }}/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_date').value = date;
        document.getElementById('edit_description').value = description;
        openModal('editHolidayModal');
    }
</script>
@endsection
