@extends('layouts.admin')

@section('title', 'Tool QR Code')
@section('page_title', 'Tool QR Code: ' . $tool->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card p-5">
            <div class="card-body">
                <h4 class="fw-bold mb-4">{{ $tool->name }}</h4>
                <div class="mb-4 bg-white d-inline-block p-3 rounded shadow-sm border">
                    {!! $qrCode !!}
                </div>
                <div class="text-muted small mb-4">
                    Brand: {{ $tool->brand ?? 'N/A' }} | SN: {{ $tool->serial_number ?? 'N/A' }}
                </div>
                <div>
                    <button class="btn btn-primary px-4" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i> Print Tag
                    </button>
                    <a href="{{ route('shop-admin.tools.index') }}" class="btn btn-light border px-4 ms-2">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
