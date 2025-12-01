// Cấu hình CSRF Token cho tất cả request AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    }
});

class MediaManager {
    constructor() {
        // 1. Cấu hình Endpoints
        this.routes = {
            list:   '/media-lib',
            upload: '/media-lib/upload',
            delete: '/media-lib/delete',
            sync:   '/media-lib/sync'
        };

        // 2. State
        this.state = {
            isOpen: false,
            mode: 'single',
            isInline: false,
            targetName: null,
            targetPreview: null,
            
            selected: new Map(),
            items: [],
            
            page: 1,
            lastPage: 1,
            loading: false,
            search: '',
            dragCounter: 0
        };

        // 3. Cache DOM
        this.dom = {
            modal:      $('#mediaModal'),
            grid:       $('#mediaGrid'),
            toolbar:    $('.media-toolbar'),
            tabs:       $('input[name="media_mode"]'),
            views: {
                library: $('#viewLibrary'),
                upload:  $('#viewUpload')
            },
            inputs: {
                search: $('#mediaSearch'),
                upload: $('#mediaUploadInput')
            },
            sidebar: {
                placeholder: $('#mediaDetailPlaceholder'),
                content:     $('#mediaDetailContent'),
                img:         $('#mediaDetailPreviewImg'),
                name:        $('#mediaDetailName'),
                time:        $('#mediaDetailTime'),
                size:        $('#mediaDetailSize'),
                url:         $('#mediaDetailUrl'),
                btnDelete:   $('#mediaDetailDeleteBtn')
            },
            footer: {
                count: $('#mediaSelectedCount'),
                btnChoose: $('#mediaChooseBtn')
            },
            overlay: $('#mediaDragDropOverlay')
        };

        this.init();
    }

    init() {
        this.bindEvents();
        this.bindGlobalEvents();
        // Gọi hàm này để hiển thị ảnh ngay khi vào trang Edit
        this.initPagePreviews();
    }

    // ==========================================
    // 1. LOGIC HIỂN THỊ ẢNH KHI LOAD TRANG
    // ==========================================
    initPagePreviews() {
        const self = this;
        $('input[data-picker="media"]').each(function() {
            const $btn = $(this);
            const name = $btn.data('name');
            const previewSelector = $btn.data('preview');
            const isMultiple = $btn.data('multiple') == 1;

            const $hidden = $(`input[name="${name}"]`);
            if (!$hidden.length) return;

            const rawVal = $hidden.val();
            if (!rawVal || rawVal.trim() === '' || rawVal === '[]') return;

            let items = [];
            try {
                let paths = [];
                if (isMultiple) {
                    paths = JSON.parse(rawVal);
                    if (!Array.isArray(paths)) paths = [];
                } else {
                    paths = [rawVal];
                }

                items = paths.map(p => ({
                    path: p,
                    // Giả định URL: Nếu path là "userfiles/a.jpg" -> "/storage/userfiles/a.jpg"
                    // Cần khớp với symlink storage của Laravel
                    url: `/storage/${p.replace(/^\/?storage\//, '')}`, 
                    name: p.split('/').pop()
                }));
            } catch (e) {
                console.error('MediaManager: Lỗi parse dữ liệu cũ', e);
            }

            if (items.length > 0) {
                // [QUAN TRỌNG] Truyền đủ 3 tham số để render đúng chỗ
                self.renderExternalPreview(items, previewSelector, name);
            }
        });
    }

