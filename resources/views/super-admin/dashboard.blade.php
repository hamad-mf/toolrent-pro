@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')
@section('page_title', 'Super Admin Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body tr-kpi">
                <div>
                    <div class="tr-kpi-label">Total Tenants</div>
                    <div class="tr-kpi-value">{{ $stats['total_tenants'] }}</div>
                </div>
                <span class="tr-kpi-chip tr-chip-primary"><i class="bi bi-shop-window"></i></span>
            </div>
            <div class="card-footer border-0 pt-0">
                <a class="text-decoration-none small fw-medium" href="{{ route('super-admin.tenants.index') }}">Manage Tenants <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body tr-kpi">
                <div>
                    <div class="tr-kpi-label">Active Tenants</div>
                    <div class="tr-kpi-value">{{ $stats['active_tenants'] }}</div>
                </div>
                <span class="tr-kpi-chip tr-chip-success"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="card-footer border-0 pt-0">
                <span class="text-muted small">Currently operational shops</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body tr-kpi">
                <div>
                    <div class="tr-kpi-label">Total Users</div>
                    <div class="tr-kpi-value">{{ $stats['total_users'] }}</div>
                </div>
                <span class="tr-kpi-chip tr-chip-info"><i class="bi bi-people"></i></span>
            </div>
            <div class="card-footer border-0 pt-0">
                <a class="text-decoration-none small fw-medium" href="{{ route('super-admin.users.index') }}">View Users <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-bold">Tenant Growth</h6>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-bold">Plan Distribution</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div style="width: 250px; height: 250px;">
                    <canvas id="planChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header py-3">
        <h6 class="mb-0 fw-bold">System Status</h6>
    </div>
    <div class="card-body p-4">
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i> All systems are operational. Platform is serving {{ $stats['total_tenants'] }} shops with zero downtime reported in the last 24h.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = (n) => getComputedStyle(document.documentElement).getPropertyValue(n).trim();
    const accent = token('--tr-primary') || '#4f46e5';

    const growthChart = new Chart(document.getElementById('growthChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($tenantGrowth->pluck('month')) !!},
            datasets: [{
                label: 'Total Tenants',
                data: {!! json_encode($tenantGrowth->pluck('total')) !!},
                borderColor: accent,
                backgroundColor: 'rgba(' + token('--tr-primary-rgb') + ', 0.08)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { color: token('--tr-text-muted') }, grid: { color: token('--tr-border') } },
                y: { ticks: { color: token('--tr-text-muted') }, grid: { color: token('--tr-border') } }
            },
            plugins: { legend: { labels: { color: token('--tr-text-muted') } } }
        }
    });

    const planChart = new Chart(document.getElementById('planChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($planStats->pluck('plan')) !!},
            datasets: [{
                data: {!! json_encode($planStats->pluck('total')) !!},
                backgroundColor: [token('--tr-text-subtle'), accent, token('--tr-warning'), token('--tr-success')]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: token('--tr-text-muted') } } }
        }
    });

    document.documentElement.addEventListener('tr-theme-changed', function() {
        const newAccent = token('--tr-primary');
        growthChart.data.datasets[0].borderColor = newAccent;
        growthChart.options.scales.x.grid.color = token('--tr-border');
        growthChart.options.scales.y.grid.color = token('--tr-border');
        growthChart.update();
        planChart.update();
    });
});
</script>
@endsection
