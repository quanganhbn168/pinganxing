<div class="form-group">
    <label for="{{ $id }}">{{ $attributes->get('label', 'Danh mục sản phẩm') }}</label>
    
    <select 
        id="{{ $id }}" {{-- Thêm dòng này --}}
        name="{{ $name }}" 
        {{ $attributes->merge(['class' => 'form-control select2']) }} 
        style="width: 100%;"
    >
        <option value="">{{ $placeholder }}</option>

        @foreach($options as $opt)
            <option 
                value="{{ $opt->id }}"
                {{ $opt->disabled ? 'disabled' : '' }}
                {{ (string)$opt->id === (string)old($name, $selected) ? 'selected' : '' }}
                style="{{ $opt->style }}"
            >
                {{ $opt->name }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
</div>