export function renderLeadNotifications(panel, leads, document) {
    const heading = document.createElement('div');
    heading.className = 'notification-panel__head';
    heading.textContent = 'Recent enquiries';
    panel.appendChild(heading);
    for (const lead of leads) {
        const item = document.createElement('a');
        item.className = 'notification-item';
        item.href = '/admin/enquiries?q=' + encodeURIComponent(lead.email || '');
        const name = document.createElement('strong');
        name.textContent = lead.full_name || 'New enquiry';
        const detail = document.createElement('small');
        detail.textContent = (lead.destination || 'Destination not specified') + ' · ' + (lead.source_page || 'Path not captured');
        item.append(name, detail);
        panel.appendChild(item);
    }
    if (!leads.length) {
        const empty = document.createElement('div');
        empty.className = 'notification-empty';
        empty.textContent = 'No enquiries yet.';
        panel.appendChild(empty);
    }
}
