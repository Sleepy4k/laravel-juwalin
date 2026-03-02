/**
 * Admin Settings page JS.
 * Handles: live app-name preview in header.
 */
document.addEventListener('DOMContentLoaded', () => {
    const nameInput   = document.getElementById('app_name');
    const namePreviews = document.querySelectorAll('[data-preview="app_name"]');

    if (!nameInput) return;

    nameInput.addEventListener('input', () => {
        namePreviews.forEach((el) => {
            el.textContent = nameInput.value || 'ADIP';
        });
    });

    // Maintenance toggle warning
    const maintenanceToggle = document.getElementById('maintenance_mode');
    const maintenanceWarn   = document.getElementById('maintenance-warn');

    if (maintenanceToggle && maintenanceWarn) {
        maintenanceToggle.addEventListener('change', () => {
            maintenanceWarn.classList.toggle('hidden', !maintenanceToggle.checked);
        });
    }
});
