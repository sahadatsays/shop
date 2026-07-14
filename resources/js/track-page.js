export const initTrackPage = () => {
    const page = document.querySelector('[data-track]');

    if (!page) {
        return;
    }

    const copyButton = page.querySelector('[data-copy-tracking]');
    const trackingNumber = page.querySelector('[data-tracking-number]');
    const copyIcon = page.querySelector('[data-copy-icon]');
    const copiedIcon = page.querySelector('[data-copied-icon]');

    copyButton?.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(trackingNumber.textContent.replaceAll(' ', ''));
        } catch {
            return;
        }

        copyIcon.hidden = true;
        copiedIcon.hidden = false;
        copyButton.setAttribute('aria-label', 'Tracking number copied');

        setTimeout(() => {
            copyIcon.hidden = false;
            copiedIcon.hidden = true;
            copyButton.setAttribute('aria-label', 'Copy tracking number');
        }, 1600);
    });
};
