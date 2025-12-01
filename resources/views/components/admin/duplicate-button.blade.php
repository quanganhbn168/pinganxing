@props([
    'model',                 // alias trong config/duplicate.php, ví dụ: 'projects'
    'id',                    // id bản ghi
    'label' => 'Nhân bản',
    'confirm' => 'Nhân bản bản ghi này?',
    'icon' => 'bi bi-files', // icon Bootstrap Icons; null để ẩn
    'size' => 'sm',          // xs|sm|md|lg tuỳ anh dùng
])

<button type="button"
        {{ $attributes->merge([
            'class' => "btn btn-{$size} btn-outline-secondary js-dup",
        ]) }}
        data-dup-btn="1"
        data-model="{{ $model }}"
        data-id="{{ $id }}"
        data-confirm="{{ $confirm }}">
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $label }}
</button>

@once
@push('js')
<script>
(function(){
  const DUP_URL = @json(route('admin.duplicate'));
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());

  document.addEventListener('click', async function(e){
    const btn = e.target.closest('[data-dup-btn]');
    if (!btn) return;

    const model = btn.dataset.model;
    const id    = btn.dataset.id;
    const msg   = btn.dataset.confirm || 'Nhân bản bản ghi này?';

    if (!model || !id) return;
    if (!confirm(msg)) return;

    if (btn.dataset.loading === '1') return;
    btn.dataset.loading = '1';

    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    btn.disabled = true;

    try {
      const res = await fetch(DUP_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ model, id })
      });
      const json = await res.json();

      if (json?.success && json.edit_url) {
        window.location.href = json.edit_url;
      } else {
        alert(json?.error || 'Không thể nhân bản.');
        btn.innerHTML = oldHtml;
        btn.disabled = false;
      }
    } catch (err) {
      alert('Không kết nối được đến máy chủ.');
      btn.innerHTML = oldHtml;
      btn.disabled = false;
    } finally {
      btn.dataset.loading = '0';
    }
  });
})();
</script>
@endpush
@endonce
