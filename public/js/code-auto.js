(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  function slugifyVietnamese(str) {
    if (!str) return '';
    return str
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // bỏ dấu
      .replace(/đ/g, 'd').replace(/Đ/g, 'D')
      .replace(/[^a-zA-Z0-9]+/g, '-') // chỉ chữ/số -> -
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '')
      .toUpperCase();
  }

  function uniqCheck(url, productId, code, statusEl) {
    if (!url || !code) {
      if (statusEl) statusEl.querySelector('[data-autocode="message"]').textContent = 'Chưa kiểm tra...';
      return;
    }
    const spinner = statusEl?.querySelector('[data-autocode="spinner"]');
    const message = statusEl?.querySelector('[data-autocode="message"]');

    spinner && spinner.classList.remove('d-none');
    message && (message.textContent = 'Đang kiểm tra...');

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        ...(csrf ? {'X-CSRF-TOKEN': csrf} : {})
      },
      body: JSON.stringify({ code: code.trim(), productId: productId || null })
    })
    .then(r => r.json())
    .then(res => {
      spinner && spinner.classList.add('d-none');
      if (res?.success) {
        message && (message.textContent = '✔ Hợp lệ, chưa bị trùng.');
        message.classList.remove('text-danger');
        message.classList.add('text-success');
      } else {
        const err = res?.errors?.code?.[0] || 'Mã đã tồn tại.';
        message && (message.textContent = `✖ ${err}`);
        message.classList.remove('text-success');
        message.classList.add('text-danger');
      }
    })
    .catch(() => {
      spinner && spinner.classList.add('d-none');
      message && (message.textContent = 'Không kiểm tra được. Thử lại.');
      message.classList.remove('text-success');
      message.classList.add('text-danger');
    });
  }

  function bindAutoCode(root) {
    const input = root.querySelector('[data-autocode="input"]');
    if (!input) return;

    const statusWrap = root.querySelector('[data-autocode="status"]');
    const checkUrl   = input.getAttribute('data-check-url') || '';
    const currentId  = input.getAttribute('data-current-id') || '';

    let t;
    input.addEventListener('input', function () {
      clearTimeout(t);
      const val = input.value.trim();
      t = setTimeout(() => uniqCheck(checkUrl, currentId, val, statusWrap), 400);
    });

    const btn = root.querySelector('[data-autocode="from-source"]');
    if (btn) {
      btn.addEventListener('click', function () {
        const srcSel = btn.getAttribute('data-source');
        const targetSel = btn.getAttribute('data-target');
        const src = document.querySelector(srcSel);
        const target = document.querySelector(targetSel);
        if (!src || !target) return;

        const base = slugifyVietnamese(src.value);
        // Chuẩn hoá code: tiền tố mặc định 'SP-' + rút gọn 24 ký tự
        const code = ('SP-' + base).replace(/-+/g, '-').slice(0, 24).replace(/-$/, '');
        target.value = code;

        uniqCheck(checkUrl, currentId, code, statusWrap);
      });
    }

    // kiểm tra lần đầu nếu có sẵn value
    if (input.value.trim()) {
      uniqCheck(checkUrl, currentId, input.value.trim(), statusWrap);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-group').forEach(bindAutoCode);
  });
})();
