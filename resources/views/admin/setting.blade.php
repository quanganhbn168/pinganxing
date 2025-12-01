@extends('layouts.admin')
@section('title','Cài đặt chung')
@section('content_header','Cài đặt chung')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cài đặt chung</h3>
    </div>
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            {{-- Các trường cũ giữ nguyên --}}
            <x-form.input name="name" label="Tên Công ty" :value="$setting->name ?? ''" />
            <x-form.input name="email" label="Email" :value="$setting->email ?? ''" />
            <x-form.input name="phone" label="Số điện thoại" :value="$setting->phone ?? ''" />
            <x-form.input name="zalo" label="Zalo" :value="$setting->zalo ?? ''" />
            <x-form.input name="mess" label="Mess" :value="$setting->mess ?? ''" />
            <x-form.input name="tiktok" label="Tiktok" :value="$setting->tiktok ?? ''" />
            <x-form.input name="youtube" label="Youtube" :value="$setting->youtube ?? ''" />
            <x-form.input name="address" label="Địa chỉ" :value="$setting->address ?? ''" />
            <x-form.ckeditor name="map" label="Iframe Google Map" :value="$setting->map ?? ''" />
            
            <x-form.image-input name="logo" label="Logo" :value="$setting->logo ?? ''" />
            <x-form.image-input name="banner" label="Banner Chung" :value="$setting->banner ?? ''" />
            <x-form.image-input name="favicon" label="Favicon" :value="$setting->favicon ?? ''" />

            <hr>
            
            {{-- [MỚI] 1. Profile Công ty (PDF) --}}
            <div class="form-group">
                <label for="profile" class="font-weight-bold">Hồ sơ năng lực (Profile PDF)</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" name="profile" class="custom-file-input" id="profile" accept=".pdf">
                        <label class="custom-file-label" for="profile">Chọn file PDF...</label>
                    </div>
                </div>
                @if(!empty($setting->profile))
                    <div class="mt-2">
                        <i class="fas fa-file-pdf text-danger"></i> 
                        <a href="{{ asset('storage/' . $setting->profile) }}" target="_blank" class="text-primary">Xem tài liệu hiện tại</a>
                    </div>
                @endif 
                @error('profile') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <hr>

            {{-- [MỚI] 2. Video Giới thiệu --}}
            <div class="form-group">
                <label class="font-weight-bold">Video giới thiệu</label>
                
                {{-- Radio chọn loại video --}}
                <div class="mb-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="type_youtube" name="video_type" value="youtube" class="custom-control-input" 
                            {{ ($setting->video_type ?? 'youtube') == 'youtube' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_youtube">Dùng Link Youtube</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="type_upload" name="video_type" value="upload" class="custom-control-input"
                             {{ ($setting->video_type ?? '') == 'upload' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_upload">Upload Video (MP4)</label>
                    </div>
                </div>

                {{-- Option A: Youtube URL --}}
                <div id="box_youtube" class="{{ ($setting->video_type ?? 'youtube') == 'youtube' ? '' : 'd-none' }}">
                    <x-form.input name="intro_video_url" label="Đường dẫn Youtube" :value="$setting->intro_video_url ?? ''" placeholder="https://www.youtube.com/watch?v=..." />
                </div>

                {{-- Option B: Upload File --}}
                <div id="box_upload" class="{{ ($setting->video_type ?? '') == 'upload' ? '' : 'd-none' }}">
                    <label for="intro_video">File Video (MP4)</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="intro_video" class="custom-file-input" id="intro_video" accept="video/mp4,video/x-m4v,video/*">
                            <label class="custom-file-label" for="intro_video">Chọn video...</label>
                        </div>
                    </div>
                    @if(!empty($setting->intro_video))
                        <div class="mt-2">
                            <i class="fas fa-video text-success"></i>
                            <a href="{{ asset('storage/' . $setting->intro_video) }}" target="_blank">Xem video đã upload</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <hr>

            {{-- Scripts & SEO giữ nguyên --}}
            <x-form.textarea name="schema_script" label="Schema JSON-LD" :value="$setting->schema_script ?? ''" />
            <x-form.textarea name="head_script" label="Code trước </head>" :value="$setting->head_script ?? ''" />
            <x-form.textarea name="body_script" label="Code trước </body>" :value="$setting->body_script ?? ''" />
            <x-form.textarea name="meta_description" label="Meta Description" :value="$setting->meta_description ?? ''" />
            <x-form.textarea name="meta_keywords" label="Meta Keyword" :value="$setting->meta_keywords ?? ''" />
            <x-form.image-input name="meta_image" label="Ảnh chia sẻ" :value="$setting->meta_image ?? ''" />

       </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
        </div>
    </form>
</div>

{{-- JAVASCRIPT XỬ LÝ ẨN HIỆN --}}
@push('js')
<script>
    $(document).ready(function() {
        // Xử lý tên file khi chọn input file (Bootstrap custom file)
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        // Xử lý ẩn hiện Video Type
        $('input[name="video_type"]').change(function() {
            if (this.value === 'youtube') {
                $('#box_youtube').removeClass('d-none');
                $('#box_upload').addClass('d-none');
            } else {
                $('#box_youtube').addClass('d-none');
                $('#box_upload').removeClass('d-none');
            }
        });
    });
</script>
@endpush
@endsection