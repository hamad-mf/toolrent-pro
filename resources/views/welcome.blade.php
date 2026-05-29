@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5 py-5">
        <div class="col-lg-6 text-center text-lg-start">
            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3">v1.0 Now Live</span>
            <h1 class="display-3 fw-bold mb-4 tracking-tight">Rental management <br><span class="text-primary">simplified.</span></h1>
            <p class="lead text-muted mb-5 pe-lg-5">Empower your power tool rental business with our modern, intuitive, and all-in-one management platform.</p>
            <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 shadow-sm">Get Started</a>
                <a href="#features" class="btn btn-light btn-lg px-5 border shadow-sm text-dark">View Features</a>
            </div>
        </div>
        <div class="col-lg-6 mt-5 mt-lg-0">
            <div class="position-relative">
                <div class="p-5 bg-white shadow-lg rounded-4 text-center border overflow-hidden">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4">
                        <i class="bi bi-tools display-4 text-primary"></i>
                    </div>
                    <h2 class="fw-bold h4">Modern Tools for Modern Shops</h2>
                    <p class="text-muted">Streamline inventory, tracking, and billing in one beautiful interface.</p>
                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h5 fw-bold mb-0">100%</div>
                                <small class="text-muted">Cloud</small>
                            </div>
                            <div class="col-4 border-start border-end">
                                <div class="h5 fw-bold mb-0">PDF</div>
                                <small class="text-muted">Invoices</small>
                            </div>
                            <div class="col-4">
                                <div class="h5 fw-bold mb-0">24/7</div>
                                <small class="text-muted">Uptime</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle opacity-10" style="width: 100px; height: 100px; z-index: -1;"></div>
                <div class="position-absolute bottom-0 end-0 translate-middle-y bg-info rounded-circle opacity-10" style="width: 150px; height: 150px; z-index: -1;"></div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div id="features" class="row g-4 py-5 border-top">
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 hover-lift">
                <div class="card-body">
                    <div class="mb-3 bg-primary bg-opacity-10 rounded-3 d-inline-flex p-3">
                        <i class="bi bi-cpu fs-2 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Smart Inventory</h4>
                    <p class="text-muted mb-0">Automated tracking of tool condition, maintenance schedules, and real-time availability across your shop.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 hover-lift">
                <div class="card-body">
                    <div class="mb-3 bg-success bg-opacity-10 rounded-3 d-inline-flex p-3">
                        <i class="bi bi-lightning-charge fs-2 text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Rapid Checkout</h4>
                    <p class="text-muted mb-0">A streamlined rental flow designed for speed. Process bookings, returns, and billing in seconds, not minutes.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 hover-lift">
                <div class="card-body">
                    <div class="mb-3 bg-info bg-opacity-10 rounded-3 d-inline-flex p-3">
                        <i class="bi bi-shield-check fs-2 text-info"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Secure & Scalable</h4>
                    <p class="text-muted mb-0">Isolated tenant data with enterprise-grade security. Grow your business without worrying about infrastructure.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-tight { letter-spacing: -0.02em; }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
</style>
@endsection
