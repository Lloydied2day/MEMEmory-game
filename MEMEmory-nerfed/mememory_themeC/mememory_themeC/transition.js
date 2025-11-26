(function () {
  const body = document.body;
  if (!body) return;

  const reveal = () => {
    requestAnimationFrame(() => {
      body.classList.add('page-visible');
      body.classList.remove('page-preload', 'page-hide');
    });
  };

  if (document.readyState === 'complete') {
    reveal();
  } else {
    window.addEventListener('load', reveal, { once: true });
  }

  let isLeaving = false;
  const startLeave = (callback) => {
    if (isLeaving) return;
    isLeaving = true;
    body.classList.add('page-hide');
    body.classList.remove('page-visible');
    setTimeout(() => callback(), 220);
  };

  window.pageFadeLeave = (callback) => {
    if (typeof callback !== 'function') return;
    if (isLeaving) {
      callback();
      return;
    }
    startLeave(callback);
  };

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link) return;
    if (link.dataset.noFade === 'true' || link.hasAttribute('download')) return;
    if (link.target && link.target !== '_self') return;
    const href = link.getAttribute('href');
    if (!href || href.startsWith('#')) return;
    const url = link.href;
    if (url === window.location.href) return;
    if (link.origin && link.origin !== window.location.origin) return;
    event.preventDefault();
    startLeave(() => { window.location.href = url; });
  });

  document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.dataset.noFade === 'true') return;
    event.preventDefault();
    startLeave(() => form.submit());
  });
})();

