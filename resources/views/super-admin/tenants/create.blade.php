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
                    <input type="text" name="system_name" class="form-control" value="ToolRent Pro">
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
                    <input type="color" name="primary_color" class="form-control form-control-color w-100" value="#0d6efd">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Shop Logo</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
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
