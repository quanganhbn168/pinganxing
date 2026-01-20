@extends('layouts.admin')

@section('title', 'Chỉnh sửa: ' . $section->name)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-edit mr-2"></i>Chỉnh sửa: {{ $section->name }}</h1>
    <a href="{{ route('admin.homepage-sections.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Quay lại
    </a>
</div>
@stop

@section('content')
<form action="{{ route('admin.homepage-sections.update', $section->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- Cột chính --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thông tin chung</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Tiêu đề Section</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $section->title) }}"
                               placeholder="Nhập tiêu đề hiển thị trên trang chủ">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Subtitle</label>
                        <input type="text" 
                               name="subtitle" 
                               id="subtitle" 
                               class="form-control @error('subtitle') is-invalid @enderror" 
                               value="{{ old('subtitle', $section->subtitle) }}"
                               placeholder="Nhập subtitle (nếu có)">
                        @error('subtitle')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4"
                                  placeholder="Nhập mô tả ngắn gọn">{{ old('description', $section->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Settings fields động theo loại section --}}
            @if(count($settingsFields) > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cấu hình bổ sung</h3>
                </div>
                <div class="card-body">
                    @foreach($settingsFields as $field)
                    <div class="form-group">
                        <label for="settings_{{ $field['name'] }}">{{ $field['label'] }}</label>
                        
                        @if($field['type'] === 'textarea')
                        <textarea name="settings[{{ $field['name'] }}]" 
                                  id="settings_{{ $field['name'] }}" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="{{ $field['placeholder'] ?? '' }}">{{ old('settings.' . $field['name'], $section->getSetting($field['name'])) }}</textarea>
                        
                        @elseif($field['type'] === 'image')
                        <x-form.image-picker 
                            :name="'settings[' . $field['name'] . ']'" 
                            :label="''"
                            :value="$section->getSetting($field['name'])"
                            :placeholder="$field['placeholder'] ?? 'Chọn ảnh'" />
                        
                        @else
                        <input type="text" 
                               name="settings[{{ $field['name'] }}]" 
                               id="settings_{{ $field['name'] }}" 
                               class="form-control" 
                               value="{{ old('settings.' . $field['name'], $section->getSetting($field['name'])) }}"
                               placeholder="{{ $field['placeholder'] ?? '' }}">
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Cột sidebar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hình ảnh</h3>
                </div>
                <div class="card-body">
                    <x-form.image-picker 
                        name="image" 
                        label="Ảnh minh họa"
                        :value="$section->image"
                        placeholder="Chọn ảnh minh họa" />

                    <x-form.image-picker 
                        name="background_image" 
                        label="Ảnh nền"
                        :value="$section->background_image"
                        placeholder="Chọn ảnh nền" />
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Trạng thái</h3>
                </div>
                <div class="card-body">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">
                            Hiển thị section này trên trang chủ
                        </label>
                    </div>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-save mr-2"></i>Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection


