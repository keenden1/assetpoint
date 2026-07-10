@csrf

<div class="row g-3">
    <div class="col-sm-6">
        <label for="sku" class="form-label">Product ID (SKU)</label>
        <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku ?? '') }}" required
            class="form-control @error('sku') is-invalid @enderror">
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6">
        <label for="name" class="form-label">Product name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required
            class="form-control @error('name') is-invalid @enderror">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label for="category_id" class="form-label">Category</label>
        <select id="category_id" name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
            <option value="" disabled @selected(! old('category_id', $product->category_id ?? ''))>Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" required class="form-select @error('status') is-invalid @enderror">
            <option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $product->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-4">
        <label for="price" class="form-label">Price</label>
        <div class="input-group @error('price') has-validation @enderror">
            <span class="input-group-text">₱</span>
            <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required
                class="form-control @error('price') is-invalid @enderror">
            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <label for="remarks" class="form-label">Remarks</label>
        <textarea id="remarks" name="remarks" rows="3"
            class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $product->remarks ?? '') }}</textarea>
        @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check-lg me-1"></i>Save</button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
