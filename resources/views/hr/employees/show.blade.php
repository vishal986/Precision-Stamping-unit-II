@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Employee Profile</h1>
    </div>
    <div>
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
            <i class="fa-solid fa-edit"></i> Edit Profile
        </a>
        <a href="{{ route('employees.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Left Column: Profile Snapshot -->
    <div>
        <div class="card" style="text-align: center; padding: 3rem 2rem;">
            @if($employee->photo_path)
                <img src="{{ Storage::url($employee->photo_path) }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid var(--primary-color); margin-bottom: 1.5rem;">
            @else
                <div style="width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 4rem; font-weight: bold; color: var(--text-secondary); margin: 0 auto 1.5rem auto; border: 4px solid rgba(255,255,255,0.2);">
                    {{ substr($employee->first_name, 0, 1) }}
                </div>
            @endif
            
            <h2 style="margin: 0 0 0.5rem 0;">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <p style="color: var(--primary-color); font-weight: 600; margin-bottom: 0.5rem; letter-spacing: 1px;">{{ $employee->employee_code }}</p>
            <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 1.5rem;">{{ $employee->designation ?? 'No Designation' }} &bull; {{ $employee->department->name ?? 'No Department' }}</p>
            
            @php
                $statusColors = [
                    'Active' => 'var(--success-color)',
                    'Inactive' => 'var(--text-secondary)',
                    'Terminated' => 'var(--danger-color)',
                    'Resigned' => '#f59e0b'
                ];
                $color = $statusColors[$employee->status] ?? 'var(--text-secondary)';
            @endphp
            <div style="display: inline-block; background: {{ $color }}22; color: {{ $color }}; padding: 0.4rem 1rem; border-radius: 20px; font-weight: 600; letter-spacing: 0.5px;">
                {{ $employee->status }}
            </div>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">Contact Info</h3>
            
            <div style="margin-bottom: 1rem; display: flex; gap: 1rem;">
                <i class="fa-solid fa-phone" style="color: var(--text-secondary); margin-top: 0.25rem;"></i>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">Phone</div>
                    <div>{{ $employee->phone ?? '-' }}</div>
                </div>
            </div>
            
            <div style="margin-bottom: 1rem; display: flex; gap: 1rem;">
                <i class="fa-solid fa-envelope" style="color: var(--text-secondary); margin-top: 0.25rem;"></i>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">Email</div>
                    <div>{{ $employee->email ?? '-' }}</div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <i class="fa-solid fa-location-dot" style="color: var(--text-secondary); margin-top: 0.25rem;"></i>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">Address</div>
                    <div>{{ $employee->address ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details -->
    <div>
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: #8b5cf6;">Employment Details</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Date of Joining</div>
                    <div style="font-weight: 500;">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') : '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Reporting Manager</div>
                    <div style="font-weight: 500;">
                        @if($employee->manager)
                            <a href="{{ route('employees.show', $employee->manager) }}" style="color: var(--primary-color);">{{ $employee->manager->first_name }} {{ $employee->manager->last_name }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Assigned Shift</div>
                    <div style="font-weight: 500;">{{ $employee->shift ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Grade / Level</div>
                    <div style="font-weight: 500;">{{ $employee->grade ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: #ec4899;">Personal & Bank Details</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Father's Name</div>
                    <div style="font-weight: 500;">{{ $employee->father_name ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Date of Birth</div>
                    <div style="font-weight: 500;">{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d M, Y') : '-' }}</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Bank Name</div>
                    <div style="font-weight: 500;">{{ $employee->bank_name ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">Account Number</div>
                    <div style="font-weight: 500;">{{ $employee->bank_account ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.25rem;">IFSC Code</div>
                    <div style="font-weight: 500;">{{ $employee->ifsc_code ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: #10b981;">Compliance Documents</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="margin: 0; color: #fff;">Aadhaar Card</h4>
                        <i class="fa-solid fa-id-card" style="color: var(--text-secondary); font-size: 1.5rem;"></i>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">Aadhaar No.</div>
                        <div style="font-weight: 600; font-family: monospace; font-size: 1.1rem; letter-spacing: 1px;">{{ $employee->aadhaar_number ?? 'Not Provided' }}</div>
                    </div>
                    @if($employee->aadhaar_file_path)
                        <a href="{{ Storage::url($employee->aadhaar_file_path) }}" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); width: 100%; text-align: center;">
                            <i class="fa-solid fa-file-pdf"></i> View Document
                        </a>
                    @else
                        <div style="padding: 0.5rem; text-align: center; color: var(--danger-color); background: rgba(239, 68, 68, 0.1); border-radius: 4px; font-size: 0.9rem;">Missing Document</div>
                    @endif
                </div>

                <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="margin: 0; color: #fff;">PAN Card</h4>
                        <i class="fa-solid fa-id-card-clip" style="color: var(--text-secondary); font-size: 1.5rem;"></i>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">PAN No.</div>
                        <div style="font-weight: 600; font-family: monospace; font-size: 1.1rem; letter-spacing: 1px; text-transform: uppercase;">{{ $employee->pan_number ?? 'Not Provided' }}</div>
                    </div>
                    @if($employee->pan_file_path)
                        <a href="{{ Storage::url($employee->pan_file_path) }}" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); width: 100%; text-align: center;">
                            <i class="fa-solid fa-file-pdf"></i> View Document
                        </a>
                    @else
                        <div style="padding: 0.5rem; text-align: center; color: var(--danger-color); background: rgba(239, 68, 68, 0.1); border-radius: 4px; font-size: 0.9rem;">Missing Document</div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
