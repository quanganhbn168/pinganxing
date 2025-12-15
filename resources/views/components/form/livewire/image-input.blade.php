@props([
    'name',
    'label' => '',
    'value' => null,      // Đường dẫn ảnh cũ (khi edit)
    'image' => null,      // Object ảnh tạm thời của Livewire
    'required' => false,
])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    
    {{-- wire:model sẽ được truyền vào đây thông qua $attributes --}}
    <input type="file" name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'form-control']) }}>
    
    {{-- Hiệu ứng loading khi ảnh đang được tải lên server --}}
    <div wire:loading wire:target="{{ $name }}" class="mt-2 text-primary">Đang tải lên...</div>

    @error($name) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

    <div class="mt-2">
        @if ($image)
            {{-- 1. Ưu tiên hiển thị preview ảnh MỚI --}}
            <img src="{{ asset('storage/livewire-tmp/' . $image->getFilename()) }}" style="max-height: 150px; border: 1px solid #ddd; padding: 4px;" onerror="this.style.display='none'">
        @elseif ($value)
            {{-- 2. Nếu không có ảnh mới, hiển thị ảnh CŨ (khi edit) --}}
            <img src="{{ asset($value) }}" style="max-height: 150px; border: 1px solid #ddd; padding: 4px;">
        @endif
        {{-- 3. Nếu không có cả hai, không hiển thị gì cả --}}
    </div>
</div>