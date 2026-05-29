@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5">
            <div class="text-center mb-5">
                @php
                    $tenantId = session('tenant_id');
                    $tenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
                @endphp
                
                @if($tenant && $tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo" class="mb-3" style="max-height: 80px;">
                @else
                    <i class="bi bi-tools text-primary display-1 mb-3"></i>
                @endif
                
                <h2 class="fw-bold">{{ $tenant->name ?? config('app.name', 'ToolRent Pro') }}</h2>
                <p class="text-muted">Sign in to manage your shop</p>
            </div>

            <div class="card shadow-lg p-4 border-0">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label fw-medium">{{ __('Password') }}</label>
                                @if (Route::has('password.request'))
                                    <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                @endif
                            </div>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small" for="remember">
                                    {{ __('Keep me signed in for 30 days') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ __('Sign In') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if(!session('tenant_id'))
            <div class="text-center mt-4">
                <p class="text-muted small">Contact administration to register a new shop.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
