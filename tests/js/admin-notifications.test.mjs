import test from 'node:test';
import assert from 'node:assert/strict';
import { renderLeadNotifications } from '../../public/assets/transglobe/js/admin-notifications.js';

function element(tag) {
    return {
        tag, children: [], textContent: '',
        set innerHTML(value) { throw new Error('Untrusted HTML insertion'); },
        appendChild(child) { this.children.push(child); },
        append(...children) { this.children.push(...children); },
    };
}
const document = { createElement: element };

test('notification names and metadata render as text, and query values are encoded', () => {
    const panel = element('div');
    renderLeadNotifications(panel, [{
        full_name: '<img src=x onerror=alert(1)>', destination: '<script>bad()</script>',
        source_page: '<b>source</b>', email: 'student@example.com&admin=1',
    }], document);
    const link = panel.children[1];
    assert.equal(link.children[0].textContent, '<img src=x onerror=alert(1)>');
    assert.equal(link.children[1].textContent, '<script>bad()</script> · <b>source</b>');
    assert.equal(link.children[0].children.length, 0);
    assert.equal(link.href, '/admin/enquiries?q=student%40example.com%26admin%3D1');
});

test('empty notifications show a useful message', () => {
    const panel = element('div');
    renderLeadNotifications(panel, [], document);
    assert.equal(panel.children[1].textContent, 'No enquiries yet.');
});
