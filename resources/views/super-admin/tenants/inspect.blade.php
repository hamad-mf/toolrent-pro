@extends('layouts.admin')

@section('title', 'Inspect Shop')
@section('page_title', 'Inspecting: ' . $tenant->name)

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-start border-4 border-primary">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Total Equipment</h6>
                <div class="fs-2 fw-bold text-dark">{{ $tools->count() }}</div>
                <div class="small text-muted mt-1">{{ $tools->where('status', 'Available')->count() }} Available</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-start border-4 border-success">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Total Customers</h6>
                <div class="fs-2 fw-bold text-dark">{{ $customers->count() }}</div>
                <div class="small text-muted mt-1">{{ $customers->where('is_active', true)->count() }} Active Accounts</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-start border-4 border-info">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Total Revenue</h6>
                <div class="fs-2 fw-bold text-dark">${{ number_format($rentals->sum('total_price'), 2) }}</div>
                <div class="small text-muted mt-1">From {{ $rentals->where('status', 'Returned')->count() }} transactions</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Recent Transactions (Last 20)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Tool</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th class="text-end pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                <tr>
                    <td class="ps-4">{{ $rental->tool->name }}</td>
                    <td>{{ $rental->customer->name }}</td>
                    <td>{{ $rental->checkout_at->format('M d, Y') }}</td>
                    <td>${{ number_format($rental->total_price ?? 0, 2) }}</td>
                    <td class="text-end pe-4">
                        @if($rental->status === 'Returned')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1">Completed</span>
                        @else
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1">Active</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No rental data for this shop.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-primary px-4">Edit Configuration</a>
    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-light border px-4">Back to List</a>
</div>
@endsection
