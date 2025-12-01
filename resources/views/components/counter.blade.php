@props([
    'from' => 0,
    'to' => 1000,
    'duration' => 1200,      // ms
    'suffix' => '+',
    'prefix' => '',
    'label' => '',
    'icon' => null,          // ví dụ: 'bi bi-calendar'
    'locale' => 'vi-VN',     // format số
])

@php
    $id = 'ctr-' . substr(md5($to.$label.microtime()), 0, 8);
@endphp

<div class="wa-counter" id="{{ $id }}">
    
    <div class="wa-counter__top">
        @if($icon)
            <div class="wa-counter__icon">
                <i class="{{ $icon }}" aria-hidden="true"></i>
            </div>
        @endif

        <div class="wa-counter__number">
            <span class="js-counter"
                  data-from="{{ (int)$from }}"
                  data-to="{{ (int)$to }}"
                  data-duration="{{ (int)$duration }}"
                  data-locale="{{ $locale }}"
                  data-prefix="{{ $prefix }}"
                  data-suffix="{{ $suffix }}">0</span>
        </div>
    </div>
    @if($label)
        <div class="wa-counter__label">{{ $label }}</div>
    @endif
</div>