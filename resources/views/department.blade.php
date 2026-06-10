@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="text-center mb-4">Manage Departments</h3>

    <!-- Display success or error messages -->
    @if (session('success'))
        <div class="alert alert-success " id="sessionMsg">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" id="sessionMsg">{{ session('error') }}</div>
    @endif

    <!-- Search -->
    <input type="text" id="search" class="form-control mb-3" placeholder="Search department...">

    <!-- Add / Edit Department -->
    <div class="card p-3 mb-4">
        <h5 id="formTitle">Add Department</h5>

        <form action="{{ url('/departments/store') }}" method="POST" id="deptForm">
            @csrf
            <input type="hidden" name="id" id="dept_id">

            <div class="row">
                <div class="col-md-4">
                    <label>Name *</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Code *</label>
                    <input type="text" name="code" id="code" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-12 mt-3">
                    <label>Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
            </div>

            <button class="btn btn-primary mt-3" type="submit" id="submitBtn">Add</button>
        </form>
    </div>

    <!-- Department List -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Status</th>
                <th>Description</th>
                <th width="150">Action</th>
            </tr>
        </thead>
        <tbody id="deptTable">
            @foreach ($departments as $d)
                <tr data-id="{{ $d->id }}" data-name="{{ $d->name }}" data-code="{{ $d->code }}"
                    data-status="{{ $d->status }}" data-description="{{ $d->description }}">
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->code }}</td>
                    <td>{{ ucfirst($d->status) }}</td>
                    <td>{{ $d->description }}</td>
                    <td>
                        <button class="btn btn-sm btn-info editBtn">Edit</button>
                        <form action="{{ url('/departments/delete/' . $d->id) }}" method="GET" class="d-inline">
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

<script>
    // Frontend search
    document.getElementById('search').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        document.querySelectorAll('#deptTable tr').forEach(tr => {
            tr.style.display = tr.dataset.name.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Hide session messages
    setTimeout(() => {
        const msg = document.getElementById('sessionMsg');
        if(msg) msg.style.display = 'none';
    }, 2000);

    // Inline Edit
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function() {
            const tr = this.closest('tr');
            const id = tr.dataset.id;
            const name = tr.dataset.name;
            const code = tr.dataset.code;
            const status = tr.dataset.status;
            const description = tr.dataset.description;

            // Fill the form
            document.getElementById('dept_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('code').value = code;
            document.getElementById('status').value = status;
            document.getElementById('description').value = description;

            // Change form action to update
            document.getElementById('deptForm').action = '/departments/update/' + id;
            document.getElementById('submitBtn').textContent = 'Update';
            document.getElementById('formTitle').textContent = 'Edit Department';
        });
    });
</script>
@endsection
