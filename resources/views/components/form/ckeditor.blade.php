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
            filebrowserBrowseUrl: '{{ asset(route('ckfinder_browser')) }}',
              filebrowserImageBrowseUrl: '{{ asset(route('ckfinder_browser')) }}?type=Images',
              filebrowserFlashBrowseUrl: '{{ asset(route('ckfinder_browser')) }}?type=Flash',
              filebrowserUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=Files',
              filebrowserImageUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=Images',
              filebrowserFlashUploadUrl: '{{ asset(route('ckfinder_connector')) }}?command=QuickUpload&type=Flash'
        });
    </script>
@endpush
