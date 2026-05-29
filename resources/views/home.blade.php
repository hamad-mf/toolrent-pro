@extends('layouts.app')

@section('title', 'Account Pending')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-shield-exclamation display-1 text-warning mb-3"></i>

                    <h2 class="fw-bold mb-2">Account not yet configured</h2>
                    <p class="text-muted mb-4">
                        Hi {{ Auth::user()->name }} — your account is signed in, but it has not been
                        assigned a role yet. Please contact your shop administrator to grant access
                        to the dashboard.
                    </p>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('logout') }}"
                           class="btn btn-outline-secondary"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i> Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
