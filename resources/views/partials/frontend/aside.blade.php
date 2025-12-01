@php
    $productCategories = \App\Models\Category::where('parent_id', 0)
                                ->with('children')
                                ->where('status', 1)
                                ->get();
@endphp
<aside class="col-lg-3 d-none d-lg-block">
    <div class="sidebar-filter">
        <div class="sidebar-widget">
            <h3 class="widget-title">Danh mục sản phẩm</h3>
            <div class="widget-content">
                <ul class="category-list">
                    @foreach($productCategories as $category)
                        <li>
                            {{-- Sửa dòng dưới đây --}}
                            <a href="{{ route('frontend.slug.handle', $category->slug) }}" class="{{ isset($currentCategory) && $currentCategory->id == $category->id ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                            @if($category->children->isNotEmpty())
                                <ul class="subcategory-list">
                                    @foreach($category->children as $childCategory)
                                        <li>
                                            {{-- Và sửa cả dòng dưới đây --}}
                                            <a href="{{ route('frontend.slug.handle', $childCategory->slug) }}" class="{{ isset($currentCategory) && $currentCategory->id == $childCategory->id ? 'active' : '' }}">
                                                {{ $childCategory->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</aside>