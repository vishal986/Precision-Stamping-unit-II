@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Create Item</h1>
    <a href="{{ route('items.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to Items
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('items.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label class="form-label">Item Name <span style="color: var(--danger-color);">*</span></label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Steel Coil 2mm" value="{{ old('name') }}">
            @error('name') <span style="color: var(--danger-color); font-size: 0.75rem;">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Article No. <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="item_code" class="form-control" required placeholder="e.g. RAW-001" value="{{ old('item_code') }}">
                @error('item_code') <span style="color: var(--danger-color); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">LFe <span style="color: var(--text-secondary); font-size: 0.8rem;">(Length of Iron / Thickness)</span></label>
                <input type="text" name="lfe" class="form-control" placeholder="e.g. 120" value="{{ old('lfe') }}">
                @error('lfe') <span style="color: var(--danger-color); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Description</label>
            @php
                $defaultDescription = "OD x ID = \nSLOTS = \nMATERIAL = \nRING = \nALU = \nBORE = \nSKEW = ";
            @endphp
            <textarea name="description" class="form-control" rows="8" placeholder="Additional details...">{{ old('description', $defaultDescription) }}</textarea>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" style="display: flex; justify-content: space-between;">
                    Category 
                    <a href="{{ route('item-categories.index') }}" style="color: var(--primary-color); font-size: 0.8rem; text-decoration: none;">+ Add Category</a>
                </label>
                <select name="item_category_id" class="form-control">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (old('item_category_id') == $category->id || (!old('item_category_id') && stripos($category->name, 'FINISHED GOOD') !== false)) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Client / Customer</label>
                <select name="client_id" class="form-control">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Unit of Measure (UOM)</label>
                <input type="text" name="uom" class="form-control" placeholder="pcs, kg, mtrs" value="{{ old('uom', 'pcs') }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Unit Price (Selling)</label>
                <input type="number" step="0.01" name="unit_price" class="form-control" value="{{ old('unit_price', '0.00') }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Cost Price (Purchase)</label>
                <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price', '0.00') }}">
            </div>
        </div>
        
        <div class="form-group" style="margin-top: 1rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} style="width: 1.2rem; height: 1.2rem;">
                <span class="form-label" style="margin-bottom: 0;">Is Active</span>
            </label>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: var(--glass-border); display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('items.index') }}" class="btn" style="background: rgba(255,255,255,0.05); color: var(--text-primary);">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Item</button>
        </div>
    </form>
</div>
@endsection
