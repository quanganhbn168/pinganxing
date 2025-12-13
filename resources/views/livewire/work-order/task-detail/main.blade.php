<div>
    {{-- 1. LOAD CSS --}}
    @include('livewire.work-order.task-detail.css')

    {{-- 2. HEADER --}}

    {{-- 3. NỘI DUNG CHÍNH --}}
    <div class="app-content-wrapper container-fluid px-2">
        @include('livewire.work-order.task-detail.header')
        
        {{-- Tabs --}}
        <ul class="nav nav-pills nav-justified mb-3 mx-1 p-1 bg-white rounded border shadow-sm">
            <li class="nav-item">
                <a class="nav-link py-2 {{ $activeTab == 'new_report' ? 'active shadow-sm font-weight-bold' : 'text-muted' }}" 
                   href="javascript:void(0)" wire:click="switchTab('new_report')">BÁO CÁO</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 {{ $activeTab == 'history' ? 'active shadow-sm font-weight-bold' : 'text-muted' }}" 
                   href="javascript:void(0)" wire:click="switchTab('history')">LỊCH SỬ</a>
            </li>
        </ul>

        {{-- TAB CONTENT --}}
        @if($activeTab == 'history')
            <div wire:key="tab-history">
                @include('livewire.work-order.task-detail.tab-history')
            </div>
        @endif

        @if($activeTab == 'new_report')
            <div wire:key="tab-report">
                @include('livewire.work-order.task-detail.tab-report')
            </div>

            {{-- FOOTER BUTTON --}}
            @if($task->status != \App\Enums\TaskStatus::COMPLETED && $task->workOrder->allowsReporting())
                <div class="mt-3 mb-4">
                    <button class="btn btn-success btn-lg btn-block shadow-sm font-weight-bold" onclick="submitReport()">
                        <i class="fas fa-paper-plane mr-2"></i> GỬI BÁO CÁO
                    </button>
                </div>
            @endif
        @endif

    </div>

    {{-- FAB: Nút tạo việc phát sinh (luôn hiện nếu WorkOrder cho phép) --}}
    @if($task->workOrder->allowsAdditionalTasks())
    <button type="button" class="fab-button" data-toggle="modal" data-target="#additionalTaskModal" title="Tạo việc phát sinh">
        <i class="fas fa-plus"></i>
    </button>
    @endif

    {{-- Modal Tạo Việc Phát Sinh --}}
    <div class="modal fade" id="additionalTaskModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Tạo việc phát sinh</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Tạo công việc mới liên quan đến phiếu này mà không cần hoàn thành task hiện tại.</p>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Nội dung công việc <span class="text-danger">*</span></label>
                        <input type="text" wire:model="newTaskTitle" class="form-control" 
                               placeholder="VD: Kiểm tra lại đường dây...">
                        @error('newTaskTitle') <span class="text-danger text-xs mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="text-sm font-weight-bold"><i class="fas fa-calendar mr-1"></i> Hẹn ngày</label>
                                <input type="datetime-local" wire:model="newTaskScheduledAt" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="text-sm font-weight-bold"><i class="fas fa-user mr-1"></i> Gán cho</label>
                                <select wire:model="newTaskAssigneeId" class="form-control form-control-sm">
                                    <option value="">-- Chưa gán --</option>
                                    @foreach(\App\Models\Admin::all() as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" wire:click="createAdditionalTask" data-dismiss="modal">
                        <i class="fas fa-plus-circle mr-1"></i> Tạo việc
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. CÁC MODAL HỖ TRỢ (Scanner, Image Viewer) --}}
    
    {{-- Scanner Overlay --}}
    <div id="scanner-overlay">
        <div class="text-white mb-2 font-weight-bold text-lg">QUÉT MÃ VẠCH</div>
        <div id="scan-counter" class="badge badge-success mb-3 px-3 py-2" style="display: none; font-size: 1rem;">
            Đã quét: 0 mã
        </div>
        <div id="scanner-box">
            <div id="scanner-line"></div>
            <div id="reader"></div>
        </div>
        <button class="btn btn-outline-light mt-4 rounded-pill px-4" onclick="closeScanner()">
            <i class="fas fa-times mr-1"></i> Đóng Camera
        </button>
        <small class="text-white-50 mt-2 d-block">Hướng camera vào mã vạch</small>
    </div>

    {{-- Image Viewer Fullscreen --}}
    <div id="imageViewer" onclick="closeImageViewer()">
        <span class="close-viewer">&times;</span>
        <img id="imageViewerSrc" src="">
    </div>

    {{-- Modal Chọn Vật Tư Trước Khi Quét --}}
    <div class="modal fade" id="materialSelectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-box-open mr-2"></i>Chọn loại vật tư</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Chọn hoặc gõ tên vật tư. Tất cả mã quét sẽ tự động điền tên này.</p>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Tên vật tư <span class="text-danger">*</span></label>
                        <input type="text" id="bulk-material-name" class="form-control form-control-lg" 
                               placeholder="VD: Camera IMOU, Dây mạng CAT6..." 
                               list="material-suggestions" autocomplete="off">
                        <datalist id="material-suggestions">
                            @foreach(\App\Models\Material::orderBy('name')->take(50)->get() as $mat)
                                <option value="{{ $mat->name }}">{{ $mat->code }} - {{ $mat->name }}</option>
                            @endforeach
                        </datalist>
                        <small class="text-muted">Gõ để tìm hoặc nhập tên mới</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="startBulkScan()">
                        <i class="fas fa-barcode mr-1"></i> Bắt đầu quét
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. LOAD JS --}}
    @include('livewire.work-order.task-detail.scripts')
</div>