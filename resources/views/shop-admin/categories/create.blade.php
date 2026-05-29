@extends('layouts.admin')

@section('title', 'Add Category')
@section('page_title', 'Add Category')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-medium">Category Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Drills, Saws, Generators" required autofocus>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text mt-2 text-muted">The slug will be generated automatically.</div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary">Save Category</button>
                        <a href="{{ route('shop-admin.categories.index') }}" class="btn btn-light border">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
