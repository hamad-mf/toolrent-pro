@extends('layouts.admin')

@section('title', 'Edit Staff Member')
@section('page_title', 'Edit Staff Profile')

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.staff.update', $staff) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $staff->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Email Address *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staff->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">New Password (Optional)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Role *</label>
                            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $staff->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status *</label>
                            <select name="is_active" class="form-select" required>
                                <option value="1" {{ old('is_active', $staff->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $staff->is_active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Update Account</button>
                        <a href="{{ route('shop-admin.staff.index') }}" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
