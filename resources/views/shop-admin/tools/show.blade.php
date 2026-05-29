@extends('layouts.admin')

@section('title', 'Tool Details')
@section('page_title', $tool->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <span class="badge bg-light text-dark border mb-2">{{ $tool->category->name }}</span>
                        <h2 class="fw-bold mb-1">{{ $tool->name }}</h2>
                        <p class="text-muted">{{ $tool->brand }} {{ $tool->model_number }}</p>
                    </div>
                    <div>
                        @if($tool->status === 'Available')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fs-6">Available</span>
                        @elseif($tool->status === 'Reserved')
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 fs-6">Reserved</span>
                        @elseif($tool->status === 'Rented')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 fs-6">Rented</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 fs-6">Maintenance</span>
                        @endif
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block mb-1">Serial Number</small>
                            <span class="fw-medium">{{ $tool->serial_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block mb-1">Daily Rental Rate</small>
                            <span class="fw-bold text-primary fs-5">${{ number_format($tool->daily_rate, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Description</h6>
                    <p class="text-muted">{{ $tool->description ?? 'No description provided.' }}</p>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Condition Notes</h6>
                    <div class="p-3 border rounded-3 bg-light">
                        {{ $tool->condition_notes ?? 'No condition notes recorded.' }}
                    </div>
                    @if($tool->condition_updated_at)
                        <div class="small text-muted mt-2">
                            Last updated {{ $tool->condition_updated_at->diffForHumans() }}
                            @if($tool->conditionUpdatedBy)
                                by {{ $tool->conditionUpdatedBy->name }}
                            @endif
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    @if(Auth::user()->hasRole(['shop-admin', 'manager']))
                    <a href="{{ route('shop-admin.tools.edit', $tool) }}" class="btn btn-primary px-4">
                        <i class="bi bi-pencil me-1"></i> Edit Tool
                    </a>
                    @endif
                    @if(Auth::user()->hasRole(['shop-admin', 'manager', 'floor-staff']) && !in_array($tool->status, ['Rented', 'Reserved']))
                    <form action="{{ route('shop-admin.tools.maintenance', $tool) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary px-4">
                            <i class="bi bi-wrench-adjustable me-1"></i>
                            {{ $tool->status === 'Maintenance' ? 'Mark Available' : 'Send to Maintenance' }}
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('shop-admin.tools.index') }}" class="btn btn-light border px-4">Back to List</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @if(Auth::user()->hasRole(['shop-admin', 'manager']) && Auth::user()->tenant->hasFeature('qrcode'))
        <div class="card mb-4 text-center">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold">Tool QR Code</h6>
            </div>
            <div class="card-body p-4">
                <div class="bg-white d-inline-block p-3 rounded shadow-sm border mb-3">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate("ToolRentPro:ToolID:{$tool->id}") !!}
                </div>
                <div>
                    <a href="{{ route('shop-admin.tools.qrcode', $tool) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer me-1"></i> Print Tag
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if($tool->image)
        <div class="card mb-4">
            <img src="{{ asset('storage/' . $tool->image) }}" class="card-img-top rounded-top" alt="{{ $tool->name }}">
        </div>
        @endif

        @if(Auth::user()->hasRole(['shop-admin', 'manager', 'floor-staff']))
        <div class="card mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold">Update Condition</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.tools.condition', $tool) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium">Condition Notes</label>
                        <textarea name="condition_notes" class="form-control" rows="4">{{ old('condition_notes', $tool->condition_notes) }}</textarea>
                    </div>
                    @if(!in_array($tool->status, ['Rented', 'Reserved']))
                    <div class="mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <select name="status" class="form-select">
                            <option value="Available" {{ $tool->status === 'Available' ? 'selected' : '' }}>Available</option>
                            <option value="Maintenance" {{ $tool->status === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    @endif
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Save Condition
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
