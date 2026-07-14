export const initSupportPage = () => {
    const page = document.querySelector('[data-support]');

    if (!page) {
        return;
    }

    const searchInput = page.querySelector('[data-support-search-input]');
    const searchResults = page.querySelector('[data-support-search-results]');
    const searchList = page.querySelector('[data-support-search-list]');
    const searchEmpty = page.querySelector('[data-support-search-empty]');
    const searchItems = [...page.querySelectorAll('[data-search-item]')];

    const chatPanel = page.querySelector('[data-support-chat]');
    const chatMessages = page.querySelector('[data-chat-messages]');
    const chatForm = page.querySelector('[data-chat-form]');
    const chatInput = page.querySelector('[data-chat-input]');

    const ticketForm = page.querySelector('[data-ticket-form]');
    const ticketsList = page.querySelector('[data-tickets-list]');
    const ticketLabel = page.querySelector('[data-ticket-label]');
    const ticketSubmit = page.querySelector('[data-ticket-submit]');
    const ticketStatus = page.querySelector('[data-ticket-status]');

    let faqFilter = 'all';
    let ticketCounter = 4830;

    const runSearch = (query) => {
        const q = query.trim().toLowerCase();

        if (q.length < 2) {
            searchResults.hidden = true;
            return;
        }

        const matches = searchItems.filter((item) => {
            const title = item.dataset.searchTitle;
            const category = item.dataset.searchCategory.toLowerCase();
            return title.includes(q) || category.includes(q);
        });

        searchList.innerHTML = '';

        if (matches.length === 0) {
            searchEmpty.hidden = false;
            searchList.hidden = true;
        } else {
            searchEmpty.hidden = true;
            searchList.hidden = false;

            matches.slice(0, 6).forEach((item) => {
                const li = document.createElement('li');
                li.innerHTML = `<a href="#faq" class="flex items-center justify-between gap-3 px-5 py-3.5 text-sm transition-colors duration-200 hover:bg-navy-50">
                    <span class="font-medium text-navy-900">${item.textContent}</span>
                    <span class="shrink-0 text-xs text-navy-400">${item.dataset.searchCategory}</span>
                </a>`;
                searchList.appendChild(li);
            });
        }

        searchResults.hidden = false;
    };

    searchInput?.addEventListener('input', () => runSearch(searchInput.value));

    page.querySelector('[data-support-search-form]')?.addEventListener('submit', (event) => {
        event.preventDefault();
        runSearch(searchInput.value);
    });

    document.addEventListener('click', (event) => {
        if (!searchResults?.contains(event.target) && event.target !== searchInput) {
            searchResults.hidden = true;
        }
    });

    page.querySelectorAll('[data-support-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.dataset.supportTrigger;

            if (target === 'chat') {
                chatPanel.hidden = false;
                chatInput?.focus();
            }

            if (target === 'tickets') {
                document.getElementById('tickets')?.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    page.querySelector('[data-support-chat-close]')?.addEventListener('click', () => {
        chatPanel.hidden = true;
    });

    const appendMessage = (text, outgoing = false) => {
        const wrap = document.createElement('div');
        wrap.className = outgoing ? 'flex justify-end' : 'flex gap-3';

        if (outgoing) {
            wrap.innerHTML = `<div class="max-w-[80%] rounded-xl rounded-tr-none bg-navy-900 px-4 py-3 text-sm text-white">${text}</div>`;
        } else {
            wrap.innerHTML = `<span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-olive-100 text-xs font-bold text-olive-700">VS</span>
                <div class="max-w-[80%] rounded-xl rounded-tl-none bg-canvas px-4 py-3 text-sm text-navy-700">${text}</div>`;
        }

        chatMessages.appendChild(wrap);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    chatForm?.addEventListener('submit', (event) => {
        event.preventDefault();

        const message = chatInput.value.trim();

        if (!message) {
            return;
        }

        appendMessage(message, true);
        chatInput.value = '';

        setTimeout(() => {
            appendMessage('Thanks for reaching out. A support specialist will be with you shortly. For order-specific questions, please include your order number.');
        }, 800);
    });

    page.querySelectorAll('[data-faq-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            faqFilter = button.dataset.faqFilter;

            page.querySelectorAll('[data-faq-filter]').forEach((sibling) => {
                const active = sibling === button;
                sibling.setAttribute('aria-pressed', String(active));
                sibling.classList.toggle('bg-navy-900', active);
                sibling.classList.toggle('text-white', active);
                sibling.classList.toggle('shadow-soft', active);
                sibling.classList.toggle('bg-surface', !active);
                sibling.classList.toggle('text-navy-700', !active);
            });

            let visible = 0;

            page.querySelectorAll('[data-faq-item]').forEach((item) => {
                const show = faqFilter === 'all' || item.dataset.faqCategory === faqFilter;
                item.hidden = !show;
                visible += show ? 1 : 0;
            });

            page.querySelector('[data-faq-empty]').hidden = visible > 0;
        });
    });

    ticketForm?.addEventListener('submit', (event) => {
        event.preventDefault();

        const subject = ticketForm.querySelector('#ticket-subject')?.value.trim();
        const category = ticketForm.querySelector('#ticket-category')?.value;
        const message = ticketForm.querySelector('#ticket-message')?.value.trim();

        if (!subject || !category || !message) {
            ticketStatus.textContent = 'Please complete all fields.';
            ticketStatus.classList.remove('text-green-700');
            return;
        }

        ticketSubmit.disabled = true;
        ticketLabel.textContent = 'Submitting\u2026';

        setTimeout(() => {
            const id = `TKT-${ticketCounter++}`;
            const card = document.createElement('article');
            card.dataset.ticketCard = '';
            card.className = 'rounded-card bg-surface p-5 shadow-soft';
            card.innerHTML = `<div class="flex flex-wrap items-start justify-between gap-2">
                    <p class="font-mono text-xs text-navy-400">${id}</p>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-bronze-100 text-bronze-800">Open</span>
                </div>
                <h3 class="mt-2 font-display text-base font-semibold text-navy-900">${subject}</h3>
                <p class="mt-1 text-xs text-navy-500">${category} · Just now</p>`;

            ticketsList.prepend(card);
            ticketForm.reset();
            ticketLabel.textContent = 'Submit ticket';
            ticketSubmit.disabled = false;
            ticketStatus.textContent = `Ticket ${id} created. We\'ll respond within 24 hours.`;
            ticketStatus.classList.add('text-green-700');
        }, 900);
    });
};
