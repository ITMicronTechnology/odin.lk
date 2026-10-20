<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function makePrimary(ProductImage $image): RedirectResponse
    {
        DB::transaction(function () use ($image): void {
            ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return back()->with('success', 'Main image updated.');
    }

    public function destroy(ProductImage $image): RedirectResponse
    {
        $image->delete();
        Storage::disk('public')->delete($image->path);

        if ($image->is_primary) {
            ProductImage::where('product_id', $image->product_id)
                ->orderBy('sort_order')
                ->first()
                ?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image deleted.');
    }
}
