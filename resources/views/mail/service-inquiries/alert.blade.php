<x-mail::message>
# New quote request {{ $inquiry->reference }}

A customer has asked for a quote for IT services.

**Service:** {{ $inquiry->service?->name ?? 'General enquiry' }}<br>
**Name:** {{ $inquiry->name }}<br>
@if ($inquiry->company)
**Company:** {{ $inquiry->company }}<br>
@endif
**Phone:** {{ $inquiry->phone }}<br>
**Email:** {{ $inquiry->email }}<br>
@if ($inquiry->budget)
**Budget:** {{ $inquiry->budget }}<br>
@endif
@if ($inquiry->timeline)
**Timeline:** {{ $inquiry->timeline }}<br>
@endif

<x-mail::panel>
{!! nl2br(e($inquiry->message)) !!}
</x-mail::panel>

<x-mail::button :url="$adminUrl" color="blue">
Open request in admin panel
</x-mail::button>
</x-mail::message>