    // ==========================================
    // 2. SỰ KIỆN NỘI BỘ MODAL
    // ==========================================
    bindEvents() {
        const self = this;
        // Chuyển Tab
        this.dom.tabs.on('change', function() {
            const mode = $(this).attr('id');
            if (mode === 'media-tab-library') {
                self.dom.views.library.removeClass('d-none');
                self.dom.views.upload.addClass('d-none').removeClass('d-flex');
            } else {
                self.dom.views.library.addClass('d-none');
                self.dom.views.upload.removeClass('d-none').addClass('d-flex');
            }
        });
        const $dropZone = this.state.isInline ? $('.media-layout-container') : this.dom.modal;

        // Tìm kiếm
        let searchTimeout;
        this.dom.inputs.search.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                self.state.search = $(this).val();
                self.state.page = 1;
                self.loadLibrary(true);
            }, 500);
        });

        // Scroll Infinite (Sửa lỗi load vô tận)
        $('#mediaScrollArea').on('scroll', function() {
            if (self.state.loading || self.state.page > self.state.lastPage) return;
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100) {
                self.loadLibrary(false);
            }
        });
        $('#mediaLoadMore').on('click', () => this.loadLibrary(false));

        // Upload
        this.dom.inputs.upload.on('change', function() {
            self.handleUpload(this.files);
        });
        $('#mediaUploadForm').on('submit', (e) => {
            e.preventDefault();
            self.handleUpload(self.dom.inputs.upload[0].files);
        });

        // Sidebar & Footer
        this.dom.sidebar.btnDelete.on('click', () => this.deleteItem());
        this.dom.footer.btnChoose.on('click', () => this.applySelection());

        // Drag & Drop
        this.bindDragDrop();
    }

    // ==========================================
    // 3. SỰ KIỆN TOÀN CỤC (Nút chọn ảnh trên form)
    // ==========================================
    bindGlobalEvents() {
        const self = this;

        $(document).on('click', '[data-picker="media"]', function(e) {
            e.preventDefault();
            const $btn = $(this);
            self.open({
                name: $btn.data('name'),
                preview: $btn.data('preview'),
                multiple: $btn.data('multiple') == 1
            });
        });

        $(document).on('click', '.mi-preview-remove', function(e) {
            e.preventDefault();
            self.removeExternalPreview($(this));
        });
    }

    // ==========================================
    // 4. CORE LOGIC
    // ==========================================
    
    open(opts) {
        this.state.isOpen = true;
        this.state.isInline = !!opts.inline;
        this.state.multiple = !!opts.multiple;
        this.state.targetName = opts.name;
        this.state.targetPreview = opts.preview;
        this.state.page = 1;
        this.state.search = '';
        this.state.selected.clear();

        // Reset UI
        this.dom.inputs.search.val('');
        this.dom.grid.empty();
        this.dom.inputs.upload.val('');
        $('#media-tab-library').prop('checked', true).trigger('change');
        if (!this.state.isInline && this.dom.modal.length) {
            this.dom.modal.modal('show');
        }
        // Load các item đã chọn trước đó vào modal (để hiện tick xanh)
        this.preloadSelectionForModal();

        this.dom.modal.modal('show');
        this.loadLibrary(true);
        this.updateUI();
    }

    loadLibrary(reset = false) {
        if (this.state.loading) return;
        if (!reset && this.state.page > this.state.lastPage) return;
        
        this.state.loading = true;

        if (reset) {
            this.dom.grid.html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>');
        } else {
            $('#mediaLoadMore').html('<i class="fas fa-spinner fa-spin"></i> Đang tải...').removeClass('d-none');
        }

        $.ajax({
            url: this.routes.list,
            data: {
                page: this.state.page,
                per_page: 60,
                s: this.state.search
            },
            success: (res) => {
                if (reset) this.dom.grid.empty();
                
                this.state.lastPage = res.last_page;
                this.state.items = res.data || [];

                if (this.state.items.length === 0 && this.state.page === 1) {
                    this.dom.grid.html('<div class="col-12 text-center text-muted mt-5"><i class="far fa-folder-open fa-3x mb-3"></i><br>Không tìm thấy ảnh nào.</div>');
                } else {
                    this.renderGrid(this.state.items);
                }

                // [FIX] Logic chặn load vô tận
                if (res.current_page < res.last_page) {
                    this.state.page = res.current_page + 1;
                    $('#mediaLoadMore').removeClass('d-none').text('Tải thêm');
                } else {
                    this.state.page = res.last_page + 1; 
                    $('#mediaLoadMore').addClass('d-none');
                    
                    if(!reset && res.data.length > 0) {
                        this.dom.grid.append('<div class="col-12 text-center text-muted small py-3 w-100" style="grid-column: 1 / -1;">— Đã hiển thị tất cả ảnh —</div>');
                    }
                }
            },
            error: () => Swal.fire('Lỗi', 'Không thể tải danh sách ảnh.', 'error'),
            complete: () => this.state.loading = false
        });
    }

    renderGrid(items) {
        items.forEach(item => {
            const isSelected = this.state.selected.has(item.path);
            
            const $card = $(`
                <div class="media-item-card ${isSelected ? 'selected' : ''}" data-path="${item.path}">
                    <div class="media-thumbnail">
                        <img src="${item.url}" alt="${item.name}" loading="lazy">
                    </div>
                    <div class="media-item-name" title="${item.name}">
                        ${item.name}
                    </div>
                </div>
            `);

            $card.on('click', () => this.toggleSelect(item, $card));
            this.dom.grid.append($card);
        });
    }

    toggleSelect(item, $card) {
        const path = item.path;

        if (this.state.multiple) {
            if (this.state.selected.has(path)) {
                this.state.selected.delete(path);
                $card.removeClass('selected');
                this.updateSidebar(null);
            } else {
                this.state.selected.set(path, item);
                $card.addClass('selected');
                this.updateSidebar(item);
            }
        } else {
            this.state.selected.clear();
            this.dom.grid.find('.media-item-card').removeClass('selected');
            
            this.state.selected.set(path, item);
            $card.addClass('selected');
            this.updateSidebar(item);
        }
        this.updateUI();
    }

    updateSidebar(item) {
        if (!item) {
            if (this.state.selected.size === 0) {
                this.dom.sidebar.placeholder.removeClass('d-none').addClass('d-flex');
                this.dom.sidebar.content.attr('style', 'display: none !important');
            }
            return;
        }
        this.dom.sidebar.placeholder.removeClass('d-flex').addClass('d-none');
        this.dom.sidebar.content.attr('style', 'display: flex !important');

        this.dom.sidebar.img.attr('src', item.url);
        this.dom.sidebar.name.text(item.name);
        this.dom.sidebar.time.text(item.time || 'N/A');
        this.dom.sidebar.size.text(item.size || 'N/A');
        this.dom.sidebar.url.val(item.url);
        this.state.currentItem = item;
    }

    updateUI() {
        const count = this.state.selected.size;
        this.dom.footer.count.text(count);
        this.dom.footer.btnChoose.prop('disabled', count === 0);
    }

    // ==========================================
    // 5. UPLOAD & DELETE
    // ==========================================
    handleUpload(fileList) {
        if (fileList.length === 0) return;
        const formData = new FormData();
        for (let i = 0; i < fileList.length; i++) {
            formData.append('files[]', fileList[i]);
        }

        $('#mediaUploadStatus').html('<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Đang tải lên...</span>');

        $.ajax({
            url: this.routes.upload,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (res) => {
                if (res.success) {
                    $('#mediaUploadStatus').html('<span class="text-success"><i class="fas fa-check"></i> Tải lên thành công!</span>');
                    setTimeout(() => {
                        $('#media-tab-library').prop('checked', true).trigger('change');
                        this.state.page = 1;
                        this.loadLibrary(true);
                        $('#mediaUploadStatus').html('');
                    }, 500);
                }
            },
            error: (xhr) => {
                const msg = xhr.responseJSON?.message || 'Lỗi tải lên';
                $('#mediaUploadStatus').html(`<span class="text-danger">${msg}</span>`);
            }
        });
    }

    deleteItem() {
        const item = this.state.currentItem;
        if (!item) return;

        Swal.fire({
            title: 'Xoá vĩnh viễn?',
            text: `File "${item.name}" sẽ bị xoá khỏi server!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Xoá ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: this.routes.delete,
                    method: 'DELETE',
                    data: { path: item.path },
                    success: (res) => {
                        if (res.success) {
                            this.dom.grid.find(`.media-item-card[data-path="${item.path}"]`).remove();
                            this.state.selected.delete(item.path);
                            this.updateSidebar(null);
                            this.updateUI();
                            Swal.fire('Đã xoá!', '', 'success');
                        }
                    },
                    error: () => Swal.fire('Lỗi', 'Không thể xoá file.', 'error')
                });
            }
        });
    }

    bindDragDrop() {
        // Ở chế độ Inline, vùng thả file là .media-layout-container
        // Ở chế độ Modal, vùng thả file là #mediaModal
        const $target = $('#media-layout-container').length ? $('#media-layout-container') : this.dom.modal;
        const $overlay = this.dom.overlay;

        if(!$target.length) return;

        $target.on('dragenter', (e) => {
            e.preventDefault(); e.stopPropagation();
            this.state.dragCounter++;
            $overlay.addClass('active');
        });
        $target.on('dragleave', (e) => {
            e.preventDefault(); e.stopPropagation();
            this.state.dragCounter--;
            if (this.state.dragCounter === 0) $overlay.removeClass('active');
        });
        $target.on('dragover', (e) => { e.preventDefault(); e.stopPropagation(); });
        $target.on('drop', (e) => {
            e.preventDefault(); e.stopPropagation();
            this.state.dragCounter = 0;
            $overlay.removeClass('active');
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) this.handleUpload(files);
        });
    }

    // ==========================================
    // 6. XỬ LÝ PRELOAD DỮ LIỆU (Vào Modal)
    // ==========================================
    preloadSelectionForModal() {
        const $hidden = $(`input[name="${this.state.targetName}"]`);
        if (!$hidden.length) return;
        const raw = $hidden.val();
        if (!raw) return;

        let paths = [];
        try {
            if (this.state.multiple) {
                paths = JSON.parse(raw);
            } else {
                paths = [raw];
            }
        } catch (e) { paths = []; }

        if (!Array.isArray(paths)) paths = [];

        paths.forEach(p => {
            if (p && p.trim() !== '') {
                const cleanPath = p.replace(/^\/?storage\//, '');
                const mockItem = {
                    path: p,
                    url: `/storage/${cleanPath}`,
                    name: p.split('/').pop()
                };
                this.state.selected.set(p, mockItem);
            }
        });
    }

    // ==========================================
    // 7. RENDER PREVIEW BÊN NGOÀI
    // ==========================================
    
    applySelection() {
        const items = Array.from(this.state.selected.values());
        const paths = items.map(i => i.path);
        
        const $hidden = $(`input[name="${this.state.targetName}"]`);
        if (this.state.multiple) {
            $hidden.val(JSON.stringify(paths)).trigger('change');
        } else {
            $hidden.val(paths[0] || '').trigger('change');
        }

        // Gọi render mà không cần tham số, nó sẽ lấy từ state
        this.renderExternalPreview(items);
        this.dom.modal.modal('hide');
    }

    // [FIXED] Hàm này giờ nhận tham số linh hoạt để dùng cho cả initPagePreviews và applySelection
    renderExternalPreview(items, containerSelector = null, inputName = null) {
        const selector = containerSelector || this.state.targetPreview;
        const name = inputName || this.state.targetName;

        if (!selector) return;
        const $container = $(selector);
        if (!$container.length) return;

        $container.empty();
        
        items.forEach(item => {
            const html = `
                <div class="mi-preview-item d-inline-block mr-2 mb-2 position-relative border rounded" 
                     data-path="${item.path}" data-input-name="${name}">
                    <img src="${item.url}" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
                    <button type="button" class="mi-preview-remove btn btn-danger btn-sm rounded-circle position-absolute" 
                            style="top: -8px; right: -8px; width: 24px; height: 24px; padding: 0; line-height: 22px; z-index: 2;">
                        &times;
                    </button>
                </div>
            `;
            $container.append(html);
        });
    }

    removeExternalPreview($btn) {
        const $item = $btn.closest('.mi-preview-item');
        const pathToRemove = $item.data('path');
        const inputName = $item.data('input-name');
        
        const $hidden = $(`input[name="${inputName}"]`);
        const isMultiple = $(`input[data-picker="media"][data-name="${inputName}"]`).data('multiple') == 1;

        if ($hidden.length) {
            if (isMultiple) {
                let paths = JSON.parse($hidden.val() || '[]');
                paths = paths.filter(p => p !== pathToRemove);
                $hidden.val(JSON.stringify(paths)).trigger('change');
            } else {
                $hidden.val('').trigger('change');
            }
        }
        $item.remove();
    }
}

$(document).ready(function() {
    window.MediaManagerInstance = new MediaManager();
});