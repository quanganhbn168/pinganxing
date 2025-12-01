<li class="dd-item" 
    data-id="{{ $item->id }}" 
    data-title="{{ $item->title }}" 
    data-url="{{ $item->url }}">

    {{-- 1. Thanh hiển thị chính --}}
    <div class="dd-handle">
        <span class="menu-title font-weight-bold">{{ $item->title }}</span>
        
        <span class="float-right font-weight-normal text-muted" style="font-size: 0.75rem; margin-top: 2px;">
            @if($item->type == 'system_route') <span class="badge badge-dark">System</span>
            @elseif($item->type == 'page') <span class="badge badge-info">Page</span>
            @elseif($item->type == 'category') <span class="badge badge-success">SP</span>
            @elseif($item->type == 'field_category') <span class="badge badge-warning">Lĩnh vực</span>
            @elseif($item->type == 'project_category') <span class="badge badge-danger">Dự án</span>
            @elseif($item->type == 'post_category') <span class="badge badge-primary">Tin tức</span>
            @else <span class="badge badge-secondary">Link</span>
            @endif
        </span>
    </div>

    {{-- 2. Nút hành động --}}
    <div class="item-actions" style="position: absolute; right: 10px; top: 8px; z-index: 5;">
        <button type="button" class="btn btn-xs btn-default btn-edit-item" title="Sửa tên"><i class="fas fa-pen"></i></button>
        <button type="button" class="btn btn-xs btn-danger btn-remove-item" title="Xóa"><i class="fas fa-times"></i></button>
    </div>

    {{-- 3. Form Sửa (Mặc định ẩn, hiện khi bấm nút Pen) --}}
    <div class="menu-item-settings bg-light border p-2 mt-1 rounded" style="display: none;">
        <div class="form-group mb-2">
            <label class="small mb-0">Tên hiển thị:</label>
            <input type="text" class="form-control form-control-sm edit-menu-title" value="{{ $item->title }}">
        </div>
        
        @if($item->type == 'custom')
        <div class="form-group mb-0">
            <label class="small mb-0">URL:</label>
            <input type="text" class="form-control form-control-sm edit-menu-url" value="{{ $item->url }}">
        </div>
        @endif
    </div>

    {{-- 4. Đệ quy con --}}
    @if($item->children && $item->children->count() > 0)
        <ol class="dd-list">
            @foreach($item->children as $child)
                @include('admin.menus.item', ['item' => $child])
            @endforeach
        </ol>
    @endif
</li>