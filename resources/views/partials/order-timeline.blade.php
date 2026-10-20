@php
    $steps = \App\Enums\OrderStatus::timeline();
    $currentStep = array_search($order->status, $steps, true);
@endphp
@if ($order->status === \App\Enums\OrderStatus::Cancelled)
    <div class="alert alert-danger mt-3 mb-0"><i class="bi bi-x-circle me-1"></i> {{ $order->status->description() }}</div>
@else
    <div class="timeline-steps">
        @foreach ($steps as $index => $step)
            <div class="step {{ $currentStep !== false && $index <= $currentStep ? 'done' : '' }} {{ $index === $currentStep ? 'current' : '' }}">
                <div class="dot">@if ($currentStep !== false && $index <= $currentStep)<i class="bi bi-check-lg"></i>@endif</div>
                <span>{{ $step->label() }}</span>
            </div>
        @endforeach
    </div>
    <p class="small text-muted text-center mb-0">{{ $order->status->description() }}</p>
@endif
