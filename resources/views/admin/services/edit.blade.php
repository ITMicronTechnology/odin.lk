@extends('admin.layout')

@section('title', 'Edit Service')

@section('actions')
    <a href="{{ route('services.show', $service) }}" target="_blank" class="btn btn-light btn-sm"><i class="bi bi-eye me-1"></i> View on store</a>
    <a href="{{ route('admin.services.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.services.update', $service) }}">
        @csrf
        @method('PUT')
        @include('admin.services._form')
    </form>
@endsection
