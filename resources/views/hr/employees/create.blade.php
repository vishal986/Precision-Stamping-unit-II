@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h1 class="page-title">{{ isset($employee) ? 'Edit Employee Profile' : 'Onboard New Employee' }}</h1>
    <a href="{{ route('employees.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to List
    </a>
</div>

<form action="{{ isset($employee) ? route('employees.update', $employee) : route('employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($employee))
        @method('PUT')
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- Main Form Details -->
        <div>
            <!-- Basic Details -->
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: var(--primary-color);">Basic Information</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Employee Code <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="employee_code" class="form-control" value="{{ old('employee_code', $employee->employee_code ?? '') }}" required>
                        @error('employee_code') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span style="color: var(--danger-color);">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="Active" {{ old('status', $employee->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status', $employee->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Terminated" {{ old('status', $employee->status ?? '') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="Resigned" {{ old('status', $employee->status ?? '') == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">First Name <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name ?? '') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Father's Name</label>
                        <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $employee->father_name ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $employee->date_of_birth ?? '') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email ?? '') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Residential Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address ?? '') }}</textarea>
                </div>
            </div>

            <!-- Employment Details -->
            <div class="card" style="margin-bottom: 2rem; position: relative; z-index: 10;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: #8b5cf6;">Employment Details</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $employee->joining_date ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-control">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" class="form-control" value="{{ old('designation', $employee->designation ?? '') }}" placeholder="e.g. Operator, Manager, Helper">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Grade</label>
                        <input type="text" name="grade" class="form-control" value="{{ old('grade', $employee->grade ?? '') }}" placeholder="e.g. A, B, L1, M2">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Assigned Shift</label>
                        <input type="text" name="shift" class="form-control" value="{{ old('shift', $employee->shift ?? '') }}" placeholder="e.g. Day Shift, 9AM-6PM">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reporting Manager</label>
                        <select name="manager_id" class="form-control searchable-select">
                            <option value="">-- None --</option>
                            @foreach($managers as $mgr)
                                <option value="{{ $mgr->id }}" {{ old('manager_id', $employee->manager_id ?? '') == $mgr->id ? 'selected' : '' }}>{{ $mgr->first_name }} {{ $mgr->last_name }} ({{ $mgr->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Documents & Bank -->
            <div class="card" style="margin-bottom: 2rem; position: relative; z-index: 5;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: #ec4899;">Bank & Compliance</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bank Account Number</label>
                        <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account', $employee->bank_account ?? '') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $employee->ifsc_code ?? '') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label">UAN Number (PF)</label>
                        <input type="text" name="uan_number" class="form-control" value="{{ old('uan_number', $employee->uan_number ?? '') }}" placeholder="12 Digit UAN">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ESI Number</label>
                        <input type="text" name="esi_number" class="form-control" value="{{ old('esi_number', $employee->esi_number ?? '') }}" placeholder="ESI IP Number">
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side: File Uploads -->
        <div>
            <div class="card" style="position: sticky; top: 2rem;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">Document Uploads</h3>
                
                <div class="form-group">
                    <label class="form-label">Profile Photo</label>
                    @if(isset($employee) && $employee->photo_path)
                        <div style="margin-bottom: 0.5rem;">
                            <img src="{{ Storage::url($employee->photo_path) }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px solid var(--primary-color);">
                        </div>
                    @endif
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label">Aadhaar Number</label>
                    <input type="text" name="aadhaar_number" class="form-control" value="{{ old('aadhaar_number', $employee->aadhaar_number ?? '') }}" placeholder="12 Digit Number">
                    
                    <label class="form-label" style="margin-top: 0.5rem;">Aadhaar Document</label>
                    @if(isset($employee) && $employee->aadhaar_file_path)
                        <div style="margin-bottom: 0.5rem; font-size: 0.8rem;">
                            <a href="{{ Storage::url($employee->aadhaar_file_path) }}" target="_blank" style="color: var(--primary-color);"><i class="fa-solid fa-file-pdf"></i> View Current File</a>
                        </div>
                    @endif
                    <input type="file" name="aadhaar_file" class="form-control" accept=".pdf,image/*">
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label">PAN Number</label>
                    <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number', $employee->pan_number ?? '') }}" placeholder="10 Digit PAN" style="text-transform: uppercase;">
                    
                    <label class="form-label" style="margin-top: 0.5rem;">PAN Document</label>
                    @if(isset($employee) && $employee->pan_file_path)
                        <div style="margin-bottom: 0.5rem; font-size: 0.8rem;">
                            <a href="{{ Storage::url($employee->pan_file_path) }}" target="_blank" style="color: var(--primary-color);"><i class="fa-solid fa-file-pdf"></i> View Current File</a>
                        </div>
                    @endif
                    <input type="file" name="pan_file" class="form-control" accept=".pdf,image/*">
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
                        <i class="fa-solid fa-save"></i> {{ isset($employee) ? 'Update Employee Profile' : 'Complete Onboarding' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.searchable-select');
        selects.forEach(select => {
            new Choices(select, {
                searchEnabled: true,
                searchPlaceholderValue: 'Search manager name...',
                searchChoices: true,
                itemSelectText: '',
                shouldSort: false
            });
        });
    });
</script>
@endsection
