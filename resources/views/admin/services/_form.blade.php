<div class="row g-3">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">Service details</div>
            <div class="card-body row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="name">Service name *</label>
                    <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="icon">Icon</label>
                    <input id="icon" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $service->icon) }}" placeholder="bi-globe2">
                    <div class="form-text"><a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Browse icons</a></div>
                </div>
                <div class="col-12">
                    <label class="form-label" for="short_description">Short description <span class="text-muted small">(shown on the service card)</span></label>
                    <input id="short_description" name="short_description" class="form-control" maxlength="500" value="{{ old('short_description', $service->short_description) }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="features">What's included <span class="text-muted small">(one per line)</span></label>
                    <textarea id="features" name="features" rows="6" class="form-control @error('features') is-invalid @enderror">{{ old('features', implode("\n", $service->features ?? [])) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Full description</label>
                    <textarea id="description" name="description" rows="6" class="form-control">{{ old('description', $service->description) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="slug">URL slug <span class="text-muted small">(auto if blank)</span></label>
                    <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $service->slug) }}">
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card mb-3">
            <div class="card-header">Group & visibility</div>
            <div class="card-body">
                <label class="form-label" for="service_category_id">Service group *</label>
                <select id="service_category_id" name="service_category_id" class="form-select mb-3 @error('service_category_id') is-invalid @enderror" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('service_category_id', $service->service_category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <label class="form-label" for="sort_order">Sort order</label>
                <input id="sort_order" type="number" min="0" name="sort_order" class="form-control mb-3" value="{{ old('sort_order', $service->sort_order) }}">
                <div class="form-check form-switch mb-2">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $service->is_active))>
                    <label class="form-check-label" for="is_active">Visible on website</label>
                </div>
                <div class="form-check form-switch">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $service->is_featured))>
                    <label class="form-check-label" for="is_featured">Show "Popular" badge</label>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Pricing & delivery</div>
            <div class="card-body">
                <label class="form-label" for="starting_price">Starting price (Rs.) <span class="text-muted small">(blank = "Free quotation")</span></label>
                <input id="starting_price" type="number" step="0.01" min="0" name="starting_price" class="form-control mb-3 @error('starting_price') is-invalid @enderror" value="{{ old('starting_price', $service->starting_price) }}">
                <label class="form-label" for="price_note">Price note</label>
                <input id="price_note" name="price_note" class="form-control mb-3" value="{{ old('price_note', $service->price_note) }}" placeholder="one-time / per month / per device">
                <label class="form-label" for="delivery_time">Typical delivery time</label>
                <input id="delivery_time" name="delivery_time" class="form-control" value="{{ old('delivery_time', $service->delivery_time) }}" placeholder="e.g. 2 - 3 weeks">
            </div>
        </div>
        <button class="btn btn-odin w-100 btn-lg"><i class="bi bi-check-lg me-1"></i> {{ $service->exists ? 'Save Changes' : 'Create Service' }}</button>
    </div>
</div>
