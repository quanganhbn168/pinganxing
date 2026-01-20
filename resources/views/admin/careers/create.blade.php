@extends('layouts.admin')

@section('title', 'Đăng tin tuyển dụng')
@section('content_header', 'Đăng tin tuyển dụng')

@section('content')
<form action="{{ route('admin.careers.store') }}" method="POST">
    @csrf
    <div class="row">
        {{-- CỘT TRÁI: NỘI DUNG --}}
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thông tin chi tiết</h3>
                </div>
                <div class="card-body">
                    <x-form.input name="name" label="Vị trí tuyển dụng" required placeholder="VD: Nhân viên Kinh doanh" />
                    <x-form.slug

                        name="slug"

                        label="Đường dẫn (slug)"

                        :value="old('slug')"

                        source="#name"

                        table="careers"

                        field="slug"

                    />
                    <div class="row">
                        <div class="col-md-4">
                            <x-form.input name="salary" label="Mức lương" placeholder="VD: 10 - 15 Triệu" />
                        </div>
                        <div class="col-md-4">
                            <x-form.input name="quantity" type="number" label="Số lượng cần tuyển" value="1" />
                        </div>
                        <div class="col-md-4">
                            <x-form.input name="type" label="Hình thức" placeholder="Full-time / Part-time" value="Full-time" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="education" label="Yêu cầu bằng cấp" placeholder="VD: Đại học, Cao đẳng..." />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="location" label="Địa điểm làm việc" placeholder="VD: Hà Nội" />
                        </div>
                    </div>

                    <x-form.ckeditor name="description" label="Mô tả công việc" :value="old('description')" />
                    <x-form.ckeditor name="requirement" label="Yêu cầu ứng viên" :value="old('requirement')" />
                    <x-form.ckeditor name="benefit" label="Quyền lợi & Đãi ngộ" :value="old('benefit')" />
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: CẤU HÌNH --}}
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thiết lập</h3>
                </div>
                <div class="card-body">
                    {{-- Status --}}
                    <div class="form-group">
                        <label>Trạng thái hiển thị</label>
                        <select name="status" class="form-control">
                            <option value="1" selected>Đang tuyển (Hiển thị)</option>
                            <option value="0">Dừng tuyển (Ẩn)</option>
                        </select>
                    </div>

                    {{-- Is Home --}}
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_home" name="is_home" value="1">
                            <label class="custom-control-label" for="is_home">Hiển thị trang chủ</label>
                        </div>
                    </div>

                    {{-- Deadline --}}
                    <div class="form-group">
                        <label>Hạn nộp hồ sơ</label>
                        <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                        <small class="text-muted">Bỏ trống nếu tuyển liên tục.</small>
                    </div>

                    {{-- Position --}}
                    <x-form.input name="position" type="number" label="Thứ tự hiển thị" value="0" />

                    <hr>

                    {{-- Image Media Manager --}}
                    <x-form.image-picker
                        name="image_original_path"
                        label="Ảnh đại diện tin"
                        :multiple="false"
                        :value="old('image_original_path')"
                    />
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Đăng tin
                    </button>
                    <a href="{{ route('admin.careers.index') }}" class="btn btn-default btn-block">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    // Kích hoạt CKEditor 4 hoặc Summernote
    $(function () {
        // Giả sử bạn dùng Summernote, nếu dùng CKEditor thì đổi lại
        if($.fn.summernote) {
            $('.editor').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }
    })
</script>
@endpush