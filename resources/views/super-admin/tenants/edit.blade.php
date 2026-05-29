@extends('layouts.admin')

@section('title', 'Edit Tenant')
@section('page_title', 'Edit Tenant: ' . $tenant->name)

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('super-admin.tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Shop Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">White-label System Name</label>
                    <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $tenant->system_name) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subdomain Slug</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $tenant->slug) }}" required>
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Primary Brand Color</label>
                    <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', $tenant->primary_color) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Plan</label>
                    <select name="plan" class="form-select">
                        <option value="Basic" {{ $tenant->plan == 'Basic' ? 'selected' : '' }}>Basic</option>
                        <option value="Standard" {{ $tenant->plan == 'Standard' ? 'selected' : '' }}>Standard</option>
                        <option value="Premium" {{ $tenant->plan == 'Premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Max Users</label>
                    <input type="number" name="max_users" class="form-control" value="{{ $tenant->max_users }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Max Tools</label>
                    <input type="number" name="max_tools" class="form-control" value="{{ $tenant->max_tools }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Shop Logo</label>
                @if($tenant->logo)
                    <div class="mb-2"><img src="{{ asset('storage/' . $tenant->logo) }}" height="40"></div>
                @endif
                <input type="file" name="logo" class="form-control" accept="image/*">
            </div>

            <div class="mb-4">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ $tenant->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$tenant->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium d-block">Enabled Menu Items (Features)</label>
                @php $activeFeatures = $tenant->features ?? []; @endphp
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="categories" id="feat-cat" {{ in_array('categories', $activeFeatures) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feat-cat">Categories</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="tools" id="feat-tools" {{ in_array('tools', $activeFeatures) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feat-tools">Tools Inventory</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="customers" id="feat-cust" {{ in_array('customers', $activeFeatures) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feat-cust">Customers</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="rentals" id="feat-rent" {{ in_array('rentals', $activeFeatures) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feat-rent">Rentals & Returns</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="reports" id="feat-rep" {{ in_array('reports', $activeFeatures) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feat-rep">Reports</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="invoicing" id="feat-inv" {{ in_array('invoicing', $activeFeatures) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feat-inv">PDF Invoicing</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="qrcode" id="feat-qr" {{ in_array('qrcode', $activeFeatures) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feat-qr">QR Codes</label>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Shop Tenant</button>
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
