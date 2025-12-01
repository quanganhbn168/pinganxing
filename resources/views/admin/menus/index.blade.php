@extends('layouts.admin')

@section('content')
<div class="row">
    {{-- CỘT TRÁI: NGUỒN DỮ LIỆU --}}
    <div class="col-md-4">
        
        {{-- 1. Trang Hệ thống --}}
        <div class="card mb-3 collapsed-card">
            <div class="card-header bg-navy" data-card-widget="collapse" style="cursor: pointer">
                <h3 class="card-title text-white" style="font-size: 1rem;">Trang Hệ thống</h3>
                <div class="card-tools"><button type="button" class="btn btn-tool text-white"><i class="fas fa-plus"></i></button></div>
            </div>
            <div class="card-body p-2" style="display: none;">
                @foreach($systemRoutes as $route => $label)
                    <div class="form-check mb-1">
                        <input class="form-check-input select-system" type="checkbox" value="{{ $route }}" data-title="{{ $label }}">
                        <label class="form-check-label small">{{ $label }}</label>
                    </div>
                @endforeach
                <button class="btn btn-sm btn-outline-navy btn-block mt-2" onclick="addSystemLinks()">Thêm vào Menu</button>
            </div>
        </div>

        {{-- 2. DANH MỤC SẢN PHẨM --}}
<div class="card mb-3 collapsed-card">
    <div class="card-header bg-success" data-card-widget="collapse" style="cursor: pointer">
        <h3 class="card-title text-white" style="font-size: 1rem;">Danh mục SP</h3>
        <div class="card-tools"><button type="button" class="btn btn-tool text-white"><i class="fas fa-plus"></i></button></div>
    </div>
    <div class="card-body p-2" style="display: none; max-height: 200px; overflow-y: auto;">
        @foreach($categories as $cat)
            <div class="form-check mb-1">
                {{-- THÊM data-menu-type="product_category" --}}
                <input class="form-check-input select-category" 
                       type="checkbox" 
                       value="{{ $cat->id }}" 
                       data-title="{{ $cat->name }}" 
                       data-menu-type="category"> 
                <label class="form-check-label small">{{ $cat->name }}</label>
            </div>
        @endforeach
        <button class="btn btn-sm btn-outline-success btn-block mt-2" onclick="addCategories()">Thêm vào Menu</button>
    </div>
</div>

{{-- 3. DANH MỤC LĨNH VỰC --}}
<div class="card mb-3 collapsed-card">
    <div class="card-header bg-warning" data-card-widget="collapse" style="cursor: pointer">
        <h3 class="card-title text-white" style="font-size: 1rem;">Lĩnh vực</h3>
        <div class="card-tools"><button type="button" class="btn btn-tool text-white"><i class="fas fa-plus"></i></button></div>
    </div>
    <div class="card-body p-2" style="display: none; max-height: 200px; overflow-y: auto;">
        @foreach($fieldCategories as $cat)
            <div class="form-check mb-1">
                {{-- THÊM data-menu-type="field_category" --}}
                <input class="form-check-input select-category" 
                       type="checkbox" 
                       value="{{ $cat->id }}" 
                       data-title="{{ $cat->name }}"
                       data-menu-type="field_category">
                <label class="form-check-label small">{{ $cat->name }}</label>
            </div>
        @endforeach
        <button class="btn btn-sm btn-outline-warning btn-block mt-2" onclick="addCategories()">Thêm vào Menu</button>
    </div>
</div>

{{-- 4. DANH MỤC DỰ ÁN --}}
<div class="card mb-3 collapsed-card">
    <div class="card-header bg-danger" data-card-widget="collapse" style="cursor: pointer">
        <h3 class="card-title text-white" style="font-size: 1rem;">Dự án</h3>
        <div class="card-tools"><button type="button" class="btn btn-tool text-white"><i class="fas fa-plus"></i></button></div>
    </div>
    <div class="card-body p-2" style="display: none; max-height: 200px; overflow-y: auto;">
        @foreach($projectCategories as $cat)
            <div class="form-check mb-1">
                {{-- THÊM data-menu-type="project_category" --}}
                <input class="form-check-input select-category" 
                       type="checkbox" 
                       value="{{ $cat->id }}" 
                       data-title="{{ $cat->name }}"
                       data-menu-type="project_category">
                <label class="form-check-label small">{{ $cat->name }}</label>
            </div>
        @endforeach
        <button class="btn btn-sm btn-outline-danger btn-block mt-2" onclick="addCategories()">Thêm vào Menu</button>
    </div>
</div>

{{-- 5. DANH MỤC TIN TỨC --}}
<div class="card mb-3 collapsed-card">
    <div class="card-header bg-primary" data-card-widget="collapse" style="cursor: pointer">
        <h3 class="card-title text-white" style="font-size: 1rem;">Tin tức</h3>
        <div class="card-tools"><button type="button" class="btn btn-tool text-white"><i class="fas fa-plus"></i></button></div>
    </div>
    <div class="card-body p-2" style="display: none; max-height: 200px; overflow-y: auto;">
        @foreach($postCategories as $cat)
            <div class="form-check mb-1">
                {{-- THÊM data-menu-type="post_category" --}}
                <input class="form-check-input select-category" 
                       type="checkbox" 
                       value="{{ $cat->id }}" 
                       data-title="{{ $cat->name }}"
                       data-menu-type="post_category">
                <label class="form-check-label small">{{ $cat->name }}</label>
            </div>
        @endforeach
        <button class="btn btn-sm btn-outline-primary btn-block mt-2" onclick="addCategories()">Thêm vào Menu</button>
    </div>
