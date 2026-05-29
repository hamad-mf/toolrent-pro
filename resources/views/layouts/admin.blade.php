<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $tenant = session('tenant_id') ? \App\Models\Tenant::find(session('tenant_id')) : null;
        $systemName = $tenant->system_name ?? config('app.name', 'ToolRent Pro');
    @endphp

    <title>@yield('title') - {{ $systemName }} Admin</title>

    @if($tenant && $tenant->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $tenant->favicon) }}">
    @endif

    {{-- Anti-FOUC + tenant accent (must come before stylesheets) --}}
    @include('partials.theme-head')

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Design System Tokens -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr (Datepicker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <style>
        header.tr-topbar {
            background: var(--tr-surface);
            border-bottom: 1px solid var(--tr-border);
        }
        .navbar-brand { color: var(--tr-text) !important; font-weight: 700; }

        .sidebar {
            background: var(--tr-surface);
            border-right: 1px solid var(--tr-border);
        }
        @media (min-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0; bottom: 0; left: 0;
                z-index: 100;
                padding-top: 60px;
                width: inherit;
            }
        }
        .sidebar-sticky { height: calc(100vh - 60px); overflow-y: auto; }
        .sidebar .nav-link {
            font-weight: 500;
            color: var(--tr-text-muted);
            padding: .7rem 1.25rem;
            display: flex;
            align-items: center;
            border-radius: var(--tr-radius-sm);
            margin: .15rem .75rem;
            position: relative;
            transition: background-color .15s ease, color .15s ease;
        }
        .sidebar .nav-link .bi { margin-right: 10px; font-size: 1.1rem; }
        .sidebar .nav-link:hover {
            background-color: rgba(var(--tr-primary-rgb), .08);
            color: var(--tr-primary);
        }
        .sidebar .nav-link.active {
            background-color: rgba(var(--tr-primary-rgb), .12);
            color: var(--tr-primary);
        }
        .sidebar .nav-link.active::before {
            content: "";
            position: absolute;
            left: 0; top: 9px; bottom: 9px;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--tr-primary);
        }
        .sidebar-heading {
            letter-spacing: .05rem;
            color: var(--tr-text-subtle);
        }
        .tr-avatar {
            width: 28px; height: 28px; font-size: .75rem;
            background: var(--tr-primary); color: var(--tr-on-primary);
        }
        .tr-user-pill { background: var(--tr-surface-2); }
        .card:hover { box-shadow: var(--tr-shadow-md); }
    </style>
    @if($tenant && $tenant->custom_css)
        <style>{!! $tenant->custom_css !!}</style>
    @endif
    @yield('styles')
</head>
<body>

