{{-- File: resources/views/components/form/image-input.blade.php --}}
@props([
    'name',
    'label',
    'value' => '', // Prop `value` chứa đường dẫn ảnh cũ
    'required' => false,
    'defaultImage' => 'images/setting/no-image.png'
])

@php
    // Nếu có `value` (ảnh cũ) thì dùng, không thì dùng ảnh mặc định.
    // Hàm old() sẽ ghi đè nếu có lỗi validation.
    $imageUrl = old($name, $value) ? asset(old($name, $value)) : asset($defaultImage);
    $inputId = 'input_' . $name;
    $previewId = 'preview_' . $name;
@endphp

<div class="form-group">
    <label for="{{ $inputId }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $inputId }}"
        accept="image/*"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        onchange="previewImage('{{ $inputId }}', '{{ $previewId }}')"
    >
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    <div class="mt-2">
        <img
            id="{{ $previewId }}"
            src="{{ $imageUrl }}"
            alt="Preview"
            style="max-height: 150px; border: 1px solid #ddd; padding: 4px; background-color: #f8f8f8;"
        >
    </div>
</div>

{{-- Script này có thể được push một lần ở layout chính để tránh lặp lại --}}
@pushOnce('js')
<script>
    function previewImage(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endPushOnce