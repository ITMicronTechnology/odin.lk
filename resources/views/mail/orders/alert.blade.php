<x-mail::message>
# New order {{ $order->order_number }}

A customer has just placed an order on Odin.lk.

**Customer:** {{ $order->customer_name }}<br>
**Phone:** {{ $order->customer_phone }}<br>
**Email:** {{ $order->customer_email }}<br>
**Payment method:** {{ $order->payment_method->label() }}<br>
**Delivery to:** {{ $order->city }}, {{ $order->district }}

<x-mail::table>
| Product | Qty | Total |
|:--------|:---:|------:|
@foreach ($items as $item)
| {{ str_replace('|', '\|', $item->product_name) }} | {{ $item->quantity }} | @money($item->line_total) |
@endforeach
| | **Total** | **@money($order->total)** |
</x-mail::table>

<x-mail::button :url="$adminUrl" color="blue">
Open order in admin panel
</x-mail::button>
</x-mail::message>
