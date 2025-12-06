<div>
    <div class="container py-3">
        @if(session()->has('success'))
            <div class="alert alert-success shadow-sm mb-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="font-weight-bold mb-0 text-dark">THÔNG BÁO</h5>
            @if($notifications->count() > 0)
                <button wire:click="markAllAsRead" class="btn btn-sm btn-outline-primary shadow-sm">
                    <i class="fas fa-check-double mr-1"></i> Đọc tất cả
                </button>
            @endif
        </div>

        <div class="list-group shadow-sm rounded">
            @forelse($notifications as $notify)
                <div class="list-group-item list-group-item-action {{ $notify->read_at ? 'bg-light' : 'bg-white border-left-primary' }}"
                     style="{{ $notify->read_at ? '' : 'border-left: 4px solid #007bff;' }}">
                    
                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                        <small class="text-muted">
                            <i class="far fa-clock mr-1"></i> {{ $notify->created_at->diffForHumans() }}
                        </small>
                        @if(!$notify->read_at)
                            <button wire:click="markAsRead('{{ $notify->id }}')" class="btn btn-xs btn-light text-primary" title="Đánh dấu đã đọc">
                                <i class="fas fa-circle" style="font-size: 8px;"></i>
                            </button>
                        @endif
                    </div>

                    <p class="mb-1 font-weight-bold text-dark text-sm" style="line-height: 1.4;">
                        {{ \Illuminate\Support\Str::limit($notify->data['message'] ?? $notify->data['content'] ?? 'Nội dung thông báo...', 100) }}
                    </p>
                    
                    @if(isset($notify->data['url']))
                        <a href="#" wire:click.prevent="handleClick('{{ $notify->id }}', '{{ $notify->data['url'] }}')" 
                           class="text-primary text-sm font-weight-bold mt-1 d-inline-block">
                            Xem chi tiết <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @elseif(isset($notify->data['work_order_id']))
                         <a href="#" wire:click.prevent="handleClick('{{ $notify->id }}', '{{ route('worker.jobs.detail', $notify->data['work_order_id']) }}')" 
                            class="text-primary text-sm font-weight-bold mt-1 d-inline-block">
                            Xem phiếu việc <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @elseif(isset($notify->data['task_id']))
                        {{-- Redirect to Job Detail even if it is a task notification, so they see the context first --}}
                        @php
                            $task = \App\Models\Task::find($notify->data['task_id']);
                        @endphp
                        @if($task)
                             <a href="#" wire:click.prevent="handleClick('{{ $notify->id }}', '{{ route('worker.jobs.detail', $task->work_order_id) }}')" 
                                class="text-primary text-sm font-weight-bold mt-1 d-inline-block">
                                Xem yêu cầu <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @endif
                    @endif
                </div>
            @empty
                <div class="list-group-item text-center py-5">
                    <i class="far fa-bell fa-3x text-gray mb-3 opacity-50"></i>
                    <p class="text-muted mb-0">Bạn chưa có thông báo nào.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
