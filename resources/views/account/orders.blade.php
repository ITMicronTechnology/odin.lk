@extends('account.layout', ['pageTitle' => 'My Orders'])

@section('title', 'My Orders')

@section('account')
    <div class="section-box">
        @if ($orders->isEmpty())
            <div class="empty-state py-4">
                <i class="bi bi-bag"></i>
                <p class="text-muted mt-2">You haven't placed any orders yet.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-odin">Start Shopping</a>
            </div>
        @else
            @include('account.partials.orders-table', ['orders' => $orders])
            <div class="mt-3">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
