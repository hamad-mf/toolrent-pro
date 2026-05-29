@extends('layouts.admin')

@section('title', 'Edit Customer')
@section('page_title', 'Edit Customer Profile')

@section('content')
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Full Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">ID Type</label>
                            <select name="id_type" class="form-select">
                                <option value="">Select...</option>
                                <option value="Driver License" {{ $customer->id_type == 'Driver License' ? 'selected' : '' }}>Driver License</option>
                                <option value="National ID" {{ $customer->id_type == 'National ID' ? 'selected' : '' }}>National ID</option>
                                <option value="Passport" {{ $customer->id_type == 'Passport' ? 'selected' : '' }}>Passport</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">ID Number</label>
                            <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $customer->id_number) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Physical Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Internal Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ $customer->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$customer->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                        <a href="{{ route('shop-admin.customers.index') }}" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
