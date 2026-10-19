import test from 'node:test';
import assert from 'node:assert/strict';
import { hasReviewEdits, startDuplicateAutoFetch } from '../../public/assets/transglobe/js/duplicate-review.js';

const originalRevision = 'a'.repeat(64);
const changedRevision = 'b'.repeat(64);

function harness() {
    let id = 0;
    const timers = new Map();
    const handlers = new Map();
    const controls = [];
    const calls = [];
    const replacements = [];
    const button = () => ({ disabled: false, textContent: '', addEventListener(name, callback) { this[name] = callback; }, setAttribute(name, value) { this[name] = value; } });
    const refresh = button();
    const toggle = button();
    const status = { textContent: '' };
    const feed = { dataset: { revision: originalRevision }, replaceChildren(...nodes) { replacements.push(nodes); } };
    const document = { hidden: false, activeElement: null, addEventListener(name, callback) { handlers.set(name, callback); } };
    const window = {
        location: { href: 'https://geic.in/admin/duplicate-review?status=pending&q=Ali&page=2' },
        navigator: { onLine: true }, scrollY: 88,
        scrollTo(value) { this.lastScroll = value; },
        addEventListener(name, callback) { handlers.set(name, callback); },
    };
    const root = {
        dataset: {},
        querySelector(selector) { return { '[data-fetch-status]': status, '[data-fetch-now]': refresh, '[data-fetch-toggle]': toggle, '[data-review-feed]': feed }[selector]; },
        querySelectorAll() { return controls; },
    };
    const incoming = { childNodes: ['safe rendered queue'], querySelector() { return null; } };
    const state = {
        root, feed, document, window, controls, calls, replacements, status, refresh, toggle, timers, handlers,
        response: { status: 200, ok: true, headers: { get: () => 'application/json' }, json: async () => ({ revision: changedRevision, unchanged: false, html: '<div data-review-feed>Updated</div>' }) },
    };
    state.environment = {
        document, window, AbortController,
        fetch: async (url, options) => { calls.push({ url, options }); if (state.fail) throw new Error('network'); return state.response; },
        DOMParser: class { parseFromString() { return { querySelector: () => incoming }; } },
        setTimeout(callback, delay) { const key = ++id; timers.set(key, { callback, delay }); return key; },
        clearTimeout(key) { timers.delete(key); },
    };
    state.controller = startDuplicateAutoFetch(root, state.environment);
    return state;
}

test('fetches the same private queue, preserves filters, updates in place, and keeps scroll', async () => {
    const h = harness();
    assert.equal([...h.timers.values()][0].delay, 15000);
    await h.controller.refresh();
    const url = new URL(h.calls[0].url);
    assert.equal(url.searchParams.get('page'), '2');
    assert.equal(url.searchParams.get('q'), 'Ali');
    assert.equal(url.searchParams.get('status'), 'pending');
    assert.equal(url.searchParams.get('revision'), originalRevision);
    assert.equal(h.calls[0].options.credentials, 'same-origin');
    assert.equal(h.calls[0].options.cache, 'no-store');
    assert.equal(h.calls[0].options.redirect, 'error');
    assert.equal(h.feed.dataset.revision, changedRevision);
    assert.equal(h.replacements.length, 1);
    assert.equal(h.window.lastScroll.top, 88);
    assert.match(h.status.textContent, /updated automatically/);
});

test('does not redraw unchanged data', async () => {
    const h = harness();
    h.response.json = async () => ({ revision: originalRevision, unchanged: true });
    await h.controller.refresh();
    assert.equal(h.replacements.length, 0);
    assert.match(h.status.textContent, /up to date/);
});

test('keeps unsaved notes and queued data until edits are cleared', async () => {
    const h = harness();
    const note = { tagName: 'INPUT', value: 'Keep my note', defaultValue: '' };
    h.controls.push(note);
    await h.controller.refresh();
    assert.equal(h.replacements.length, 0);
    assert.equal(note.value, 'Keep my note');
    assert.match(h.status.textContent, /Finish or clear your edits/);
    note.value = '';
    await h.controller.refresh();
    assert.equal(h.replacements.length, 1);
});

test('protects focused review fields and changed match selections', () => {
    const h = harness();
    h.document.activeElement = { closest: () => ({}) };
    assert.equal(hasReviewEdits(h.root, h.document), true);
    h.document.activeElement = null;
    h.controls.push({ tagName: 'SELECT', value: '22', options: [{ value: '', defaultSelected: true }, { value: '22' }] });
    assert.equal(hasReviewEdits(h.root, h.document), true);
});

test('hidden tabs and offline clients do not send requests', async () => {
    const h = harness();
    h.document.hidden = true;
    await h.controller.refresh();
    h.document.hidden = false;
    h.window.navigator.onLine = false;
    await h.controller.refresh();
    assert.equal(h.calls.length, 0);
});

test('backs off on failures without clearing the queue or editing controls', async () => {
    const h = harness();
    h.fail = true;
    await h.controller.refresh();
    assert.equal([...h.timers.values()][0].delay, 30000);
    assert.equal(h.replacements.length, 0);
    assert.equal(h.refresh.disabled, false);
    assert.match(h.status.textContent, /edits are safe/);
    await h.controller.refresh();
    assert.equal([...h.timers.values()][0].delay, 60000);
});

test('expired or forbidden sessions stop automatic requests', async () => {
    const h = harness();
    h.response = { status: 401 };
    await h.controller.refresh();
    assert.equal(h.refresh.disabled, true);
    assert.equal(h.timers.size, 0);
    await h.controller.refresh();
    assert.equal(h.calls.length, 1);
});

test('can pause and resume and will not duplicate overlapping requests', async () => {
    const h = harness();
    h.toggle.click();
    await h.controller.refresh();
    assert.equal(h.calls.length, 0);
    assert.equal(h.toggle['aria-pressed'], 'true');
    h.toggle.click();
    await new Promise(resolve => setImmediate(resolve));
    assert.equal(h.calls.length, 1);
    h.response.json = async () => {
        await h.controller.refresh();
        return { revision: originalRevision, unchanged: true };
    };
    await h.controller.refresh();
    assert.equal(h.calls.length, 2);
});

test('malformed snapshots never replace the visible queue', async () => {
    const h = harness();
    h.response.json = async () => ({ revision: '<script>', html: 'unsafe' });
    await h.controller.refresh();
    assert.equal(h.replacements.length, 0);
    assert.equal(h.feed.dataset.revision, originalRevision);
});

test('aborts a slow request after eight seconds and cancels timers on leaving', async () => {
    const h = harness();
    h.environment.fetch = (url, options) => new Promise((resolve, reject) => {
        options.signal.addEventListener('abort', () => reject(new Error('aborted')), { once: true });
    });
    h.controller.stop();
    h.controller = startDuplicateAutoFetch(h.root, h.environment);
    const pending = h.controller.refresh();
    const timeout = [...h.timers.values()].find(timer => timer.delay === 8000);
    timeout.callback();
    await pending;
    assert.match(h.status.textContent, /retrying automatically/);
    h.handlers.get('pagehide')();
    assert.equal(h.timers.size, 0);
});
