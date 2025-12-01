<div class="product_item">
    <div class="product_item-img">
        <a href="{{ route('frontend.slug.handle', $product->slugValue) }}">
            <img class="main-image" src="{{ optional($product->mainImage())->url() }}" alt="{{ $product->name }}">
        </a>
    </div>
    <div class="product_item-name">
        <a href="{{ route('frontend.slug.handle', $product->slugValue) }}">
            {{ $product->name }}
        </a>
    </div>
    <div class="product_item-price">
        @if($product->price > 0)
            <p>Giá: <span class="text-danger text-bold">{{ number_format($product->price) }}₫</span></p>
        @else
            <p>Giá: Liên hệ</p>
        @endif
        <div class="product_item-arrow">
            <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 11L6.5 6L1.5 1" stroke="#00427A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>
@once
@push('css')
<style>
.product_item {
    background-color: #fff;
    border-radius: 0;
    overflow: visible;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.product_item-img {
    position: relative; 
    aspect-ratio: 4 / 3;
    overflow: hidden; 
    margin-bottom: 20px;
}
.product_item-img .main-image {
    width: 100%;
    height: 100%;
    object-fit: contain; 
}
.product_item-badge {
    position: absolute;
}
.product_item-name {
    flex-grow: 1; 
    border-bottom: 1px dotted #ccc; 
    margin: 0 15px; 
}
.product_item-name a {
    color: var(--cnet-bg-dark, #333);
    font-weight: 700;
    line-height: 1.4;
    text-decoration: none;
    font-size: 1.1rem; 
    text-transform: uppercase; 
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    height: calc(1.4em * 2);
    margin-bottom: 20px;
}
.product_item-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
}
.product_item-price p {
    margin: 0;
    font-weight: 500;
    color: #555; 
    font-size: 0.9rem;
}
.product_item-arrow {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #e9f5ff; 
    display: flex;
    align-items: center;
    justify-content: center;
}
.product_item-arrow svg path {
    stroke: var(--cnet-blue-primary, #00427A); 
}
</style>
@endpush
@endonce