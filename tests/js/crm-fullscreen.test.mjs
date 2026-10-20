import test from 'node:test';
import assert from 'node:assert/strict';
import { setupCrmFullscreen } from '../../public/assets/transglobe/js/crm-fullscreen.js';

function fixture(enabled = true) {
    const attributes = {};
    const events = {};
    const button = { hidden: true, disabled: false, setAttribute: (key, value) => attributes[key] = value, addEventListener: (name, fn) => events[name] = fn };
    const feedback = { textContent: '' };
    const document = {
        fullscreenEnabled: enabled, fullscreenElement: null,
        querySelector: selector => selector === '[data-crm-fullscreen]' ? button : feedback,
        addEventListener: (name, fn) => events[name] = fn,
        documentElement: { requestFullscreen: async () => { document.fullscreenElement = {}; } },
        exitFullscreen: async () => { document.fullscreenElement = null; },
    };
    return { document, button, feedback, attributes, events };
}

test('fullscreen follows browser changes and exposes the current action accessibly', async () => {
    const state = fixture();
    setupCrmFullscreen(state.document);
    assert.equal(state.button.hidden, false);
    await state.events.click();
    assert.equal(state.attributes['aria-label'], 'Exit fullscreen');
    assert.equal(state.attributes['aria-pressed'], 'true');
    state.document.fullscreenElement = null;
    state.events.fullscreenchange();
    assert.equal(state.attributes['aria-pressed'], 'false');
});

test('unsupported fullscreen is hidden and a denied request gives safe feedback', async () => {
    const unsupported = fixture(false);
    setupCrmFullscreen(unsupported.document);
    assert.equal(unsupported.button.hidden, true);
    const denied = fixture();
    denied.document.documentElement.requestFullscreen = async () => { throw new Error('private browser error'); };
    setupCrmFullscreen(denied.document);
    await denied.events.click();
    assert.match(denied.feedback.textContent, /Fullscreen is unavailable/);
    assert.equal(denied.button.disabled, false);
    assert.equal(denied.attributes['aria-pressed'], 'false');
    assert.doesNotMatch(denied.feedback.textContent, /private browser error/);
});
