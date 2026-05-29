@extends('layouts.admin')

@section('title', 'Manage Customers')
@section('page_title', 'Customers')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="mb-0 fw-bold">Customer Directory</h5>
        <a href="{{ route('shop-admin.customers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Add Customer
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Customer Name</th>
                    <th>Contact</th>
                    <th>ID Details</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="ps-4">
                        <div class="fw-medium text-dark">{{ $customer->name }}</div>
                        <div class="small text-muted">{{ Str::limit($customer->address, 30) }}</div>
                    </td>
                    <td>
                        <div class="small"><i class="bi bi-envelope me-1 text-muted"></i>{{ $customer->email ?? 'N/A' }}</div>
                        <div class="small"><i class="bi bi-telephone me-1 text-muted"></i>{{ $customer->phone ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div class="small fw-medium">{{ $customer->id_type ?? '-' }}</div>
                        <div class="small text-muted">{{ $customer->id_number }}</div>
                    </td>
                    <td>
                        @if($customer->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('shop-admin.customers.edit', $customer) }}" class="btn btn-sm btn-light border text-primary me-2">Edit</a>
                        <form action="{{ route('shop-admin.customers.destroy', $customer) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-confirm="Delete this customer profile permanently? This will fail if they have rental history.">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-people display-4 mb-3 d-block opacity-25"></i>
                        No customers found. Add your first customer to start renting tools.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
