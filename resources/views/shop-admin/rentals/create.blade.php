@extends('layouts.admin')

@section('title', 'New Checkout')
@section('page_title', 'Tool Checkout')

@section('content')
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.rentals.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Select Customer *</label>
                            <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">Choose a customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="mt-2">
                                <a href="{{ route('shop-admin.customers.create') }}" class="small text-decoration-none">+ Register New Customer</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Select Tool *</label>
                            <select name="tool_id" class="form-select @error('tool_id') is-invalid @enderror" required>
                                <option value="">Choose an available tool...</option>
                                @foreach($tools as $tool)
                                    <option value="{{ $tool->id }}" {{ old('tool_id') == $tool->id ? 'selected' : '' }}>
                                        {{ $tool->name }} - ${{ number_format($tool->daily_rate, 2) }}/day
                                    </option>
                                @endforeach
                            </select>
                            @error('tool_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Return Due Date *</label>
                            <input type="text" name="due_at" id="due_at" class="form-control @error('due_at') is-invalid @enderror" value="{{ old('due_at', date('Y-m-d', strtotime('+1 day'))) }}" required>
                            @error('due_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Security Deposit</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="deposit" class="form-control @error('deposit') is-invalid @enderror" value="{{ old('deposit', 0) }}">
                            </div>
                            <div class="form-text">Refundable amount held during the rental.</div>
                            @error('deposit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Discount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', 0) }}">
                            </div>
                            <div class="form-text">Applied to the final total on return.</div>
                            @error('discount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Rental Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Condition at checkout, accessories included, etc.">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Process Checkout</button>
                        <a href="{{ route('shop-admin.rentals.index') }}" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Staff Guidance</h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-2">Ensure the customer's ID has been verified before checkout.</li>
                    <li class="mb-2">Brief the customer on safe operation of the tool.</li>
                    <li class="mb-2">Inspect the tool for existing damage and note it above.</li>
                    <li>The daily rate is fixed at checkout.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    flatpickr("#due_at", {
        minDate: "today",
        dateFormat: "Y-m-d",
        defaultDate: "{{ old('due_at', date('Y-m-d', strtotime('+1 day'))) }}"
    });
});
</script>
@endsection
