export function updateCrmRecordOverview(form, record) {
    const overview = form.closest?.('.rocket-detail-layout')?.querySelector('[data-crm-record-overview]');
    if (!overview) return;
    const write = (selector, value) => {
        const element = overview.querySelector(selector);
        if (element && typeof value === 'string') element.textContent = value || 'Not provided';
    };
    const optionLabel = (name, value) => {
        const select = form.elements.namedItem(name);
        return Array.from(select?.options ?? []).find(option => option.value === String(value))?.textContent;
    };
    const title = record.full_name ?? record.title;
    write('[data-overview-title]', title);
    if (typeof title === 'string') write('[data-overview-avatar]', Array.from(title)[0]?.toLocaleUpperCase());
    const statusField = record.lead_status !== undefined ? 'lead_status' : (record.status_id !== undefined ? 'status_id' : 'status');
    write('[data-overview-status]', optionLabel(statusField, record[statusField]));
    if (typeof record.stage === 'string') write('[data-overview-subtitle]', optionLabel('stage', record.stage));
    for (const field of ['email', 'phone']) write('[data-overview-field="'+field+'"]', record[field] ?? '');
    for (const field of ['course', 'intake', 'visa_type']) {
        if (typeof record[field] === 'string') write('[data-overview-field="'+field.replaceAll('_', '-')+'"]', record[field]);
    }
    write('[data-overview-field="priority"]', optionLabel('priority', record.priority));
    if (typeof record.is_active === 'boolean') write('[data-overview-field="activity"]', record.is_active ? 'Active' : 'Inactive');
    if (Array.isArray(record.assignees)) write('[data-overview-field="assignees"]', record.assignees.map(member => member.name).filter(name => typeof name === 'string').join(', '));
    if (typeof record.due_at === 'string' && Number.isFinite(Date.parse(record.due_at))) {
        write('[data-overview-field="due-ist"]', new Intl.DateTimeFormat('en-IN', { timeZone: 'Asia/Kolkata', dateStyle: 'medium', timeStyle: 'short' }).format(new Date(record.due_at)));
    }
}

export function bindCrmStudentForm(form, environment = globalThis) {
    const { fetch, FormData, AbortController, setTimeout, clearTimeout, window, onSaved } = environment;
    const submit = form.querySelector('button[type="submit"]');
    const errors = form.querySelector('[data-crm-errors]');
    const status = form.querySelector('[data-crm-status]');
    const reload = form.querySelector('[data-crm-reload]');
    const version = form.elements.namedItem('version');
    if (!submit || !errors || !status || !reload || !version) return null;
    let busy = false;
    let dirty = false;
    const showError = (message, conflict = false) => {
        errors.hidden = false;
        errors.textContent = message;
        reload.hidden = !conflict;
        errors.focus();
    };
    const save = async event => {
        event.preventDefault();
        if (busy) return;
        busy = true;
        const body = new FormData(form);
        const fields = Array.from(form.querySelectorAll('fieldset')).map(field => ({ field, disabled: field.disabled }));
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 8000);
        fields.forEach(({ field }) => { field.disabled = true; });
        submit.disabled = true;
        form.setAttribute('aria-busy', 'true');
        errors.hidden = true;
        reload.hidden = true;
        status.textContent = 'Saving profile…';
        try {
            const response = await fetch(form.action, {
                method: 'POST', body, credentials: 'same-origin', cache: 'no-store',
                redirect: 'error', headers: { Accept: 'application/json' }, signal: controller.signal,
            });
            if ([401, 403, 404, 419].includes(response.status)) {
                showError('Your session or case access changed. Your edits are still here; sign in again or ask an administrator for access.');
                return;
            }
            if (response.status === 409) {
                showError('Another staff member changed this profile. Your edits are still here and were not saved. Review them before reloading the latest profile.', true);
                return;
            }
            if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('Invalid response');
            const data = await response.json();
            if (response.status === 422) {
                const messages = Object.values(data.errors ?? {}).flat().filter(value => typeof value === 'string');
                showError(messages.join(' ') || 'Check your details and try saving again. Your edits have been kept.');
                return;
            }
            if (!response.ok || !Number.isSafeInteger(data.data?.version) || data.data.version < Number(version.value)
                || String(data.data.id) !== form.dataset.studentId) throw new Error('Unconfirmed save');
            version.value = String(data.data.version);
            const transfer = form.elements.namedItem('apply_status_transfer');
            if (transfer?.checked) {
                const stage = form.elements.namedItem('stage');
                const levelStatus = form.elements.namedItem('stage_status_id');
                if (stage && typeof data.data.stage === 'string') stage.value = data.data.stage;
                if (levelStatus) levelStatus.value = data.data.stage_status_id == null ? '' : String(data.data.stage_status_id);
                transfer.checked = false;
            }
            const linkedTransfer = form.elements.namedItem('apply_linked_application_status');
            const applicationVersion = form.elements.namedItem('application_version');
            if (applicationVersion && Number.isSafeInteger(data.data.linked_application_version)) applicationVersion.value=String(data.data.linked_application_version);
            if (linkedTransfer) linkedTransfer.checked=false;
            const remark = form.elements.namedItem('remark');
            if (remark) remark.value = '';
            dirty = false;
            status.textContent = form.dataset.savedMessage || 'Profile saved. Original submissions are unchanged.';
            try { updateCrmRecordOverview(form, data.data); onSaved?.(data); } catch { /* A refresh failure must not undo confirmed success. */ }
        } catch {
            showError('The save could not be confirmed. Your edits are still here. Check your connection; if a retry reports a newer version, reload and review that profile.');
        } finally {
            clearTimeout(timeout);
            fields.forEach(({ field, disabled }) => { field.disabled = disabled; });
            submit.disabled = false;
            form.removeAttribute('aria-busy');
            if (!errors.hidden) status.textContent = '';
            busy = false;
        }
    };
    form.addEventListener('submit', save);
    const markDirty = () => { dirty = true; };
    const beforeUnload = event => {
        if (!dirty && !busy) return;
        event.preventDefault();
        event.returnValue = '';
    };
    form.addEventListener('input', markDirty);
    form.addEventListener('change', markDirty);
    window?.addEventListener('beforeunload', beforeUnload);
    return {
        save, isDirty: () => dirty, isBusy: () => busy,
        destroy() {
            form.removeEventListener?.('submit', save);
            form.removeEventListener?.('input', markDirty);
            form.removeEventListener?.('change', markDirty);
            window?.removeEventListener?.('beforeunload', beforeUnload);
        },
    };
}

if (typeof document !== 'undefined') {
    const form = document.querySelector('[data-crm-student-form]');
    if (form) bindCrmStudentForm(form, {
        window, fetch: window.fetch.bind(window), FormData: window.FormData,
        AbortController: window.AbortController,
        setTimeout: window.setTimeout.bind(window), clearTimeout: window.clearTimeout.bind(window),
    });
}
