@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Company Settings</h1>
</div>

<div class="card">
    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label>Company Name <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $setting->company_name ?? 'PrecisionStampings') }}" required>
                @error('company_name')<span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email ?? '') }}">
                @error('email')<span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span>@enderror
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone ?? '') }}">
            </div>
            
            <div class="form-group">
                <label>Tax ID (GST/VAT)</label>
                <input type="text" name="tax_id" class="form-control" value="{{ old('tax_id', $setting->tax_id ?? '') }}">
            </div>
            
            <div class="form-group">
                <label>Currency</label>
                <input type="text" name="currency" class="form-control" value="{{ old('currency', $setting->currency ?? 'USD') }}">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address', $setting->address ?? '') }}</textarea>
            </div>
        </div>
        
        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
