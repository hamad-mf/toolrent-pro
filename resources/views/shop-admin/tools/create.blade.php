@extends('layouts.admin')

@section('title', 'Add Tool')
@section('page_title', 'Add Tool to Inventory')

@section('content')
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('shop-admin.tools.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Tool Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Category *</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="e.g. DeWalt, Makita">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Model Number</label>
                            <input type="text" name="model_number" class="form-control" value="{{ old('model_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Daily Rental Rate ($) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">$</span>
                                <input type="number" step="0.01" name="daily_rate" class="form-control border-start-0 ps-0 @error('daily_rate') is-invalid @enderror" value="{{ old('daily_rate', '0.00') }}" required>
                            </div>
                            @error('daily_rate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Initial Status *</label>
                            <select name="status" class="form-select" required>
                                <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Condition details, included accessories, etc.">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Condition Notes</label>
                        <textarea name="condition_notes" class="form-control" rows="3" placeholder="Current condition, included accessories, visible wear, etc.">{{ old('condition_notes') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Tool Photo</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text mt-2 text-muted">Max 2MB (PNG, JPG).</div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Add Tool</button>
                        <a href="{{ route('shop-admin.tools.index') }}" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
