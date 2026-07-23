export const initAdminNotifications = () => {
    const root = document.querySelector('[data-admin-notifications]');

    if (! root) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const markRead = async (notificationId) => {
        await fetch(`/admin/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
    };

    root.querySelector('[data-admin-notifications-mark-all]')?.addEventListener('click', async (event) => {
        event.preventDefault();

        await fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        root.querySelectorAll('[data-admin-notification-item]').forEach((item) => {
            item.dataset.read = 'true';
            item.querySelector('[data-notification-dot]')?.classList.remove('bg-admin-brand');
            item.querySelector('[data-notification-dot]')?.classList.add('bg-admin-muted');
        });

        root.querySelector('[data-admin-notification-badge]')?.remove();
        event.currentTarget.disabled = true;
        window.adminToast?.push({ title: 'All notifications marked as read', type: 'success' });
    });

    root.querySelectorAll('[data-admin-notification-item]').forEach((item) => {
        item.addEventListener('click', async () => {
            const notificationId = item.dataset.notificationId;
            const actionUrl = item.dataset.actionUrl;

            if (item.dataset.read === 'false' && notificationId) {
                await markRead(notificationId);
                item.dataset.read = 'true';
                item.querySelector('[data-notification-dot]')?.classList.remove('bg-admin-brand');
                item.querySelector('[data-notification-dot]')?.classList.add('bg-admin-muted');
            }

            if (actionUrl) {
                window.location.href = actionUrl;
            }
        });
    });
};
