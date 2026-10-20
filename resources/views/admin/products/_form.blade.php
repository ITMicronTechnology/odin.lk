@php
    $specText = old('specifications', collect($product->specifications ?? [])->map(fn ($value, $key) => "{$key}: {$value}")->implode("\n"));
@endphp
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card mb-3">
            <div class="card-header">Basic information</div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="name">Product name *</label>
                    <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="sku">SKU *</label>
                    <input id="sku" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="slug">URL slug <span class="text-muted small">(auto if blank)</span></label>
                    <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $product->slug) }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="short_description">Short description</label>
                    <input id="short_description" name="short_description" class="form-control" maxlength="500" value="{{ old('short_description', $product->short_description) }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Full description</label>
                    <textarea id="description" name="description" rows="6" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" for="specifications">Specifications <span class="text-muted small">(one per line, e.g. <code>RAM: 16GB DDR5</code>)</span></label>
                    <textarea id="specifications" name="specifications" rows="6" class="form-control font-monospace small">{{ $specText }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Images</div>
            <div class="card-body">
                @if ($product->exists && $product->images->isNotEmpty())
                    <div class="row row-cols-3 row-cols-md-5 g-2 mb-3">
                        @foreach ($product->images as $image)
                            <div class="col">
                                <div class="image-tile {{ $image->is_primary ? 'primary' : '' }}">
                                    @if ($image->is_primary)<span class="badge bg-danger tag">Main</span>@endif
                                    <img src="{{ $image->url }}" alt="">
                                    <div class="actions">
                                        @unless ($image->is_primary)
                                            <button form="primary-{{ $image->id }}" class="btn btn-sm btn-light flex-fill" title="Set as main"><i class="bi bi-star"></i></button>
                                        @endunless
                                        <button form="delete-image-{{ $image->id }}" class="btn btn-sm btn-light text-danger flex-fill" title="Delete"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <label class="form-label" for="images">Upload images <span class="text-muted small">(JPG, PNG or WEBP, max 4 MB each; square 800×800 works best)</span></label>
                <input id="images" type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple data-preview="#imagePreview">
                <div id="imagePreview" class="mt-2"></div>
                @if ($product->exists)
                    <div class="form-text">Tip: upload your real photos, set one as <strong>Main</strong>, then delete the sample images.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-3">
            <div class="card-header">Organise</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select category</option>
                        @foreach ($categories as $root)
                            <option value="{{ $root->id }}" @selected(old('category_id', $product->category_id) == $root->id)>{{ $root->name }}</option>
                            @foreach ($root->children as $child)
                                <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id) == $child->id)>&nbsp;&nbsp;— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="brand_id">Brand</label>
                    <select id="brand_id" name="brand_id" class="form-select">
                        <option value="">No brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-check form-switch mb-2">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active))>
                    <label class="form-check-label" for="is_active">Visible on store</label>
                </div>
                <div class="form-check form-switch">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $product->is_featured))>
                    <label class="form-check-label" for="is_featured">Featured on home page</label>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Pricing & stock</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="price">Regular price (Rs.) *</label>
                    <input id="price" type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="sale_price">Sale price (Rs.) <span class="text-muted small">(leave blank if no discount)</span></label>
                    <input id="sale_price" type="number" step="0.01" min="0" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price', $product->sale_price) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="stock">Stock quantity *</label>
                    <input id="stock" type="number" min="0" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" required>
                </div>
                <div>
                    <label class="form-label" for="warranty">Warranty</label>
                    <input id="warranty" name="warranty" class="form-control" placeholder="e.g. 2 Years" value="{{ old('warranty', $product->warranty) }}">
                </div>
            </div>
        </div>

        <button class="btn btn-odin w-100 btn-lg"><i class="bi bi-check-lg me-1"></i> {{ $product->exists ? 'Save Changes' : 'Create Product' }}</button>
    </div>
</div>
