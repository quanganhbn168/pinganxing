@props(['headerMenu' => [], 'setting' => null])

<!-- Mobile Drawer Navigation -->
<div id="drawer-navigation" class="fixed top-0 left-0 z-[110] w-72 h-screen p-0 overflow-y-auto transition-transform -translate-x-full bg-white dark:bg-gray-900 shadow-2xl" tabindex="-1" aria-labelledby="drawer-navigation-label">
    <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h5 id="drawer-navigation-label" class="text-lg font-bold text-gray-900 uppercase dark:text-white flex items-center">
            <img src="{{ asset($setting->logo ?? 'images/setting/no-image.png') }}" class="h-6 mr-3" alt="Logo" />
        </h5>
        <button type="button" onclick="this.blur()" data-drawer-hide="drawer-navigation" aria-controls="drawer-navigation" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-4 right-4 inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            <span class="sr-only">Đóng menu</span>
        </button>
    </div>
    
    <div class="py-6 px-4">
        <ul class="space-y-4 font-medium mb-6">
            @if(isset($headerMenu) && count($headerMenu) > 0)
                @foreach($headerMenu as $menuItem)
                    @if($menuItem->children && $menuItem->children->count() > 0)
                        <li>
                            <div class="flex items-center rounded-xl transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-800 {{ $menuItem->is_active_route ? 'bg-blue-50 text-blue-600' : 'text-gray-900 dark:text-white' }}">
                                <a href="{{ $menuItem->link }}" target="{{ $menuItem->link_target }}" class="flex min-w-0 flex-1 items-center p-3 text-base uppercase font-bold">
                                    @if($menuItem->icon)<i class="{{ $menuItem->icon }} mr-2 text-sm"></i>@endif
                                    <span class="ms-3 truncate">{{ $menuItem->title }}</span>
                                </a>
                                <button type="button" class="mr-2 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-gray-500 hover:bg-white hover:text-blue-600 dark:text-gray-300 dark:hover:bg-gray-700" aria-controls="dropdown-mobile-{{ $loop->index }}" data-collapse-toggle="dropdown-mobile-{{ $loop->index }}" aria-label="Mở menu con {{ $menuItem->title }}">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                            </div>
                            <ul id="dropdown-mobile-{{ $loop->index }}" class="hidden py-2 space-y-1">
                                @foreach($menuItem->children as $childItem)
                                    <li>
                                        <a href="{{ $childItem->link }}" target="{{ $childItem->link_target }}" class="flex items-center w-full p-2.5 text-gray-600 transition duration-75 rounded-lg pl-11 group hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                                            @if($childItem->icon)<i class="{{ $childItem->icon }} text-[10px] mr-3 opacity-70"></i>@else<i class="fas fa-minus text-[10px] mr-3 opacity-50"></i>@endif {{ $childItem->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li>
                            <a href="{{ $menuItem->link }}" target="{{ $menuItem->link_target }}" class="flex items-center p-3 rounded-xl uppercase font-bold group transition-colors {{ $menuItem->is_active_route ? 'text-blue-600 bg-blue-50' : 'text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800' }}">
                                @if($menuItem->icon)<i class="{{ $menuItem->icon }} ms-3 mr-1 text-sm"></i>@endif <span class="ms-3">{{ $menuItem->title }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            @else
                <li><a href="/" class="flex items-center p-3 rounded-xl uppercase font-bold {{ request()->is('/') ? 'text-blue-600 bg-blue-50' : 'text-gray-900 hover:bg-gray-100' }}"><span class="ms-3">Trang chủ</span></a></li>
                <li><a href="/dich-vu" class="flex items-center p-3 rounded-xl uppercase font-bold {{ request()->is('dich-vu*') ? 'text-blue-600 bg-blue-50' : 'text-gray-900 hover:bg-gray-100' }}"><span class="ms-3">Dịch vụ</span></a></li>
                <li><a href="/linh-vuc" class="flex items-center p-3 rounded-xl uppercase font-bold {{ request()->is('linh-vuc*') ? 'text-blue-600 bg-blue-50' : 'text-gray-900 hover:bg-gray-100' }}"><span class="ms-3">Lĩnh vực</span></a></li>
                <li><a href="/du-an" class="flex items-center p-3 rounded-xl uppercase font-bold {{ request()->is('du-an*') ? 'text-blue-600 bg-blue-50' : 'text-gray-900 hover:bg-gray-100' }}"><span class="ms-3">Dự án</span></a></li>
                <li><a href="/san-pham" class="flex items-center p-3 rounded-xl uppercase font-bold {{ request()->is('san-pham*') ? 'text-blue-600 bg-blue-50' : 'text-gray-900 hover:bg-gray-100' }}"><span class="ms-3">Sản phẩm</span></a></li>
            @endif
        </ul>
        
        <div class="mt-8 pt-6 px-3">
            <p class="text-xs text-center uppercase font-bold text-gray-400 mb-4 tracking-widest">Tư vấn & Hỗ trợ</p>
            
            <div class="space-y-3 mb-8">
                <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}" class="flex items-center justify-center w-full p-3.5 text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow-lg shadow-blue-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all uppercase font-bold tracking-wide">
                    <i class="fas fa-phone-alt mr-2"></i> {{ $setting->phone ?? 'Gọi điện ngay' }}
                </a>
                
                @php
                    $drawerZaloLink = isset($setting->zalo) && filled($setting->zalo) && ! in_array(rtrim(trim((string) $setting->zalo), '/'), ['http://zalo.me', 'https://zalo.me'], true)
                        ? trim((string) $setting->zalo)
                        : null;
                @endphp
                @if($drawerZaloLink)
                <a href="{{ $drawerZaloLink }}" target="_blank" class="flex items-center justify-center w-full p-3.5 text-blue-700 bg-white border border-blue-100 rounded-xl hover:border-blue-300 hover:bg-blue-50 active:scale-[0.98] transition-all uppercase font-bold tracking-wide shadow-sm dark:bg-gray-800 dark:text-blue-400 dark:border-gray-700 dark:hover:bg-gray-700">
                    <img src="{{ asset('images/setting/Icon_of_Zalo.svg') }}" onerror="this.src='{{ asset('images/setting/zalo.png') }}'; this.onerror=null;" class="w-5 h-5 mr-2" alt="" aria-hidden="true"> Zalo Chat
                </a>
                @endif
            </div>

            <!-- Ngôn ngữ -->
            <div class="border-t border-gray-100 dark:border-gray-800 pt-6 mt-6">
                <p class="text-[10px] text-center uppercase font-bold text-gray-400 mb-4 tracking-widest">Ngôn ngữ</p>
                <x-frontend.language-switcher type="mobile" />
            </div>
        </div>
    </div>
</div>
