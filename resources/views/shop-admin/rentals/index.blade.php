@extends('layouts.admin')

@section('title', 'Rentals & Returns')
@section('page_title', 'Rental Tracking')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h5 class="mb-0 fw-bold">Active & Recent Rentals</h5>
        <a href="{{ route('shop-admin.rentals.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-cart-check me-1"></i> New Checkout
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Tool</th>
                    <th>Customer</th>
                    <th>Checkout / Due</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                <tr>
                    <td class="ps-4">
                        <div class="fw-medium text-dark"><a href="{{ route('shop-admin.rentals.show', $rental) }}" class="text-decoration-none text-dark">{{ $rental->tool->name }}</a></div>
                        <div class="small text-muted">{{ $rental->tool->brand }}</div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $rental->customer->name }}</div>
                        <div class="small text-muted">{{ $rental->customer->phone }}</div>
                    </td>
                    <td>
                        <div class="small">Out: {{ $rental->checkout_at->format('M d, Y') }}</div>
                        <div class="small {{ $rental->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                            Due: {{ $rental->due_at->format('M d, Y') }}
                        </div>
                    </td>
                    <td>
                        @if($rental->status === 'Active')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">In Use</span>
                        @elseif($rental->status === 'Returned')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Returned</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Overdue</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        @if($rental->status === 'Active' || $rental->status === 'Overdue')
                        <form action="{{ route('shop-admin.rentals.return', $rental) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success px-3" data-confirm="Process tool return and calculate final price?">
                                Return Tool
                            </button>
                        </form>
                        @else
                            @if(Auth::user()->tenant->hasFeature('invoicing'))
                                <a href="{{ route('shop-admin.rentals.invoice', $rental) }}" class="btn btn-sm btn-light border text-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Invoice
                                </a>
                            @else
                                <span class="text-muted small">Returned on {{ $rental->returned_at->format('M d') }}</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-4 mb-3 d-block opacity-25"></i>
                        No rental records found. Click "New Checkout" to start.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($rentals->hasPages())
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $rentals->links() }}
    </div>
    @endif
</div>
@endsection
