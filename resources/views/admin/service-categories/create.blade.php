@extends('admin.layout')

@section('title', 'Add Service Group')

@section('content')
    <form method="POST" action="{{ route('admin.service-categories.store') }}">
        @csrf
        @include('admin.service-categories._form')
    </form>
@endsection
