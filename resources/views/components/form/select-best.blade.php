@props([
    'name',
    'label',
    'collection' => [], // Thay đổi ở đây: mặc định là mảng rỗng cho an toàn
    'valueField' => 'id',
    'textField' => 'name',
    'selected' => null,
    'placeholder' => '--- Vui lòng chọn ---',
    'required' => false,
])

@php
    // ===================================================================
    // SỬA LỖI isNotEmpty() NẰM Ở ĐÂY
    // Dòng này đảm bảo dù anh truyền vào là array hay collection thì nó đều hoạt động
    $collection = collect($collection);
    // ===================================================================

    $selectedValue = old($name, $selected);
@endphp

<div class="form-group mb-3">
    <label for="{{ $name }}" class="form-label fw-bold">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control form-select @error($name) is-invalid @enderror }}"
        @if($required) required @endif
    >
        <option value="0">{{ $placeholder }}</option>
        
        {{-- Code ở đây giờ sẽ luôn đúng vì $collection đã là một Collection object --}}
        @if($collection->isNotEmpty())
            @foreach ($collection as $item)
                <option
                    value="{{ data_get($item, $valueField) }}" {{-- Dùng data_get an toàn hơn --}}
                    @if ((string)$selectedValue === (string)data_get($item, $valueField)) selected @endif
                >
                    {{ data_get($item, $textField) }} {{-- Dùng data_get an toàn hơn --}}
                </option>
            @endforeach
        @endif
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>