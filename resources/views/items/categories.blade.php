@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Manage Item Categories</h1>
    <a href="{{ route('items.create') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Back to Create Item
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Add Category Form -->
    <div>
        <div class="card">
            <h2 class="card-title" style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Add New Category</h2>
            
            <form action="{{ route('item-categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Category Name <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Raw Material" value="{{ old('name') }}">
                    @error('name') <span style="color: var(--danger-color); font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief description...">{{ old('description') }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-plus"></i> Save Category
                </button>
            </form>
        </div>
    </div>

    <!-- Category List -->
    <div>
        <div class="card">
            <h2 class="card-title" style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Existing Categories</h2>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td style="font-weight: 500;">{{ $category->name }}</td>
                        <td style="color: var(--text-secondary); font-size: 0.9rem;">{{ $category->description ?: '-' }}</td>
                        <td style="text-align: center;">
                            <form action="{{ route('item-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--danger-color); cursor: pointer; padding: 0.5rem;" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No categories created yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
