@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ isset($contact) ? 'Edit Contact: ' . $contact->name : 'Create Contact' }}</h1>
    <a href="{{ route('contacts.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to Contacts
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ isset($contact) ? route('contacts.update', $contact) : route('contacts.store') }}" method="POST">
        @csrf
        @if(isset($contact))
            @method('PUT')
        @endif
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Contact / Company Name <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Bosch GmbH" value="{{ old('name', $contact->name ?? '') }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Type <span style="color: var(--danger-color);">*</span></label>
                <select name="type" class="form-control" required>
                    <option value="customer" {{ old('type', $contact->type ?? '') == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="supplier" {{ old('type', $contact->type ?? '') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="both" {{ old('type', $contact->type ?? '') == 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>
        </div>


        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3">{{ old('address', $contact->address ?? '') }}</textarea>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: var(--glass-border); display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('contacts.index') }}" class="btn" style="background: rgba(255,255,255,0.05); color: var(--text-primary);">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ isset($contact) ? 'Update Contact' : 'Save Contact' }}</button>
        </div>
    </form>
</div>
@endsection
