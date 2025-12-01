<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">

            {{-- 1. HEADER --}}
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="far fa-images mr-2 text-primary"></i>Thư viện Media
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- 2. BODY (Split Layout) --}}
            <div class="modal-body p-0 position-relative">
                
                {{-- Dropzone Overlay --}}
                <div id="mediaDragDropOverlay" class="media-drag-overlay">
                    <div class="text-primary mb-3" style="font-size: 4rem;"><i class="fas fa-cloud-upload-alt"></i></div>
                    <h4 class="font-weight-bold text-dark">Thả file vào đây để tải lên</h4>
                </div>

                <div class="media-layout-container">
                    
                    {{-- === LEFT: MAIN CONTENT === --}}
                    <div class="media-main-content">
                        
                        {{-- Toolbar --}}
                        <div class="media-toolbar">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-secondary active btn-sm" id="media-tab-library-lbl">
                                    <input type="radio" name="media_mode" id="media-tab-library" checked> 
                                    <i class="fas fa-th mr-1"></i> Thư viện
                                </label>
                                <label class="btn btn-outline-secondary btn-sm" id="media-tab-upload-lbl">
                                    <input type="radio" name="media_mode" id="media-tab-upload"> 
                                    <i class="fas fa-upload mr-1"></i> Tải lên
                                </label>
                            </div>
                            
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" id="mediaSearch" class="form-control" placeholder="Tìm kiếm file...">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>

                        {{-- Scroll Area --}}
                        <div class="media-scroll-area" id="mediaScrollArea">
                            
                            {{-- View: Library --}}
                            <div id="viewLibrary" class="h-100">
                                <div id="mediaGrid" class="media-grid">
                                    {{-- JS sẽ render .media-item-card vào đây --}}
                                </div>
                                <div class="text-center mt-4 mb-2">
                                    <button id="mediaLoadMore" class="btn btn-light btn-sm border shadow-sm d-none">
                                        Tải thêm ảnh <i class="fas fa-chevron-down ml-1"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- View: Upload --}}
                            <div id="viewUpload" class="h-100 d-none flex-column align-items-center justify-content-center">
                                <div class="p-5 border-2 border-dashed rounded text-center bg-light w-75" style="border: 2px dashed #ccc;">
                                    <i class="fas fa-cloud-upload-alt text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5>Kéo thả file hoặc click để chọn</h5>
                                    <input type="file" id="mediaUploadInput" class="d-none" multiple accept="image/*">
                                    <button type="button" class="btn btn-primary mt-3" onclick="$('#mediaUploadInput').click()">
                                        Chọn file từ máy tính
                                    </button>
                                </div>
                                <div id="mediaUploadStatus" class="mt-3 text-muted"></div>
                            </div>

                        </div>
                    </div>

                    {{-- === RIGHT: SIDEBAR === --}}
                    <div class="media-sidebar" id="mediaDetailSidebar">
                        {{-- Placeholder khi chưa chọn --}}
                        <div id="mediaDetailPlaceholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted p-4 text-center">
                            <i class="far fa-image mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="small">Chọn một file để xem chi tiết</p>
                        </div>

                        {{-- Nội dung chi tiết --}}
                        <div id="mediaDetailContent" class="d-flex flex-column h-100" style="display: none !important;">
                            <div class="sidebar-scroll">
                                <div class="detail-preview-box">
                                    <img id="mediaDetailPreviewImg" src="" alt="Preview">
                                </div>

                                <div class="detail-info-group">
                                    <div class="detail-label">Tên file</div>
                                    <div id="mediaDetailName" class="detail-value font-weight-bold">...</div>
                                </div>

                                <div class="detail-info-group">
                                    <div class="detail-label">Ngày tải lên</div>
                                    <div id="mediaDetailTime" class="detail-value">...</div>
                                </div>
                                
                                <div class="detail-info-group">
                                    <div class="detail-label">Kích thước</div>
                                    <div id="mediaDetailSize" class="detail-value">...</div>
                                </div>

                                <div class="detail-info-group">
                                    <div class="detail-label">Đường dẫn URL</div>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="mediaDetailUrl" class="form-control bg-white" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" title="Copy" onclick="
                                                var copyText = document.getElementById('mediaDetailUrl');
                                                copyText.select();
                                                document.execCommand('copy');
                                                $(this).find('i').removeClass('fa-copy').addClass('fa-check');
                                                setTimeout(() => $(this).find('i').removeClass('fa-check').addClass('fa-copy'), 1000);
                                            ">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sidebar-footer">
                                <button type="button" id="mediaDetailDeleteBtn" class="btn btn-outline-danger btn-sm btn-block">
                                    <i class="far fa-trash-alt mr-1"></i> Xoá vĩnh viễn
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 3. FOOTER --}}
            <div class="modal-footer bg-light py-2">
                <div class="mr-auto small text-muted">
                    Đã chọn: <strong id="mediaSelectedCount" class="text-primary">0</strong> file
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
                <button type="button" id="mediaChooseBtn" class="btn btn-primary px-4 font-weight-bold" disabled>
                    <i class="fas fa-check mr-1"></i> Sử dụng file này
                </button>
            </div>
        </div>
    </div>
</div>