import test from 'node:test';
import assert from 'node:assert/strict';
import { bindCrmStudentForm, updateCrmRecordOverview } from '../../public/assets/transglobe/js/crm-student-form.js';

function harness() {
    const handlers = new Map();
    const submit = { disabled: false };
    const errors = { hidden: true, textContent: '', focus() { this.focused = true; } };
    const status = { textContent: '' };
    const reload = { hidden: true };
    const version = { value: '1' };
    const fields = [{ disabled: false }, { disabled: true }];
    const form = {
        action: 'https://geic.test/admin/crm/students/7',
        dataset: { studentId: '7' },
        elements: { namedItem: name => name === 'version' ? version : null },
        querySelector: selector => ({
            'button[type="submit"]': submit, '[data-crm-errors]': errors,
            '[data-crm-status]': status, '[data-crm-reload]': reload,
        })[selector],
        querySelectorAll: () => fields,
        setAttribute() {}, removeAttribute() {},
        addEventListener: (name, callback) => handlers.set(name, callback),
    };
    const h = {
        form, handlers, submit, errors, status, reload, version, fields, calls: [],
        response: {
            status: 200, ok: true, headers: { get: () => 'application/json' },
            json: async () => ({ data: { id: 7, version: 2 } }),
        },
    };
    h.controller = bindCrmStudentForm(form, {
        fetch: async (url, options) => {
            h.calls.push({ url, options });
            assert.equal(submit.disabled, true);
            if (h.fail) throw new Error('network');
            return h.response;
        },
        FormData: class { constructor() { assert.equal(fields[0].disabled, false); } },
        AbortController,
        setTimeout: () => 1, clearTimeout() {},
        window: { addEventListener: (name, callback) => handlers.set(name, callback) },
    });
    h.save = () => h.controller.save({ preventDefault() {} });
    return h;
}

test('converted Rocket overview uses confirmed record values as text, not HTML', () => {
    const nodes = new Map(['title','avatar','status','field="priority"','field="due-ist"','field="assignees"'].map(key => [key, {textContent:''}]));
    const overview = {querySelector(selector) {
        return nodes.get(selector.replace('[data-overview-', '').replace(/\]$/, ''));
    }};
    const form = {closest: () => ({querySelector: () => overview}), elements: {namedItem: name => ({
        options: [{value:'completed', textContent:'Completed'}, {value:'high', textContent:'High'}]
    })}};
    updateCrmRecordOverview(form, {title:'<script>literal title</script>',status:'completed',priority:'high',assignees:[{name:'Assigned staff'}],due_at:'2026-10-21T10:30:00.000000Z'});
    assert.equal(nodes.get('title').textContent, '<script>literal title</script>');
    assert.equal(nodes.get('status').textContent, 'Completed');
    assert.equal(nodes.get('field="priority"').textContent, 'High');
    assert.equal(nodes.get('field="assignees"').textContent, 'Assigned staff');
    assert.match(nodes.get('field="due-ist"').textContent, /21 Oct 2026/);
});

test('application overview resolves configured status labels instead of exposing reference IDs', () => {
    const status = {textContent:''};
    const course = {textContent:''};
    const overview = {querySelector: selector => ({'[data-overview-status]':status,'[data-overview-field="course"]':course})[selector]};
    const form = {closest: () => ({querySelector: () => overview}), elements: {namedItem: name => name === 'status_id' ? {options:[{value:'42',textContent:'Offer received'}]} : null}};
    updateCrmRecordOverview(form, {status_id:42,course:'Updated program'});
    assert.equal(status.textContent,'Offer received');
    assert.equal(course.textContent,'Updated program');
});

test('saves without navigation and restores field permissions', async () => {
    const h = harness();
    h.handlers.get('input')();
    await h.save();
    assert.equal(h.version.value, '2');
    assert.match(h.status.textContent, /Profile saved/);
    assert.equal(h.submit.disabled, false);
    assert.deepEqual(h.fields.map(field => field.disabled), [false, true]);
    assert.equal(h.calls[0].options.credentials, 'same-origin');
    assert.equal(h.calls[0].options.redirect, 'error');
    let blocked = false;
    h.handlers.get('beforeunload')({ preventDefault() { blocked = true; } });
    assert.equal(blocked, false);
});

test('confirmed lifecycle transfers reset the transfer checkbox and select the returned level', async () => {
    const h=harness();const transfer={checked:true};const stage={value:'lead'};const levelStatus={value:'7'};
    h.form.elements.namedItem=name=>({version:h.version,apply_status_transfer:transfer,stage,stage_status_id:levelStatus})[name];
    h.response.json=async()=>({data:{id:7,version:2,stage:'application',stage_status_id:null}});
    await h.save();
    assert.equal(stage.value,'application');assert.equal(levelStatus.value,'');assert.equal(transfer.checked,false);
});

test('confirmed explicit application mapping updates its version and clears repeat intent', async () => {
    const h=harness();const mapped={checked:true};const applicationVersion={value:'1'};
    h.form.elements.namedItem=name=>({version:h.version,apply_linked_application_status:mapped,application_version:applicationVersion})[name];
    h.response.json=async()=>({data:{id:7,version:2,linked_application_version:3}});
    await h.save();assert.equal(applicationVersion.value,'3');assert.equal(mapped.checked,false);
});

for (const code of [401, 403, 404, 409, 419, 422, 500]) {
    test('keeps edits and version on failure ' + code, async () => {
        const h = harness();
        h.handlers.get('input')();
        h.response.status = code;
        h.response.ok = false;
        h.response.json = async () => ({ errors: { phone: ['Check phone <script>'] } });
        await h.save();
        assert.equal(h.version.value, '1');
        assert.equal(h.errors.hidden, false);
        assert.equal(h.errors.focused, true);
        assert.equal(h.reload.hidden, code !== 409);
        assert.equal(h.submit.disabled, false);
        let blocked = false;
        h.handlers.get('beforeunload')({ preventDefault() { blocked = true; } });
        assert.equal(blocked, true);
    });
}

test('network failure and untrusted success cannot confirm a save', async () => {
    for (const payload of [null, { id: 99, version: 2 }, { id: 7, version: 0 }]) {
        const h = harness();
        h.fail = payload === null;
        h.response.json = async () => ({ data: payload });
        await h.save();
        assert.match(h.errors.textContent, /could not be confirmed/);
        assert.equal(h.version.value, '1');
    }
});

test('clears a remark only after confirmed save and uses the record-specific message', async () => {
    const h = harness();
    const remark = { value: 'Call completed' };
    h.form.elements.namedItem = name => ({ version: h.version, remark })[name];
    h.form.dataset.savedMessage = 'Follow-up saved.';
    await h.save();
    assert.equal(remark.value, '');
    assert.equal(h.status.textContent, 'Follow-up saved.');
    remark.value = 'Keep unsaved remark';
    h.fail = true;
    await h.save();
    assert.equal(remark.value, 'Keep unsaved remark');
});
