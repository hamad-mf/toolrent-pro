@extends('layouts.admin')

@section('title', 'Add Staff Member')
@section('page_title', 'Add Staff Member')

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.staff.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Email Address *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Password *</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Role *</label>
                        <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                            <option value="">Select Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text mt-2 small text-muted">
                            <strong>Manager:</strong> All features except settings.<br>
                            <strong>Counter Staff:</strong> Rentals, Returns, Customers.<br>
                            <strong>Floor Staff:</strong> Tool status and maintenance only.
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Create Account</button>
                        <a href="{{ route('shop-admin.staff.index') }}" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
