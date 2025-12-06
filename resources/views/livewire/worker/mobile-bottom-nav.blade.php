<div class="bottom-nav">
    {{-- 1. Tất cả các việc (Mặc định) --}}
    <a href="{{ route('worker.jobs', ['filter' => 'all']) }}" 
       class="nav-item-mobile {{ request()->routeIs('worker.jobs') && request()->input('filter') == 'all' ? 'active' : '' }}">
        <i class="fas fa-list"></i>
        Tất cả
    </a>

    {{-- 2. Việc cần làm (Pending) --}}
    <a href="{{ route('worker.jobs', ['filter' => 'pending']) }}" 
       class="nav-item-mobile position-relative {{ request()->routeIs('worker.jobs') && request()->input('filter') == 'pending' ? 'active' : '' }}">
        <i class="fas fa-exclamation-circle"></i>
        @if($counts['pending'] > 0)
            <span class="badge badge-danger position-absolute" style="top: 2px; right: 15px; font-size: 0.6rem; padding: 3px 5px;">{{ $counts['pending'] }}</span>
        @endif
        Cần làm
    </a>
    
    {{-- 3. Việc đang làm (Processing) --}}
    <a href="{{ route('worker.jobs', ['filter' => 'processing']) }}" 
       class="nav-item-mobile {{ request()->routeIs('worker.jobs') && request()->input('filter') == 'processing' ? 'active' : '' }}">
        <i class="fas fa-hammer"></i>
        Đang làm
    </a>

     {{-- 4. Việc khẩn cấp (Urgent) --}}
     <a href="{{ route('worker.jobs', ['filter' => 'urgent']) }}" 
        class="nav-item-mobile position-relative {{ request()->routeIs('worker.jobs') && request()->input('filter') == 'urgent' ? 'active' : '' }}">
         <i class="fas fa-fire-alt"></i>
         @if($counts['urgent'] > 0)
            <span class="badge badge-danger position-absolute" style="top: 2px; right: 15px; font-size: 0.6rem; padding: 3px 5px;">{{ $counts['urgent'] }}</span>
         @endif
         Khẩn cấp
     </a>

     {{-- 5. Thông báo --}}
     <a href="{{ route('worker.notifications') }}"
        class="nav-item-mobile position-relative {{ request()->routeIs('worker.notifications') ? 'active' : '' }}">
         <i class="fas fa-bell"></i>
         @if($counts['unread'] > 0)
            <span class="badge badge-danger position-absolute" style="top: 2px; right: 15px; font-size: 0.6rem; padding: 3px 5px;">{{ $counts['unread'] }}</span>
         @endif
         Thông báo
     </a>
</div>
