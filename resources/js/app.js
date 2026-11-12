const menuButton = document.querySelector('[data-menu-toggle]');
const storeNavigation = menuButton ? document.getElementById(menuButton.getAttribute('aria-controls')) : null;
function closeStoreNavigation() {
    if (!menuButton || !storeNavigation) return;
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Buka navigasi');
    storeNavigation.classList.remove('is-open');
}
menuButton?.addEventListener('click', () => {
    const expanded = menuButton.getAttribute('aria-expanded') !== 'true';
    menuButton.setAttribute('aria-expanded', String(expanded));
    menuButton.setAttribute('aria-label', expanded ? 'Tutup navigasi' : 'Buka navigasi');
    storeNavigation?.classList.toggle('is-open', expanded);
});
storeNavigation?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeStoreNavigation));
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && menuButton?.getAttribute('aria-expanded') === 'true') {
        closeStoreNavigation();
        menuButton.focus();
    }
});
const storeHeader = document.querySelector('[data-store-header]');
if (storeHeader) {
    const updateHeader = () => storeHeader.classList.toggle('is-scrolled', window.scrollY > 16);
    updateHeader();
    window.addEventListener('scroll', updateHeader, {passive: true});
}
if (document.body.classList.contains('storefront') && window.matchMedia) {
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    let revealObserver;
    const initializeReveal = () => {
        revealObserver?.disconnect();
        document.body.classList.remove('motion-ready');
        if (preference.matches || !('IntersectionObserver' in window)) return;
        try {
            revealObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, {threshold: 0.08, rootMargin: '0px 0px -15px 0px'});
            document.querySelectorAll('[data-reveal-group]').forEach(group => {
                group.querySelectorAll('[data-reveal]').forEach((element, index) => {
                    element.style.setProperty('--reveal-delay', `${(index % 3) * 65}ms`);
                });
            });
            document.querySelectorAll('[data-reveal]').forEach(element => revealObserver.observe(element));
            document.body.classList.add('motion-ready');
        } catch {
            revealObserver?.disconnect();
            document.body.classList.remove('motion-ready');
        }
    };
    initializeReveal();
    preference.addEventListener?.('change', initializeReveal);
}
document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirmDelete)) event.preventDefault();
    });
});
document.querySelector('[data-image-input]')?.addEventListener('change', (event) => {
    const input = event.currentTarget;
    const preview = document.querySelector('[data-image-preview]');
    const file = input.files?.[0];
    if (preview.dataset.objectUrl) URL.revokeObjectURL(preview.dataset.objectUrl);
    if (!file) {
        preview.src = preview.dataset.original || '';
        preview.hidden = !preview.dataset.original;
        return;
    }
    if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
        input.value = '';
        preview.src = preview.dataset.original || '';
        preview.hidden = !preview.dataset.original;
        window.alert('Pilih foto JPG, PNG, atau WebP dengan ukuran maksimal 5 MB.');
        return;
    }
    const url = URL.createObjectURL(file);
    preview.dataset.objectUrl = url;
    preview.src = url;
    preview.hidden = false;
});
