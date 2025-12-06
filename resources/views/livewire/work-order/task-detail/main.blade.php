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

            {{-- 4. FOOTER BUTTON (Chỉ hiện khi ở tab báo cáo và chưa xong) --}}
            @if($task->status != \App\Enums\TaskStatus::COMPLETED)
                <div class="mt-3 mb-4">
                    <button class="btn btn-success btn-lg btn-block shadow-sm font-weight-bold" onclick="submitReport()">
                        <i class="fas fa-paper-plane mr-2"></i> GỬI BÁO CÁO
                    </button>
                </div>
            @endif
        @endif

    </div>

    {{-- 5. CÁC MODAL HỖ TRỢ (Scanner, Image Viewer) --}}
    
    {{-- Scanner Overlay --}}
    <div id="scanner-overlay">
        <div class="text-white mb-3 font-weight-bold text-lg">QUÉT MÃ VẠCH</div>
        <div id="scanner-box">
            <div id="scanner-line"></div>
            <div id="reader"></div>
        </div>
        <button class="btn btn-outline-light mt-5 rounded-pill px-4" onclick="closeScanner()">
            <i class="fas fa-times mr-1"></i> Đóng Camera
        </button>
    </div>

    {{-- Image Viewer Fullscreen --}}
    <div id="imageViewer" onclick="closeImageViewer()">
        <span class="close-viewer">&times;</span>
        <img id="imageViewerSrc" src="">
    </div>

    {{-- 6. LOAD JS --}}
    @include('livewire.work-order.task-detail.scripts')
</div>