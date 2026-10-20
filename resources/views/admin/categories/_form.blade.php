<div class="card" style="max-width: 860px">
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Name *</label>
            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="slug">URL slug <span class="text-muted small">(auto if blank)</span></label>
            <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category->slug) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="parent_id">Parent category</label>
            <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                <option value="">— None (main category) —</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="icon">Icon</label>
            <input id="icon" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $category->icon) }}" placeholder="bi-laptop">
            <div class="form-text"><a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Browse icons</a></div>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="sort_order">Sort order</label>
            <input id="sort_order" type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}">
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
        </div>
        <div class="col-md-8">
            <label class="form-label" for="image">Category image <span class="text-muted small">(square, 400×400)</span></label>
            <input id="image" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" data-preview="#categoryPreview">
            <div id="categoryPreview" class="mt-2">
                @if ($category->image)<img src="{{ $category->image_url }}" class="thumb" style="width:80px;height:80px" alt="">@endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch mt-md-4">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $category->is_active))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <div class="form-check form-switch">
                <input type="hidden" name="show_on_home" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="show_on_home" value="1" id="show_on_home" @checked(old('show_on_home', $category->show_on_home))>
                <label class="form-check-label" for="show_on_home">Show a section on home page</label>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white">
        <button class="btn btn-odin"><i class="bi bi-check-lg me-1"></i> Save Category</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
    </div>
</div>
