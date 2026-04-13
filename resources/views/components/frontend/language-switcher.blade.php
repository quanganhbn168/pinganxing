@props(['type' => 'desktop'])

@php
    $languages = [
        ['code' => 'vi', 'name' => 'Tiếng Việt', 'flag' => 'images/flags/vn.png', 'short' => 'VN'],
        ['code' => 'en', 'name' => 'English', 'flag' => 'images/flags/us.png', 'short' => 'EN'],
        ['code' => 'ko', 'name' => '한국어', 'flag' => 'images/flags/kr.png', 'short' => 'KR'],
        ['code' => 'zh-CN', 'name' => '中文', 'flag' => 'images/flags/cn.png', 'short' => 'CN'],
    ];
@endphp

@if($type === 'mobile')
    <div class="flex justify-center gap-4 items-center">
        @foreach($languages as $index => $lang)
            <a href="#" onclick="doGTranslate('vi|{{ $lang['code'] }}'); updateFlag('{{ asset($lang['flag']) }}'); return false;" class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white hover:bg-gray-50 focus:ring-4 focus:ring-blue-100 transition-all dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 shadow-sm" title="{{ $lang['name'] }}">
                <img src="{{ asset($lang['flag']) }}" class="w-6 h-6 rounded-full object-cover" alt="{{ $lang['short'] }}">
            </a>
        @endforeach
    </div>
@else
    <div class="hidden md:block">
        <!-- Language Dropdown -->
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdownLang" class="text-gray-900 bg-gray-50 border border-gray-200 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-full text-sm px-3 py-2 text-center inline-flex items-center dark:bg-gray-800 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition-colors" type="button">
            <img src="{{ asset('images/flags/vn.png') }}" class="w-5 h-5 rounded-full me-2 current-flag-img" alt="VN">
            <span class="hidden sm:inline-block text-xs font-semibold">VN</span>
        </button>
        <div id="dropdownLang" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-xl shadow-lg w-40 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                @foreach($languages as $lang)
                <li>
                    <a href="#" onclick="doGTranslate('vi|{{ $lang['code'] }}'); updateFlag('{{ asset($lang['flag']) }}'); return false;" class="flex items-center px-4 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/50 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                        <img src="{{ asset($lang['flag']) }}" class="w-5 h-5 rounded-full me-3 shadow-sm" alt="{{ $lang['short'] }}"> {{ $lang['name'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