<header class="tr-topbar navbar sticky-top flex-md-nowrap p-0 shadow-none">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-4 fs-5 d-flex align-items-center" href="{{ url('/home') }}">
        @if($tenant && $tenant->logo)
            <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo" style="max-height: 30px;" class="me-2">
        @else
            <i class="bi bi-tools text-primary me-2"></i>
        @endif
        {{ session('tenant_name', 'ToolRent Pro') }}
    </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed border-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="w-100 d-flex flex-row justify-content-end align-items-center px-4 gap-3">
        @include('partials.theme-toggle')
        <div class="d-flex align-items-center">
            <div class="tr-user-pill rounded-pill px-3 py-1 d-flex align-items-center">
                <span class="text-muted small me-2">{{ Auth::user()->name }}</span>
                <div class="tr-avatar rounded-circle d-flex align-items-center justify-content-center">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
            <a class="ms-3 text-muted text-decoration-none small" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Sign out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3 sidebar-sticky">
                <ul class="nav flex-column">
                    @if(Auth::user()->isSuperAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('super-admin.dashboard') ? 'active' : '' }}" href="{{ route('super-admin.dashboard') }}">
                                <i class="bi bi-grid"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('super-admin.tenants.*') ? 'active' : '' }}" href="{{ route('super-admin.tenants.index') }}">
                                <i class="bi bi-shop-window"></i> Tenants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('super-admin.users.*') ? 'active' : '' }}" href="{{ route('super-admin.users.index') }}">
                                <i class="bi bi-people"></i> User Management
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('shop-admin.dashboard') ? 'active' : '' }}" href="{{ route('shop-admin.dashboard') }}">
                                <i class="bi bi-grid"></i> Dashboard
                            </a>
                        </li>

                        @if(Auth::user()->isShopAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('shop-admin.staff.*') ? 'active' : '' }}" href="{{ route('shop-admin.staff.index') }}">
                                <i class="bi bi-person-badge"></i>
                                Staff Management
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->hasRole(['shop-admin', 'manager']) && Auth::user()->tenant->hasFeature('categories'))
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('shop-admin.categories.*') ? 'active' : '' }}" href="{{ route('shop-admin.categories.index') }}">
                                <i class="bi bi-tags"></i>
                                Categories
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->hasRole(['shop-admin', 'manager', 'counter-staff', 'floor-staff']) && Auth::user()->tenant->hasFeature('tools'))
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('shop-admin.tools.*') ? 'active' : '' }}" href="{{ route('shop-admin.tools.index') }}">
                                <i class="bi bi-tools"></i>
                                {{ Auth::user()->hasRole('floor-staff') ? 'Equipment List' : 'Tools Inventory' }}
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->hasRole(['shop-admin', 'manager', 'counter-staff']) && Auth::user()->tenant->hasFeature('customers'))
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('shop-admin.customers.*') ? 'active' : '' }}" href="{{ route('shop-admin.customers.index') }}">
                                <i class="bi bi-people"></i>
                                Customers
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->hasRole(['shop-admin', 'manager', 'counter-staff']) && Auth::user()->tenant->hasFeature('rentals'))
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('shop-admin.rentals.*') ? 'active' : '' }}" href="{{ route('shop-admin.rentals.index') }}">
                                <i class="bi bi-calendar-check"></i>
                                Rentals & Returns
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->hasRole(['shop-admin', 'manager']) && Auth::user()->tenant->hasFeature('reports'))
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('shop-admin.reports.*') ? 'active' : '' }}" href="{{ route('shop-admin.reports.index') }}">
                                <i class="bi bi-graph-up"></i>
                                Business Reports
                            </a>
                        </li>
                        @endif
                    @endif
                </ul>

                @if(Auth::user()->isSuperAdmin() || Auth::user()->isShopAdmin())
                <div class="px-4 mt-5 mb-2 text-uppercase small fw-bold sidebar-heading">System</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        @php
                            $settingsRoute = Auth::user()->isSuperAdmin() ? route('super-admin.settings.index') : route('shop-admin.settings.edit');
                            $settingsActive = Auth::user()->isSuperAdmin() ? Route::is('super-admin.settings.index') : Route::is('shop-admin.settings.edit');
                        @endphp
                        <a class="nav-link {{ $settingsActive ? 'active' : '' }}" href="{{ $settingsRoute }}">
                            <i class="bi bi-sliders"></i> Settings
                        </a>
                    </li>
                </ul>
                @endif
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4" style="border-bottom: 1px solid var(--tr-border);">
                <h1 class="h3 fw-bold mb-0">@yield('page_title', 'Dashboard')</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger shadow-sm alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger shadow-sm alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // DataTables (search + sort only; Laravel handles pagination)
    if (window.jQuery && jQuery.fn.DataTable) {
        $('.datatable').each(function () {
            var $table = $(this);
            // Skip empty tables: the "empty" placeholder uses a single colspan cell,
            // which would trigger a DataTables "Incorrect column count" warning.
            if ($table.find('tbody td[colspan]').length > 0 || $table.find('tbody tr').length === 0) {
                return;
            }
            $table.DataTable({
                "paging": false,
                "info": false,
                "searching": true,
                "ordering": true
            });
        });
    }

    // SweetAlert2 confirmation for [data-confirm] elements
    function accent() {
        return getComputedStyle(document.documentElement).getPropertyValue('--tr-primary').trim() || '#4f46e5';
    }
    document.body.addEventListener('click', function(e) {
        const confirmBtn = e.target.closest('[data-confirm]');
        if (confirmBtn) {
            e.preventDefault();
            const message = confirmBtn.getAttribute('data-confirm') || 'Are you sure?';
            const form = confirmBtn.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: accent(),
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (form) { form.submit(); }
                    else if (confirmBtn.tagName === 'A') { window.location.href = confirmBtn.href; }
                }
            });
        }
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success!', text: @json(session('success')), timer: 3000, showConfirmButton: false, toast: true, position: 'top-end' });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Error!', text: @json(session('error')), timer: 5000, showConfirmButton: true, toast: true, position: 'top-end' });
    @endif
});
</script>
@yield('scripts')
</body>
</html>
