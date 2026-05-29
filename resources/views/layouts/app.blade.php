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

    <title>@yield('title') {{ session('tenant_name') ? '- ' . session('tenant_name') : $systemName }}</title>

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

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .tr-public-navbar {
            background-color: var(--tr-surface);
            border-bottom: 1px solid var(--tr-border);
        }
        .card:hover { box-shadow: var(--tr-shadow-md); }
        .btn-primary:hover { transform: translateY(-1px); }
    </style>
    @if($tenant && $tenant->custom_css)
        <style>{!! $tenant->custom_css !!}</style>
    @endif
    @yield('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md tr-public-navbar sticky-top">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                    @if($tenant && $tenant->logo)
                        <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo" style="max-height: 28px;" class="me-2">
                    @else
                        <i class="bi bi-tools me-2 text-primary"></i>
                    @endif
                    <span>{{ session('tenant_name', 'ToolRent Pro') }}</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto align-items-center gap-2">
                        <li class="nav-item d-flex align-items-center">
                            @include('partials.theme-toggle')
                        </li>
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem; background: var(--tr-primary); color: var(--tr-on-primary);">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2">
                                    <a class="dropdown-item py-2" href="{{ route('home') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-5">
            @if(\App\Models\Setting::get('maintenance_mode', '0') === '1')
                <div class="container">
                    <div class="alert alert-warning shadow-sm">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Platform maintenance mode is enabled.
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
