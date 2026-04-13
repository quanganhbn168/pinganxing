@props(['list'])
<div class="toc-wrapper">
    @if(isset($list) && is_array($list) && count($list) > 0)
        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
            @foreach($list as $item)
                <li>
                    <a href="#{{ $item['id'] ?? '' }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                        {{ $item['text'] ?? '' }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>