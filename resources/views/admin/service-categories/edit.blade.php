@extends('admin.layout')

@section('title', 'Edit Service Group')

@section('content')
    <form method="POST" action="{{ route('admin.service-categories.update', $category) }}">
        @csrf
        @method('PUT')
        @include('admin.service-categories._form')
    </form>
@endsection
