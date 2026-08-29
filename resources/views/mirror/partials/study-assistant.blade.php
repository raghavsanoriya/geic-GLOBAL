<style>
    .tg-assistant-launcher { position: fixed; z-index: 1200; right: 24px; bottom: 24px; display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; padding: 8px; border: 0; border-radius: 50%; background: #fff; box-shadow: 0 14px 32px rgba(14,33,69,.28); cursor: pointer; transition: transform .2s ease, background-color .2s ease; }
    .tg-assistant-launcher:hover { background: #f5f8f9; transform: translateY(-3px); }
    .tg-assistant-launcher:focus-visible, .tg-assistant-close:focus-visible, .tg-assistant-send:focus-visible, .tg-assistant-chip:focus-visible { outline: 3px solid #F3951E; outline-offset: 3px; }
    .tg-assistant-launcher img { width: 100%; height: 100%; border-radius: 50%; background: #fff; object-fit: contain; }
    .tg-assistant-panel { position: fixed; z-index: 1201; right: 24px; bottom: 100px; display: flex; flex-direction: column; width: min(390px, calc(100vw - 32px)); max-height: min(680px, calc(100vh - 128px)); overflow: hidden; border: 1px solid rgba(14,33,69,.14); border-radius: 22px; background: #fff; box-shadow: 0 24px 70px rgba(14,33,69,.25); opacity: 0; pointer-events: none; transform: translateY(14px) scale(.98); transform-origin: bottom right; transition: opacity .2s ease, transform .2s ease; }
    .tg-assistant-panel.is-open { opacity: 1; pointer-events: auto; transform: translateY(0) scale(1); }
    .tg-assistant-head { display: flex; align-items: center; gap: 12px; padding: 16px 18px; color: #fff; background: #0e2145; }
    .tg-assistant-head img { width: 40px; height: 40px; padding: 4px; border-radius: 50%; background: #fff; }
    .tg-assistant-head h2 { margin: 0; color: #fff; font-size: 16px; line-height: 1.25; }
    .tg-assistant-head p { margin: 3px 0 0; color: rgba(255,255,255,.72); font-size: 12px; }
    .tg-assistant-close { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; margin-left: auto; border: 0; border-radius: 10px; color: #fff; background: transparent; cursor: pointer; font-size: 25px; line-height: 1; }
    .tg-assistant-body { display: flex; min-height: 240px; flex: 1; flex-direction: column; gap: 12px; padding: 16px; overflow-y: auto; background: #f5f8f9; }
    .tg-assistant-message { max-width: 88%; padding: 11px 13px; border-radius: 15px; color: #0e2145; font-size: 14px; line-height: 1.5; white-space: pre-wrap; }
    .tg-assistant-message--assistant { align-self: flex-start; border-bottom-left-radius: 5px; background: #fff; box-shadow: 0 4px 14px rgba(14,33,69,.07); }
    .tg-assistant-message--user { align-self: flex-end; border-bottom-right-radius: 5px; color: #fff; background: #E31E24; }
    .tg-assistant-message--error { color: #8b1820; background: #fff0f0; }
    .tg-assistant-message--typing { display: inline-flex; align-items: center; gap: 4px; color: #71819a; }
    .tg-assistant-message--typing span { width: 5px; height: 5px; border-radius: 50%; background: currentColor; animation: tg-assistant-bounce 1s infinite ease-in-out; }
    .tg-assistant-message--typing span:nth-child(2) { animation-delay: .15s; }
    .tg-assistant-message--typing span:nth-child(3) { animation-delay: .3s; }
    @keyframes tg-assistant-bounce { 0%, 60%, 100% { opacity: .35; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-3px); } }
    .tg-assistant-chips { display: flex; flex-wrap: wrap; gap: 7px; margin-top: auto; }
    .tg-assistant-chip { padding: 8px 10px; border: 1px solid #dce4ee; border-radius: 999px; color: #0e2145; background: #fff; cursor: pointer; font: inherit; font-size: 12px; }
    .tg-assistant-chip:hover { border-color: #F3951E; }
    .tg-assistant-form { position: relative; display: flex; align-items: center; gap: 8px; padding: 12px; border-top: 1px solid #e6ebf2; background: #fff; }
    .tg-assistant-input { min-width: 0; min-height: 44px; flex: 1; padding: 11px 12px; border: 1px solid #dce4ee; border-radius: 12px; color: #0e2145; background: #fff; font: inherit; font-size: 14px; line-height: 1.35; }
    .tg-assistant-input:focus { border-color: #2563eb; outline: 2px solid rgba(37,99,235,.2); }
    .tg-assistant-send { display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; flex: 0 0 44px; border: 0; border-radius: 12px; color: #fff; background: #E31E24; cursor: pointer; }
    .tg-assistant-send:disabled { opacity: .55; cursor: wait; }
    .tg-assistant-send svg { width: 20px; height: 20px; }
    .tg-assistant-note { padding: 0 14px 10px; color: #71819a; background: #fff; font-size: 10px; line-height: 1.4; }
    .tg-assistant-visually-hidden { position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0, 0, 0, 0) !important; white-space: nowrap !important; border: 0 !important; }
    @media (max-width: 560px) {
        .tg-assistant-launcher { right: 16px; bottom: max(16px, env(safe-area-inset-bottom)); width: 58px; height: 58px; }
        .tg-assistant-panel { right: 12px; bottom: 86px; width: calc(100vw - 24px); max-height: calc(100vh - 106px); border-radius: 18px; }
        /* The homepage has a fixed 72px bottom navigation bar. Keep the assistant in its own safe zone above it. */
        body.home-page .tg-assistant-launcher { bottom: calc(92px + env(safe-area-inset-bottom)); }
        body.home-page .tg-assistant-panel { bottom: calc(158px + env(safe-area-inset-bottom)); max-height: calc(100vh - 178px - env(safe-area-inset-bottom)); }
    }
    @media (prefers-reduced-motion: reduce) { .tg-assistant-launcher, .tg-assistant-panel { transition: none; } .tg-assistant-message--typing span { animation: none; } }
</style>

<button class="tg-assistant-launcher" id="tgAssistantLauncher" type="button" aria-label="Open Trans Globe study assistant" aria-expanded="false" aria-controls="tgAssistantPanel">
    <img src="{{ asset('assets/admin/trans-globe-indore-icon.svg') }}" alt="">
</button>

<section class="tg-assistant-panel" id="tgAssistantPanel" aria-label="Trans Globe study assistant" aria-hidden="true">
    <header class="tg-assistant-head">
        <img src="{{ asset('assets/admin/trans-globe-indore-icon.svg') }}" alt="">
        <div><h2>Trans Globe Study Assistant</h2><p>Ask about destinations, admissions and tests</p></div>
        <button class="tg-assistant-close" id="tgAssistantClose" type="button" aria-label="Close study assistant">&times;</button>
    </header>
    <div class="tg-assistant-body" id="tgAssistantMessages" aria-live="polite">
        <div class="tg-assistant-message tg-assistant-message--assistant">Hi! I can help you plan your study-abroad journey. What destination, course or intake are you exploring?</div>
        <div class="tg-assistant-chips" id="tgAssistantChips"></div>
    </div>
    <div class="tg-assistant-note">Guidance is general information. Requirements and visa rules should be confirmed with your university or counsellor.</div>
    <form class="tg-assistant-form" id="tgAssistantForm">
        <label class="tg-assistant-visually-hidden" for="tgAssistantInput">Your study-abroad question</label>
        <input class="tg-assistant-input" id="tgAssistantInput" type="text" maxlength="1200" autocomplete="off" placeholder="Ask your question…" required>
        <button class="tg-assistant-send" type="submit" aria-label="Send question">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 3 10.4 13.6M21 3l-6.7 18-3.9-7.4L3 9.7 21 3Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </form>
</section>

<script>
    (function () {
        const launcher = document.getElementById('tgAssistantLauncher');
        const panel = document.getElementById('tgAssistantPanel');
        const close = document.getElementById('tgAssistantClose');
        const form = document.getElementById('tgAssistantForm');
        const input = document.getElementById('tgAssistantInput');
        const messages = document.getElementById('tgAssistantMessages');
        const chips = document.getElementById('tgAssistantChips');
        const send = form?.querySelector('button[type="submit"]');
        const history = [];
        if (!launcher || !panel || !form || !input || !messages) return;

        const suggestionSets = {
            default: ['Build my study plan', 'Which country fits my profile?', 'How do scholarships work?'],
            planning: ['Build my study plan', 'When should I start?', 'What documents do I need?'],
            destinations: ['What are the entry requirements?', 'Compare Australia and the UK', 'Which city is best for students?'],
            tests: ['What score should I target?', 'How long should I prepare?', 'Book test-prep counselling'],
            scholarships: ['Where can I find merit awards?', 'Do I qualify for a scholarship?', 'When should I apply?'],
            visa: ['What documents are usually needed?', 'How much financial evidence is required?', 'Can a counsellor review my plan?'],
            costs: ['What is the total study budget?', 'Which destinations offer better value?', 'How can I find funding?'],
            services: ['How does counselling work?', 'Can you help with my SOP?', 'What happens after an offer?']
        };

        function renderSuggestions(topic) {
            const suggestions = suggestionSets[topic] || suggestionSets.default;
            chips.replaceChildren();
            suggestions.forEach((suggestion) => {
                const button = document.createElement('button');
                button.className = 'tg-assistant-chip';
                button.type = 'button';
                button.textContent = suggestion;
                button.addEventListener('click', () => { input.value = suggestion; form.requestSubmit(); });
                chips.appendChild(button);
            });
        }

        function topicFor(text) {
            const value = text.toLowerCase();
            if (value.includes('ielts') || value.includes('pte') || value.includes('toefl') || value.includes('test') || value.includes('score')) return 'tests';
            if (value.includes('scholarship') || value.includes('funding') || value.includes('award')) return 'scholarships';
            if (value.includes('visa') || value.includes('permit') || value.includes('financial evidence')) return 'visa';
            if (value.includes('cost') || value.includes('budget') || value.includes('fee') || value.includes('tuition')) return 'costs';
            if (value.includes('plan') || value.includes('roadmap') || value.includes('timeline') || value.includes('start')) return 'planning';
            if (value.includes('counselling') || value.includes('sop') || value.includes('admission') || value.includes('application')) return 'services';
            if (value.includes('country') || value.includes('destination') || value.includes('australia') || value.includes('uk') || value.includes('canada') || value.includes('usa')) return 'destinations';
            return 'default';
        }

        function toggle(open) {
            panel.classList.toggle('is-open', open);
            panel.setAttribute('aria-hidden', String(!open));
            launcher.setAttribute('aria-expanded', String(open));
            if (open) window.setTimeout(() => input.focus(), 120);
            else launcher.focus();
        }
        function addMessage(role, text, isError) {
            const item = document.createElement('div');
            item.className = 'tg-assistant-message tg-assistant-message--' + (isError ? 'error' : role);
            item.textContent = text;
            messages.insertBefore(item, chips);
            messages.scrollTop = messages.scrollHeight;
            if (!isError) history.push({ role: role, content: text });
        }
        function addTyping() {
            const item = document.createElement('div');
            item.className = 'tg-assistant-message tg-assistant-message--assistant tg-assistant-message--typing';
            item.setAttribute('role', 'status');
            item.setAttribute('aria-label', 'Assistant is typing');
            item.innerHTML = '<span></span><span></span><span></span>';
            messages.insertBefore(item, chips);
            messages.scrollTop = messages.scrollHeight;
            return item;
        }
        function guidedReply(question) {
            const text = question.toLowerCase();
            if (text.includes('study plan') || text.includes('plan my') || text.includes('roadmap')) return 'Let’s build your study-abroad plan. Start with your course, study level, academic results and target intake; compare destination, course and budget fit; prepare your test and documents; then confirm each university’s official requirements before applying. Share your destination, budget and intake for a tailored checklist.';
            if (text.includes('ielts') || text.includes('pte') || text.includes('toefl') || text.includes('test')) return 'The right English test depends on your destination, university and course. We can help you compare IELTS, PTE and TOEFL, set a target score and plan preparation around your intake. Which country and course are you targeting?';
            if (text.includes('scholarship') || text.includes('funding')) return 'Scholarships can be merit-based, course-specific or linked to a university and intake. Tell me your destination and study level, and I will suggest the best places to start looking.';
            if (text.includes('visa') || text.includes('permit')) return 'Visa evidence varies by destination and your personal circumstances. Which destination are you considering?';
            if (text.includes('cost') || text.includes('budget') || text.includes('fee')) return 'Your total budget depends on tuition, city, accommodation, insurance, travel and visa costs. Share your destination, course level and approximate budget so we can compare realistic options.';
            return 'I can help you explore destinations, courses, scholarships, English tests, admissions and visa preparation. Tell me your preferred country, study level and intended intake.';
        }
        function setBusy(busy) { send.disabled = busy; input.disabled = busy; send.setAttribute('aria-label', busy ? 'Sending question' : 'Send question'); }

        launcher.addEventListener('click', () => toggle(!panel.classList.contains('is-open')));
        close.addEventListener('click', () => toggle(false));
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && panel.classList.contains('is-open')) toggle(false); });
        renderSuggestions('default');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const question = input.value.trim();
            if (!question || send.disabled) return;
            addMessage('user', question);
            input.value = '';
            setBusy(true);
            const typing = addTyping();
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), 18000);
            try {
                const response = await fetch('{{ route('study-assistant.chat') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    signal: controller.signal,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ message: question, history: history.slice(0, -1).slice(-10) })
                });
                const payload = await response.json();
                if (!response.ok || !payload.reply) throw new Error(payload.message || 'Unable to reach the assistant');
                typing.remove();
                addMessage('assistant', payload.reply);
                renderSuggestions(topicFor(question + ' ' + payload.reply));
            } catch (error) {
                typing.remove();
                addMessage('assistant', guidedReply(question));
                renderSuggestions(topicFor(question));
            } finally { window.clearTimeout(timeout); setBusy(false); input.focus(); }
        });
    }());
</script>
