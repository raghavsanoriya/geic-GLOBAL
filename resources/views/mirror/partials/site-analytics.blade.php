<script>
(() => {
    const endpoint = @json(route('site-events.store'));
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token || navigator.webdriver) return;

    const params = new URLSearchParams(window.location.search);
    const campaign = {
        utm_source: params.get('utm_source') || sessionStorage.getItem('geic_utm_source'),
        utm_medium: params.get('utm_medium') || sessionStorage.getItem('geic_utm_medium'),
        utm_campaign: params.get('utm_campaign') || sessionStorage.getItem('geic_utm_campaign'),
    };
    Object.entries(campaign).forEach(([key, value]) => value && sessionStorage.setItem('geic_' + key, value));

    let sessionId = sessionStorage.getItem('geic_session_id');
    if (!sessionId) {
        sessionId = crypto.randomUUID ? crypto.randomUUID() : `${Date.now()}-${Math.random()}`;
        sessionStorage.setItem('geic_session_id', sessionId);
    }

    document.querySelectorAll('form[action*="enquire"], form[action*="hero-enquire"]').forEach((form) => {
        Object.entries({...campaign, analytics_session_id: sessionId}).forEach(([name, value]) => {
            if (!value || form.querySelector(`[name="${name}"]`)) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });
    });

    const send = (eventType, details = {}) => {
        const payload = JSON.stringify({
            event_type: eventType,
            path: window.location.pathname,
            referrer: document.referrer || null,
            session_id: sessionId,
            ...campaign,
            ...details,
        });
        fetch(endpoint, {
            method: 'POST',
            keepalive: true,
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
            body: payload,
        }).catch(() => {});
    };

    send('page_view');
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a,button');
        if (!link) return;
        const href = link instanceof HTMLAnchorElement ? link.href : null;
        const outbound = href && new URL(href, window.location.href).origin !== window.location.origin;
        const isCta = link.matches('.button,.sdn-button,.td-button,.tg-btn,.btn,[data-track-cta]') || /counsell|enquir|apply|contact|book|start/i.test(link.textContent || '');
        if (!outbound && !isCta) return;
        send(outbound ? 'outbound_click' : 'cta_click', {
            label: (link.textContent || link.getAttribute('aria-label') || '').trim().slice(0, 180) || null,
            target: href ? new URL(href, window.location.href).pathname : null,
        });
    }, {capture: true});
})();
</script>
