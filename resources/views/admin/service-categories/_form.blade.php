<div class="card" style="max-width: 820px">
    <div class="card-body row g-3">
        <div class="col-md-8">
            <label class="form-label" for="name">Group name *</label>
            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="icon">Icon</label>
            <input id="icon" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $category->icon) }}" placeholder="bi-globe2">
        </div>
        <div class="col-12">
            <label class="form-label" for="tagline">Tagline</label>
            <input id="tagline" name="tagline" class="form-control" value="{{ old('tagline', $category->tagline) }}" placeholder="e.g. Fast, modern websites that bring you customers">
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
        </div>
        <div class="col-md-5">
            <label class="form-label" for="slug">URL slug <span class="text-muted small">(auto if blank)</span></label>
            <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category->slug) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="sort_order">Sort order</label>
            <input id="sort_order" type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}">
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch mt-md-4">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $category->is_active))>
                <label class="form-check-label" for="is_active">Visible on website</label>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white">
        <button class="btn btn-odin"><i class="bi bi-check-lg me-1"></i> Save Group</button>
        <a href="{{ route('admin.services.index') }}" class="btn btn-light">Cancel</a>
    </div>
</div>
