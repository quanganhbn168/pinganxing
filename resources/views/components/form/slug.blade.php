@props([
    'name' => 'slug',
    'label' => 'Slug',
    'value' => null,
    'source' => '#title',
    'table' => '',
    'field' => 'slug',
    'checkUrl' => route('admin.ajax.slug.check'),
    'currentId' => null,
    'placeholder' => 'vd: my-post-title',
])

@php $inputId = $attributes->get('id') ?? $name; @endphp

<div class="form-group"
     x-data="SlugField({
        inputSelector: '#{{ $inputId }}',
        sourceSelector: '{{ $source }}',
        checkUrl: '{{ $checkUrl }}',
        table: '{{ $table }}',
        field: '{{ $field }}',
        currentId: {{ $currentId ? (int)$currentId : 'null' }},
     })"
     x-init="init()">

    <label for="{{ $inputId }}">{{ $label }}</label>
    <div class="input-group">
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control"
            placeholder="{{ $placeholder }}"
        >
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary" x-on:click="fromSource()">Tạo từ tiêu đề</button>
        </div>
    </div>

    {{-- Dòng thông báo trạng thái slug --}}
    <small class="slug-status mt-2 d-flex align-items-center text-muted">
        <span class="spinner-border spinner-border-sm mr-2 d-none" id="{{ $inputId }}-spinner"></span>
        <span class="slug-message">Chưa kiểm tra...</span>
    </small>
</div>

@push('js')
<script src="{{ asset('js/slug-field.js') }}"></script>
@endpush
