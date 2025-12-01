{{-- resources/views/components/form/partials/category-radio-item.blade.php --}}

<div class="category-item">
    <div class="custom-control custom-radio">
        <input type="radio" 
               id="category_{{ $category->id }}" 
               name="{{ $name }}" 
               value="{{ $category->id }}"
               class="custom-control-input"
               {{ $selected == $category->id ? 'checked' : '' }}>
        <label class="custom-control-label" for="category_{{ $category->id }}">{{ $category->name }}</label>
    </div>

    {{-- Đệ quy: Nếu danh mục này có con, thì lặp và gọi lại chính view này --}}
    @if ($category->children && $category->children->isNotEmpty())
        <div class="category-children">
            @foreach ($category->children as $child)
                @include('components.form.partials.category-radio-item', ['category' => $child])
            @endforeach
        </div>
    @endif
</div>