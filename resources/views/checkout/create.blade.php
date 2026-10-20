@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    @include('partials.page-header', ['crumbs' => ['Cart' => route('cart.index'), 'Checkout' => null], 'heading' => 'Checkout'])

    <div class="container py-4">
        @error('cart')
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i> {{ $message }} <a href="{{ route('cart.index') }}" class="alert-link">Go to cart</a></div>
        @enderror

        <form method="POST" action="{{ route('checkout.store') }}" class="row g-4" novalidate>
            @csrf
            <div class="col-lg-7">
                <div class="section-box mb-4">
                    <h5 class="fw-bold mb-3"><span class="badge bg-odin-red me-2">1</span>Contact Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer_name">Full Name *</label>
                            <input id="customer_name" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $user->name) }}" required>
                            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer_phone">Mobile Number *</label>
                            <input id="customer_phone" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone', $user->phone) }}" placeholder="077 123 4567" required>
                            @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="customer_email">Email *</label>
                            <input id="customer_email" type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email', $user->email) }}" required>
                            @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="section-box mb-4">
                    <h5 class="fw-bold mb-3"><span class="badge bg-odin-red me-2">2</span>Delivery Address</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="address">Street Address *</label>
                            <input id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}" placeholder="House no, street name" required>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="city">City *</label>
                            <input id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $user->city) }}" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="district">District *</label>
                            <select id="district" name="district" class="form-select @error('district') is-invalid @enderror" required>
                                <option value="">Select district</option>
                                @foreach (config('shop.districts') as $district)
                                    <option value="{{ $district }}" @selected(old('district', $user->district) === $district)>{{ $district }}</option>
                                @endforeach
                            </select>
                            @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="postal_code">Postal Code</label>
                            <input id="postal_code" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $user->postal_code) }}">
                            @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="notes">Order Notes <span class="text-muted small">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="2" class="form-control" placeholder="Landmark, preferred delivery time...">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="save_address" value="1" id="save_address" @checked(old('save_address', true))>
                                <label class="form-check-label" for="save_address">Save these details to my account</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-box">
                    <h5 class="fw-bold mb-3"><span class="badge bg-odin-red me-2">3</span>Payment Method</h5>
                    <div class="d-grid gap-2">
                        @foreach ($paymentMethods as $method)
                            <label class="pay-option">
                                <input class="form-check-input mt-0" type="radio" name="payment_method" value="{{ $method->value }}" @checked(old('payment_method', 'cod') === $method->value)>
                                <i class="bi {{ $method->value === 'cod' ? 'bi-cash-coin' : 'bi-bank' }} fs-4 text-odin-blue"></i>
                                <div>
                                    <div class="fw-semibold">{{ $method->label() }}</div>
                                    <div class="small text-muted">
                                        {{ $method->value === 'cod' ? 'Pay in cash when your order arrives.' : 'Transfer to our bank account. Details are shown after you place the order.' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                        @error('payment_method')<div class="text-danger small">{{ $message }}</div>@enderror
                        <div class="pay-option opacity-50" aria-disabled="true">
                            <i class="bi bi-credit-card fs-4 ms-4"></i>
                            <div><div class="fw-semibold">Credit / Debit Card</div><div class="small text-muted">Coming soon</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="summary-card">
                    <h5 class="fw-bold mb-3">Your Order ({{ $cart->count() }} {{ \Illuminate\Support\Str::plural('item', $cart->count()) }})</h5>
                    @foreach ($cart->items() as $line)
                        <div class="d-flex gap-3 align-items-center py-2 border-bottom cart-item">
                            <img src="{{ $line['product']->image_url }}" alt="" style="width:56px;height:56px">
                            <div class="flex-grow-1 small">
                                <div class="fw-medium">{{ $line['product']->name }}</div>
                                <div class="text-muted">Qty {{ $line['quantity'] }} &times; @money($line['product']->final_price)</div>
                            </div>
                            <div class="fw-semibold small text-nowrap">@money($line['line_total'])</div>
                        </div>
                    @endforeach
                    <div class="summary-row mt-2"><span>Subtotal</span><span>@money($cart->subtotal())</span></div>
                    <div class="summary-row"><span>Delivery</span><span>@if ($cart->shippingFee() > 0) @money($cart->shippingFee()) @else FREE @endif</span></div>
                    <div class="summary-row summary-total"><span>Total</span><span class="text-odin-red">@money($cart->total())</span></div>
                    <button class="btn btn-odin btn-lg w-100 mt-3"><i class="bi bi-bag-check me-1"></i> Place Order</button>
                    <p class="small text-muted text-center mt-3 mb-0">By placing your order you agree to our terms of sale.</p>
                </div>
            </div>
        </form>
    </div>
@endsection
