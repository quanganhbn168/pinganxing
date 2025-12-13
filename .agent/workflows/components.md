---
description: Hướng dẫn sử dụng Blade Components trong dự án CNETPOS
---

# Blade Components Guide

Dự án sử dụng **Laravel Blade Components** để tái sử dụng UI. File này hướng dẫn AI agent cách dùng các component có sẵn.

## 1. Cấu Trúc Thư Mục

```
resources/views/components/
├── admin/                    # Components dùng riêng cho admin
│   ├── bulk-action-bar.blade.php
│   ├── duplicate-button.blade.php
│   └── form/
│       └── media-input.blade.php
├── form/                     # Form inputs
│   ├── input.blade.php
│   ├── select.blade.php
│   ├── switch.blade.php
│   ├── slug.blade.php
│   ├── ckeditor.blade.php
│   ├── textarea.blade.php
│   └── ...
├── boolean-toggle.blade.php  # AJAX toggle status
├── action-buttons.blade.php  # Edit/Delete buttons
└── ...
```

---

## 2. Component Quan Trọng

### 2.1 `<x-boolean-toggle>` - Toggle Status AJAX

**Mục đích:** Hiển thị badge click để toggle boolean field qua AJAX (không reload).

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `model` | string | ✅ | Tên Model class (VD: `App\Models\Post`) |
| `record` | object/int | ✅ | Model instance hoặc ID |
| `field` | string | ✅ | Tên field cần toggle (VD: `status`, `is_home`) |
| `onText` | string | ❌ | Text hiển thị khi ON (default: "Hiện") |
| `offText` | string | ❌ | Text hiển thị khi OFF (default: "Ẩn") |

**Ví dụ:**
```blade
<x-boolean-toggle 
    model="App\Models\Post" 
    :record="$post" 
    field="status" 
    onText="Công khai" 
    offText="Nháp"
/>
```

**Backend Route (đã có):** `POST /admin/toggle` (DashboardController@toggleField)

---

### 2.2 `<x-admin.bulk-action-bar>` - Bulk Actions

**Mục đích:** Hiển thị thanh thao tác hàng loạt khi user check checkbox.

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `model` | string | ✅ | Tên Model class đầy đủ |

**Ví dụ:**
```blade
<x-admin.bulk-action-bar model="App\Models\Post" />
```

**Kết hợp với checkbox trong table:**
```blade
<input type="checkbox" class="bulk-checkbox" value="{{ $post->id }}">
```

**JS dependency:** `public/js/universal-bulk.js` (auto-loaded).

---

### 2.3 `<x-form.input>` - Text Input

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `name` | string | ✅ | Tên field |
| `label` | string | ✅ | Label hiển thị |
| `value` | string | ❌ | Giá trị (auto bind old()) |
| `type` | string | ❌ | Input type (default: "text") |
| `required` | bool | ❌ | Hiện dấu * |
| `placeholder` | string | ❌ | Placeholder |

**Ví dụ:**
```blade
<x-form.input name="name" label="Tên sản phẩm" :value="$product->name" required />
```

---

### 2.4 `<x-form.select>` - Dropdown Select

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `name` | string | ✅ | Tên field |
| `label` | string | ❌ | Label |
| `options` | array | ✅ | Mảng key => value |
| `selected`/`value` | mixed | ❌ | Giá trị được chọn |
| `placeholder` | string | ❌ | Option đầu tiên |
| `required` | bool | ❌ | Bắt buộc |

**Ví dụ:**
```blade
<x-form.select 
    name="category_id" 
    label="Danh mục"
    :options="$categories->pluck('name', 'id')"
    :selected="$product->category_id"
    required
/>
```

---

### 2.5 `<x-form.switch>` - Toggle Switch (Form)

**Khác với boolean-toggle:** Component này dùng trong FORM, không AJAX.

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `name` | string | ✅ | Tên field |
| `label` | string | ✅ | Label |
| `checked` | bool | ❌ | Trạng thái |
| `onText` | string | ❌ | Text ON |
| `offText` | string | ❌ | Text OFF |

**Ví dụ:**
```blade
<x-form.switch 
    name="status" 
    label="Trạng thái"
    :checked="$post->status"
    onText="Công khai"
    offText="Ẩn"
/>
```

---

### 2.6 `<x-form.slug>` - Auto Slug Generator

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `name` | string | ❌ | Field name (default: "slug") |
| `label` | string | ❌ | Label |
| `value` | string | ❌ | Giá trị slug |
| `source` | string | ❌ | CSS selector nguồn (default: "#title") |
| `table` | string | ✅ | Tên bảng check unique |
| `field` | string | ❌ | Field check (default: "slug") |
| `currentId` | int | ❌ | ID exclude khi update |

