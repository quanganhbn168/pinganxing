@extends('layouts.admin')

@section('title', 'Quản lý Media')
@section('content_header', 'Quản lý Media')

@push('css')
<style>
    /* --- LAYOUT CỐ ĐỊNH --- */
    .media-wrapper {
        display: flex; flex-direction: column; 
        height: calc(100vh - 220px); min-height: 600px;
        background: #fff; border: 1px solid #d1d3e2; border-radius: 0.35rem; 
        overflow: hidden; position: relative;
    }

    /* Toolbar */
    .media-toolbar {
        flex: 0 0 50px; /* Cao cố định */
        padding: 0 15px; background: #f8f9fc; border-bottom: 1px solid #e3e6f0;
        display: flex; justify-content: space-between; align-items: center;
    }

    /* Container chính: Chia 2 cột cố định */
    .media-container { display: flex; flex: 1; overflow: hidden; }
    
    /* 1. CỘT TRÁI: GRID (Chiếm phần còn lại) */
    .media-grid-wrapper { flex: 1; overflow-y: auto; padding: 15px; position: relative; background: #fff; }
    .media-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px;
        align-content: start;
    }
    
    /* 2. CỘT PHẢI: SIDEBAR (Luôn hiển thị, Width cố định) */
    .media-sidebar {
        flex: 0 0 320px; width: 320px; /* Cố định cứng */
        background: #fdfdfd; border-left: 1px solid #e3e6f0;
        display: flex; flex-direction: column; z-index: 10;
    }

    /* ITEM ẢNH */
    .m-item {
        border: 1px solid #eaecf4; border-radius: 4px; overflow: hidden;
        cursor: pointer; position: relative; transition: all 0.1s;
        background: #fff; user-select: none;
    }
    .m-item:hover { border-color: #4e73df; transform: translateY(-2px); }
    .m-item.active { border: 2px solid #2e59d9; background: #f0f4ff; }
    
    .m-thumb { width: 100%; padding-top: 100%; position: relative; background: #eee; }
    .m-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .m-name { padding: 6px; font-size: 11px; text-align: center; color: #555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* SIDEBAR ELEMENTS */
    .ms-placeholder {
        flex: 1; display: flex; flex-direction: column; 
        align-items: center; justify-content: center; 
        color: #858796; text-align: center; padding: 20px;
    }
    .ms-content { display: none; flex-direction: column; height: 100%; } /* Mặc định ẩn nội dung */
    .ms-content.show { display: flex; }

    .ms-header { padding: 15px; border-bottom: 1px solid #f1f3f9; font-weight: bold; color: #4e73df; background: #fff; }
    .ms-body { flex: 1; overflow-y: auto; padding: 15px; }
    .ms-preview { 
        width: 100%; height: 200px; background: #eaecf4; border-radius: 4px; 
        display: flex; align-items: center; justify-content: center; 
        margin-bottom: 15px; border: 1px solid #d1d3e2; overflow: hidden;
    }
    .ms-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
    
    .ms-row { margin-bottom: 12px; }
    .ms-label { display: block; font-size: 10px; text-transform: uppercase; color: #888; font-weight: 700; margin-bottom: 3px;}
    .ms-val { font-size: 13px; color: #333; word-break: break-all; font-family: monospace; }

    /* Overlay Upload */
    .media-upload-overlay {
        position: absolute; inset: 0; background: rgba(255,255,255,0.95); z-index: 50;
        display: none; flex-direction: column; align-items: center; justify-content: center;
    }
    .media-upload-overlay.show { display: flex; }
</style>
@endpush

@section('content')
<div class="container-fluid pb-3">
    
    {{-- DASHBOARD THỐNG KÊ --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-left-primary shadow-sm py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng file</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalFiles) }}</div></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow-sm py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Dung lượng</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSize }}</div></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow-sm py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Hình ảnh</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['images']) }}</div></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow-sm py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Khác</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['others']) }}</div></div>
            </div>
        </div>
    </div>

    {{-- GIAO DIỆN MEDIA --}}
    <div class="media-wrapper" id="mediaApp">
        
        {{-- Overlay Upload --}}
        <div class="media-upload-overlay" id="dropZone">
            <i class="fas fa-cloud-upload-alt fa-5x text-primary mb-3"></i>
            <h3>Thả file để tải lên ngay</h3>
        </div>

        {{-- Toolbar --}}
        <div class="media-toolbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-primary btn-sm mr-2" onclick="$('#uploadInput').click()">
                    <i class="fas fa-upload"></i> Tải lên
                </button>
                <button class="btn btn-info btn-sm mr-2" onclick="MediaApp.sync()">
                    <i class="fas fa-sync-alt"></i> Đồng bộ
                </button>
                <input type="file" id="uploadInput" multiple hidden accept="image/*">
                
                {{-- Nút Xóa Nhiều (Hiện khi có chọn) --}}
                <button class="btn btn-danger btn-sm d-none" id="btnBulkDelete" onclick="MediaApp.deleteSelected()">
                    <i class="fas fa-trash"></i> Xóa (<span id="countSelected">0</span>)
                </button>
            </div>

            <div class="d-flex">
                <form method="GET" class="d-flex">
                    <input type="text" name="keyword" class="form-control form-control-sm mr-2" 
                           placeholder="Tìm tên file..." value="{{ request('keyword') }}" style="width: 200px;">
                    <button class="btn btn-light btn-sm border"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        {{-- Main Area --}}
        <div class="media-container">
            {{-- Grid (Cột Trái) --}}
            <div class="media-grid-wrapper">
                <div class="media-grid">
                    @forelse($files as $file)
                        <div class="m-item" 
                             onclick="MediaApp.toggleItem(this, event)"
                             data-id="{{ $file->id }}"
                             data-path="{{ $file->path }}"
                             data-url="{{ $file->full_url }}"
                             data-name="{{ $file->filename }}"
                             data-size="{{ $file->formatted_size }}"
                             data-time="{{ $file->created_at->format('d/m/Y H:i') }}">
                            
                            <div class="m-thumb">
                                <img src="{{ $file->full_url }}" loading="lazy" onerror="this.src='/images/no-image.png'">
                            </div>
                            <div class="m-name" title="{{ $file->filename }}">{{ $file->filename }}</div>
                        </div>
                    @empty
                        <div class="col-12 text-center p-5 text-muted" style="grid-column: 1 / -1;">
                            <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i><br>Không có file nào.
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-3 d-flex justify-content-center">
                    {{ $files->appends(request()->query())->links() }}
                </div>
            </div>

            {{-- Sidebar (Cột Phải - LUÔN HIỂN THỊ) --}}
            <div class="media-sidebar">
                
                {{-- 1. Placeholder: Hiện khi chưa chọn gì --}}
                <div id="sbPlaceholder" class="ms-placeholder">
                    <i class="fas fa-mouse-pointer fa-3x mb-3 opacity-25"></i>
                    <div>Chọn một file để xem chi tiết</div>
                </div>

                {{-- 2. Content: Hiện khi đã chọn --}}
                <div id="sbContent" class="ms-content">
                    <div class="ms-header">Chi tiết file</div>
                    <div class="ms-body">
                        <div class="ms-preview">
                            <img id="sbImg" src="">
                        </div>
                        <div class="ms-row">
                            <span class="ms-label">Tên file</span>
                            <div class="ms-val" id="sbName"></div>
                        </div>
                        <div class="ms-row">
                            <span class="ms-label">Ngày tải lên</span>
                            <div class="ms-val" id="sbTime"></div>
                        </div>
                        <div class="ms-row">
                            <span class="ms-label">Kích thước</span>
                            <div class="ms-val" id="sbSize"></div>
                        </div>
                        <div class="ms-row">
                            <span class="ms-label">Đường dẫn</span>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control bg-white" id="sbUrl" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" onclick="MediaApp.copyUrl()"><i class="far fa-copy"></i></button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button class="btn btn-danger btn-sm btn-block" onclick="MediaApp.deleteSingle()">
                            <i class="fas fa-trash-alt"></i> Xóa file này
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const MediaApp = {
        selected: new Set(),
        lastSelected: null,

        init() {
            this.bindEvents();
            this.bindDragDrop();
        },

        // 1. Chọn ảnh
        toggleItem(el, event) {
            const $el = $(el);
            const id = $el.data('id');

            // Ctrl/Meta để chọn nhiều
            if (event.ctrlKey || event.metaKey) {
                if (this.selected.has(id)) {
                    this.selected.delete(id);
                    $el.removeClass('active');
                } else {
                    this.selected.add(id);
                    $el.addClass('active');
                }
            } else {
                // Click thường: Chọn 1
                $('.m-item').removeClass('active');
                this.selected.clear();
                this.selected.add(id);
                $el.addClass('active');
            }

            this.updateUI($el);
        },

        // 2. Cập nhật giao diện Sidebar & Toolbar
        updateUI($lastEl) {
            const count = this.selected.size;
            $('#countSelected').text(count);

            // Nút Bulk Delete
            if (count > 0) $('#btnBulkDelete').removeClass('d-none');
            else $('#btnBulkDelete').addClass('d-none');

            // Toggle Sidebar Content
            if (count === 1) {
                this.lastSelected = $lastEl;
                this.fillSidebar($lastEl);
                
                $('#sbPlaceholder').hide();
                $('#sbContent').addClass('show');
            } else {
                // Nếu chọn 0 hoặc >1 file -> Về placeholder để tránh rối
                this.lastSelected = null;
                $('#sbContent').removeClass('show');
                $('#sbPlaceholder').show();
            }
        },

        fillSidebar($el) {
            $('#sbImg').attr('src', $el.data('url'));
            $('#sbName').text($el.data('name'));
            $('#sbTime').text($el.data('time'));
            $('#sbSize').text($el.data('size'));
            $('#sbUrl').val($el.data('url'));
        },

        // 3. Upload
        handleUpload(files) {
            if (files.length === 0) return;
            let formData = new FormData();
            for(let i=0; i<files.length; i++) formData.append('files[]', files[i]);

            Swal.fire({ title: 'Đang tải lên...', didOpen: () => Swal.showLoading() });

            $.ajax({
                url: '/media-lib/upload', method: 'POST', data: formData,
                processData: false, contentType: false,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: (res) => {
                    if(res.success) {
                        Swal.fire('Thành công', 'Tải lên xong!', 'success').then(() => location.reload());
                    }
                },
                error: () => Swal.fire('Lỗi', 'Upload thất bại', 'error')
            });
        },

        // 4. Xóa 1 (từ Sidebar)
        deleteSingle() {
            if(!this.lastSelected) return;
            this.confirmDelete([this.lastSelected.data('path')]);
        },

        // 5. Xóa Nhiều
        deleteSelected() {
            if(this.selected.size === 0) return;
            let ids = [];
            // Ở đây dùng Model ID để xóa qua Global Bulk Action cho xịn
            $('.m-item.active').each(function() {
                ids.push($(this).data('id'));
            });
            
            Swal.fire({
                title: `Xóa ${this.selected.size} file?`, icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#d33', confirmButtonText: 'Xóa hết'
            }).then((r) => {
                if(r.isConfirmed) {
                    this.submitGlobalBulk(ids);
                }
            });
        },

        // Dùng chung Controller Global Bulk Action
        submitGlobalBulk(ids) {
            $.ajax({
                url: '{{ route("admin.global.bulk_action") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids,
                    action: 'delete',
                    model: 'media_file' // Đảm bảo đã khai báo trong GlobalBulkActionController
                },
                success: (res) => {
                    Swal.fire('Thành công', 'Đã xóa xong', 'success').then(() => location.reload());
                },
                error: () => Swal.fire('Lỗi', 'Không thể xóa', 'error')
            });
        },

        // Utils
        sync() {
            const btn = event.currentTarget;
            $(btn).prop('disabled', true).find('i').addClass('fa-spin');
            $.post('/media-lib/sync', {_token: '{{ csrf_token() }}'}, function() {
                Swal.fire('Đồng bộ xong', '', 'success').then(() => location.reload());
            });
        },

        copyUrl() {
            $('#sbUrl').select();
            document.execCommand('copy');
            const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
            Toast.fire({icon: 'success', title: 'Đã copy link'});
        },

        bindEvents() {
            $('#uploadInput').change(function() { MediaApp.handleUpload(this.files); });
        },

        bindDragDrop() {
            const $drop = $('#dropZone');
            const $app = $('#mediaApp');
            
            $app.on('dragenter', (e) => { e.preventDefault(); $drop.addClass('show'); });
            $drop.on('dragleave', (e) => { e.preventDefault(); $drop.removeClass('show'); });
            $drop.on('dragover', (e) => { e.preventDefault(); });
            $drop.on('drop', (e) => {
                e.preventDefault(); $drop.removeClass('show');
                this.handleUpload(e.originalEvent.dataTransfer.files);
            });
        }
    };

    $(document).ready(() => MediaApp.init());
</script>
@endpush