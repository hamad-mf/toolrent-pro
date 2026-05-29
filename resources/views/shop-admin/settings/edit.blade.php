@extends('layouts.admin')

@section('title', 'Shop Configuration')
@section('page_title', 'My Shop Settings')

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold">Brand Configuration</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-medium">Shop Display Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">White-label System Name</label>
                        <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $tenant->system_name) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-medium">Primary Brand Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', $tenant->primary_color) }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-medium">Secondary Brand Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color w-100" value="{{ old('secondary_color', $tenant->secondary_color) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Shop Logo</label>
                        @if($tenant->logo)
                            <div class="mb-2"><img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo" style="max-height: 40px;"></div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Favicon</label>
                        @if($tenant->favicon)
                            <div class="mb-2"><img src="{{ asset('storage/' . $tenant->favicon) }}" alt="Favicon" style="max-height: 24px;"></div>
                        @endif
                        <input type="file" name="favicon" class="form-control" accept=".ico,.png,.jpg,.jpeg,.svg">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Custom CSS</label>
                        <textarea name="custom_css" class="form-control" rows="4" placeholder="Optional trusted tenant CSS">{{ old('custom_css', $tenant->custom_css) }}</textarea>
                        <div class="form-text">Only add CSS from trusted shop administrators.</div>
                    </div>

                    <div class="pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Save Brand Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold">Plan & Limits</h6>
            </div>
            <div class="card-body p-4">
                <div class="row text-center">
                    <div class="col-4 border-end">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Plan</div>
                        <div class="badge bg-primary px-3 py-2">{{ $tenant->plan }}</div>
                    </div>
                    <div class="col-4 border-end">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Users</div>
                        <div class="fs-4 fw-bold text-dark">{{ $tenant->max_users }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Equipment</div>
                        <div class="fs-4 fw-bold text-dark">{{ $tenant->max_tools }}</div>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Enabled Features</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($tenant->features ?? [] as $feature)
                            <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-check-circle-fill text-success me-1"></i> {{ ucfirst($feature) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
