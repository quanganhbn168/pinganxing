@props([
    'name',
    'label' => '',
    'value' => '',
    'required' => false,
    'config' => [],
])

@php
    use Illuminate\Support\Str;
    $editorId = Str::slug($name, '_') . '_' . uniqid();
    $inputValue = old($name, $value);
@endphp

<div class="form-group">
    <label for="{{ $editorId }}">
        {{ $label }}
        @if($required)<span class="text-danger">*</span>@endif
    </label>

    
        <textarea
            name="{{ $name }}"
            id="{{ $editorId }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        >{{ $inputValue }}</textarea>

        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    
</div>

@push('js')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('{{ $editorId }}', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
        });
    </script>
@endpush
