{{-- File: resources/views/components/form/image-multi-input.blade.php --}}
@props([
    'name',
    'label',
    'images' => [],
    'required' => false,
])

@php
    $inputId = 'input_' . $name;
    $wrapperId = 'wrapper_' . $name;
@endphp

<div class="form-group">
    <label for="{{ $inputId }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>

    {{-- Input để upload các file ảnh mới --}}
    <input
        type="file"
        name="{{ $name }}[]"
        id="{{ $inputId }}"
        multiple
        accept="image/*"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        onchange="previewMultiImage('{{ $inputId }}', '{{ $wrapperId }}')"
    >

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    {{-- Vùng hiển thị các ảnh cũ và ảnh mới được chọn --}}
    <div id="{{ $wrapperId }}" class="d-flex flex-wrap mt-3" style="gap: 0.5rem;">
        {{-- Lặp qua các ảnh cũ (nếu có) --}}
        @foreach($images as $image)
            <div class="preview-image-item position-relative" style="width: 120px; border: 1px solid #ddd; padding: 4px; border-radius: 4px;">
                {{-- Hiển thị ảnh cũ --}}
                <img src="{{ asset($image->image) }}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                
                {{-- Input ẩn để báo cho backend biết những ảnh cũ nào được giữ lại --}}
                <input type="hidden" name="gallery_old[]" value="{{ $image->id }}">
                
                {{-- Nút xóa ảnh cũ --}}
                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 3px; right: 3px; padding: 0.15rem 0.4rem;"
                        onclick="this.parentElement.remove()">×</button>
            </div>
        @endforeach
    </div>
</div>

@pushOnce('js')
<script>
    // Map để lưu trữ danh sách các file mới được chọn
    const fileListMap = new Map();

    function previewMultiImage(inputId, wrapperId) {
        const input = document.getElementById(inputId);
        const wrapper = document.getElementById(wrapperId);

        // Tạo một DataTransfer object để quản lý danh sách file mới
        const dataTransfer = new DataTransfer();
        
        // Thêm các file đã có trong Map (nếu có) vào DataTransfer
        if (fileListMap.has(inputId)) {
            fileListMap.get(inputId).forEach(file => dataTransfer.items.add(file));
        }

        // Xử lý các file mới được chọn
        Array.from(input.files).forEach(file => {
            // Thêm file mới vào DataTransfer và Map
            dataTransfer.items.add(file);
            
            if (!fileListMap.has(inputId)) {
                fileListMap.set(inputId, []);
            }
            fileListMap.get(inputId).push(file);

            // Tạo preview
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'preview-image-item position-relative';
                div.style.cssText = 'width: 120px; border: 1px solid #ddd; padding: 4px; border-radius: 4px;';
                div.dataset.fileName = file.name; // Lưu tên file để nhận biết khi xóa

                div.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute"
                            style="top: 3px; right: 3px; padding: 0.15rem 0.4rem;"
                            onclick="removeNewImage(this, '${inputId}')">×</button>
                `;
                wrapper.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        // Cập nhật lại danh sách file trong input
        input.files = dataTransfer.files;
    }

    function removeNewImage(button, inputId) {
        const itemToRemove = button.parentElement;
        const fileName = itemToRemove.dataset.fileName;
        
        // Xóa file khỏi Map
        if (fileListMap.has(inputId)) {
            const updatedFiles = fileListMap.get(inputId).filter(file => file.name !== fileName);
            fileListMap.set(inputId, updatedFiles);
        }

        // Cập nhật lại file list trong input
        const input = document.getElementById(inputId);
        const dataTransfer = new DataTransfer();
        if (fileListMap.has(inputId)) {
            fileListMap.get(inputId).forEach(file => dataTransfer.items.add(file));
        }
        input.files = dataTransfer.files;

        // Xóa preview khỏi DOM
        itemToRemove.remove();
    }
</script>
@endPushOnce