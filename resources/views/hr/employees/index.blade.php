@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Employee Master</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Manage HR data, onboarding documents, and employee profiles.</p>
    </div>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Add New Employee
    </a>
</div>

<div class="card" style="margin-bottom: 1rem; padding: 1rem; display: flex; gap: 1rem; align-items: center; flex-direction: row;">
    <i class="fa-solid fa-search" style="color: var(--text-secondary);"></i>
    <input type="text" id="searchInput" class="form-control" placeholder="Search employees..." onkeyup="filterTable()" style="max-width: 300px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
</div>

<div class="card">
    <table class="table" id="dataTable">
        <thead>
            <tr>
                <th>Emp Code</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
            <tr>
                <td style="font-weight: 600;">{{ $employee->employee_code }}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        @if($employee->photo_path)
                            <img src="{{ Storage::url($employee->photo_path) }}" alt="{{ $employee->first_name }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                        @else
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--text-secondary);">
                                {{ substr($employee->first_name, 0, 1) }}
                            </div>
                        @endif
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </div>
                </td>
                <td>{{ $employee->department->name ?? '-' }}</td>
                <td>{{ $employee->designation ?? '-' }}</td>
                <td>{{ $employee->phone ?? '-' }}</td>
                <td>
                    @php
                        $statusColors = [
                            'Active' => 'var(--success-color)',
                            'Inactive' => 'var(--text-secondary)',
                            'Terminated' => 'var(--danger-color)',
                            'Resigned' => '#f59e0b'
                        ];
                        $color = $statusColors[$employee->status] ?? 'var(--text-secondary)';
                    @endphp
                    <span style="background: {{ $color }}22; color: {{ $color }}; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                        {{ $employee->status }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('employees.show', $employee) }}" class="btn" style="background: rgba(255,255,255,0.1); padding: 0.25rem 0.5rem;">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('employees.edit', $employee) }}" class="btn" style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.25rem 0.5rem;">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No employees found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
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
