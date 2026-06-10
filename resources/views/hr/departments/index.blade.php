@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Department Master</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Create and manage company departments.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Add Department Form -->
    <div class="card" style="align-self: start;">
        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: var(--primary-color);">Add New Department</h3>
        
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Department Name <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Production, Accounts">
                @error('name') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Department Code <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="code" class="form-control" required placeholder="e.g. PROD, ACC">
                @error('code') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
                <i class="fa-solid fa-plus"></i> Save Department
            </button>
        </form>
    </div>

    <!-- Departments List -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Department Name</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr>
                    <td style="font-weight: 600; color: var(--primary-color);">{{ $dept->code }}</td>
                    <td>{{ $dept->name }}</td>
                    <td style="color: var(--text-secondary);">{{ $dept->created_at->format('d M, Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn" style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.25rem 0.5rem;" onclick="editDept({{ json_encode($dept) }})">
                                <i class="fa-solid fa-edit"></i>
                            </button>
                            <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-color); padding: 0.25rem 0.5rem;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No departments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 400px;">
        <h3 style="margin-bottom: 1.5rem;">Edit Department</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Department Name</label>
                <input type="text" name="name" id="editName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Department Code</label>
                <input type="text" name="code" id="editCode" class="form-control" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn" style="background: rgba(255,255,255,0.1); flex: 1;" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editDept(dept) {
        document.getElementById('editName').value = dept.name;
        document.getElementById('editCode').value = dept.code;
        document.getElementById('editForm').action = "/departments/" + dept.id;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
@endsection
