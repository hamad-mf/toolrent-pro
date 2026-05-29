@extends('layouts.admin')

@section('title', 'Business Reports')
@section('page_title', 'Analytics & Reports')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Total Revenue (All Time)</h6>
                <div class="d-flex align-items-center">
                    <div class="fs-1 fw-bold text-success me-3">${{ number_format($revenue, 2) }}</div>
                    <i class="bi bi-graph-up-arrow fs-3 text-success opacity-50"></i>
                </div>
                <p class="text-muted small mt-2">Based on {{ $totalRentals }} completed rentals.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Utilization Rate</h6>
                <div class="d-flex align-items-center">
                    <div class="fs-1 fw-bold text-primary me-3">{{ $utilizationRate }}%</div>
                    <i class="bi bi-speedometer2 fs-3 text-primary opacity-50"></i>
                </div>
                <p class="text-muted small mt-2">Reserved or rented tools compared with total inventory.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Top Rented Equipment</h6>
                <ul class="list-group list-group-flush">
                    @forelse($topTools as $tool)
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                            <span>{{ $tool->name }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $tool->rentals_count }} times</span>
                        </li>
                    @empty
                        <li class="list-group-item px-0 bg-transparent text-muted small">No rental data yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3 border-bottom-0">
        <h6 class="mb-0 fw-bold">Currently Overdue</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Tool</th>
                    <th>Customer</th>
                    <th>Due Date</th>
                    <th class="text-end pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overdueRentals as $rental)
                <tr>
                    <td class="ps-4">{{ $rental->tool->name }}</td>
                    <td>{{ $rental->customer->name }}</td>
                    <td class="text-danger fw-medium">{{ $rental->due_at?->format('M d, Y') }}</td>
                    <td class="text-end pe-4">
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Overdue</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No overdue rentals right now.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3 border-bottom-0">
        <h6 class="mb-0 fw-bold">Revenue Trend (Last 30 Days)</h6>
    </div>
    <div class="card-body">
        <div style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = (n) => getComputedStyle(document.documentElement).getPropertyValue(n).trim();
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = {!! json_encode($recentRevenue) !!};

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.date),
            datasets: [{
                label: 'Daily Revenue ($)',
                data: chartData.map(item => item.total),
                borderColor: token('--tr-primary') || '#0d6efd',
                backgroundColor: `rgba(${token('--tr-primary-rgb') || '13, 110, 253'}, 0.05)`,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '$' + value; }
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Refresh chart colors when the theme changes
    document.documentElement.addEventListener('tr-theme-changed', function() {
        chart.data.datasets[0].borderColor = token('--tr-primary');
        chart.data.datasets[0].backgroundColor = `rgba(${token('--tr-primary-rgb')}, 0.05)`;
        chart.update();
    });
});
</script>
@endsection
