@php
    /** @var \App\Models\ProjectCategory $category */
@endphp

<div class="row">
    <div class="col-md-8">
        {{-- Tên & Slug --}}
        <x-form.input name="name" label="Tên danh mục" :value="old('name', $category->name)" required="true" />

        {{-- Mô tả ngắn & Nội dung --}}
        <x-form.textarea name="description" label="Mô tả ngắn" :value="old('description', $category->description)" />
        <x-form.ckeditor name="content" label="Nội dung" :value="old('content', $category->content)" />

        {{-- SEO --}}
        <div class="card mt-3">
            <div class="card-header"><strong>SEO</strong></div>
            <div class="card-body">
                <x-form.textarea name="meta_description" label="Meta Description" :value="old('meta_description', $category->meta_description)" />
                <x-form.input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $category->meta_keywords)" />
                <x-form.image-input name="meta_image" label="Ảnh chia sẻ (OG/Twitter)" :value="old('meta_image', $category->meta_image)" />
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Danh mục cha (dùng select-best) --}}
        <x-form.select-best
            name="parent_id"
            label="Danh mục cha"
            :collection="$parents"
            valueField="id"
            textField="name"
            :selected="old('parent_id', $category->parent_id ?? 0)"
            placeholder="Root (Không có cha)"
        />

        {{-- Ảnh & Banner --}}
        <x-form.image-input name="image" label="Ảnh đại diện" :value="old('image', $category->image)" required="{{ $category->exists ? false : true }}" />
        <x-form.image-input name="banner" label="Banner" :value="old('banner', $category->banner)" />

        {{-- Vị trí & Cờ --}}
        <x-form.input name="position" label="Thứ tự" type="number" :value="old('position', $category->position)" />
        <x-form.switch name="status" label="Hiển thị" :checked="old('status', $category->status)" onText="Bật" offText="Tắt" />
        <x-form.switch name="is_home" label="Hiển thị trang chủ" :checked="old('is_home', $category->is_home)" onText="Bật" offText="Tắt" />
        <x-form.switch name="is_menu" label="Hiển thị menu" :checked="old('is_menu', $category->is_menu)" onText="Bật" offText="Tắt" />
        <x-form.switch name="is_footer" label="Hiển thị footer" :checked="old('is_footer', $category->is_footer)" onText="Bật" offText="Tắt" />
    </div>
</div>
