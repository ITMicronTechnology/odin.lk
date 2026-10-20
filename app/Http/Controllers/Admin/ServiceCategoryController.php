<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ServiceCategoryController extends Controller
{
    public function create(): View
    {
        return view('admin.service-categories.create', [
            'category' => new ServiceCategory(['is_active' => true, 'sort_order' => ServiceCategory::max('sort_order') + 1]),
        ]);
    }

    public function store(ServiceCategoryRequest $request): RedirectResponse
    {
        ServiceCategory::create($this->attributes($request));

        return redirect()->route('admin.services.index')->with('success', 'Service group created.');
    }

    public function edit(ServiceCategory $serviceCategory): View
    {
        return view('admin.service-categories.edit', ['category' => $serviceCategory]);
    }

    public function update(ServiceCategoryRequest $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $serviceCategory->update($this->attributes($request, $serviceCategory));

        return redirect()->route('admin.services.index')->with('success', 'Service group updated.');
    }

    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        if ($serviceCategory->services()->exists()) {
            return back()->with('error', 'Move or delete the services in this group first.');
        }

        $serviceCategory->delete();

        return back()->with('success', 'Service group deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(ServiceCategoryRequest $request, ?ServiceCategory $category = null): array
    {
        $data = $request->validated();

        return [
            ...$data,
            'slug' => $data['slug'] ?? null ?: ServiceCategory::uniqueSlug($data['name'], $category?->id),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
