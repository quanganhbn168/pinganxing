@props([
    'categories' => collect(),
    'selectedId' => null,
    'level' => 0,                 
])

@foreach($categories as $cat)
    <option value="{{ $cat->id }}" @selected($selectedId == $cat->id)>
        {{ str_repeat('— ', $level) }}{{ $cat->name }}
    </option>

    @php
        // Ưu tiên dùng childrenRecursive nếu có, fallback về children
        $children = $cat->childrenRecursive ?? $cat->children ?? collect();
    @endphp

    @if($children->isNotEmpty())
        <x-form.category-tree
            :categories="$children"
            :selectedId="$selectedId"
            :level="$level + 1"
        />
    @endif
@endforeach
