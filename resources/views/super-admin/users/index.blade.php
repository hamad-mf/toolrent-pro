@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page_title', 'System Users')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 border-bottom-0">
        <h5 class="mb-0 fw-bold">All Platform Users</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Shop / Tenant</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="ps-4">
                        <div class="fw-medium text-dark">{{ $user->name }}</div>
                        <div class="small text-muted">Created {{ $user->created_at->format('M d, Y') }}</div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $user->role->name ?? 'No Role' }}</span>
                    </td>
                    <td>
                        {{ $user->tenant->name ?? 'Global (Super Admin)' }}
                    </td>
                    <td class="text-end pe-4">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-confirm="Permanently delete this user account?" {{ $user->processed_rentals_count > 0 ? 'disabled' : '' }}>
                                Delete
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $users->links() }}
    </div>
</div>
@endsection
