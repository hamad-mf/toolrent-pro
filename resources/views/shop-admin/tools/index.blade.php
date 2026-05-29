@extends('layouts.admin')

@section('title', 'Manage Tools')
@section('page_title', 'Tools Inventory')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold">All Tools</h5>
            <div class="btn-group">
                <a href="{{ route('shop-admin.tools.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-light border' }}">All</a>
                <a href="{{ route('shop-admin.tools.index', ['status' => 'Available']) }}" class="btn btn-sm {{ request('status') === 'Available' ? 'btn-primary' : 'btn-light border' }}">Available</a>
                <a href="{{ route('shop-admin.tools.index', ['status' => 'Rented']) }}" class="btn btn-sm {{ request('status') === 'Rented' ? 'btn-primary' : 'btn-light border' }}">Rented</a>
            </div>
        </div>
        <a href="{{ route('shop-admin.tools.create') }}" class="btn btn-primary btn-sm {{ Auth::user()->hasRole(['shop-admin', 'manager']) ? '' : 'd-none' }}">
            <i class="bi bi-plus-lg me-1"></i> Add Tool
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Tool Name</th>
                    <th>Category</th>
                    <th>Brand/Model</th>
                    <th>Daily Rate</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tools as $tool)
                <tr>
                    <td class="ps-4">
                        <div class="fw-medium text-dark"><a href="{{ route('shop-admin.tools.show', $tool) }}" class="text-decoration-none text-dark">{{ $tool->name }}</a></div>
                        <div class="small text-muted">SN: {{ $tool->serial_number ?? 'N/A' }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $tool->category->name }}</span></td>
                    <td>
                        <div>{{ $tool->brand ?? '-' }}</div>
                        <div class="small text-muted">{{ $tool->model_number }}</div>
                    </td>
                    <td>${{ number_format($tool->daily_rate, 2) }}</td>
                    <td>
                        @if($tool->status === 'Available')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Available</span>
                        @elseif($tool->status === 'Rented')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Rented</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">Maintenance</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        @if(Auth::user()->tenant->hasFeature('qrcode'))
                            <a href="{{ route('shop-admin.tools.qrcode', $tool) }}" class="btn btn-sm btn-light border text-secondary me-1" title="QR Code"><i class="bi bi-qr-code"></i></a>
                        @endif
                        
                        @if(Auth::user()->hasRole(['shop-admin', 'manager', 'floor-staff']))
                            <form action="{{ route('shop-admin.tools.maintenance', $tool) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light border text-warning me-1" title="{{ $tool->status === 'Maintenance' ? 'Mark Available' : 'Mark Maintenance' }}" {{ $tool->status === 'Rented' ? 'disabled' : '' }}>
                                    <i class="bi {{ $tool->status === 'Maintenance' ? 'bi-check-circle' : 'bi-tools' }}"></i>
                                </button>
                            </form>
                        @endif

                        @if(Auth::user()->hasRole(['shop-admin', 'manager']))
                        <a href="{{ route('shop-admin.tools.edit', $tool) }}" class="btn btn-sm btn-light border text-primary me-2">Edit</a>
                        <form action="{{ route('shop-admin.tools.destroy', $tool) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-confirm="Delete this tool permanently?" {{ $tool->rentals()->count() > 0 ? 'disabled' : '' }}>
                                Delete
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-tools display-4 mb-3 d-block opacity-25"></i>
                        No tools found. Start building your inventory!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tools->hasPages())
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $tools->links() }}
    </div>
    @endif
</div>
@endsection
