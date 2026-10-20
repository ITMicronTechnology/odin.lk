@extends('admin.layout')

@section('title', 'Edit Brand')

@section('content')
    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.brands._form')
    </form>
@endsection
