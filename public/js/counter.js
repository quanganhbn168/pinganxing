(function () {
  function animate(el) {
    const from = parseInt(el.dataset.from || '0', 10);
    const to = parseInt(el.dataset.to || '0', 10);
    const duration = parseInt(el.dataset.duration || '1200', 10);
    const locale = el.dataset.locale || 'vi-VN';
    const prefix = el.dataset.prefix || '';
    const suffix = el.dataset.suffix || '';

    const start = performance.now();
    function frame(now) {
      const p = Math.min((now - start) / duration, 1);
      // easeOutCubic
      const eased = 1 - Math.pow(1 - p, 3);
      const val = Math.round(from + (to - from) * eased);
      el.textContent = prefix + val.toLocaleString(locale) + (suffix || '');
      if (p < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  const els = Array.from(document.querySelectorAll('.js-counter'));
  if (!('IntersectionObserver' in window)) {
    els.forEach(animate);
    return;
  }

  const obs = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting && !entry.target.dataset.done) {
        entry.target.dataset.done = '1';
        animate(entry.target);
        // chỉ chạy 1 lần
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.4 });

  els.forEach((el) => obs.observe(el));
})();
