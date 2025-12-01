<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    
    <select name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-control {{ $errors->has($name) ? ' is-invalid' : '' }}"
            {{ $required ? 'required' : '' }}>
            
        <option value="0">— Chọn danh mục —</option>
        
        @foreach ($flatCategories as $category)
            <option value="{{ $category['id'] }}" @selected($selected == $category['id'])>
                {{ $category['name'] }}
            </option>
        @endforeach
    </select>

    @error($name)
        <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>