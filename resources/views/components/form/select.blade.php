@props([
  'name',
  'label' => null,
  'options' => [],
  // Hỗ trợ cả hai cách: selected (cũ) và value (alias mới)
  'selected' => null,
  'value' => null,
  'required' => false,
  'placeholder' => '--- Chọn ---',
  'id' => null,
])

@php
    // id ưu tiên props->id, fallback = name
    $inputId = $id ?: $name;

    // Giá trị ban đầu do dev set (ưu tiên selected, rồi đến value)
    $preset = isset($selected) ? $selected : $value;

    // Giá trị hiện tại: ưu tiên old(), nếu không có thì preset
    $current = old($name, $preset);

    // Chuẩn hóa sang string để so sánh key số/chuỗi không lệch
    $currentStr = is_null($current) ? '' : (string) $current;

    $hasError = $errors->has($name);
@endphp

<div class="form-group">
  @if($label)
    <label for="{{ $inputId }}">
      {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
  @endif

  <select
    name="{{ $name }}"
    id="{{ $inputId }}"
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'form-control'.($hasError ? ' is-invalid' : '')]) }}
  >
    <option value="">{{ $placeholder }}</option>

    @foreach($options as $key => $text)
      @php $keyStr = is_null($key) ? '' : (string) $key; @endphp
      <option value="{{ $keyStr }}" @selected($currentStr === $keyStr)>{{ $text }}</option>
    @endforeach
  </select>

  @error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>
