@include('mirror.partials.header', ['siteCms' => $cms ?? []])
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/'), 'mobileBackLabel' => 'Back to home'])

<style>
    .tg-planner{overflow:clip;background:#f4f7fb;color:#0e2145;font-family:'Plus Jakarta Sans',sans-serif}.tg-planner *{box-sizing:border-box}.tg-planner__wrap{width:min(1120px,calc(100% - 40px));margin-inline:auto}.tg-planner__hero{padding:92px 0 64px;background:linear-gradient(135deg,#0e2145,#173763 70%,#28537e);color:#fff}.tg-planner__eyebrow{display:inline-flex;align-items:center;gap:10px;color:#ff858a;font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}.tg-planner__eyebrow:before{width:26px;height:2px;background:currentColor;content:''}.tg-planner h1{max-width:720px;margin:14px 0 0;color:#fff;font-size:clamp(36px,5vw,62px);line-height:1.02;letter-spacing:-.055em}.tg-planner__lead{max-width:680px;margin:18px 0 0;color:rgba(255,255,255,.76);font-size:16px;line-height:1.65}.tg-planner__actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:26px}.tg-planner__button{display:inline-flex;min-height:46px;align-items:center;justify-content:center;padding:0 18px;border-radius:11px;background:#e31e24;color:#fff!important;font-size:12px;font-weight:800;text-decoration:none;box-shadow:0 10px 24px rgba(0,0,0,.14)}.tg-planner__button--ghost{border:1px solid rgba(255,255,255,.28);background:transparent;box-shadow:none}.tg-planner__meta{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:38px}.tg-planner__meta div{padding:16px;border:1px solid rgba(255,255,255,.15);border-radius:16px;background:rgba(255,255,255,.08)}.tg-planner__meta strong{display:block;color:#fff;font-size:18px}.tg-planner__meta span{display:block;margin-top:4px;color:rgba(255,255,255,.62);font-size:11px}.tg-planner__section{padding:64px 0}.tg-planner__grid{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:20px;align-items:start}.tg-planner__card{padding:28px;border:1px solid #dfe7f0;border-radius:24px;background:#fff;box-shadow:0 14px 36px rgba(14,33,69,.07)}.tg-planner__card h2{margin:0;color:#0e2145;font-size:28px;line-height:1.1;letter-spacing:-.04em}.tg-planner__card p{color:#667695;font-size:13px;line-height:1.6}.tg-planner__field{display:grid;gap:7px;margin-top:17px}.tg-planner__field label{color:#0e2145;font-size:12px;font-weight:800}.tg-planner__field input,.tg-planner__field select{width:100%;height:46px;padding:0 12px;border:1px solid #dfe7f0;border-radius:11px;background:#fff;color:#0e2145;font:inherit;font-size:13px}.tg-planner__field input:focus-visible,.tg-planner__field select:focus-visible,.tg-planner__button:focus-visible{outline:3px solid rgba(227,30,36,.25);outline-offset:3px}.tg-planner__submit{width:100%;margin-top:22px;border:0;cursor:pointer}.tg-planner__submit:disabled{opacity:.6;cursor:wait}.tg-planner__result{min-height:390px;background:linear-gradient(145deg,#0e2145,#193564);color:#fff}.tg-planner__result h2{color:#fff}.tg-planner__result p{color:rgba(255,255,255,.7)}.tg-planner__result-box{min-height:220px;margin-top:20px;padding:18px;border:1px solid rgba(255,255,255,.16);border-radius:16px;background:rgba(255,255,255,.07);color:rgba(255,255,255,.9);font-size:14px;line-height:1.7;white-space:pre-wrap}.tg-planner__hint{margin-top:14px;color:rgba(255,255,255,.55)!important;font-size:11px!important}.tg-planner__benefits{display:grid;grid-template-columns:repeat(3,1fr);gap:15px;margin-top:20px}.tg-planner__benefit{padding:22px;border:1px solid #dfe7f0;border-radius:18px;background:#fff}.tg-planner__benefit b{display:grid;width:36px;height:36px;place-items:center;border-radius:12px;background:#fff0f0;color:#e31e24;font-size:13px}.tg-planner__benefit h3{margin:14px 0 7px;font-size:17px}.tg-planner__benefit p{margin:0;color:#667695;font-size:13px;line-height:1.6}@media(max-width:767px){.tg-planner__wrap{width:min(100% - 24px,620px)}.tg-planner__hero{padding:76px 0 44px}.tg-planner h1{font-size:38px}.tg-planner__lead{font-size:14px}.tg-planner__meta,.tg-planner__benefits{grid-template-columns:1fr}.tg-planner__section{padding:44px 0}.tg-planner__grid{grid-template-columns:1fr}.tg-planner__card{padding:22px}.tg-planner__result{min-height:330px}}
</style>

<main class="tg-planner">
    <section class="tg-planner__hero">
        <div class="tg-planner__wrap">
            <span class="tg-planner__eyebrow">Your next chapter, mapped</span>
            <h1>Build a study plan that moves with you.</h1>
            <p class="tg-planner__lead">Turn your destination, course and budget goals into a clear next-step checklist with the Trans Globe Study Assistant.</p>
            <div class="tg-planner__actions"><a class="tg-planner__button" href="#planner">Start planning <span aria-hidden="true">↓</span></a><a class="tg-planner__button tg-planner__button--ghost" href="{{ url('/contact#enquiry') }}">Talk to a counsellor</a></div>
            <div class="tg-planner__meta"><div><strong>01</strong><span>Share your profile</span></div><div><strong>02</strong><span>Compare your options</span></div><div><strong>03</strong><span>Leave with next steps</span></div></div>
        </div>
    </section>
    <section class="tg-planner__section" id="planner">
        <div class="tg-planner__wrap tg-planner__grid">
            <form class="tg-planner__card" data-planner-form>
                <span class="tg-planner__eyebrow" style="color:#e31e24">Start with the basics</span>
                <h2>Tell us what you’re planning.</h2>
                <p>A few details help the assistant make the guidance more useful.</p>
                <div class="tg-planner__field"><label for="planner-destination">Preferred destination</label><select id="planner-destination" name="destination"><option>Australia</option><option>Canada</option><option>United Kingdom</option><option>United States</option><option>Germany</option><option>Ireland</option><option>New Zealand</option><option>Not sure yet</option></select></div>
                <div class="tg-planner__field"><label for="planner-course">Course or subject</label><input id="planner-course" name="course" type="text" placeholder="e.g. Master’s in Data Science" maxlength="120" required></div>
                <div class="tg-planner__field"><label for="planner-level">Study level</label><select id="planner-level" name="level"><option>Undergraduate</option><option>Postgraduate</option><option>PhD / Research</option><option>Not sure yet</option></select></div>
                <div class="tg-planner__field"><label for="planner-budget">Approximate budget</label><input id="planner-budget" name="budget" type="text" placeholder="e.g. ₹25 lakh total" maxlength="80"></div>
                <div class="tg-planner__field"><label for="planner-intake">Target intake</label><input id="planner-intake" name="intake" type="text" placeholder="e.g. September 2027" maxlength="80"></div>
                <button class="tg-planner__button tg-planner__submit" type="submit">Build my study plan <span aria-hidden="true">→</span></button>
            </form>
            <article class="tg-planner__card tg-planner__result" aria-live="polite"><span class="tg-planner__eyebrow">Your planning view</span><h2>Your next four moves.</h2><p>We’ll turn your answers into a practical checklist you can discuss with a counsellor.</p><div class="tg-planner__result-box" data-planner-result>Complete the form to generate your personalised study-plan checklist.</div><p class="tg-planner__hint">Guidance is general information. Always confirm current university and visa requirements with official sources.</p></article>
        </div>
    </section>
    <section class="tg-planner__section" style="padding-top:0"><div class="tg-planner__wrap"><div class="tg-planner__benefits"><article class="tg-planner__benefit"><b>01</b><h3>Profile clarity</h3><p>Know which academic, test and document details matter before you shortlist.</p></article><article class="tg-planner__benefit"><b>02</b><h3>Smarter comparisons</h3><p>Balance course fit, total cost, destination and timing—not rankings alone.</p></article><article class="tg-planner__benefit"><b>03</b><h3>Human handoff</h3><p>Take your checklist to a Trans Globe counsellor for a profile-led review.</p></article></div></div></section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('[data-planner-form]');
        const result = document.querySelector('[data-planner-result]');
        if (!form || !result) return;
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            const values = Object.fromEntries(new FormData(form).entries());
            const message = `Build my study plan for ${values.destination}. Course: ${values.course}. Study level: ${values.level}. Budget: ${values.budget || 'not provided'}. Intake: ${values.intake || 'not provided'}.`;
            button.disabled = true;
            button.textContent = 'Building your plan…';
            result.textContent = 'Reviewing your profile and planning the next steps…';
            try {
                const response = await fetch('{{ route('study-assistant.chat') }}', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: JSON.stringify({ message }) });
                const payload = await response.json();
                if (!response.ok || !payload.reply) throw new Error('Unable to reach the assistant');
                result.textContent = payload.reply;
            } catch (error) {
                result.textContent = '1. Confirm your course, study level and English-test status.\n2. Compare destination, tuition, living costs and visa pathway.\n3. Prepare your transcripts, passport, CV, statement and references.\n4. Check the official university deadline, then book a counselling review.';
            } finally {
                button.disabled = false;
                button.innerHTML = 'Build my study plan <span aria-hidden="true">→</span>';
            }
        });
    });
</script>

@include('mirror.partials.footer', ['siteCms' => $cms ?? []])
