<div class="card" style="max-width: 720px">
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Brand name *</label>
            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $brand->name) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="slug">URL slug <span class="text-muted small">(auto if blank)</span></label>
            <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $brand->slug) }}">
        </div>
        <div class="col-md-8">
            <label class="form-label" for="logo">Logo <span class="text-muted small">(PNG with transparent background, 320×140)</span></label>
            <input id="logo" type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" data-preview="#logoPreview">
            <div id="logoPreview" class="mt-2">
                @if ($brand->logo_url)<img src="{{ $brand->logo_url }}" alt="" style="height:56px" class="border rounded">@endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch mt-md-4">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $brand->is_active))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white">
        <button class="btn btn-odin"><i class="bi bi-check-lg me-1"></i> Save Brand</button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-light">Cancel</a>
    </div>
</div>
