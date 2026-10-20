<div class="product-grid row row-cols-2 row-cols-md-3 row-cols-lg-4 {{ $wide ?? true ? 'row-cols-xl-5' : '' }} g-2 g-md-3">
    @foreach ($products as $product)
        <div class="col">@include('partials.product-card', ['product' => $product])</div>
    @endforeach
</div>
