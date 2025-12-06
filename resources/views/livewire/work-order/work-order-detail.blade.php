<div>
    <style>
        body { background-color: #f4f6f9; }
        .task-card {
            background: #fff; 
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            margin-bottom: 12px; 
            border: 1px solid #e9ecef;
            overflow: hidden; 
        }
        .status-border-completed { border-left: 5px solid #28a745; }
        .status-border-processing { border-left: 5px solid #17a2b8; }
        .status-border-pending { border-left: 5px solid #ffc107; }
    </style>

    {{-- HEADER WITH STATUS DROPDOWN --}}
    <div class="content-header bg-white shadow-sm pb-2 pt-3 sticky-top">
        <div class="container-fluid px-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="{{ route('admin.work-orders.index') }}" class="btn btn-sm btn-light text-muted font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Danh sách
                </a>
                
                {{-- Status Display (Simple) --}}
                <div class="d-flex align-items-center">
                    @php
                        $statusEnum = $workOrder->status instanceof \App\Enums\WorkOrderStatus 
                            ? $workOrder->status 
                            : \App\Enums\WorkOrderStatus::tryFrom($workOrder->status);
                    @endphp
                    @if($statusEnum)
                        <span class="badge badge-{{ $statusEnum->color() }} font-weight-bold shadow-sm px-3 py-2" style="font-size: 13px;">
                            {{ $statusEnum->label() }}
                        </span>
                    @endif

                     <a href="{{ route('admin.work-orders.edit', $workOrder->id) }}" class="btn btn-sm btn-outline-primary shadow-sm bg-white ml-2 rounded-circle border-0" title="Chỉnh sửa">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO CARD --}}
    <section class="content pt-0 pb-2">
        <div class="container-fluid px-2">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge badge-light border">#{{ $workOrder->code }}</span>
                         @php
                            $priorityEnum = $workOrder->priority instanceof \App\Enums\WorkOrderPriority 
                                ? $workOrder->priority 
                                : \App\Enums\WorkOrderPriority::tryFrom($workOrder->priority);
                        @endphp
                        @if($priorityEnum)
                            <span class="badge badge-{{ $priorityEnum->color() }}">{{ $priorityEnum->label() }}</span>
                        @endif
                    </div>
                    
                    <h5 class="font-weight-bold text-primary mb-2">{{ $workOrder->title }}</h5>

                    @if($workOrder->description)
                        <div class="bg-light p-2 rounded text-sm text-muted mb-3">
                            <i class="fas fa-sticky-note mr-1"></i> {{ $workOrder->description }}
                        </div>
                    @endif

                    <div class="border-top pt-2 mt-2">
                        <div class="font-weight-bold text-dark mb-1">{{ $workOrder->customer->name }}</div>
                        <div class="small text-muted mb-1">
                            <a href="https://maps.google.com/?q={{ $workOrder->site_address }}" target="_blank" class="text-dark">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $workOrder->site_address ?? 'Chưa có địa chỉ' }}
                            </a>
                        </div>
                        <div class="small text-muted">
                            <a href="tel:{{ $workOrder->contact_phone }}" class="text-dark">
                                <i class="fas fa-phone text-success mr-1"></i> {{ $workOrder->contact_phone ?? '---' }} 
                            </a>
                            ({{ $workOrder->contact_person ?? '---' }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-5">
        <div class="container-fluid px-2">
            
            <div class="card card-primary card-outline card-outline-tabs border-0 shadow-sm">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab == 'progress' ? 'active' : '' }}" 
                               wire:click="$set('activeTab', 'progress')"
                               href="javascript:void(0)" role="tab">
                                <i class="fas fa-tasks mr-1"></i> Công việc
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab == 'history' ? 'active' : '' }}" 
                               wire:click="$set('activeTab', 'history')"
                               href="javascript:void(0)" role="tab">
                                <i class="fas fa-history mr-1"></i> Lịch sử
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab == 'materials' ? 'active' : '' }}" 
                               wire:click="$set('activeTab', 'materials')"
                               href="javascript:void(0)" role="tab">
                                <i class="fas fa-box mr-1"></i> Vật tư
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab == 'finance' ? 'active' : '' }}" 
                               wire:click="$set('activeTab', 'finance')"
                               href="javascript:void(0)" role="tab">
                                <i class="fas fa-money-bill-wave mr-1"></i> Thu tiền
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-3">
                    <div class="tab-content">
                        
                        {{-- TAB 1: CÔNG VIỆC --}}
                        <div class="tab-pane fade {{ $activeTab == 'progress' ? 'show active' : '' }}" id="tab-progress" role="tabpanel">
                            
                             @php 
                                $total = $tasks->count();
                                $done = $tasks->where('status', \App\Enums\TaskStatus::COMPLETED)->count();
                                $percent = $total > 0 ? round(($done/$total)*100) : 0;
                            @endphp
                            <div class="mb-3 text-muted small font-weight-bold">
                                TIẾN ĐỘ: {{ $done }}/{{ $total }} ({{ $percent }}%)
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            
                            @foreach($tasks as $task)
                                <div class="task-card {{ $task->status === \App\Enums\TaskStatus::COMPLETED ? 'status-border-completed' : ($task->status === \App\Enums\TaskStatus::PROCESSING ? 'status-border-processing' : 'status-border-pending') }}">
                                    
                                    {{-- Link to Admin Task Detail --}}
                                    <a href="{{ route('admin.tasks.detail', $task->id) }}" class="d-block p-3 text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div style="flex: 1; padding-right: 10px;">
                                                <h6 class="mb-1 font-weight-bold" style="font-size: 15px; line-height: 1.4;">
                                                    {{ $task->report_content }}
                                                </h6>
                                                <div class="text-xs text-muted mt-1">
                                                     <i class="fas fa-user text-xs mr-1"></i> {{ $task->performer->name ?? 'Chưa gán' }}
                                                </div>
                                            </div>

                                            <div class="text-right" style="min-width: 80px;">
                                                @if($task->status === \App\Enums\TaskStatus::COMPLETED)
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Xong</span>
                                                @elseif($task->status === \App\Enums\TaskStatus::PROCESSING)
                                                    <span class="badge badge-info px-2 py-1">Đang làm</span>
                                                @else
                                                    <span class="badge badge-warning px-2 py-1">Chờ làm</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>

                                    <div class="px-3 py-2 border-top bg-light d-flex justify-content-end align-items-center">
                                        
                                        {{-- NÚT: ĐÃ XONG (Chỉ hiện khi chưa xong VÀ đã có báo cáo - giống Worker) --}}
                                        @if($task->status !== \App\Enums\TaskStatus::COMPLETED && $task->reports->count() > 0)
                                            <button wire:click="quickFinishTask({{ $task->id }})" 
                                                    wire:confirm="Admin: Xác nhận nhiệm vụ này ĐÃ HOÀN THÀNH?"
                                                    class="btn btn-sm btn-outline-success font-weight-bold mr-2">
                                                <i class="fas fa-check"></i> Xong việc
                                            </button>
                                        @endif

                                        {{-- NÚT: MỞ LẠI (Chỉ hiện khi đã xong) --}}
                                        @if($task->status === \App\Enums\TaskStatus::COMPLETED)
                                            <button wire:click="reopenTask({{ $task->id }})" 
                                                    wire:confirm="Admin: Mở lại nhiệm vụ này?"
                                                    class="btn btn-sm btn-outline-danger font-weight-bold mr-2">
                                                <i class="fas fa-undo"></i> Mở lại
                                            </button>
                                        @endif

                                        <a href="{{ route('admin.tasks.detail', $task->id) }}" class="btn btn-sm btn-primary font-weight-bold">
                                            Chi tiết <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- TAB 2: VẬT TƯ --}}
                        <div class="tab-pane fade {{ $activeTab == 'materials' ? 'show active' : '' }}" id="tab-materials" role="tabpanel">
                             <div class="d-flex justify-content-end mb-2">
                                <button wire:click="openMaterialModal" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Thêm vật tư</button>
                            </div>

                            <table class="table table-sm table-striped mb-0 text-sm">
                                <thead>
                                    <tr>
                                        <th>Vật tư</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-right">Hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allItems as $item)
                                        <tr>
                                            <td>
                                                {{ $item['name'] }}
                                                <div class="text-muted text-xs">{{ $item['serial'] ?? '' }}</div>
                                            </td>
                                            <td class="text-center">{{ $item['quantity'] }}</td>
                                            <td class="text-right">
                                                <button wire:confirm="Xóa vật tư này?" wire:click="deleteMaterial({{ $item['id'] }})" class="btn btn-xs text-danger"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Chưa có vật tư.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- TAB 3: LỊCH SỬ --}}
                        <div class="tab-pane fade {{ $activeTab == 'history' ? 'show active' : '' }}" id="tab-history" role="tabpanel">
                             @if(count($allReports) == 0)
                                <div class="text-center py-5 mt-3 opacity-50">
                                    <i class="fas fa-history fa-3x text-gray mb-3"></i>
                                    <p class="text-muted small">Chưa có lịch sử báo cáo nào.</p>
                                </div>
                            @else
                                <div class="px-1">
                                    @foreach($allReports as $rpt)
                                        <div class="card mb-2 shadow-sm border-0">
                                            <a href="{{ route('admin.tasks.detail', $rpt->task_id) }}" class="text-decoration-none">
                                                <div class="card-body p-2">
                                                     <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                                        <div>
                                                            <div class="font-weight-bold text-dark text-sm">
                                                                <i class="fas fa-user-circle text-primary mr-1"></i> {{ $rpt->reporter->name ?? 'NV' }}
                                                            </div>
                                                            <div class="text-muted text-xs">{{ $rpt->created_at->format('H:i - d/m/Y') }}</div>
                                                        </div>
                                                        <div>
                                                             @if($rpt->is_completed) 
                                                                <span class="badge badge-success text-xs"><i class="fas fa-check"></i> Xong</span> 
                                                             @elseif(str_contains($rpt->content, 'Mở lại:'))
                                                                <span class="badge badge-danger text-xs"><i class="fas fa-undo"></i> Mở lại</span>
                                                             @endif
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 text-dark text-sm" style="line-height: 1.4;">{{ $rpt->content }}</div>
                                                    
                                                    {{-- Ảnh --}}
                                                    @if($rpt->images->count() > 0)
                                                        <div class="d-flex flex-wrap mt-2">
                                                            @foreach($rpt->images as $img)
                                                                <div class="mr-2 mb-2">
                                                                    <img src="{{ Storage::url($img->image_path) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- TAB 4: TÀI CHÍNH (THU TIỀN) --}}
                        <div class="tab-pane fade {{ $activeTab == 'finance' ? 'show active' : '' }}" id="tab-finance" role="tabpanel">
                            {{-- SUMMARY CARD --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="bg-white border rounded p-3 shadow-sm d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted font-weight-bold text-uppercase d-block">Tổng thu (Đã duyệt)</small>
                                            <h4 class="text-success font-weight-bold mb-0 mt-1">{{ number_format($totalCollected) }} đ</h4>
                                        </div>
                                        <i class="fas fa-wallet text-success fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- TABLE: LỊCH SỬ THANH TOÁN --}}
                             <div class="card shadow-none border mb-0">
                                <div class="card-header bg-light py-2">
                                    <h3 class="card-title text-sm font-weight-bold text-muted text-uppercase">
                                        Lịch sử thu tiền
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0 text-sm">
                                        <thead>
                                            <tr>
                                                <th>Ngày</th>
                                                <th>Hình thức</th>
                                                <th class="text-right">Số tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allPayments as $payment)
                                                <tr>
                                                    <td>
                                                        <div class="font-weight-bold">{{ $payment['date']->format('d/m') }}</div>
                                                        <div class="text-xs text-muted">{{ $payment['date']->format('H:i') }}</div>
                                                    </td>
                                                    <td>
                                                        @if($payment['method'] == 'transfer')
                                                            <span class="badge badge-info text-xs">CK {{ $payment['target'] == 'company' ? '(Cty)' : '(CN)' }}</span>
                                                        @else
                                                            <span class="badge badge-secondary text-xs">Tiền mặt</span>
                                                        @endif
                                                        <div class="mt-1">
                                                            @if($payment['status'] == 'verified') <i class="fas fa-check-circle text-success text-xs" title="Đã duyệt"></i>
                                                            @elseif($payment['status'] == 'handed_over') <i class="fas fa-info-circle text-info text-xs" title="Đã nộp"></i>
                                                            @else <i class="fas fa-clock text-warning text-xs" title="Chờ duyệt"></i>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-right font-weight-bold text-success">+{{ number_format($payment['amount']) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Chưa có khoản thu nào.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- MODAL THÊM VẬT TƯ --}}
            @if($showMaterialModal)
                <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Thêm vật tư / Linh kiện</h5>
                                <button type="button" class="close" wire:click="$set('showMaterialModal', false)">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Gán vào Task <span class="text-danger">*</span></label>
                                    <select wire:model="newMaterial.task_id" class="form-control">
                                        @foreach($tasks as $t)
                                            <option value="{{ $t->id }}">#{{ $t->id }} - {{ Str::limit($t->report_content, 50) }} ({{ $t->performer->name ?? 'N/A' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tên vật tư <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="newMaterial.name" class="form-control" placeholder="VD: Camera Hikvision...">
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Serial (nếu có)</label>
                                            <input type="text" wire:model="newMaterial.serial" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Số lượng</label>
                                            <input type="number" wire:model="newMaterial.quantity" class="form-control" min="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="newMaterial.price" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="$set('showMaterialModal', false)">Hủy</button>
                                <button type="button" class="btn btn-primary" wire:click="saveMaterial">Lưu lại</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>
</div>