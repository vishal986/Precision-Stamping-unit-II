@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Item: {{ $item->item_code }} (Article No.)</h1>
    <a href="{{ route('items.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to Items
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('items.update', $item) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Item Name <span style="color: var(--danger-color);">*</span></label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $item->name) }}">
            @error('name') <span style="color: var(--danger-color); font-size: 0.75rem;">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Article No. <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="item_code" class="form-control" required value="{{ old('item_code', $item->item_code) }}">
                @error('item_code') <span style="color: var(--danger-color); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">LFe <span style="color: var(--text-secondary); font-size: 0.8rem;">(Length of Iron / Thickness)</span></label>
                <input type="text" name="lfe" class="form-control" placeholder="e.g. 120" value="{{ old('lfe', $item->lfe) }}">
                @error('lfe') <span style="color: var(--danger-color); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Description</label>
            @php
                $defaultDescription = "OD x ID = \nSLOTS = \nMATERIAL = \nRING = \nALU = \nBORE = \nSKEW = ";
                $descValue = $item->description ? $item->description : $defaultDescription;
            @endphp
            <textarea name="description" class="form-control" rows="8">{{ old('description', $descValue) }}</textarea>
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
                        <option value="{{ $category->id }}" {{ old('item_category_id', $item->item_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Client / Customer</label>
                <select name="client_id" class="form-control">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $item->client_id) == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Unit of Measure (UOM)</label>
                <input type="text" name="uom" class="form-control" value="{{ old('uom', $item->uom) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Unit Price (Selling)</label>
                <input type="number" step="0.01" name="unit_price" class="form-control" value="{{ old('unit_price', $item->unit_price) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Cost Price (Purchase)</label>
                <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price', $item->cost_price) }}">
            </div>
        </div>
        
        <div class="form-group" style="margin-top: 1rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }} style="width: 1.2rem; height: 1.2rem;">
                <span class="form-label" style="margin-bottom: 0;">Is Active</span>
            </label>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: var(--glass-border); display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('items.index') }}" class="btn" style="background: rgba(255,255,255,0.05); color: var(--text-primary);">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </div>
    </form>
</div>

<!-- BOM Section -->
<div class="card" style="max-width: 800px; margin: 2rem auto;">
    <h2 class="card-title" style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Bill of Materials (BOM)</h2>
    
    @if($item->boms->count() > 0)
        <table class="table" style="margin-bottom: 2rem;">
            <thead>
                <tr>
                    <th>Grade Required</th>
                    <th>Job Size Required</th>
                    <th>Weight Per Unit (kg)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->boms as $bom)
                <tr>
                    <td>{{ $bom->grade }}</td>
                    <td>{{ $bom->job_size }}</td>
                    <td>{{ number_format($bom->weight_per_unit, 3) }}</td>
                    <td>
                        <form action="{{ route('boms.destroy', $bom) }}" method="POST" onsubmit="return confirm('Remove this BOM entry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="background: rgba(239,68,68,0.1); color: var(--danger-color); padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">No BOM defined for this item yet.</p>
    @endif

    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-primary);">Add Raw Material Requirement</h3>
    <form action="{{ route('items.boms.store', $item) }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Grade</label>
                <input type="text" name="grade" class="form-control" placeholder="e.g. CRCA" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Job Size</label>
                <input type="text" name="job_size" class="form-control" placeholder="e.g. 0.5mm" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Weight Per Unit (kg)</label>
                <input type="number" step="0.001" name="weight_per_unit" class="form-control" placeholder="0.500" required>
            </div>
        </div>
        <div style="margin-top: 1rem;">
            <button type="submit" class="btn btn-primary" style="background: var(--success-color);"><i class="fa-solid fa-plus"></i> Add to BOM</button>
        </div>
    </form>
</div>
@endsection
