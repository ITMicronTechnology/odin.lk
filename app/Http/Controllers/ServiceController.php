<?php

namespace App\Http\Controllers;

use App\Enums\ServiceInquiryStatus;
use App\Http\Requests\ServiceInquiryRequest;
use App\Mail\NewServiceInquiryAlert;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceInquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ServiceController extends Controller
{
    /**
     * Portfolio uploaded through the admin panel (replaces the sample in public/downloads).
     */
    public const UPLOADED_PORTFOLIO = 'portfolio/odin-portfolio.pdf';

    public function index(): View
    {
        return view('services.index', ['categories' => $this->catalog()]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->is_active && $service->category?->is_active, 404);

        return view('services.show', [
            'service' => $service->load('category'),
            'related' => $service->category->services()->active()->whereKeyNot($service->id)->take(6)->get(),
            'user' => auth()->user(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('services.request', [
            'categories' => $this->catalog(),
            'selected' => Service::active()->where('slug', $request->query('service'))->value('id'),
            'user' => $request->user(),
        ]);
    }

    public function store(ServiceInquiryRequest $request): RedirectResponse
    {
        $inquiry = ServiceInquiry::create([
            ...$request->safe()->except('website'),
            'reference' => ServiceInquiry::generateReference(),
            'user_id' => $request->user()?->id,
            'status' => ServiceInquiryStatus::New,
        ]);

        try {
            Mail::to(config('shop.order_alert_email'))->send(new NewServiceInquiryAlert($inquiry));
        } catch (Throwable $exception) {
            report($exception);
        }

        return redirect()->route('services.thanks')->with('service_reference', $inquiry->reference);
    }

    public function thanks(): View|RedirectResponse
    {
        $reference = session('service_reference');

        if (! $reference) {
            return redirect()->route('services.index');
        }

        return view('services.thanks', [
            'inquiry' => ServiceInquiry::with('service')->where('reference', $reference)->firstOrFail(),
        ]);
    }

    /**
     * Download the company portfolio (the one uploaded in the admin panel, or the sample).
     */
    public function portfolio(): BinaryFileResponse
    {
        return response()->download(self::portfolioPath(), 'Odin-IT-Services-Portfolio.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Where the current portfolio PDF lives.
     */
    public static function portfolioPath(): string
    {
        return Storage::disk('public')->exists(self::UPLOADED_PORTFOLIO)
            ? Storage::disk('public')->path(self::UPLOADED_PORTFOLIO)
            : public_path('downloads/odin-portfolio.pdf');
    }

    /**
     * Active categories with their active services, in display order.
     *
     * @return Collection<int, ServiceCategory>
     */
    private function catalog(): Collection
    {
        return ServiceCategory::active()
            ->with(['services' => fn ($query) => $query->active()])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (ServiceCategory $category): bool => $category->services->isNotEmpty())
            ->values();
    }
}
