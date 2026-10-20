@extends('layouts.app')

@section('title', 'Request received')

@section('content')
    <div class="container py-5">
        <div class="section-box text-center mx-auto" style="max-width: 680px">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success text-white" style="width:84px;height:84px">
                <i class="bi bi-check-lg" style="font-size:2.8rem"></i>
            </div>
            <h2 class="fw-bold">Thank you, {{ \Illuminate\Support\Str::of($inquiry->name)->before(' ') }}!</h2>
            <p class="text-muted mb-3">We've received your request{{ $inquiry->service ? ' for '.$inquiry->service->name : '' }}. Our team will contact you on <strong>{{ $inquiry->phone }}</strong> or <strong>{{ $inquiry->email }}</strong> within 1 working day.</p>
            <div class="d-inline-flex align-items-center gap-2 border rounded-3 px-3 py-2 mb-4 bg-light">
                <span class="small text-muted">Reference</span>
                <strong class="fs-5">{{ $inquiry->reference }}</strong>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-copy="{{ $inquiry->reference }}"><i class="bi bi-copy me-1"></i>Copy</button>
            </div>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                @auth
                    <a href="{{ route('account.services') }}" class="btn btn-odin-blue"><i class="bi bi-list-check me-1"></i> My Service Requests</a>
                @endauth
                <a href="{{ route('services.index') }}" class="btn btn-odin">Browse Services</a>
                <a href="{{ route('home') }}" class="btn btn-light border">Back to Shop</a>
            </div>
        </div>
    </div>
@endsection
