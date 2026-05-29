@extends('layouts.admin')

@section('title', 'Create Tenant')
@section('page_title', 'Create Tenant')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('super-admin.tenants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Shop Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">White-label System Name</label>
                    <input type="text" name="system_name" class="form-control" value="{{ old('system_name', 'ToolRent Pro') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subdomain Slug</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Primary Brand Color</label>
                    <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', '#0d6efd') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Plan</label>
                    <select name="plan" class="form-select" required>
                        @foreach(['Basic', 'Standard', 'Premium'] as $plan)
                            <option value="{{ $plan }}" {{ old('plan', $defaultPlan) === $plan ? 'selected' : '' }}>{{ $plan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Max Users</label>
                    <input type="number" name="max_users" class="form-control" value="{{ old('max_users', 5) }}" min="1" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Max Tools</label>
                    <input type="number" name="max_tools" class="form-control" value="{{ old('max_tools', 50) }}" min="1" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Secondary Brand Color</label>
                    <input type="color" name="secondary_color" class="form-control form-control-color w-100" value="{{ old('secondary_color', '#6c757d') }}">
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Shop Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Favicon</label>
                <input type="file" name="favicon" class="form-control" accept=".ico,.png,.jpg,.jpeg,.svg">
            </div>

            <div class="mb-4">
                <label class="form-label">Custom CSS</label>
                <textarea name="custom_css" class="form-control" rows="4" placeholder="Optional trusted tenant CSS">{{ old('custom_css') }}</textarea>
                <div class="form-text">Only add CSS from trusted shop administrators.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium d-block">Enable Menu Items (Features)</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="categories" id="feat-cat" checked>
                    <label class="form-check-label" for="feat-cat">Categories</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="tools" id="feat-tools" checked>
                    <label class="form-check-label" for="feat-tools">Tools Inventory</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="customers" id="feat-cust" checked>
                    <label class="form-check-label" for="feat-cust">Customers</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="rentals" id="feat-rent" checked>
                    <label class="form-check-label" for="feat-rent">Rentals & Returns</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="reports" id="feat-rep" checked>
                    <label class="form-check-label" for="feat-rep">Reports</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="invoicing" id="feat-inv" checked>
                    <label class="form-check-label" for="feat-inv">PDF Invoicing</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="features[]" value="qrcode" id="feat-qr" checked>
                    <label class="form-check-label" for="feat-qr">QR Codes</label>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Create Shop Tenant</button>
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
