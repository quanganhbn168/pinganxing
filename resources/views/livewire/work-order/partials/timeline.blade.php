{{-- Thay thế dòng @include('livewire.work-order.partials.timeline') bằng đoạn này --}}

<div class="timeline mt-4">
    <div class="time-label">
        <span class="bg-red">Lịch sử làm việc</span>
    </div>

    @foreach($tasks as $task)
    <div>
        <i class="fas fa-tools bg-blue"></i>
        <div class="timeline-item">
            <span class="time"><i class="fas fa-clock"></i> {{ $task->created_at->format('H:i d/m/Y') }}</span>
            <h3 class="timeline-header">
                <span class="text-primary font-weight-bold">{{ $task->performer->name ?? 'Nhân viên' }}</span> 
                đã báo cáo
            </h3>

            <div class="timeline-body">
                <p class="mb-2">{{ $task->report_content }}</p>
                
                {{-- Hiển thị vật tư --}}
                @if($task->items->count() > 0)
                    <div class="callout callout-info py-2 mb-2 bg-light">
                        <strong class="text-info text-sm"><i class="fas fa-box"></i> Vật tư sử dụng:</strong>
                        <ul class="mb-0 pl-3 text-sm mt-1">
                            @foreach($task->items as $tItem)
                                <li>
                                    <strong>{{ $tItem->item_name }}</strong> 
                                    (SL: {{ $tItem->quantity }})
                                    @if($tItem->serial_number) 
                                        - SN: <span class="badge badge-warning">{{ $tItem->serial_number }}</span> 
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Hiển thị tiền thu --}}
                @if($task->collected_amount > 0)
                    <div class="mt-2 text-success font-weight-bold border-top pt-2">
                        <i class="fas fa-money-bill-wave"></i> Đã thu tiền mặt: {{ number_format($task->collected_amount) }} đ
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach

    <div>
        <i class="fas fa-clock bg-gray"></i>
    </div>
</div>