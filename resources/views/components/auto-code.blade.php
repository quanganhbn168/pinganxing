@props([])

<div class="form-group">
    <label for="{{ $inputId }}">{{ $label }}</label>
    <div class="input-group">
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control @error($name) is-invalid @enderror"
            placeholder="{{ $placeholder }}"
            data-autocode="input"
            data-check-url="{{ $checkUrl }}"
            data-current-id="{{ $currentId ?? '' }}"
        >
        <div class="input-group-append">
            <button type="button"
                    class="btn btn-outline-secondary"
                    data-autocode="from-source"
                    data-source="{{ $source }}"
                    data-target="#{{ $inputId }}">
                Tạo tự động
            </button>
        </div>
    </div>

    <small class="d-flex align-items-center mt-2 text-muted" data-autocode="status" data-target="#{{ $inputId }}">
        <span class="spinner-border spinner-border-sm mr-2 d-none" data-autocode="spinner"></span>
        <span data-autocode="message">Chưa kiểm tra...</span>
    </small>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@once
    @push('js')
        <script src="{{ asset('js/code-auto.js') }}"></script>
    @endpush
@endonce
