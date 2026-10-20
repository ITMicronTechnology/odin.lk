@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    @include('partials.page-header', ['crumbs' => ['Shopping Cart' => null], 'heading' => 'Shopping Cart'])

    <div class="container py-4">
        @if ($cart->isEmpty())
            <div class="section-box empty-state">
                <i class="bi bi-cart-x"></i>
                <h4 class="mt-3">Your cart is empty</h4>
                <p class="text-muted">Looks like you haven't added any tech yet.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-odin btn-lg">Start Shopping</a>
            </div>
        @else
            @php($freeShippingGap = config('shop.free_shipping_threshold') - $cart->subtotal())
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="section-box p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th class="ps-4">Product</th><th>Price</th><th class="text-center">Quantity</th><th class="text-end">Total</th><th></th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart->items() as $line)
                                        @php($product = $line['product'])
                                        <tr class="cart-item">
                                            <td class="ps-4">
                                                <div class="d-flex gap-3 align-items-center">
                                                    <img src="{{ $product->image_url }}" alt="">
                                                    <div>
                                                        <div class="pc-brand">{{ $product->brand?->name }}</div>
                                                        <a href="{{ route('products.show', $product) }}" class="text-dark fw-medium">{{ $product->name }}</a>
                                                        <div class="small text-muted">SKU: {{ $product->sku }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-nowrap">@money($product->final_price)</td>
                                            <td class="text-center">
                                                <div class="qty-group">
                                                    <button type="button" data-qty-step="-1" aria-label="Decrease">&minus;</button>
                                                    <input id="qty-{{ $product->id }}" type="number" class="js-cart-qty" value="{{ $line['quantity'] }}" min="1"
                                                           max="{{ min($product->stock, config('shop.max_quantity_per_item')) }}" data-url="{{ route('cart.update', $product) }}" aria-label="Quantity">
                                                    <button type="button" data-qty-step="1" aria-label="Increase">+</button>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold text-nowrap" id="line-total-{{ $product->id }}">@money($line['line_total'])</td>
                                            <td class="pe-3">
                                                <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger" aria-label="Remove"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <a href="{{ route('shop.index') }}" class="btn btn-link mt-2 px-0"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card">
                        <h5 class="fw-bold mb-3">Order Summary</h5>
                        @if ($freeShippingGap > 0)
                            <div class="alert alert-info small py-2">
                                <i class="bi bi-truck"></i> Add <strong>@money($freeShippingGap)</strong> more for <strong>FREE delivery</strong>.
                            </div>
                        @endif
                        <div class="summary-row"><span>Subtotal</span><span class="js-cart-subtotal">@money($cart->subtotal())</span></div>
                        <div class="summary-row"><span>Delivery</span><span class="js-cart-shipping">@if ($cart->shippingFee() > 0) @money($cart->shippingFee()) @else FREE @endif</span></div>
                        <div class="summary-row summary-total"><span>Total</span><span class="text-odin-red js-cart-total">@money($cart->total())</span></div>
                        <a href="{{ route('checkout.create') }}" class="btn btn-odin btn-lg w-100 mt-3">Proceed to Checkout <i class="bi bi-arrow-right"></i></a>
                        <div class="text-center small text-muted mt-3"><i class="bi bi-lock-fill"></i> Secure checkout &middot; Cash on delivery available</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
