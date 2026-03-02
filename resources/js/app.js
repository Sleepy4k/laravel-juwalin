import './bootstrap';

/**
 * Global application bootstrap.
 * Handles: flash dismiss, mobile nav toggle, dropdown menus.
 */

// ── Flash messages auto-dismiss ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-flash-dismiss]').forEach((el) => {
        el.addEventListener('click', () => {
            el.closest('[role="alert"]')?.remove();
        });

        setTimeout(() => el.closest('[role="alert"]')?.remove(), 5000);
    });
});

// ── Mobile navigation toggle ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('mobile-menu-toggle');
    const drawer = document.getElementById('mobile-menu');

    if (!toggle || !drawer) return;

    toggle.addEventListener('click', () => {
        const open = drawer.classList.toggle('hidden');
        toggle.setAttribute('aria-expanded', String(!open));
    });

    drawer.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => drawer.classList.add('hidden'));
    });
});

// ── Dropdown menus ────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dropdown-trigger]').forEach((trigger) => {
        const target = document.querySelector(trigger.dataset.dropdownTrigger);
        if (!target) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !target.classList.contains('hidden');
            document.querySelectorAll('[data-dropdown]').forEach((d) => d.classList.add('hidden'));
            if (!isOpen) target.classList.remove('hidden');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown]').forEach((d) => d.classList.add('hidden'));
    });
});
