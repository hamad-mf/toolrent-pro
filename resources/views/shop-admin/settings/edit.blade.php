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
                <div class="alert alert-light border small text-muted mb-4">
                    <i class="bi bi-info-circle me-1"></i> These settings are managed by the system administrator and are read-only for shop staff.
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">Shop Display Name</label>
                    <div class="fs-5 fw-medium">{{ $tenant->name }}</div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">Brand Color</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle border" style="width: 24px; height: 24px; background-color: {{ $tenant->primary_color }}"></div>
                        <code>{{ $tenant->primary_color }}</code>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label text-muted small text-uppercase fw-bold">Shop Logo</label>
                    <div class="p-3 bg-light rounded d-inline-block d-block">
                        @if($tenant->logo)
                            <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo" style="max-height: 40px;">
                        @else
                            <span class="text-muted italic small">No logo uploaded by administrator.</span>
                        @endif
                    </div>
                </div>
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
