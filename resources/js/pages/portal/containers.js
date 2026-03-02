/**
 * Portal Containers page JS.
 * Handles: status polling for provisioning containers.
 */
document.addEventListener('DOMContentLoaded', () => {
    const pollingItems = document.querySelectorAll('[data-poll-status]');

    if (!pollingItems.length) return;

    async function pollStatus(el) {
        const url = el.dataset.pollStatus;
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) return;

            const data = await response.json();

            if (data.status && data.status !== 'provisioning') {
                // Reload page to reflect new state
                window.location.reload();
            }
        } catch {
            // silently ignore network errors during polling
        }
    }

    // Poll every 5 seconds
    setInterval(() => {
        pollingItems.forEach(pollStatus);
    }, 5000);
});
