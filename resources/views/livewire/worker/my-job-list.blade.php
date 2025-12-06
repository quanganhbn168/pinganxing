<div>
    <div class="container py-3">
        
        {{-- SECTION: BIG COUNTERS (Chỉ hiện khi ở trang Tổng quan hoặc Tất cả/Unfinished) --}}
        @if(in_array($filter, ['all', 'unfinished']))
        <div class="row">
            {{-- 1. TẤT CẢ --}}
            <div class="col-6 mb-3">
                <div class="card card-big-counter {{ $filter == 'all' ? 'bg-info text-white' : 'bg-white text-info' }}"
                     wire:click="setFilter('all')">
                    <div class="card-body text-center p-3">
                        <div class="counter-number">{{ $counts['all'] }}</div>
                        <div class="counter-label">Tất cả</div>
                    </div>
                </div>
            </div>

            {{-- 2. CẦN LÀM (Pending) --}}
            <div class="col-6 mb-3">
                <div class="card card-big-counter {{ $filter == 'pending' ? 'bg-primary text-white' : 'bg-white text-primary' }}" 
                     wire:click="setFilter('pending')">
                    <div class="card-body text-center p-3">
                        <div class="counter-number">{{ $counts['pending'] }}</div>
                        <div class="counter-label">Cần làm</div>
                    </div>
                </div>
            </div>

            {{-- 3. ĐANG LÀM (Processing) --}}
            <div class="col-6 mb-3">
                <div class="card card-big-counter {{ $filter == 'processing' ? 'bg-warning text-white' : 'bg-white text-warning' }}" 
                     wire:click="setFilter('processing')">
                    <div class="card-body text-center p-3">
                        <div class="counter-number">{{ $counts['processing'] }}</div>
                        <div class="counter-label">Đang làm</div>
                    </div>
                </div>
            </div>

            {{-- 4. KHẨN CẤP --}}
            <div class="col-6 mb-3">
                <div class="card card-big-counter {{ $filter == 'urgent' ? 'bg-danger text-white' : 'bg-white text-danger' }}"
                     wire:click="setFilter('urgent')">
                    <div class="card-body text-center p-3">
                        <div class="counter-number">{{ $counts['urgent'] }}</div>
                        <div class="counter-label">Khẩn cấp</div>
                    </div>
                </div>
            </div>

            {{-- 5. ĐÃ XONG (Completed) --}}
            <div class="col-12 mb-3">
                <div class="card card-big-counter {{ $filter == 'completed' ? 'bg-success text-white' : 'bg-white text-success' }}"
                     wire:click="setFilter('completed')">
                    <div class="card-body text-center p-3">
                        <div class="counter-number">{{ $counts['completed'] }}</div>
                        <div class="counter-label">Đã xong</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- SECTION: LIST --}}
        <div class="mt-2">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h5 class="font-weight-bold text-dark mb-0 text-uppercase">
                    @if($filter == 'pending') CẦN LÀM
                    @elseif($filter == 'processing') ĐANG LÀM
                    @elseif($filter == 'urgent') KHẨN CẤP
                    @elseif($filter == 'completed') ĐÃ HOÀN THÀNH
                    @else DANH SÁCH VIỆC
                    @endif
                    <span class="badge badge-pill badge-secondary ml-1" style="font-size: 0.8em; vertical-align: middle;">{{ $jobs->total() }}</span>
                </h5>
                
                @if(!in_array($filter, ['unfinished', 'all']))
                    <button wire:click="setFilter('unfinished')" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </button>
                @endif
            </div>

            <div class="row">
                @forelse($jobs as $job)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-outline {{ $job->status === \App\Enums\WorkOrderStatus::COMPLETED ? 'card-success' : 'card-primary' }} mb-3 shadow-sm border-0">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <h5 class="font-weight-bold text-dark mb-1">{{ $job->title }}</h5>
                                    <span class="badge badge-{{ $job->priority?->color() ?? 'secondary' }}" style="height: fit-content;">
                                        {{ $job->priority?->label() ?? 'Bình thường' }}
                                    </span>
                                </div>
                                
                                <div class="text-muted small mb-2">
                                    <i class="fas fa-barcode mr-1"></i> #{{ $job->code }} | 
                                    <i class="far fa-clock ml-1 mr-1"></i> {{ $job->created_at ? $job->created_at->format('d/m/Y') : '--/--/----' }}
                                </div>

                                <div class="bg-light p-2 rounded mb-2">
                                    <div class="d-flex align-items-start mb-1">
                                        <i class="fas fa-user-circle text-secondary mt-1 mr-2"></i>
                                        <span class="font-weight-bold text-dark">{{ $job->contact_person }}</span>
                                    </div>
                                    @if($job->contact_phone)
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-phone-alt text-success mr-2"></i>
                                        <a href="tel:{{ $job->contact_phone }}" class="text-dark font-weight-bold">{{ $job->contact_phone }}</a>
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-map-marker-alt text-danger mt-1 mr-2"></i>
                                        <a href="https://maps.google.com/?q={{ $job->site_address }}" target="_blank" class="text-dark" style="line-height:1.2;">
                                            {{ $job->site_address }}
                                        </a>
                                    </div>
                                </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="badge badge-{{ $job->status->color() }} px-2 py-1" style="font-size: 85%;">
                                            {{ $job->status->label() }}
                                        </span>
                                        @if($job->status === \App\Enums\WorkOrderStatus::PENDING)
                                            <a href="{{ route('worker.jobs.detail', $job->id) }}" class="btn btn-sm btn-info px-4 font-weight-bold rounded-pill shadow-sm">
                                                <i class="fas fa-eye mr-1"></i> Xem yêu cầu
                                            </a>
                                        @else
                                            <a href="{{ route('worker.jobs.detail', $job->id) }}" class="btn btn-sm btn-primary px-3 font-weight-bold rounded-pill">
                                                Chi tiết <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        @endif
                                    </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check fa-4x text-gray mb-3 opacity-50"></i>
                            <p class="text-muted">Không có công việc nào trong danh sách này.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
