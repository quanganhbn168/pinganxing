@php
    use Illuminate\Support\Facades\Route as RouteFacade;

    $menu = $menu ?? config('menu', []);
    $user = auth()->user();

    $canSee = function (array $item) use ($user): bool {
        return !isset($item['permission']) || ($user && $user->can($item['permission']));
    };

    $makeUrl = function (?string $routeName, array $params = []) {
        if (!$routeName || !RouteFacade::has($routeName)) return '#';
        try { return route($routeName, $params); } catch (\Throwable) { return '#'; }
    };

    $matchActive = function ($pattern): bool {
        if (!$pattern) return false;
        if (is_array($pattern)) {
            foreach ($pattern as $p) if (request()->routeIs($p)) return true;
            return false;
        }
        return request()->routeIs($pattern);
    };

    $isItemActive = function (array $item) use ($matchActive): bool {
        if ($matchActive($item['active_pattern'] ?? null)) return true;
        if (!empty($item['submenu'])) {
            foreach ($item['submenu'] as $sub)
                if ($matchActive($sub['active_pattern'] ?? ($sub['route'] ?? null))) return true;
            return false;
        }
        return isset($item['route']) && request()->routeIs($item['route']);
    };

    $isMenuOpen = fn(array $item) => !empty($item['submenu']) && $isItemActive($item);
@endphp

<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
@foreach ($menu as $item)
    @continue(!$canSee($item))

    {{-- HEADER --}}
    @if(($item['type'] ?? null) === 'header')
        <li class="nav-header">{{ $item['title'] ?? 'HEADER' }}</li>
        @continue
    @endif

    @php
        $hasSub = !empty($item['submenu']);
        $open   = $isMenuOpen($item);
        $active = $isItemActive($item);
        $url    = $hasSub ? '#' : $makeUrl($item['route'] ?? null, $item['params'] ?? []);
        $icon   = $item['icon'] ?? 'far fa-circle'; // icon của item cấp 1
        $badge  = $item['badge'] ?? null; // ['text'=>'2','class'=>'badge-info']
    @endphp

    <li class="nav-item {{ $open ? 'menu-is-opening menu-open' : '' }}">
        <a href="{{ $url }}" class="nav-link {{ $active && !$hasSub ? 'active' : '' }}">
            <i class="nav-icon {{ $icon }}"></i>
            <p>
                {{ $item['title'] ?? 'Menu Item' }}
                @if($badge)
                    <span class="right badge {{ $badge['class'] ?? 'badge-info' }}">{{ $badge['text'] ?? '' }}</span>
                @endif
                @if ($hasSub)
                    <i class="right fas fa-angle-left"></i>
                @endif
            </p>
        </a>

        @if ($hasSub)
            <ul class="nav nav-treeview">
                @foreach ($item['submenu'] as $sub)
                    @continue(!$canSee($sub))
                    @php
                        $subUrl    = $makeUrl($sub['route'] ?? null, $sub['params'] ?? []);
                        $subActive = $matchActive($sub['active_pattern'] ?? ($sub['route'] ?? null));
                        $subBadge  = $sub['badge'] ?? null;
                    @endphp
                    <li class="nav-item">
                        <a href="{{ $subUrl }}" class="nav-link {{ $subActive ? 'active' : '' }}">
                            {{-- icon mặc định hình tròn của AdminLTE --}}
                            <i class="far fa-circle nav-icon"></i>
                            <p>
                                {{ $sub['title'] ?? 'Sub Item' }}
                                @if($subBadge)
                                    <span class="right badge {{ $subBadge['class'] ?? 'badge-info' }}">{{ $subBadge['text'] ?? '' }}</span>
                                @endif
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
</ul>
