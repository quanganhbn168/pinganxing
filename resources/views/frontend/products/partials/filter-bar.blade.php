@props([
    'allCategories' => collect(),
    'allBrands' => collect(),
    'action' => route('products.index'),
    'showCategory' => true,
])

<form method="GET" action="{{ $action }}" class="mb-8 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
        <div class="xl:col-span-2">
            <label for="product-filter-q" class="sr-only">Tìm sản phẩm</label>
            <div class="relative">
                <input
                    id="product-filter-q"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Tìm tên, mã sản phẩm..."
                    class="h-11 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 pr-10 text-sm font-medium text-gray-900 outline-none transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                >
                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
            </div>
        </div>

        @if($showCategory)
            <select name="category_id" class="h-11 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm font-semibold text-gray-700 outline-none transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
                <option value="">Tất cả danh mục</option>
                @foreach($allCategories as $filterCategory)
                    <option value="{{ $filterCategory->id }}" @selected((string) request('category_id') === (string) $filterCategory->id)>
                        {{ $filterCategory->name }}
                    </option>
                @endforeach
            </select>
        @endif

        <select name="brand_id" class="h-11 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm font-semibold text-gray-700 outline-none transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
            <option value="">Tất cả hãng</option>
            @foreach($allBrands as $brand)
                <option value="{{ $brand->id }}" @selected((string) request('brand_id') === (string) $brand->id)>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>

        <select name="price" class="h-11 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm font-semibold text-gray-700 outline-none transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
            <option value="">Khoảng giá</option>
            <option value="under-2m" @selected(request('price') === 'under-2m')>Dưới 2 triệu</option>
            <option value="2m-5m" @selected(request('price') === '2m-5m')>2 - 5 triệu</option>
            <option value="5m-10m" @selected(request('price') === '5m-10m')>5 - 10 triệu</option>
            <option value="over-10m" @selected(request('price') === 'over-10m')>Trên 10 triệu</option>
            <option value="contact" @selected(request('price') === 'contact')>Liên hệ</option>
        </select>

        <select name="sort" class="h-11 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm font-semibold text-gray-700 outline-none transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
            <option value="">Mới nhất</option>
            <option value="price-asc" @selected(request('sort') === 'price-asc')>Giá thấp đến cao</option>
            <option value="price-desc" @selected(request('sort') === 'price-desc')>Giá cao đến thấp</option>
            <option value="name-asc" @selected(request('sort') === 'name-asc')>Tên A-Z</option>
        </select>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-blue-700 px-5 text-sm font-bold text-white transition-colors hover:bg-blue-800">
            Lọc sản phẩm
        </button>
        <a href="{{ $action }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-gray-200 px-4 text-sm font-bold text-gray-600 transition-colors hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Xóa lọc
        </a>
    </div>
</form>
