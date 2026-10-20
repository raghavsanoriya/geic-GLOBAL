export function setupCrmFullscreen(document) {
    const button = document.querySelector('[data-crm-fullscreen]');
    const feedback = document.querySelector('[data-crm-fullscreen-feedback]');
    if (!button || !document.fullscreenEnabled || !document.documentElement.requestFullscreen) return;
    button.hidden = false;
    const sync = () => {
        const active = Boolean(document.fullscreenElement);
        const label = active ? 'Exit fullscreen' : 'Enter fullscreen';
        button.setAttribute('aria-pressed', String(active));
        button.setAttribute('aria-label', label);
        button.title = label;
    };
    document.addEventListener('fullscreenchange', sync);
    button.addEventListener('click', async () => {
        if (button.disabled) return;
        button.disabled = true;
        if (feedback) feedback.textContent = '';
        try {
            if (document.fullscreenElement) await document.exitFullscreen();
            else await document.documentElement.requestFullscreen();
        } catch {
            if (feedback) feedback.textContent = 'Fullscreen is unavailable in this browser. You can continue using the normal view.';
        } finally {
            button.disabled = false;
            sync();
        }
    });
    sync();
}
