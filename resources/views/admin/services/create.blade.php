@extends('admin.layout')

@section('title', 'Add Service')

@section('actions')
    <a href="{{ route('admin.services.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.services.store') }}">
        @csrf
        @include('admin.services._form')
    </form>
@endsection
