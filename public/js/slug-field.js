window.SlugField = function SlugField(opts) {
  const state = {
    inputEl: null,
    sourceEl: null,
    checkUrl: opts.checkUrl,
    table: opts.table,
    field: opts.field || 'slug',
    currentId: opts.currentId || null,
    statusWrap: null,
    spinner: null,
    message: null,
    loading: false,
  };

  function $(sel) { return document.querySelector(sel); }

  function slugify(str) {
    if (!str) return '';
    let s = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    s = s.toLowerCase()
      .replace(/đ/g, 'd')
      .replace(/[^a-z0-9\s\-]/g, ' ')
      .replace(/\s+/g, '-')
      .replace(/\-+/g, '-')
      .replace(/^-+|-+$/g, '');
    return s;
  }

  // --- UI helpers ---
  function setState(text, cls, showSpinner = false) {
    if (!state.statusWrap) return;
    state.spinner.classList.toggle('d-none', !showSpinner);
    state.message.textContent = text;
    state.message.className = 'slug-message ' + cls;
  }

  function setLoading() {
    state.loading = true;
    state.inputEl.classList.remove('is-valid', 'is-invalid');
    setState('Đang kiểm tra...', 'text-secondary', true);
  }

  function setValid() {
    state.loading = false;
    state.inputEl.classList.remove('is-invalid');
    state.inputEl.classList.add('is-valid');
    setState('Slug hợp lệ', 'text-success', false);
  }

  function setInvalid(msg) {
    state.loading = false;
    state.inputEl.classList.remove('is-valid');
    state.inputEl.classList.add('is-invalid');
    setState(msg || 'Slug đã tồn tại', 'text-danger', false);
  }

  let aborter = null;
  async function checkSlug(slug) {
    if (!slug) return setInvalid('Vui lòng nhập slug.');
    setLoading();

    try {
      if (aborter) aborter.abort();
      aborter = new AbortController();
      const url = new URL(state.checkUrl, window.location.origin);
      url.searchParams.set('slug', slug);
      url.searchParams.set('table', state.table);
      url.searchParams.set('field', state.field);
      if (state.currentId) url.searchParams.set('id', state.currentId);

      const res = await fetch(url, { signal: aborter.signal });
      const data = await res.json();

      if (data.ok) setValid();
      else setInvalid('Slug đã tồn tại');
    } catch (e) {
      if (e.name !== 'AbortError') setInvalid('Không thể kiểm tra slug.');
    }
  }

  let debounceTimer = null;
  function handleInput(val) {
    const s = slugify(val);
    state.inputEl.value = s;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => checkSlug(s), 400);
  }

  function fromSource() {
    if (!state.sourceEl) return;
    const s = slugify(state.sourceEl.value || '');
    state.inputEl.value = s;
    checkSlug(s);
  }

  function init() {
    state.inputEl = $(opts.inputSelector);
    state.sourceEl = $(opts.sourceSelector);
    state.statusWrap = state.inputEl.closest('.form-group').querySelector('.slug-status');
    state.spinner = state.statusWrap.querySelector('.spinner-border');
    state.message = state.statusWrap.querySelector('.slug-message');

    if (!state.inputEl) return;

    // Check khi có sẵn giá trị (edit)
    if (state.inputEl.value) checkSlug(state.inputEl.value);

    // Theo dõi source (title/name)
    if (state.sourceEl) {
      let timer = null;
      state.sourceEl.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          const s = slugify(state.sourceEl.value);
          state.inputEl.value = s;
          checkSlug(s);
        }, 400);
      });
    }

    // Khi user gõ hoặc dán vào slug thủ công
    state.inputEl.addEventListener('input', () => handleInput(state.inputEl.value));
    state.inputEl.addEventListener('blur', () => checkSlug(state.inputEl.value));
  }

  return { init, fromSource, handleInput };
};
