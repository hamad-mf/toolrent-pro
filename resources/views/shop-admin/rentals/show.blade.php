@extends('layouts.admin')

@section('title', 'Rental Details')
@section('page_title', 'Rental Record #' . sprintf('%06d', $rental->id))

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                <h6 class="mb-0 fw-bold">Rental Information</h6>
                <div>
                    @if($rental->status === 'Pending')
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2">Booked</span>
                    @elseif($rental->status === 'Active')
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">In Use</span>
                    @elseif($rental->status === 'Returned')
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">Returned</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">Overdue</span>
                    @endif
                </div>
            </div>
            <div class="card-body p-4 border-top">
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <h6 class="text-muted small text-uppercase fw-bold mb-3">Customer</h6>
                        <h5 class="fw-bold mb-1">{{ $rental->customer->name }}</h5>
                        <p class="text-muted mb-1"><i class="bi bi-telephone me-2"></i>{{ $rental->customer->phone }}</p>
                        <p class="text-muted mb-0"><i class="bi bi-envelope me-2"></i>{{ $rental->customer->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-muted small text-uppercase fw-bold mb-3">Tool Details</h6>
                        <h5 class="fw-bold mb-1">{{ $rental->tool->name }}</h5>
                        <p class="text-muted mb-1">{{ $rental->tool->brand }} {{ $rental->tool->model_number }}</p>
                        <p class="text-muted mb-0">SN: {{ $rental->tool->serial_number }}</p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block mb-1">Checkout Date</small>
                            <span class="fw-medium">{{ $rental->checkout_at?->format('M d, Y H:i') ?? 'Pending checkout' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block mb-1">Due Date</small>
                            <span class="fw-medium">{{ $rental->due_at?->format('M d, Y') ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block mb-1">Returned Date</small>
                            <span class="fw-medium">{{ $rental->returned_at ? $rental->returned_at->format('M d, Y H:i') : 'Pending' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1">Daily Rate</small>
                            <span class="fw-bold text-dark fs-5">${{ number_format($rental->daily_rate, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3 h-100 border border-primary border-opacity-10">
                            <small class="text-primary d-block mb-1 fw-bold">Total Price</small>
                            <span class="fw-bold text-primary fs-4">${{ number_format($rental->total_price ?? 0, 2) }}</span>
                            @if(!$rental->returned_at)
                                <small class="d-block text-primary text-opacity-75 mt-1">(Estimated upon return)</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1">Deposit</small>
                            <span class="fw-medium">${{ number_format($rental->deposit, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1">Discount</small>
                            <span class="fw-medium">${{ number_format($rental->discount, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1">Late Fee</small>
                            <span class="fw-medium">${{ number_format($rental->late_fee, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Internal Notes</h6>
                    <div class="p-3 border rounded-3 bg-light min-vh-10">
                        {{ $rental->notes ?? 'No notes recorded.' }}
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @if($rental->status === 'Pending')
                    <form action="{{ route('shop-admin.rentals.checkout', $rental) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary px-4" data-confirm="Check out this booking now?">
                            <i class="bi bi-cart-check me-1"></i> Check Out Booking
                        </button>
                    </form>
                    @endif

                    @if(in_array($rental->status, ['Active', 'Overdue']))
                    <form action="{{ route('shop-admin.rentals.return', $rental) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4" data-confirm="Confirm tool return and calculate the final price?">
                            <i class="bi bi-box-arrow-in-left me-1"></i> Return Tool
                        </button>
                    </form>
                    @endif

                    @if($rental->status === 'Returned' && Auth::user()->tenant->hasFeature('invoicing'))
                    <a href="{{ route('shop-admin.rentals.invoice', $rental) }}" class="btn btn-primary px-4">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Download Invoice
                    </a>
                    @endif
                    
                    <a href="{{ route('shop-admin.rentals.index') }}" class="btn btn-light border px-4">Back to List</a>
                </div>
            </div>
            <div class="card-footer bg-light py-3 border-top-0">
                <small class="text-muted">Processed by: <strong>{{ $rental->staff->name }}</strong> on {{ $rental->created_at->format('M d, Y') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
