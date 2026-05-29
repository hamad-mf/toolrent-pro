@extends('layouts.admin')

@section('title', 'Manage Categories')
@section('page_title', 'Tool Categories')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="mb-0 fw-bold">Categories</h5>
        <a href="{{ route('shop-admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Name</th>
                    <th>Slug</th>
                    <th>Total Tools</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="ps-4 fw-medium">{{ $category->name }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $category->slug }}</span></td>
                    <td>{{ $category->tools_count }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('shop-admin.categories.edit', $category) }}" class="btn btn-sm btn-light border text-primary me-2">Edit</a>
                        <form action="{{ route('shop-admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-confirm="Delete this category? This only works if it's empty." {{ $category->tools_count > 0 ? 'disabled' : '' }}>
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No categories found. Start by adding one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
