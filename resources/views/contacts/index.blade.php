@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h1 class="page-title">Contacts</h1>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('contacts.export') }}" class="btn btn-secondary" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color); border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> New Contact
        </a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Name / Company</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr>
                    <td style="color: var(--text-secondary); font-size: 0.85rem;">{{ $loop->iteration }}</td>
                    <td style="font-weight: 600;">{{ $contact->name }}</td>
                    <td>
                        <span style="background: rgba(59, 130, 246, 0.2); color: var(--primary-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize;">{{ $contact->type }}</span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('contacts.edit', $contact) }}" style="color: var(--text-secondary);"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--text-secondary); cursor: pointer;" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem;">
                        <div style="color: var(--text-secondary); margin-bottom: 1rem;"><i class="fa-solid fa-users fa-3x"></i></div>
                        <h4>No Contacts Found</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Add your first customer or supplier.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
