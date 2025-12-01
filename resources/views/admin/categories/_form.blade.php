<form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($category->exists)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-12">
            {{-- Tên và Slug --}}
            <x-form.input name="name" label="Tên danh mục" :value="$category->name" required />
            {{-- Gán thuộc tính bằng Duallistbox --}}
            @php
    $placeholderText = ($category->parent_id == 0)
        ? 'Đây là danh mục cha'
        : '--- Bỏ chọn (đưa về danh mục gốc) ---';
@endphp

            <x-form.select name="parent_id"
               label="Danh mục cha"
               :options="$categories"
               :selected="$category->parent_id"
               :placeholder="$placeholderText" />
            <x-form.ckeditor name="description" label="Mô tả ngắn" :value="$category->description" />
            <x-form.ckeditor name="content" label="Nội dung chi tiết" :value="$category->content" />

            <x-form.switch name="status" label="Trạng thái" :checked="$category->status ?? true" />
            {{-- Upload Ảnh --}}
            <div class="form-group">
                <x-form.image-input name="image" label="Ảnh đại diện" :value="$category->image"/>
            </div>
            <div class="form-group">
                <x-form.image-input name="banner" label="Banner" :value="$category->banner"/>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            {{ $category->exists ? 'Cập nhật' : 'Lưu' }}
        </button>
        <a href="route('admin.categories.index')" class="btn btn-secondary">Hủy</a>
    </div>
</form>