<x-mail::message>
# Thank you for your order!

Hi {{ $order->customer_name }},

Thank you for shopping with **Odin.lk**. We have received your order and our team will call you shortly to confirm it.

**Order number:** {{ $order->order_number }}<br>
**Order date:** {{ $order->created_at->format('d M Y, g:i A') }}

<x-mail::table>
| Product | Qty | Unit price | Total |
|:--------|:---:|-----------:|------:|
@foreach ($items as $item)
| {{ str_replace('|', '\|', $item->product_name) }} | {{ $item->quantity }} | @money($item->price) | @money($item->line_total) |
@endforeach
| | | **Subtotal** | @money($order->subtotal) |
| | | **Delivery** | @if ((float) $order->shipping_fee > 0) @money($order->shipping_fee) @else FREE @endif |
| | | **Total** | **@money($order->total)** |
</x-mail::table>

## Payment: {{ $order->payment_method->label() }}

@if ($order->payment_method === \App\Enums\PaymentMethod::BankTransfer)
<x-mail::panel>
Please transfer **@money($order->total)** to the account below and use your order number **{{ $order->order_number }}** as the payment reference.

**Bank:** {{ config('shop.bank.name') }} ({{ config('shop.bank.branch') }})<br>
**Account name:** {{ config('shop.bank.account_name') }}<br>
**Account number:** {{ config('shop.bank.account_number') }}
</x-mail::panel>
@else
You pay **@money($order->total)** in cash when your order arrives.
@endif

## Delivery details

{{ $order->customer_name }}<br>
{{ $order->address }}<br>
{{ $order->city }}, {{ $order->district }}@if ($order->postal_code) {{ $order->postal_code }}@endif<br>
{{ $order->customer_phone }}

<x-mail::button :url="$trackUrl">
Track your order
</x-mail::button>

Thank you again for choosing Odin.lk. We look forward to serving you!

Warm regards,<br>
The Odin.lk Team

<x-mail::subcopy>
Questions about your order? Call our hotline **{{ config('shop.hotline') }}**, WhatsApp **{{ config('shop.whatsapp') }}** or email [{{ config('shop.email') }}](mailto:{{ config('shop.email') }}) ({{ config('shop.support_hours') }}).
</x-mail::subcopy>
</x-mail::message>
