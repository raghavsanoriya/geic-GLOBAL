import test from 'node:test';
import assert from 'node:assert/strict';
import { bindFollowUpList } from '../../public/assets/transglobe/js/follow-up-list.js';

function node() {
    return {
        children: [], handlers: new Map(), dataset: {}, textContent: '',
        setAttribute() {},
        replaceChildren(...children) { this.children = children; },
        append(...children) { this.children.push(...children); },
        addEventListener(name, fn) { this.handlers.set(name, fn); },
        querySelector() { return null; }, focus() { this.focused = true; },
    };
}
function harness() {
    const root = node(), dialog = node(), body = node(), closeButton = node(), list = node(), listStatus = node();
    dialog.open = false;
    dialog.showModal = () => { dialog.open = true; };
    dialog.close = () => { dialog.open = false; };
    root.querySelector = selector => ({
        '[data-follow-up-dialog]': dialog, '[data-follow-up-body]': body,
        '[data-follow-up-close]': closeButton, '[data-follow-up-list]': list,
        '[data-follow-up-list-status]': listStatus,
    })[selector] ?? null;
    const panel = node(), form = node();
    panel.dataset.followUpId = '7';
    body.querySelector = selector => selector === '[data-crm-student-form]' ? form : null;
    const h = {
        root, dialog, body, closeButton, list, listStatus, calls: [], dirty: false, busy: false,
        confirm: false, destroyed: 0, status: 200, contentType: 'text/html',
        link: { href: 'http://127.0.0.1:8091/admin/crm/follow-ups/7', dataset: { followUpId: '7' }, isConnected: true, focus() { this.focused = true; } },
    };
    h.controller = bindFollowUpList(root, {
        window: { location: { href: 'http://127.0.0.1:8091/admin/crm/follow-ups?queue=all' }, confirm: () => h.confirm },
        document: { createElement: () => node() }, AbortController,
        setTimeout: () => 1, clearTimeout() {},
        fetch: async (url, options) => {
            h.calls.push({ url, options });
            if (h.fail) throw new Error('network');
            return { ok: h.status === 200, headers: { get: () => h.contentType }, text: async () => 'server-rendered panel' };
        },
        DOMParser: class { parseFromString() { return { querySelector: selector => selector === '[data-follow-up-panel]' ? panel : null }; } },
        bindForm: () => ({ isDirty: () => h.dirty, isBusy: () => h.busy, destroy: () => { h.destroyed++; } }),
    });
    return h;
}

test('opens details privately in a modal without navigating the list', async () => {
    const h = harness();
    await h.controller.open(h.link);
    assert.equal(h.dialog.open, true);
    assert.match(h.calls[0].url, /\/7\?panel=1$/);
    assert.equal(h.calls[0].options.cache, 'no-store');
    assert.equal(h.calls[0].options.credentials, 'same-origin');
    assert.equal(h.calls[0].options.redirect, 'error');
    assert.equal(h.body.children[0].dataset.followUpId, '7');
    assert.equal(h.controller.close(), true);
    assert.equal(h.link.focused, true);
    assert.equal(h.destroyed, 1);
});

test('busy saves and unconfirmed discarded edits keep the modal open', async () => {
    const h = harness();
    await h.controller.open(h.link);
    h.busy = true;
    assert.equal(h.controller.close(), false);
    h.busy = false;
    h.dirty = true;
    assert.equal(h.controller.close(), false);
    assert.equal(h.dialog.open, true);
    h.confirm = true;
    assert.equal(h.controller.close(), true);
});

test('session, network and invalid response failures show retry and fallback without a form', async () => {
    for (const scenario of ['session', 'network', 'content']) {
        const h = harness();
        if (scenario === 'session') h.status = 403;
        if (scenario === 'network') h.fail = true;
        if (scenario === 'content') h.contentType = 'application/json';
        await h.controller.open(h.link);
        assert.equal(h.dialog.open, true);
        assert.match(h.body.children[0].textContent, /could not be loaded/);
        assert.equal(h.body.children[1].textContent, 'Retry loading details');
        assert.equal(h.body.children[2].href, h.link.href);
    }
});