**Ví dụ:**
```blade
<x-form.slug 
    source="#name" 
    table="posts"
    :value="$post->slug"
    :currentId="$post->id"
/>
```

---

### 2.7 `<x-form.ckeditor>` - Rich Text Editor

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `name` | string | ✅ | Field name |
| `label` | string | ❌ | Label |
| `value` | string | ❌ | Nội dung HTML |
| `required` | bool | ❌ | Bắt buộc |

**Ví dụ:**
```blade
<x-form.ckeditor 
    name="content" 
    label="Nội dung bài viết"
    :value="$post->content"
/>
```

---

### 2.8 `<x-admin.form.media-input>` - Image Picker

**Props:**
| Prop | Type | Required | Mô tả |
|------|------|----------|-------|
| `name` | string | ✅ | Field name (VD: "image_original_path") |
| `label` | string | ❌ | Label |
| `value` | string/array | ❌ | Path ảnh hiện tại |
| `multiple` | bool | ❌ | Cho phép chọn nhiều (gallery) |
| `required` | bool | ❌ | Bắt buộc |
| `help` | string | ❌ | Text mô tả |

**Ví dụ Single:**
```blade
<x-admin.form.media-input 
    name="image_original_path" 
    label="Ảnh đại diện"
    :value="$post->image"
/>
```

**Ví dụ Gallery:**
```blade
<x-admin.form.media-input 
    name="gallery_original_paths" 
    label="Thư viện ảnh"
    :value="$product->galleryPaths"
    :multiple="true"
/>
```

---

## 3. Pattern Chuẩn Khi Tạo CRUD

### 3.1 Route Pattern
```php
Route::resource('posts', PostController::class)->except(['show']);
Route::post('posts/bulk-action', [PostController::class, 'bulkAction'])->name('posts.bulk_action');
```

### 3.2 Index View Pattern
```blade
@extends('layouts.admin')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <h1>Quản lý Bài viết</h1>
            <div>
                <x-admin.bulk-action-bar model="App\Models\Post" />
                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td><input type="checkbox" class="bulk-checkbox" value="{{ $post->id }}"></td>
                        <td>{{ $post->title }}</td>
                        <td>
                            <x-boolean-toggle 
                                model="App\Models\Post" 
                                :record="$post" 
                                field="status"
                            />
                        </td>
                        <td>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $post->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $posts->links() }}
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// SweetAlert2 Delete
$('.btn-delete').on('click', function() {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Xác nhận xóa?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/posts/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    location.reload();
                }
            });
        }
    });
});
</script>
@endpush
```

### 3.3 Form View Pattern
```blade
@extends('layouts.admin')

@section('content')
<form action="{{ $post ? route('admin.posts.update', $post) : route('admin.posts.store') }}" method="POST">
    @csrf
    @if($post) @method('PUT') @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <x-form.input name="title" label="Tiêu đề" :value="$post->title ?? ''" required />
                    <x-form.slug source="#title" table="posts" :value="$post->slug ?? ''" :currentId="$post->id ?? null" />
                    <x-form.ckeditor name="content" label="Nội dung" :value="$post->content ?? ''" />
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <x-form.select name="category_id" label="Danh mục" :options="$categories" :selected="$post->category_id ?? ''" required />
                    <x-admin.form.media-input name="image_original_path" label="Ảnh đại diện" :value="$post->image ?? ''" />
                    <x-form.switch name="status" label="Trạng thái" :checked="$post->status ?? true" />
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-success">Lưu</button>
</form>
@endsection
```

---

## 4. Dependencies

| Component | JS Dependency |
|-----------|---------------|
| `boolean-toggle` | Inline (đã có trong component) |
| `bulk-action-bar` | `public/js/universal-bulk.js` |
| `slug` | `public/js/slug-field.js` |
| `ckeditor` | `public/ckeditor/ckeditor.js` |
| `media-input` | `public/js/media-picker.js` |

---

## 5. Quy Tắc Khi Tạo Component Mới

1. **Đặt trong đúng thư mục:**
   - Form inputs → `components/form/`
   - Admin-only → `components/admin/`
   - Chung → `components/`

2. **Props pattern:**
```blade
@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
])
```

3. **Error handling:**
```blade
@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
```

4. **Old value binding:**
```php
$inputValue = old($name, $value);
```

5. **JS scripts dùng @push:**
```blade
@push('js')
<script>...</script>
@endpush
```
