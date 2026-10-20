@extends('account.layout', ['pageTitle' => 'My Service Requests'])

@section('title', 'My Service Requests')

@section('account')
    <div class="section-box">
        @if ($inquiries->isEmpty())
            <div class="empty-state py-4">
                <i class="bi bi-briefcase"></i>
                <p class="text-muted mt-2">You haven't requested any services yet.</p>
                <a href="{{ route('services.index') }}" class="btn btn-odin">Explore IT Services</a>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach ($inquiries as $inquiry)
                    <div class="border rounded-3 p-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <div>
                                <div class="fw-semibold">{{ $inquiry->service?->name ?? 'General enquiry' }}</div>
                                <div class="small text-muted">Ref. {{ $inquiry->reference }} &middot; {{ $inquiry->created_at->format('d M Y') }}</div>
                            </div>
                            <span class="badge align-self-start text-bg-{{ $inquiry->status->color() }}">{{ $inquiry->status->label() }}</span>
                        </div>
                        <p class="small mb-1 mt-2">{{ \Illuminate\Support\Str::limit($inquiry->message, 180) }}</p>
                        <div class="small text-muted"><i class="bi bi-info-circle me-1"></i>{{ $inquiry->status->description() }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">{{ $inquiries->links() }}</div>
        @endif
    </div>
@endsection
