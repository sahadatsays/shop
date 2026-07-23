export const initNotificationsPage = () => {
    const page = document.querySelector('[data-notifications]');

    if (! page) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const markAllUrl = page.dataset.markAllUrl;

    const markAllButton = page.querySelector('[data-notifications-mark-all]');
    const unreadBadge = page.querySelector('[data-notifications-unread-badge]');
    const summaryEl = page.querySelector('[data-notifications-summary]');
    const filterEmpty = page.querySelector('[data-notifications-filter-empty]');
    const allReadBanner = page.querySelector('[data-notifications-all-read]');
    const statusEl = page.querySelector('[data-notifications-status]');
    const navBadge = document.querySelector('[data-nav-notifications-badge]');

    let activeFilter = page.querySelector('[data-notifications-filter][aria-pressed="true"]')?.dataset.notificationsFilter ?? 'All';

    const getNotifications = () => [...page.querySelectorAll('[data-notification]')];

    const getUnreadCount = () => getNotifications().filter((n) => n.dataset.read === 'false').length;

    const filterTypeMap = {
        All: null,
        Orders: 'orders',
        Promotions: 'promotions',
        Account: 'account',
    };

    const persistMarkRead = async (notification) => {
        const url = notification.dataset.markReadUrl;

        if (! url) {
            return;
        }

        await fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
    };

    const updateBadge = (count) => {
        if (count > 0) {
            unreadBadge.hidden = false;
            unreadBadge.textContent = String(count);
        } else {
            unreadBadge.hidden = true;
        }

        if (navBadge) {
            if (count > 0) {
                navBadge.hidden = false;
                navBadge.textContent = String(count);
            } else {
                navBadge.hidden = true;
            }
        }

        markAllButton.disabled = count === 0;
        allReadBanner.hidden = count > 0;

        const total = getNotifications().length;
        summaryEl.textContent = `${count} unread · ${total} total`;
    };

    const markRead = async (notification) => {
        if (notification.dataset.read === 'true') {
            return;
        }

        await persistMarkRead(notification);

        notification.dataset.read = 'true';
        notification.classList.remove('is-unread');

        notification.querySelector('[data-unread-indicator]')?.remove();
        notification.querySelector('[data-notification-mark-read]')?.remove();

        const dot = notification.querySelector('[data-notification-dot]');
        dot?.classList.remove('bg-bronze-500');
        dot?.classList.add('bg-navy-200');

        const card = notification.querySelector('[data-notification-card]');
        card?.classList.remove('border-bronze-200/60', 'bg-bronze-50/30');
        card?.classList.add('border-navy-100');

        updateBadge(getUnreadCount());
    };

    const applyFilter = () => {
        const type = filterTypeMap[activeFilter];
        let visible = 0;

        page.querySelectorAll('[data-notification-group]').forEach((group) => {
            let groupVisible = 0;

            group.querySelectorAll('[data-notification]').forEach((notification) => {
                const show = ! type || notification.dataset.type === type;
                notification.hidden = ! show;
                groupVisible += show ? 1 : 0;
            });

            group.hidden = groupVisible === 0;
            visible += groupVisible;
        });

        filterEmpty.hidden = visible > 0;
        page.querySelector('[data-notifications-list]').hidden = visible === 0;
    };

    page.querySelectorAll('[data-notifications-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            activeFilter = button.dataset.notificationsFilter;

            page.querySelectorAll('[data-notifications-filter]').forEach((sibling) => {
                const active = sibling === button;
                sibling.setAttribute('aria-pressed', String(active));
                sibling.classList.toggle('bg-navy-900', active);
                sibling.classList.toggle('text-white', active);
                sibling.classList.toggle('shadow-soft', active);
                sibling.classList.toggle('bg-surface', ! active);
                sibling.classList.toggle('text-navy-700', ! active);
            });

            applyFilter();
        });
    });

    markAllButton?.addEventListener('click', async () => {
        if (! markAllUrl) {
            getNotifications().forEach(markRead);

            return;
        }

        await fetch(markAllUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        getNotifications().forEach((notification) => {
            notification.dataset.read = 'true';
            notification.classList.remove('is-unread');
            notification.querySelector('[data-unread-indicator]')?.remove();
            notification.querySelector('[data-notification-mark-read]')?.remove();
            notification.querySelector('[data-notification-dot]')?.classList.replace('bg-bronze-500', 'bg-navy-200');
            notification.querySelector('[data-notification-card]')?.classList.remove('border-bronze-200/60', 'bg-bronze-50/30');
            notification.querySelector('[data-notification-card]')?.classList.add('border-navy-100');
        });

        statusEl.textContent = 'All notifications marked as read.';
        statusEl.classList.add('text-green-700');
        updateBadge(0);
    });

    page.addEventListener('click', (event) => {
        const markBtn = event.target.closest('[data-notification-mark-read]');
        const notification = event.target.closest('[data-notification]');

        if (markBtn && notification) {
            event.preventDefault();
            markRead(notification);

            return;
        }

        if (notification && ! event.target.closest('a, button') && notification.dataset.read === 'false') {
            markRead(notification);
        }
    });

    page.querySelectorAll('[data-notification]').forEach((notification) => {
        notification.addEventListener('keydown', (event) => {
            if ((event.key === 'Enter' || event.key === ' ') && notification.dataset.read === 'false') {
                event.preventDefault();
                markRead(notification);
            }
        });
    });

    updateBadge(getUnreadCount());
    applyFilter();
};
