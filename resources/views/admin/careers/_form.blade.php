@csrf
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label>Tên vị trí <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $career->name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Đường dẫn tĩnh (Slug)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $career->slug ?? '') }}">
                </div>
                <div class="form-group">
                    <x-form.ckeditor name="description" label="Mô tả công việc" :value="$career->description ?? ''" />
                </div>
                <div class="form-group">
                    <x-form.ckeditor name="requirements" label="Yêu cầu ứng viên" :value="$career->requirements ?? ''" />
                </div>
                <div class="form-group">
                    <x-form.ckeditor name="benefits" label="Quyền lợi" :value="$career->benefits ?? ''" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $career->quantity ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Mức lương</label>
                    <input type="text" name="salary" class="form-control" value="{{ old('salary', $career->salary ?? '') }}" placeholder="VD: Thỏa thuận">
                </div>
                <div class="form-group">
                    <label>Kinh nghiệm</label>
                    <input type="text" name="experience" class="form-control" value="{{ old('experience', $career->experience ?? '') }}" placeholder="VD: 2 năm">
                </div>
                <div class="form-group">
                    <label>Hạn nộp hồ sơ</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline', $career->deadline ? $career->deadline->format('Y-m-d') : '') }}">
                </div>
                <hr>
                <x-form.switch name="status" label="Trạng thái" :checked="old('status', $career->status ?? true)" />
                <hr>
                <x-form.image-input name="image" label="Ảnh minh họa" :value="$career->image ?? ''" :required="!$career->exists" />
            </div>
        </div>
    </div>
</div>