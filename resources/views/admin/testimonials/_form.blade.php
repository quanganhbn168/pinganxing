@csrf
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="form-group">
            <label for="name">Tên khách hàng / Công ty <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $testimonial->name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="position">Chức vụ / Ngành nghề</label>
            <input type="text" name="position" class="form-control" value="{{ old('position', $testimonial->position ?? '') }}">
        </div>
        <div class="form-group">
            <label for="content">Nội dung đánh giá <span class="text-danger">*</span></label>
            <textarea name="content" class="form-control" rows="5" required>{{ old('content', $testimonial->content ?? '') }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-form.image-input name="image" label="Ảnh đại diện / Logo" :value="$testimonial->image ?? ''" :required="!$testimonial->exists" />
            </div>
            <div class="col-md-6">
                <x-form.switch name="status" label="Trạng thái" :checked="old('status', $testimonial->status ?? true)" />
            </div>
        </div>
    </div>
</div>