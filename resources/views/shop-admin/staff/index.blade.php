@extends('layouts.admin')

@section('title', 'Staff Management')
@section('page_title', 'Shop Staff')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="mb-0 fw-bold">Staff Directory</h5>
        <a href="{{ route('shop-admin.staff.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Add Staff Member
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="ps-4">
                        <div class="fw-medium text-dark">{{ $user->name }}</div>
                        <div class="small text-muted">Joined {{ $user->created_at->format('M d, Y') }}</div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $user->role->name }}</span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('shop-admin.staff.edit', $user) }}" class="btn btn-sm btn-light border text-primary me-2">Edit</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('shop-admin.staff.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-confirm="Delete this staff member permanently?">
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
    @if($users->hasPages())
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
