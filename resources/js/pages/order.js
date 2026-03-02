/**
 * Order / pricing page JS.
 * Handles: package selection highlight, spec preview.
 */
document.addEventListener('DOMContentLoaded', () => {
    const cards   = document.querySelectorAll('[data-package-card]');
    const input   = document.getElementById('selected_package_id');
    const summary = document.getElementById('order-summary');

    if (!cards.length) return;

    function selectPackage(card) {
        cards.forEach((c) => {
            c.classList.remove('ring-2', 'ring-brand-500', 'scale-105');
            c.querySelector('[data-check]')?.classList.add('hidden');
        });

        card.classList.add('ring-2', 'ring-brand-500');
        card.querySelector('[data-check]')?.classList.remove('hidden');

        if (input) input.value = card.dataset.packageId;

        if (summary) {
            summary.querySelector('[data-pkg-name]').textContent  = card.dataset.packageName  ?? '';
            summary.querySelector('[data-pkg-price]').textContent = card.dataset.packagePrice ?? '';
            summary.querySelector('[data-pkg-specs]').textContent = card.dataset.packageSpecs ?? '';
            summary.classList.remove('hidden');
        }
    }

    cards.forEach((card) => {
        card.addEventListener('click', () => selectPackage(card));
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectPackage(card);
            }
        });
    });

    // Auto-select first/featured
    const featured = document.querySelector('[data-package-card][data-featured="true"]') ?? cards[0];
    if (featured) selectPackage(featured);
});
