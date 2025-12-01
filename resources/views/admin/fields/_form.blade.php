@csrf
<div class="row">
    {{-- Cột nội dung chính --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Thông tin Lĩnh vực</h6></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Tên lĩnh vực <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $field->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="field_category_id">Danh mục <span class="text-danger">*</span></label>
                    <select id="field_category_id" name="field_category_id" class="form-control" required>
                        <option value="">— Chọn danh mục —</option>
                        {{-- Tái sử dụng component category-tree --}}
                        <x-form.category-tree :categories="$categories" :selectedId="old('field_category_id', $field->field_category_id ?? null)" />
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="slug">Đường dẫn tĩnh (Slug)</label>
                    <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $field->slug ?? '') }}" placeholder="Để trống để tự động tạo">
                </div>

                <div class="form-group">
                    <label for="summary">Mô tả ngắn</label>
                    <textarea id="summary" name="summary" class="form-control" rows="4">{{ old('summary', $field->summary ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <x-form.ckeditor name="content" label="Nội dung chi tiết" :value="$field->content ?? ''" />
                </div>
            </div>
        </div>

        {{-- Phần SEO --}}
        <div class="card shadow mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">SEO</h6></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ old('meta_title', $field->meta_title ?? '') }}">
                </div>
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3">{{ old('meta_description', $field->meta_description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $field->meta_keywords ?? '') }}" placeholder="Ví dụ: keyword 1, keyword 2">
                </div>
            </div>
        </div>
    </div>

    {{-- Cột thông tin phụ --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Thông tin phụ</h6></div>
            <div class="card-body">
                <x-form.switch name="status" label="Trạng thái" :checked="old('status', $field->status ?? true)" />
                <hr>
                <x-form.switch name="is_featured" label="Nổi bật" :checked="old('is_featured', $field->is_featured ?? false)" />
                <hr>
                <x-form.image-input name="image" label="Ảnh đại diện" :value="$field->image ?? ''" :required="!$field->exists" />
            </div>
        </div>
    </div>
</div>