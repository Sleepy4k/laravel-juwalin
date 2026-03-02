/**
 * Confirm dialogs.
 * Handles: [data-confirm] attribute on buttons/links to show a native confirm().
 * Usage: <button data-confirm="Yakin ingin menghapus?">Hapus</button>
 *        <form data-confirm-form="Konfirmasi?"> ... </form>
 */
document.addEventListener('DOMContentLoaded', () => {
    // Element click confirmation
    document.querySelectorAll('[data-confirm]').forEach((el) => {
        el.addEventListener('click', (e) => {
            if (!window.confirm(el.dataset.confirm ?? 'Apakah Anda yakin?')) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        });
    });

    // Form submit confirmation
    document.querySelectorAll('[data-confirm-form]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!window.confirm(form.dataset.confirmForm ?? 'Apakah Anda yakin?')) {
                e.preventDefault();
            }
        });
    });
});
