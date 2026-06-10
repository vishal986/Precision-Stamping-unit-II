@extends('layouts.app')

@section('title')
    Material
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ request()->route('id') ? 'Edit Material' : 'Add New Material' }}</h1>
    <a href="{{ url('/coil-data') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-layer-group"></i> View All Material
    </a>
</div>

@if (session('success'))
    <div class="alert" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color); border: 1px solid var(--success-color); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <strong>{{ session('success') }}</strong>
    </div>
@endif

@if ($errors->any())
    <div class="alert" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-color); border: 1px solid var(--danger-color); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form method="post" action="{{ url('/store/material') }}" id="materialForm">
        @csrf
        <input type="hidden" name="coil_id" value="{{ $coil->id ?? '' }}">
        <input type="hidden" name="force_reset" id="force_reset" value="0">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Material Name <span style="color: var(--danger-color);">*</span></label>
                <input type="text" class="form-control" name="material_name" id="materialName" value="{{ old('material_name', $coil->coil_name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Material Number <span style="color: var(--danger-color);">*</span></label>
                <input type="text" class="form-control" name="coil_num" id="coilNum" value="{{ old('coil_num', $coil->coil_num ?? '') }}" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Job Size <span style="color: var(--danger-color);">*</span></label>
                <input type="text" class="form-control" name="job_size" id="jobSize" value="{{ old('job_size', $coil->job_size ?? '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Grade <span style="color: var(--danger-color);">*</span></label>
                <input type="text" class="form-control" name="coil_grade" id="coilGrade" value="{{ old('coil_grade', $coil->grade ?? '') }}" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Quantity <span style="color: var(--danger-color);">*</span></label>
                <input type="number" class="form-control" name="quantity" id="quantity" min="1" value="{{ old('quantity', $coil->quantity ?? '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Weight <span style="color: var(--danger-color);">*</span></label>
                <input type="number" class="form-control" name="weight_value" id="weight_value" step="0.001" value="{{ old('weight_value', $coil->weight_value ?? '') }}" required>
                <div id="weightError" style="color: var(--danger-color); font-size: 0.75rem; margin-top: 0.25rem;"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Unit <span style="color: var(--danger-color);">*</span></label>
                <select id="weight_unit" name="weight_unit" class="form-control" required>
                    <option value="mt" {{ old('weight_unit', $coil->weight_unit ?? 'mt') == 'mt' ? 'selected' : '' }}>Metric Ton (mt)</option>
                    <option value="kg" {{ old('weight_unit', $coil->weight_unit ?? 'mt') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                    <option value="g" {{ old('weight_unit', $coil->weight_unit ?? 'mt') == 'g' ? 'selected' : '' }}>Gram (g)</option>
                    <option value="nos" {{ old('weight_unit', $coil->weight_unit ?? 'mt') == 'nos' ? 'selected' : '' }}>Numbers (nos)</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: var(--glass-border); display: flex; justify-content: flex-end; gap: 1rem;">
            <button type="submit" class="btn btn-primary">{{ request()->route('id') ? 'Update Coil' : 'Save Coil' }}</button>
        </div>
    </form>
</div>

@section('script')
<script>
    document.getElementById("materialForm").addEventListener("submit", function(event) {
        let weightValue = document.getElementById("weight_value").value.trim();
        let errorBox = document.getElementById("weightError");
        errorBox.innerHTML = "";

        const decimalCheck = /^\d+(\.\d{1,3})?$/;
        if (!decimalCheck.test(weightValue)) {
            errorBox.innerHTML = "Weight must have max 3 decimal places!";
            event.preventDefault();
            setTimeout(() => {
                errorBox.innerHTML = "";
            }, 3000);
            return false;
        }

        const hasIssue = {{ isset($coil) && $coil->issues && $coil->issues->count() > 0 ? 'true' : 'false' }};
        if (hasIssue) {
            const confirmReset = confirm('This coil already has issued material.\nDo you want to force reset remaining weight?');
            if (!confirmReset) {
                event.preventDefault();
                return false;
            }
            document.getElementById('force_reset').value = 1;
        }
    });
</script>
@endsection
@endsection
