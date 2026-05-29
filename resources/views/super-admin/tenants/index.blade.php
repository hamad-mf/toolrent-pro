@extends('layouts.admin')

@section('title', 'Manage Tenants')
@section('page_title', 'Manage Tenants')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="mb-0 fw-bold">Tenants List</h5>
        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary btn-sm">Add New Tenant</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Plan</th>
                    <th>Active</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $tenant)
                <tr>
                    <td class="ps-4">{{ $tenant->id }}</td>
                    <td>{{ $tenant->name }}</td>
                    <td><code>{{ $tenant->slug }}</code></td>
                    <td>{{ $tenant->plan }}</td>
                    <td>
                        @if($tenant->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('super-admin.tenants.inspect', $tenant) }}" class="btn btn-sm btn-light border text-primary me-1" title="Inspect Shop Data"><i class="bi bi-search"></i></a>
                        <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-light border text-primary me-2">Edit</a>
                        <form action="{{ route('super-admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-confirm="Delete this tenant and ALL its data permanently? This cannot be undone.">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
