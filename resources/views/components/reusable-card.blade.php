@props([
    'item', // Đây là object (ví dụ: $project, $news, $product)
    'title' => $item->name, // Mặc định lấy title từ thuộc tính 'name' của object
    'subtitle' => null, // Dòng phụ, không bắt buộc
    'image' => $item->image // Mặc định lấy ảnh từ thuộc tính 'image' của object
])

<div class="item-card">
    {{-- Phần hình ảnh nền --}}
    <div class="item-card__image">
        <img src="{{ optional($item->mainImage())->url() }}" alt="{{ $title }}">
    </div>

    {{-- Lớp phủ chứa thông tin --}}
    <a href="{{ route('frontend.slug.handle', $item->slug) }}" class="item-card__overlay">
        <div class="item-card__info">
            <h3 class="item-card__title">{{ $title }}</h3>
            {{-- Dòng subtitle chỉ hiển thị nếu được truyền vào --}}
            @if ($subtitle)
                <p class="item-card__subtitle">{{ $subtitle }}</p>
            @endif
        </div>
    </a>
</div>