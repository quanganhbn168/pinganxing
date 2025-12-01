@csrf
<div class="row">
    {{-- Cột nội dung chính --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Thông tin chính</h6></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Tên dự án <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $project->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="project_category_id">Danh mục dự án <span class="text-danger">*</span></label>
                    <select id="project_category_id" name="project_category_id" class="form-control" required>
                        <option value="">— Chọn danh mục —</option>
                        <x-form.category-tree :categories="$categories" :selectedId="old('project_category_id', $project->project_category_id ?? null)" />
                    </select>
                </div>

                <div class="form-group">
                    <label for="slug">Đường dẫn tĩnh (Slug)</label>
                    <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $project->slug ?? '') }}" placeholder="Để trống để tự động tạo">
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết dự án</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="investor">Chủ đầu tư</label>
                        <input type="text" id="investor" name="investor" class="form-control" value="{{ old('investor', $project->investor ?? '') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="address">Địa chỉ</label>
                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $project->address ?? '') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="year">Năm thực hiện</label>
                        <input type="number" id="year" name="year" class="form-control" value="{{ old('year', $project->year ?? '') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="value">Giá trị gói thầu</label>
                        <input type="text" id="value" name="value" class="form-control" value="{{ old('value', $project->value ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Mô tả & Nội dung</h6></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="description">Mô tả ngắn</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $project->description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <x-form.ckeditor name="content" label="Nội dung chi tiết" :value="$project->content ?? ''" />
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
             <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Thư viện ảnh</h6></div>
             <div class="card-body">
                {{-- Sử dụng component multi-image-input --}}
                <x-form.image-multi-input name="images" label="Tải lên các ảnh mới" :images="$project->images ?? []" />
             </div>
        </div>
    </div>

    {{-- Cột thông tin phụ --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Cấu hình</h6></div>
            <div class="card-body">
                <x-form.switch name="status" label="Trạng thái" :checked="old('status', $project->status ?? true)" />
                <hr>
                <x-form.switch name="is_home" label="Hiển thị trang chủ" :checked="old('is_home', $project->is_home ?? false)" />
                <hr>
                <x-form.image-input name="image" label="Ảnh đại diện" :value="$project->image ?? ''" :required="!$project->exists" />
                <hr>
                <x-form.image-input name="banner" label="Ảnh banner (nếu có)" :value="$project->banner ?? ''" />
            </div>
        </div>
    </div>
</div>