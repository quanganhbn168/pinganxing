@csrf
<div class="row">
    {{-- Cột Nội dung chính --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin chính</h6>
            </div>
            <div class="card-body">
                <x-form.input 
                    name="name" 
                    label="Tên danh mục" 
                    :value="$field_category->name ?? ''" 
                    required 
                />

                <div class="form-group">
                    <label for="parent_id">Danh mục cha</label>
                    <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                        <option value="">— Là danh mục gốc —</option>
                        <x-form.category-tree 
                            :categories="$parentCategories"
                            :selectedId="old('parent_id', $field_category->parent_id ?? null)"
                            :excludeId="$field_category->id ?? null"
                        />
                    </select>
                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <x-form.slug
                    name="slug"
                    label="Đường dẫn (slug)"
                    :value="old('slug', $field_category->slug ?? '')"
                    source="#name"
                    table="field_categories"
                    field="slug"
                    :current-id="$field_category->id ?? null"
                />
                
                <x-form.textarea 
                    name="description" 
                    label="Mô tả" 
                    :value="$field_category->description ?? ''" 
                    rows="5" 
                />

                <x-form.ckeditor
                    name="content"
                    label="Nội dung chi tiết"
                    :value="$field_category->content ?? ''"
                />
            </div>
        </div>
    </div>

    {{-- Cột Thông tin phụ --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin phụ</h6>
            </div>
            <div class="card-body">
                <x-form.switch 
                    name="status"
                    label="Trạng thái"
                    :checked="old('status', $field_category->status ?? true)"
                />
                
                <hr>
                
                <x-form.input 
                    name="order" 
                    label="Thứ tự" 
                    type="number" 
                    :value="$field_category->order ?? 0" 
                />

                <hr>

                <x-admin.form.media-input
                    name="image_original_path"
                    label="Ảnh đại diện"
                    :multiple="false"
                    :value="optional($field_category->mainImage())->original_path ?? old('image_original_path')"
                />

                <x-admin.form.media-input
                    name="banner_original_path"
                    label="Banner"
                    :multiple="false"
                    :value="optional($field_category->bannerImage())->original_path ?? old('banner_original_path')"
                />
            </div>
        </div>
    </div>
</div>