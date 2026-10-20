<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ServiceController as PublicServiceController;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(): View
    {
        $portfolioPath = PublicServiceController::portfolioPath();

        return view('admin.services.index', [
            'categories' => ServiceCategory::with(['services' => fn ($query) => $query->withCount('inquiries')])
                ->orderBy('sort_order')
                ->get(),
            'portfolio' => [
                'custom' => Storage::disk('public')->exists(PublicServiceController::UPLOADED_PORTFOLIO),
                'size' => round(filesize($portfolioPath) / 1024 / 1024, 2),
                'updated' => date('d M Y, h:i A', filemtime($portfolioPath)),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.services.create', [
            'service' => new Service(['is_active' => true, 'sort_order' => 0, 'price_note' => 'one-time']),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        Service::create($this->attributes($request));

        return redirect()->route('admin.services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', [
            'service' => $service,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($this->attributes($request, $service));

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return back()->with('success', 'Service deleted. Its past requests are kept as general enquiries.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(ServiceRequest $request, ?Service $service = null): array
    {
        $data = $request->safe()->except('features');

        return [
            ...$data,
            'slug' => $data['slug'] ?? null ?: Service::uniqueSlug($data['name'], $service?->id),
            'features' => $request->features(),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * @return Collection<int, ServiceCategory>
     */
    private function categoryOptions(): Collection
    {
        return ServiceCategory::orderBy('sort_order')->get();
    }
}
