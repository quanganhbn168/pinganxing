{{-- Header với Context của Work Order --}}
<div class="bg-white shadow-sm border-bottom mb-3">
    {{-- Row 1: Back Button + Task Title --}}
    <div class="d-flex align-items-center px-3 py-2 border-bottom">
        <a href="{{ route('admin.work-orders.show', $task->work_order_id) }}" class="btn btn-light btn-sm mr-3 border-0 text-muted">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <div style="flex: 1; min-width: 0;">
            <h6 class="m-0 font-weight-bold text-dark text-truncate">{{ $task->report_content }}</h6>
            @if($task->isSpawned())
                <small class="text-info"><i class="fas fa-code-branch mr-1"></i>Phát sinh từ task khác</small>
            @endif
        </div>
        {{-- Status Badge --}}
        <span class="badge badge-{{ $task->status->color() }} px-2 py-1">
            {{ $task->status->label() }}
        </span>
    </div>

    {{-- Row 2: Work Order Context (ĐỀ BÀI) --}}
    <div class="px-3 py-2 bg-light">
        <div class="d-flex align-items-start">
            <div class="text-primary mr-2" style="min-width: 24px;">
                <i class="fas fa-clipboard-list fa-lg"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                {{-- Work Order Title --}}
                <a href="{{ route('admin.work-orders.show', $task->work_order_id) }}" class="text-dark font-weight-bold d-block text-truncate">
                    {{ $task->workOrder->title }}
                </a>
                {{-- Work Order Code & Priority --}}
                <div class="d-flex flex-wrap align-items-center mt-1" style="gap: 8px;">
                    <span class="badge badge-secondary">{{ $task->workOrder->code }}</span>
                    <span class="badge badge-{{ $task->workOrder->priority->color() }}">
                        {{ $task->workOrder->priority->label() }}
                    </span>
                    @if($task->workOrder->deadline)
                        <span class="text-muted small">
                            <i class="far fa-clock mr-1"></i>Hạn: {{ $task->workOrder->deadline->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Site Info (Địa chỉ + Liên hệ) --}}
    <div class="px-3 py-2 border-top">
        <div class="row text-sm">
            {{-- Khách hàng --}}
            <div class="col-12 col-md-4 mb-1 mb-md-0">
                <span class="text-muted"><i class="fas fa-user mr-1"></i></span>
                <span class="font-weight-bold">{{ $task->workOrder->customer->name ?? 'Khách lẻ' }}</span>
            </div>
            {{-- Địa chỉ --}}
            <div class="col-12 col-md-4 mb-1 mb-md-0">
                <span class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i></span>
                <span>{{ Str::limit($task->workOrder->site_address, 40) }}</span>
            </div>
            {{-- Liên hệ --}}
            <div class="col-12 col-md-4">
                <span class="text-muted"><i class="fas fa-phone mr-1"></i></span>
                <a href="tel:{{ $task->workOrder->contact_phone }}" class="text-dark">
                    {{ $task->workOrder->contact_person }} - {{ $task->workOrder->contact_phone }}
                </a>
            </div>
        </div>
    </div>

    {{-- Row 4: Tags (nếu có) --}}
    @if($task->workOrder->tags->count() > 0)
    <div class="px-3 py-2 border-top">
        <div class="d-flex flex-wrap" style="gap: 5px;">
            @foreach($task->workOrder->tags as $tag)
                <span class="badge" style="background-color: {{ $tag->color }}; color: {{ $tag->text_color }};">
                    {{ $tag->name }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Row 5: Ghi chú / Mô tả (nếu có) --}}
    @if($task->workOrder->description)
    <div class="px-3 py-2 border-top bg-warning-light" style="background-color: #fff8e1;">
        <div class="d-flex align-items-start">
            <span class="text-warning mr-2"><i class="fas fa-sticky-note"></i></span>
            <div class="text-sm" style="white-space: pre-line;">{{ $task->workOrder->description }}</div>
        </div>
    </div>
    @endif
</div>