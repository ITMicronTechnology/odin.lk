<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.index', [
            'banners' => Banner::orderBy('position')->orderBy('sort_order')->get()->groupBy('position'),
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.create', ['banner' => new Banner(['is_active' => true, 'position' => 'slider'])]);
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        Banner::create($this->attributes($request));

        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', ['banner' => $banner]);
    }

    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $oldImage = $banner->image;
        $banner->update($this->attributes($request));

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();
        Storage::disk('public')->delete($banner->image);

        return back()->with('success', 'Banner deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(BannerRequest $request): array
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        return [
            ...$data,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
