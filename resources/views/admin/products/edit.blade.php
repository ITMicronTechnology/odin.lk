@extends('admin.layout')

@section('title', 'Edit Product')

@section('actions')
    <a href="{{ route('products.show', $product) }}" target="_blank" class="btn btn-light btn-sm"><i class="bi bi-eye me-1"></i> View on store</a>
    <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form')
    </form>

    {{-- Image action forms live outside the main form (HTML forms cannot be nested). --}}
    @foreach ($product->images as $image)
        <form id="primary-{{ $image->id }}" method="POST" action="{{ route('admin.product-images.primary', $image) }}" class="d-none">
            @csrf
            @method('PATCH')
        </form>
        <form id="delete-image-{{ $image->id }}" method="POST" action="{{ route('admin.product-images.destroy', $image) }}" class="d-none" data-confirm="Delete this image?">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