</div>
        
        
        {{-- 4. Custom Link --}}
        <div class="card mb-3">
            <div class="card-header bg-secondary">
                <h3 class="card-title text-white" style="font-size: 1rem;">Link Tự do</h3>
            </div>
            <div class="card-body p-2">
                <input type="text" id="custom-title" class="form-control form-control-sm mb-2" placeholder="Tên hiển thị">
                <input type="text" id="custom-url" class="form-control form-control-sm mb-2" placeholder="https://...">
                <button class="btn btn-sm btn-outline-secondary btn-block" onclick="addCustom()">Thêm vào Menu</button>
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: KÉO THẢ MENU --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h3 class="card-title text-white m-0">Cấu trúc Menu</h3>
                <button class="btn btn-warning btn-sm font-weight-bold shadow" onclick="saveMenuOrder()">
                    <i class="fas fa-save mr-1"></i> LƯU MENU
                </button>
            </div>
            <div class="card-body bg-light">
                <div class="dd" id="nestable">
                    <ol class="dd-list" id="menu-container">
                        @foreach($menuItems as $item)
                            @include('admin.menus.item', ['item' => $item])
                        @endforeach
                    </ol>
                </div>
                @if($menuItems->isEmpty())
                    <p class="text-center text-muted mt-5" id="empty-msg">Chưa có menu nào. Hãy thêm từ cột trái.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css" />
<style>
    .dd { max-width: 100%; }
    .dd-handle { height: auto; padding: 10px 15px; border: 1px solid #ddd; background: #fff; border-radius: 4px; }
    .dd-handle:hover { color: #007bff; background: #f4f6f9; }
    .dd-item > button { margin-top: 5px; } 
    .dd-placeholder { border: 1px dashed #007bff; background: #eef7ff; height: 40px; }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
<script>
    $(document).ready(function() {
        // CẤU HÌNH NESTABLE (MAX 5 CẤP)
        $('#nestable').nestable({ maxDepth: 5 });

        // SỰ KIỆN: Bấm nút Edit hiện form
        $('body').on('click', '.btn-edit-item', function() {
            $(this).closest('li').find('> .menu-item-settings').slideToggle(200);
        });

        // SỰ KIỆN: Sửa tên -> Cập nhật hiển thị & Data attribute
        $('body').on('keyup', '.edit-menu-title', function() {
            let val = $(this).val();
            let li = $(this).closest('li');
            li.find('> .dd-handle .menu-title').text(val);
            li.data('title', val); 
        });

        // SỰ KIỆN: Sửa URL -> Cập nhật Data attribute
        $('body').on('keyup', '.edit-menu-url', function() {
            let val = $(this).val();
            $(this).closest('li').data('url', val);
        });

        // SỰ KIỆN: Xóa
        $('body').on('click', '.btn-remove-item', function() {
            if(!confirm('Xóa mục này?')) return;
            let li = $(this).closest('li');
            let id = li.data('id');
            // Ajax xóa DB ngay (hoặc đợi Save mới xóa tùy anh, ở đây xóa luôn cho sạch)
            $.ajax({
                url: '/admin/menus/' + id,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function() { li.fadeOut(300, function(){ $(this).remove(); }); }
            });
        });
    });

    // HÀM HELPER ADD AJAX
    function addMenuAjax(title, type, url, ref_id) {
        $.post("{{ route('admin.menus.store') }}", {
            _token: "{{ csrf_token() }}",
            title: title, type: type, url: url, reference_id: ref_id
        }, function(res) {
            if(res.status === 'success') {
                $('#empty-msg').remove();
                $('#menu-container').append(res.html);
                // Scroll xuống cuối
                $('html, body').animate({ scrollTop: $(document).height() }, 500);
            }
        });
    }

    // 1. ADD SYSTEM
    function addSystemLinks() {
        $('.select-system:checked').each(function() {
            addMenuAjax($(this).data('title'), 'system_route', $(this).val(), null);
            $(this).prop('checked', false);
        });
    }

    // Thay thế hàm addCategories cũ bằng hàm này
    function addCategories() {
        $('.select-category:checked').each(function() {
            let title = $(this).data('title');
            let id = $(this).val();

        // [QUAN TRỌNG] Lấy đúng loại type từ data attribute
            let type = $(this).data('menu-type'); 

            addMenuAjax(title, type, null, id);
            $(this).prop('checked', false);
        });
    }

    // 3. ADD PAGES
    function addPages() {
        $('.select-page:checked').each(function() {
            addMenuAjax($(this).data('title'), 'page', null, $(this).val());
            $(this).prop('checked', false);
        });
    }

    // 4. ADD CUSTOM
    function addCustom() {
        let t = $('#custom-title').val();
        let u = $('#custom-url').val();
        if(t && u) {
            addMenuAjax(t, 'custom', u, null);
            $('#custom-title').val(''); $('#custom-url').val('');
        } else { alert('Vui lòng nhập tên và link'); }
    }

    // LƯU TOÀN BỘ CÂY
    function saveMenuOrder() {
        let data = $('#nestable').nestable('serialize');
        $.post("{{ route('admin.menus.updateTree') }}", {
            _token: "{{ csrf_token() }}",
            menu: data
        }, function(res) {
            alert('Đã lưu menu thành công!');
        });
    }
</script>
@endpush
@endsection