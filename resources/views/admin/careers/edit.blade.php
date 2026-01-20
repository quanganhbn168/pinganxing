@extends('layouts.admin')

@section('title', 'Cập nhật tin tuyển dụng')
@section('content_header', 'Cập nhật: ' . $career->name)

@section('content')
<form action="{{ route('admin.careers.update', $career->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        {{-- CỘT TRÁI --}}
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <x-form.input name="name" label="Vị trí tuyển dụng" required :value="$career->name" />
                    <x-form.slug

                        name="slug"

                        label="Đường dẫn (slug)"

                        :value="old('slug', $career->slug)"

                        source="#name"

                        table="careers"

                        field="slug"

                        :current-id="$career->id"

                    />
                    <div class="row">
                        <div class="col-md-4">
                            <x-form.input name="salary" label="Mức lương" :value="$career->salary" />
                        </div>
                        <div class="col-md-4">
                            <x-form.input name="quantity" type="number" label="Số lượng" :value="$career->quantity" />
                        </div>
                        <div class="col-md-4">
                            <x-form.input name="type" label="Hình thức" :value="$career->type" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="education" label="Bằng cấp" :value="$career->education" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="location" label="Địa điểm" :value="$career->location" />
                        </div>
                    </div>

                    <x-form.ckeditor name="description" label="Mô tả công việc" :value="old('description', $career->description)" />
                    <x-form.ckeditor name="requirement" label="Yêu cầu ứng viên" :value="old('requirement', $career->requirement)" />
                    <x-form.ckeditor name="benefit" label="Quyền lợi & Đãi ngộ" :value="old('benefit', $career->benefit)" />
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI --}}
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thiết lập</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $career->status ? 'selected' : '' }}>Đang tuyển</option>
                            <option value="0" {{ !$career->status ? 'selected' : '' }}>Dừng tuyển</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_home" name="is_home" value="1" {{ $career->is_home ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_home">Hiển thị trang chủ</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hạn nộp hồ sơ</label>
                        <input type="date" name="deadline" class="form-control" 
                               value="{{ $career->deadline ? $career->deadline->format('Y-m-d') : '' }}">
                    </div>

                    <x-form.input name="position" type="number" label="Thứ tự" :value="$career->position" />

                    <hr>

                    {{-- Media Manager --}}
                    <x-form.image-picker
                        name="image_original_path"
                        label="Ảnh đại diện"
                        :multiple="false"
                        :value="old('image_original_path', $career->image)"
                    />
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.careers.index') }}" class="btn btn-default btn-block">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')

@endpush