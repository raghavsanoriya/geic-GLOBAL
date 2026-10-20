import { bindCrmStudentForm } from './crm-student-form.js';

export function bindFollowUpList(root, environment = globalThis) {
    const { window, document, fetch, DOMParser, AbortController, setTimeout, clearTimeout } = environment;
    const dialog = root.querySelector('[data-follow-up-dialog]');
    const body = root.querySelector('[data-follow-up-body]');
    const closeButton = root.querySelector('[data-follow-up-close]');
    const list = root.querySelector('[data-follow-up-list]');
    const listStatus = root.querySelector('[data-follow-up-list-status]');
    if (!dialog?.showModal || !body || !closeButton || !list || !listStatus) return null;
    let editor = null;
    let opener = null;
    let activeUrl = null;
    let requestController = null;
    let revision = 0;

    const message = (text, error = false) => {
        const node = document.createElement('p');
        node.className = 'notice';
        node.textContent = text;
        node.setAttribute('role', error ? 'alert' : 'status');
        body.replaceChildren(node);
    };
    const requestHtml = async (url, controller = new AbortController()) => {
        const target = new URL(url, window.location.href);
        if (target.origin !== new URL(window.location.href).origin) throw new Error('Different origin');
        const timeout = setTimeout(() => controller.abort(), 8000);
        try {
            const response = await fetch(target.href, {
                credentials: 'same-origin', cache: 'no-store', redirect: 'error',
                headers: { Accept: 'text/html' }, signal: controller.signal,
            });
            if (!response.ok || !response.headers.get('content-type')?.includes('text/html')) throw new Error('Cannot load');
            return new DOMParser().parseFromString(await response.text(), 'text/html');
        } finally {
            clearTimeout(timeout);
        }
    };
    const canLeave = () => {
        if (editor?.isBusy()) {
            listStatus.textContent = 'Please wait for the current save to finish.';
            return false;
        }
        return !editor?.isDirty() || window.confirm('Discard the unsaved follow-up edits?');
    };
    const close = () => {
        if (!canLeave()) return false;
        revision++;
        requestController?.abort();
        editor?.destroy();
        editor = null;
        dialog.close();
        body.replaceChildren();
        if (opener?.isConnected) opener.focus();
        else root.querySelector('h1')?.focus();
        return true;
    };
    const refreshSaved = async (token, url) => {
        listStatus.textContent = 'Follow-up saved. Updating the list…';
        try {
            const [incomingList, incomingDetails] = await Promise.all([
                requestHtml(window.location.href), requestHtml(url),
            ]);
            const nextList = incomingList.querySelector('[data-follow-up-list]');
            if (!nextList) throw new Error('Missing list');
            list.replaceChildren(...nextList.childNodes);
            if (token === revision && dialog.open) {
                const nextHistory = incomingDetails.querySelector('[data-follow-up-history]');
                const currentHistory = body.querySelector('[data-follow-up-history]');
                if (nextHistory && currentHistory) currentHistory.replaceWith(nextHistory);
            }
            listStatus.textContent = 'Follow-up saved. List and remarks are up to date.';
        } catch {
            listStatus.textContent = 'Follow-up saved. Refresh the list to see the latest queue and remarks.';
        }
    };
    const open = async link => {
        if (!canLeave()) return;
        const token = ++revision;
        requestController?.abort();
        editor?.destroy();
        editor = null;
        opener = link;
        const url = new URL(link.href, window.location.href);
        url.searchParams.set('panel', '1');
        activeUrl = url.href;
        requestController = new AbortController();
        message('Loading follow-up details…');
        if (!dialog.open) dialog.showModal();
        try {
            const incoming = await requestHtml(activeUrl, requestController);
            if (token !== revision || !dialog.open) return;
            const panel = incoming.querySelector('[data-follow-up-panel]');
            if (!panel || panel.dataset.followUpId !== link.dataset.followUpId) throw new Error('Mismatched record');
            body.replaceChildren(panel);
            const form = body.querySelector('[data-crm-student-form]');
            if (form) {
                const binder = environment.bindForm ?? bindCrmStudentForm;
                editor = binder(form, {
                    ...environment,
                    onSaved: () => { void refreshSaved(token, url.href); },
                });
            }
            body.querySelector('input:not([type="hidden"]), select, textarea')?.focus();
        } catch {
            if (token !== revision || !dialog.open) return;
            message('Details could not be loaded. Check your connection and session, then retry or open the full page.', true);
            const retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'button button--quiet';
            retry.textContent = 'Retry loading details';
            retry.addEventListener('click', () => { void open(link); });
            const fallback = document.createElement('a');
            fallback.className = 'button button--quiet';
            fallback.textContent = 'Open full details page';
            fallback.href = link.href;
            body.append(retry, fallback);
        }
    };
    root.addEventListener('click', event => {
        const link = event.target.closest?.('a[data-follow-up-open]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button > 0) return;
        event.preventDefault();
        void open(link);
    });
    closeButton.addEventListener('click', close);
    dialog.addEventListener('cancel', event => { event.preventDefault(); close(); });
    return { open, close };
}

if (typeof document !== 'undefined') {
    const root = document.querySelector('[data-follow-up-workspace]');
    if (root) bindFollowUpList(root, {
        window, document, DOMParser, AbortController, FormData,
        fetch: window.fetch.bind(window), setTimeout: window.setTimeout.bind(window),
        clearTimeout: window.clearTimeout.bind(window),
    });
}
