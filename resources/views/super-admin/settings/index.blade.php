@extends('layouts.admin')

@section('title', 'Global Settings')
@section('page_title', 'System Settings')

@section('content')
<div class="row">
    <div class="col-xl-7">
        <div class="tr-card">
            <div class="tr-card-header">
                <h6 class="mb-0 fw-bold">Global Platform Configuration</h6>
            </div>
            <div class="tr-card-body">
                <form action="{{ route('super-admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-medium">Platform Name</label>
                        <input type="text" name="platform_name" class="form-control" value="{{ old('platform_name', $settings['platform_name']) }}" required>
                        <div class="form-text">Default product name shown to tenants without a custom system name.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Support Email</label>
                        <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $settings['support_email']) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Default Plan for New Tenants</label>
                        <select name="default_plan" class="form-select">
                            @foreach(['Basic', 'Standard', 'Premium'] as $plan)
                                <option value="{{ $plan }}" {{ $settings['default_plan'] === $plan ? 'selected' : '' }}>{{ $plan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" {{ $settings['maintenance_mode'] === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                        </div>
                        <div class="form-text">When enabled, you can display a maintenance notice across the platform.</div>
                    </div>

                    <div class="pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Save Global Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
