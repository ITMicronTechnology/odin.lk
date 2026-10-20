<div class="card" style="max-width: 860px">
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label" for="title">Title *</label>
            <input id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $banner->title) }}" required>
            <div class="form-text">Used as the image alt text.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="subtitle">Subtitle</label>
            <input id="subtitle" name="subtitle" class="form-control" value="{{ old('subtitle', $banner->subtitle) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="position">Position *</label>
            <select id="position" name="position" class="form-select">
                @foreach (\App\Models\Banner::POSITIONS as $value => $label)
                    <option value="{{ $value }}" @selected(old('position', $banner->position) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="link">Link</label>
            <input id="link" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $banner->link) }}" placeholder="/category/laptops">
        </div>
        <div class="col-md-8">
            <label class="form-label" for="image">Banner image {{ $banner->exists ? '' : '*' }} <span class="text-muted small">(JPG, PNG or WEBP, max 4 MB)</span></label>
            <input id="image" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" data-preview="#bannerPreview" {{ $banner->exists ? '' : 'required' }}>
            <div id="bannerPreview" class="mt-2">
                @if ($banner->exists)<img src="{{ $banner->image_url }}" alt="" class="img-fluid rounded border" style="max-height:160px">@endif
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="sort_order">Sort order</label>
            <input id="sort_order" type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order ?? 0) }}">
            <div class="form-check form-switch mt-3">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $banner->is_active))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white">
        <button class="btn btn-odin"><i class="bi bi-check-lg me-1"></i> Save Banner</button>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-light">Cancel</a>
    </div>
</div>
