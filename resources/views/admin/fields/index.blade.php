@extends('layouts.admin')

@section('title', 'Quản lý Lĩnh vực')
@section('content_header_title', 'Quản lý Lĩnh vực')

@push('css')
<style>
    /* Checkbox to dễ bấm */
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
    
    /* Tinh chỉnh ảnh thumbnail */
    .table-hover .thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
    
    /* Căn giữa nội dung bảng */
    .table-hover td { vertical-align: middle !important; }
</style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form method="GET" action="{{ route('admin.fields.index') }}" class="row">
                <div class="col-md-5">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tên lĩnh vực/Slug..." />
                </div>
                <div class="col-md-3">
                    <x-form.select
                        name="status"
                        label="Trạng thái"
                        :options="['1' => 'Hiển thị', '0' => 'Ẩn']"
                        :selected="request('status')"
                        placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>
                    <button class="btn btn-secondary btn-block"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Lĩnh vực</h3>
            <div class="card-tools d-flex align-items-center">
                {{-- [MỚI] Nút Bulk Delete --}}
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm mr-2" style="display: none;" onclick="FieldManager.submitBulk('delete')">
                    <i class="fas fa-trash mr-1"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
                </button>

                <a href="{{ route('admin.fields.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Thêm lĩnh vực
                </a>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                <tr>
                    {{-- [MỚI] Checkbox All --}}
                    <th class="text-center" style="width: 40px">
                        <input type="checkbox" id="checkAll" class="custom-checkbox">
                    </th>
                    <th style="width: 50px">#</th>
                    <th>Ảnh</th>
                    <th>Tên lĩnh vực</th>
                    <th>Danh mục cha</th>
                    <th class="text-center">Home</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 120px" class="text-center">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($fields as $key => $field)
                    @php
                        $row = ($fields->currentPage() - 1) * $fields->perPage() + $key + 1;
                        $img = method_exists($field,'mainImage') ? $field->mainImage() : null;
                        $thumbUrl = $img ? ($img->url('thumbnail') ?: $img->url()) : asset('images/setting/no-image.png');
                    @endphp
                    <tr>
                        {{-- [MỚI] Checkbox Item --}}
                        <td class="text-center">
                            <input type="checkbox" class="custom-checkbox check-item" value="{{ $field->id }}">
                        </td>
                        <td>{{ $row }}</td>
                        <td>
                            <img src="{{ $thumbUrl }}" alt="{{ $field->name }}" class="thumb">
                        </td>
                        <td>
                            <strong>{{ $field->name }}</strong>
                            @if($field->slug)
                                <br><small class="text-muted">{{ $field->slug }}</small>
                            @endif
                        </td>
                        <td>{{ $field->category->name ?? '—' }}</td>
                        
                        {{-- Các toggle --}}
                        <td class="text-center">
                            <x-boolean-toggle model="Field" :record="$field" field="is_home" />
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="Field" :record="$field" field="status" />
                        </td>

                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.fields.edit', $field) }}" class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                {{-- Sửa model thành fields (hoặc field_categories tùy logic) --}}
                                <x-admin.duplicate-button 
                                    model="fields" 
                                    :id="$field->id"
                                    label="" 
                                    icon="fas fa-copy" 
                                    confirm="Nhân bản lĩnh vực này?" 
                                    class="btn btn-sm btn-info" 
                                />

                                {{-- Nút xóa dùng JS chung --}}
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="FieldManager.deleteSingle({{ $field->id }})" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                            Chưa có lĩnh vực nào
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($fields->hasPages())
            <div class="card-footer clearfix">
                {{ $fields->links() }}
            </div>
        @endif
    </div>

    {{-- FORM ẨN BULK ACTION --}}
    <form id="actionForm" action="{{ route('admin.fields.bulk_action') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="formAction">
        <div id="formIds"></div>
    </form>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        
        // Cấu hình API
        const api = {
            list:   '/media-lib',        // Route lấy danh sách
            upload: '/media-lib/upload', // Route upload
            delete: '/media-lib/delete', // Route xóa
            sync:   '/media-lib/sync'    // Route đồng bộ
        };

        let state = {
            page: 1, 
            lastPage: 1, 
            loading: false, 
            search: '', 
            selected: null 
        };

        // --- 1. CHẠY NGAY KHI VÀO TRANG ---
        console.log('Media Manager: Bắt đầu tải dữ liệu...');
        loadData(true); 

        // --- 2. HÀM LOAD DỮ LIỆU CỐT LÕI ---
        function loadData(reset = false) {
            if (state.loading) return;
            state.loading = true;
            
            // Hiển thị loading nhỏ ở dưới
            $('#loadingText').removeClass('d-none');

            if (reset) {
                state.page = 1;
                $('#mediaGrid').empty(); // Xóa cũ
            }

            $.ajax({
                url: api.list,
                method: 'GET',
                data: { 
                    page: state.page, 
                    s: state.search, 
                    per_page: 50 
                },
                success: function(res) {
                    console.log('API Response:', res); // Debug: Xem dữ liệu trả về ở Console

                    state.lastPage = res.last_page || 1;
                    
                    // Nếu trang 1 mà không có dữ liệu -> Gọi Sync tự động
                    if (reset && (!res.data || res.data.length === 0)) {
                        console.warn('Danh sách trống -> Đang thử tự động đồng bộ...');
                        $('#mediaGrid').html('<div class="text-center w-100 py-5 text-muted">Đang quét dữ liệu lần đầu...</div>');
                        autoSync();
                        return;
                    }

                    renderGrid(res.data || []);
                    updateLoadMoreBtn();
                },
                error: function(xhr) {
                    console.error('Lỗi tải ảnh:', xhr);
                    $('#mediaGrid').html('<div class="text-center w-100 py-5 text-danger">Không thể tải dữ liệu. Kiểm tra Console (F12).</div>');
                },
                complete: function() {
                    state.loading = false;
                    $('#loadingText').addClass('d-none');
                }
            });
        }

        // Hàm tự động đồng bộ nếu danh sách rỗng
        function autoSync() {
            $.post(api.sync, function() {
                console.log('Đồng bộ hoàn tất -> Tải lại trang.');
                loadData(true); // Gọi lại loadData sau khi sync xong
            });
        }

        // --- 3. RENDER HTML (VẼ ẢNH) ---
        function renderGrid(items) {
            if (items.length === 0) {
                if(state.page === 1) $('#mediaGrid').html('<div class="text-center w-100 py-5 text-muted" style="grid-column: 1/-1">Không có file nào.</div>');
                return;
            }

            items.forEach(item => {
                // Tạo thẻ HTML cho từng ảnh
                const $el = $(`
                    <div class="me-item" data-path="${item.path}">
                        <div class="me-thumb">
                            <img src="${item.url}" loading="lazy" onerror="this.src='/images/no-image.png'">
                        </div>
                        <div class="me-name" title="${item.name}">${item.name}</div>
                    </div>
                `);

                // Sự kiện click vào ảnh
                $el.on('click', function() {
                    $('.me-item').removeClass('selected');
                    $(this).addClass('selected');
                    showDetail(item);
                });

                $('#mediaGrid').append($el);
            });
        }

        // --- 4. HIỂN THỊ CHI TIẾT (SIDEBAR) ---
        function showDetail(item) {
            state.selected = item;
            $('#sidebarEmpty').addClass('d-none');
            $('#sidebarContent').removeClass('d-none').addClass('d-flex');

            $('#dtImg').attr('src', item.url);
            $('#dtName').text(item.name);
            $('#dtTime').text(item.time || 'N/A');
            $('#dtSize').text(item.size || 'N/A');
            $('#dtUrl').val(item.url);
        }

        // --- 5. CÁC SỰ KIỆN KHÁC ---
        
        // Nút tải thêm
        function updateLoadMoreBtn() {
            if (state.page < state.lastPage) {
                state.page++;
                $('#btnLoadMore').removeClass('d-none');
            } else {
                $('#btnLoadMore').addClass('d-none');
            }
        }
        $('#btnLoadMore').click(() => loadData(false));

        // Cuộn chuột để tải thêm
        $('#mediaGrid').on('scroll', function() {
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100) {
                loadData(false);
            }
        });

        // Tìm kiếm
        let timer;
        $('#searchInput').on('input', function() {
            clearTimeout(timer);
            state.search = $(this).val();
            timer = setTimeout(() => loadData(true), 500);
        });

        // Upload file
        $('#fileInput').change(function() { handleUpload(this.files); });

        function handleUpload(files) {
            if(files.length === 0) return;
            let formData = new FormData();
            for(let i=0; i<files.length; i++) formData.append('files[]', files[i]);

            Swal.fire({
                title: 'Đang tải lên...',
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: api.upload, method: 'POST', data: formData,
                processData: false, contentType: false,
                success: function(res) {
                    if(res.success) {
                        Swal.close();
                        loadData(true); // Reload lại grid
                        const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                        Toast.fire({icon: 'success', title: 'Tải lên thành công'});
                    }
                },
                error: (xhr) => Swal.fire('Lỗi', 'Upload thất bại', 'error')
            });
        }

        // Xóa file
        $('#btnDelete').click(function() {
            if(!state.selected) return;
            Swal.fire({
                title: 'Xóa file?', text: "Không thể khôi phục!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Xóa'
            }).then((r) => {
                if(r.isConfirmed) {
                    $.ajax({
                        url: api.delete, method: 'DELETE', data: { path: state.selected.path },
                        success: () => {
                            $(`.me-item[data-path="${state.selected.path}"]`).remove();
                            $('#sidebarContent').removeClass('d-flex').addClass('d-none');
                            $('#sidebarEmpty').removeClass('d-none');
                            state.selected = null;
                            Swal.fire('Đã xóa', '', 'success');
                        }
                    });
                }
            });
        });

        // Nút Đồng bộ thủ công
        $('#btnSync').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).find('i').addClass('fa-spin');
            $.post(api.sync, function() {
                loadData(true);
                btn.prop('disabled', false).find('i').removeClass('fa-spin');
                Swal.fire('Đồng bộ xong', '', 'success');
            });
        });

        // Copy Link
        window.copyUrl = function() {
            $('#dtUrl').select();
            document.execCommand('copy');
            const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
            Toast.fire({icon: 'success', title: 'Đã copy link'});
        }

        // Drag & Drop
        const $dropZone = $('#dropZone');
        const $wrapper = $('#mediaExplorer');

        $wrapper.on('dragenter', (e) => { e.preventDefault(); $dropZone.addClass('active'); });
        $dropZone.on('dragleave', (e) => { e.preventDefault(); $dropZone.removeClass('active'); });
        $dropZone.on('dragover', (e) => { e.preventDefault(); });
        $dropZone.on('drop', (e) => {
            e.preventDefault(); $dropZone.removeClass('active');
            handleUpload(e.originalEvent.dataTransfer.files);
        });
    });
</script>
@endpush