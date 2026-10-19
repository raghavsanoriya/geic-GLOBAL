export function hasReviewEdits(root, document) {
    if (document.activeElement?.closest?.('.review-decision, .review-filters')) return true;
    return Array.from(root.querySelectorAll('.review-decision input:not([type="hidden"]), .review-decision select, .review-filters input:not([type="hidden"])'))
        .some(input => {
            if (input.tagName === 'SELECT') {
                const initial = Array.from(input.options).find(option => option.defaultSelected)?.value ?? input.options[0]?.value ?? '';
                return input.value !== initial;
            }
            return input.value !== input.defaultValue;
        });
}

export function startDuplicateAutoFetch(root, environment = globalThis) {
    const { document, window, fetch, DOMParser, AbortController, setTimeout, clearTimeout } = environment;
    const status = root.querySelector('[data-fetch-status]');
    const refreshButton = root.querySelector('[data-fetch-now]');
    const toggleButton = root.querySelector('[data-fetch-toggle]');
    const feed = root.querySelector('[data-review-feed]');
    if (!status || !refreshButton || !toggleButton || !feed) return null;
    let paused = false;
    let stopped = false;
    let busy = false;
    let failures = 0;
    let timer;
    let request;

    const say = message => { status.textContent = message; };
    const schedule = () => {
        clearTimeout(timer);
        if (!stopped && !paused) timer = setTimeout(() => refresh(), Math.min(120000, 15000 * (2 ** failures)));
    };
    const refresh = async () => {
        if (stopped || paused || busy) return;
        clearTimeout(timer);
        if (document.hidden || window.navigator.onLine === false) {
            say(document.hidden ? 'Auto fetch is waiting while this tab is hidden.' : 'You are offline. Auto fetch will resume when connected.');
            schedule();
            return;
        }
        busy = true;
        refreshButton.disabled = true;
        request = new AbortController();
        const timeout = setTimeout(() => request.abort(), 8000);
        try {
            const url = new URL(window.location.href);
            url.hash = '';
            url.searchParams.set('revision', feed.dataset.revision);
            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
                redirect: 'error',
                signal: request.signal,
            });
            if (response.status === 401 || response.status === 403 || response.status === 419) {
                stopped = true;
                say('Your session or access changed. Sign in again before fetching this queue.');
                return;
            }
            if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) throw new Error('Queue unavailable');
            const data = await response.json();
            if (!/^[a-f0-9]{64}$/.test(data.revision)) throw new Error('Invalid revision');
            failures = 0;
            if (stopped || paused || document.hidden) return;
            if (data.revision !== feed.dataset.revision) {
                if (hasReviewEdits(root, document)) {
                    say('New queue updates are available. Finish or clear your edits, then choose Fetch now.');
                    return;
                }
                if (typeof data.html !== 'string') throw new Error('Missing queue');
                const incoming = new DOMParser().parseFromString(data.html, 'text/html').querySelector('[data-review-feed]');
                if (!incoming || incoming.querySelector('script, iframe, object, embed')) throw new Error('Invalid queue');
                const scrollPosition = window.scrollY;
                feed.replaceChildren(...incoming.childNodes);
                feed.dataset.revision = data.revision;
                window.scrollTo({ top: scrollPosition, behavior: 'instant' });
                say('Queue updated automatically. Your filters and page are unchanged.');
            } else {
                say('Auto fetch is on. Queue is up to date.');
            }
            root.dataset.lastFetch = new Date().toISOString();
        } catch {
            if (!stopped && !paused) {
                failures = Math.min(failures + 1, 3);
                say('Could not fetch updates. Your queue and edits are safe; retrying automatically.');
            }
        } finally {
            clearTimeout(timeout);
            request = null;
            busy = false;
            refreshButton.disabled = stopped || paused;
            schedule();
        }
    };
    const stop = () => {
        stopped = true;
        clearTimeout(timer);
        request?.abort();
    };
    refreshButton.addEventListener('click', refresh);
    toggleButton.addEventListener('click', () => {
        if (stopped) return;
        paused = !paused;
        toggleButton.setAttribute('aria-pressed', String(paused));
        toggleButton.textContent = paused ? 'Resume auto fetch' : 'Pause auto fetch';
        refreshButton.disabled = paused || busy;
        if (paused) {
            clearTimeout(timer);
            say('Auto fetch is paused.');
        } else {
            say('Auto fetch resumed. Checking for updates.');
            void refresh();
        }
    });
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && !paused && !stopped) void refresh();
    });
    window.addEventListener('online', () => { if (!paused && !stopped) void refresh(); });
    window.addEventListener('pagehide', stop, { once: true });
    schedule();
    return { refresh, stop };
}

if (typeof document !== 'undefined') {
    const root = document.querySelector('[data-duplicate-auto-fetch]');
    if (root) startDuplicateAutoFetch(root, {
        document,
        window,
        fetch: window.fetch.bind(window),
        DOMParser: window.DOMParser,
        AbortController: window.AbortController,
        setTimeout: window.setTimeout.bind(window),
        clearTimeout: window.clearTimeout.bind(window),
    });
}
