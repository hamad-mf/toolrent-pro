@extends('layouts.admin')

@section('title', 'Shop Dashboard')
@section('page_title', 'Shop Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body tr-kpi">
                <div>
                    <div class="tr-kpi-label">Total Tools</div>
                    <div class="tr-kpi-value">{{ $stats['total_tools'] }}</div>
                </div>
                <span class="tr-kpi-chip tr-chip-primary"><i class="bi bi-tools"></i></span>
            </div>
            <div class="card-footer border-0 pt-0">
                @if(Auth::user()->tenant->hasFeature('tools'))
                <a class="text-decoration-none small fw-medium" href="{{ route('shop-admin.tools.index') }}">Manage Tools <i class="bi bi-arrow-right"></i></a>
                @else
                <span class="text-muted small">Tools module disabled</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body tr-kpi">
                <div>
                    <div class="tr-kpi-label">Available</div>
                    <div class="tr-kpi-value">{{ $stats['available_tools'] }}</div>
                </div>
                <span class="tr-kpi-chip tr-chip-success"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="card-footer border-0 pt-0">
                @if(Auth::user()->tenant->hasFeature('tools'))
                <a class="text-decoration-none small fw-medium" href="{{ route('shop-admin.tools.index', ['status' => 'Available']) }}">View Available <i class="bi bi-arrow-right"></i></a>
                @else
                <span class="text-muted small">Tools module disabled</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body tr-kpi">
                <div>
                    <div class="tr-kpi-label">Rented Out</div>
                    <div class="tr-kpi-value">{{ $stats['rented_tools'] }}</div>
                </div>
                <span class="tr-kpi-chip tr-chip-warning"><i class="bi bi-calendar-check"></i></span>
            </div>
            <div class="card-footer border-0 pt-0">
                @if(Auth::user()->tenant->hasFeature('tools'))
                <a class="text-decoration-none small fw-medium" href="{{ route('shop-admin.tools.index', ['status' => 'Rented']) }}">View Rented <i class="bi bi-arrow-right"></i></a>
                @else
                <span class="text-muted small">Tools module disabled</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body tr-kpi">
                <div>
                    <div class="tr-kpi-label">Categories</div>
                    <div class="tr-kpi-value">{{ $stats['total_categories'] }}</div>
                </div>
                <span class="tr-kpi-chip tr-chip-info"><i class="bi bi-tags"></i></span>
            </div>
            <div class="card-footer border-0 pt-0">
                @if(Auth::user()->hasRole(['shop-admin', 'manager']) && Auth::user()->tenant->hasFeature('categories'))
                <a class="text-decoration-none small fw-medium" href="{{ route('shop-admin.categories.index') }}">Manage Categories <i class="bi bi-arrow-right"></i></a>
                @else
                <span class="text-muted small">Inventory grouping</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-bold">Inventory Status Distribution</h6>
            </div>
            <div class="card-body d-flex justify-content-center">
                <div style="width: 300px; height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-bold">Recent Rentals</h6>
            </div>
            @if($canViewRentals)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                    <thead>
                        <tr class="table-light">
                            <th class="ps-3">Tool</th>
                            <th>Customer</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRentals as $rental)
                        <tr>
                            <td class="ps-3">{{ $rental->tool->name }}</td>
                            <td>{{ $rental->customer->name }}</td>
                            <td>
                                @if($rental->status === 'Pending')
                                    <span class="badge bg-info px-2 py-1">Booked</span>
                                @elseif($rental->status === 'Active')
                                    <span class="badge bg-primary px-2 py-1">In Use</span>
                                @elseif($rental->status === 'Returned')
                                    <span class="badge bg-success px-2 py-1">Done</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1">Overdue</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted small">No recent rentals.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentRentals->count() > 0)
            <div class="card-footer text-center border-0">
                <a href="{{ route('shop-admin.rentals.index') }}" class="small text-decoration-none">View all rentals</a>
            </div>
            @endif
            @else
            <div class="card-body d-flex align-items-center justify-content-center text-center text-muted">
                <div>
                    <i class="bi bi-shield-lock display-6 d-block mb-2 opacity-50"></i>
                    <div class="small">Rental records are not available for this role or shop plan.</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = (n) => getComputedStyle(document.documentElement).getPropertyValue(n).trim();
    const ctx = document.getElementById('statusChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: [
                    token('--tr-success') || '#198754',
                    token('--tr-info') || '#0dcaf0',
                    token('--tr-warning') || '#ffc107',
                    token('--tr-text-subtle') || '#6c757d'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: token('--tr-text-muted') } }
            }
        }
    });

    // Refresh chart colors when the theme changes
    document.documentElement.addEventListener('tr-theme-changed', function() {
        chart.data.datasets[0].backgroundColor = [token('--tr-success'), token('--tr-info'), token('--tr-warning'), token('--tr-text-subtle')];
        chart.options.plugins.legend.labels.color = token('--tr-text-muted');
        chart.update();
    });
});
</script>
@endsection
