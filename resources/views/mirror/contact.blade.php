@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/'), 'mobileBackLabel' => 'Back to home'])

<style>
    :root { --ct-navy:#0e2145; --ct-red:#e31e24; --ct-soft:#f4f7fb; --ct-ink:#15294d; --ct-muted:#64748b; --ct-line:#dfe7f0; }
    .ct-page { overflow:clip; background:#fff; color:var(--ct-ink); }
    .ct-wrap { width:min(1280px,calc(100% - 48px)); margin-inline:auto; }
    .ct-section { padding:88px 0; scroll-margin-top:92px; }
    .ct-kicker { display:inline-flex; align-items:center; gap:10px; color:var(--ct-red); font-size:12px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
    .ct-kicker::before { width:28px; height:2px; background:currentColor; content:''; }
    .ct-title { max-width:760px; margin:14px 0 0; color:var(--ct-navy); font-size:clamp(34px,4vw,52px); line-height:1.08; font-weight:800; letter-spacing:-.048em; text-wrap:balance; }
    .ct-title span { color:var(--ct-red); }
    .ct-lead { max-width:650px; margin:17px 0 0; color:var(--ct-muted); font-size:16px; line-height:1.75; }
    .ct-page :is(a,input,select,textarea,button):focus-visible { outline:3px solid rgba(227,30,36,.3); outline-offset:3px; }
    .ct-button { display:inline-flex; min-height:52px; align-items:center; justify-content:center; gap:9px; padding:0 22px; border:0; border-radius:14px; background:var(--ct-red); color:#fff!important; font-size:14px; font-weight:800; box-shadow:0 11px 22px rgba(227,30,36,.18); transition:transform .2s ease,background .2s ease; }
    .ct-button:hover { background:#c81820; color:#fff; transform:translateY(-2px); }
    .ct-button--light { background:#fff; color:var(--ct-navy)!important; box-shadow:none; }
    .ct-button--light:hover { background:#fff; color:var(--ct-red)!important; }

    .ct-hero { padding:128px 0 0; background:var(--ct-soft); }
    .ct-hero__shell { position:relative; display:grid; grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr); align-items:center; gap:46px; overflow:hidden; min-height:438px; padding:64px; border-radius:34px; background:var(--ct-navy); box-shadow:0 26px 65px rgba(14,33,69,.19); }
    .ct-hero__shell::before { position:absolute; inset:0; opacity:.23; background-image:linear-gradient(rgba(255,255,255,.12) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.12) 1px,transparent 1px); background-size:42px 42px; mask-image:linear-gradient(90deg,#000 0%,transparent 90%); content:''; }
    .ct-hero__orb { position:absolute; top:-190px; right:-108px; width:560px; height:560px; border:76px solid rgba(227,30,36,.48); border-radius:50%; }
    .ct-hero__orb::after { position:absolute; top:88px; left:88px; width:238px; height:238px; border:1px solid rgba(255,255,255,.24); border-radius:50%; content:''; }
    .ct-hero__content { position:relative; z-index:1; max-width:740px; }
    .ct-crumbs { display:flex; flex-wrap:wrap; gap:8px; color:rgba(255,255,255,.64); font-size:13px; }
    .ct-crumbs a { color:#fff; font-weight:700; }
    .ct-hero .ct-kicker { margin-top:32px; color:rgba(255,255,255,.74); }
    .ct-hero h1 { max-width:680px; margin:16px 0 0; color:#fff; font-size:clamp(44px,5.3vw,67px); line-height:1.02; font-weight:800; letter-spacing:-.056em; text-wrap:balance; }
    .ct-hero h1 span { color:#ff626a; }
    .ct-hero p { max-width:610px; margin:18px 0 0; color:rgba(255,255,255,.8); font-size:17px; line-height:1.72; }
    .ct-hero__actions { display:flex; flex-wrap:wrap; gap:13px; margin-top:28px; }
    .ct-hero__call { display:inline-flex; min-height:52px; align-items:center; gap:9px; padding:0 8px; color:#fff; font-size:14px; font-weight:800; }
    .ct-hero__call svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2; }
    .ct-hero__visual { position:relative; z-index:1; overflow:hidden; width:100%; aspect-ratio:4 / 3; border:1px solid rgba(255,255,255,.18); border-radius:25px; background:#1b335e; box-shadow:0 22px 48px rgba(4,15,36,.3); }
    .ct-hero__visual::after { position:absolute; inset:0; background:linear-gradient(145deg,rgba(14,33,69,.24),transparent 48%,rgba(227,30,36,.18)); pointer-events:none; content:''; }
    .ct-hero__visual img { display:block; width:100%; height:100%; object-fit:cover; object-position:center; }

    .ct-quick { position:relative; z-index:2; display:grid; grid-template-columns:repeat(3,1fr); margin:-1px 30px 0; border:1px solid var(--ct-line); border-radius:0 0 25px 25px; background:#fff; box-shadow:0 18px 42px rgba(14,33,69,.08); }
    .ct-quick__item { display:flex; align-items:center; gap:13px; min-width:0; padding:22px 24px; border-right:1px solid var(--ct-line); }
    .ct-quick__item:last-child { border:0; }
    .ct-quick__icon { display:grid; width:39px; height:39px; flex:0 0 39px; place-items:center; border-radius:12px; color:var(--ct-red); background:#fdebed; }
    .ct-quick__icon svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:1.9; }
    .ct-quick strong { display:block; color:var(--ct-navy); font-size:14px; }
    .ct-quick span { display:block; margin-top:3px; color:var(--ct-muted); font-size:12px; line-height:1.4; }

    .ct-connect { display:grid; grid-template-columns:.9fr 1.1fr; align-items:start; gap:60px; }
    .ct-contact-list { display:grid; gap:12px; margin-top:30px; }
    .ct-contact-item { display:flex; gap:15px; padding:18px; border:1px solid var(--ct-line); border-radius:18px; background:#fff; box-shadow:0 10px 24px rgba(14,33,69,.04); }
    .ct-contact-item__icon { display:grid; width:43px; height:43px; flex:0 0 43px; place-items:center; border-radius:13px; color:var(--ct-red); background:#fdebed; }
    .ct-contact-item__icon svg { width:22px; height:22px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:1.8; }
    .ct-contact-item small { display:block; color:var(--ct-muted); font-size:11px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; }
    .ct-contact-item strong,.ct-contact-item a { display:block; margin-top:5px; color:var(--ct-navy); font-size:15px; font-weight:800; line-height:1.5; }
    .ct-contact-item a:hover { color:var(--ct-red); }
    .ct-contact-item p { margin:5px 0 0; color:var(--ct-muted); font-size:13px; line-height:1.48; }
    .ct-map { overflow:hidden; min-height:532px; border:1px solid var(--ct-line); border-radius:26px; background:#e5ebf1; box-shadow:0 18px 42px rgba(14,33,69,.1); }
    .ct-map iframe { display:block; width:100%; height:532px; border:0; }
    .ct-map__fallback { display:flex; min-height:532px; align-items:center; justify-content:center; padding:32px; background:linear-gradient(135deg,#dfe7ed,#f7f9fb); text-align:center; }

    .ct-enquiry { background:var(--ct-soft); }
    .ct-form-layout { display:grid; grid-template-columns:.76fr 1.24fr; align-items:start; gap:58px; }
    .ct-form-intro { position:sticky; top:100px; }
    .ct-form-card { padding:31px; border:1px solid var(--ct-line); border-radius:25px; background:#fff; box-shadow:0 18px 42px rgba(14,33,69,.08); }
    .ct-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:17px; }
    .ct-field { min-width:0; }
    .ct-field--full { grid-column:1 / -1; }
    .ct-field label { display:block; margin-bottom:8px; color:var(--ct-navy); font-size:13px; font-weight:800; }
    .ct-field input,.ct-field select,.ct-field textarea { width:100%; min-height:50px; padding:0 14px; border:1px solid #d7e0eb; border-radius:13px; background:#fff; color:var(--ct-ink); font:inherit; font-size:14px; transition:border-color .2s ease,box-shadow .2s ease; }
    .ct-field textarea { min-height:118px; padding-top:13px; resize:vertical; }
    .ct-field input:focus,.ct-field select:focus,.ct-field textarea:focus { border-color:var(--ct-red); box-shadow:0 0 0 4px rgba(227,30,36,.08); outline:0; }
    .ct-consent { display:flex!important; align-items:flex-start; gap:10px; margin:4px 0 0!important; color:var(--ct-muted)!important; font-size:12px!important; font-weight:500!important; line-height:1.5; }
    .ct-consent input { width:17px; min-height:17px; height:17px; margin:1px 0 0; accent-color:var(--ct-red); }
    .ct-alert { padding:14px 16px; border:1px solid rgba(29,128,78,.24); border-radius:13px; background:#effaf4; color:#177445; font-size:14px; line-height:1.5; }
    .ct-alert--error { border-color:rgba(227,30,36,.25); background:#fff1f1; color:#a91d24; }
    .ct-alert ul { margin:8px 0 0; padding-left:18px; }
    .ct-error { display:block; margin-top:6px; color:#c81820; font-size:12px; font-weight:700; }
    .ct-honeypot { position:absolute; left:-10000px; width:1px; height:1px; overflow:hidden; }
    .ct-form-note { margin:14px 0 0; color:var(--ct-muted); font-size:12px; line-height:1.6; }

    @media (max-width:991px) { .ct-hero { padding-top:106px; } .ct-hero__shell { grid-template-columns:minmax(0,1fr) minmax(280px,.75fr); gap:30px; padding:46px; } .ct-connect,.ct-form-layout { grid-template-columns:1fr; gap:42px; } .ct-form-intro { position:static; } .ct-map { min-height:420px; } .ct-map iframe,.ct-map__fallback { min-height:420px; height:420px; } }
    @media (max-width:767px) { .ct-page { padding-bottom:76px; } .ct-wrap { width:min(100% - 28px,620px); } .ct-section { padding:58px 0; } .ct-hero { padding-top:82px; } .ct-hero__shell { grid-template-columns:1fr; gap:28px; min-height:0; padding:29px 24px; border-radius:25px; } .ct-hero__orb { top:-102px; right:-125px; width:370px; height:370px; border-width:52px; } .ct-hero h1 { font-size:42px; } .ct-hero p { font-size:15px; line-height:1.65; } .ct-hero__actions { display:grid; grid-template-columns:1fr; } .ct-button,.ct-hero__call { width:100%; } .ct-hero__call { justify-content:center; } .ct-hero__visual { aspect-ratio:16 / 10; border-radius:20px; } .ct-quick { display:flex; overflow-x:auto; margin:0 10px; border-radius:0 0 21px 21px; scroll-snap-type:x mandatory; scrollbar-width:none; } .ct-quick::-webkit-scrollbar { display:none; } .ct-quick__item { flex:0 0 84%; padding:19px; border-right:1px solid var(--ct-line)!important; scroll-snap-align:start; } .ct-title { font-size:32px; } .ct-lead { font-size:15px; } .ct-contact-list { margin-top:24px; } .ct-map,.ct-map iframe,.ct-map__fallback { min-height:350px; height:350px; } .ct-form-card { padding:22px; border-radius:21px; } .ct-form-grid { grid-template-columns:1fr; gap:14px; } .ct-field--full { grid-column:auto; } }
    @media (prefers-reduced-motion:reduce) { .ct-page * { transition-duration:.01ms!important; scroll-behavior:auto!important; } }
</style>

<main class="ct-page">
    <section class="ct-hero">
        <div class="ct-wrap">
            <div class="ct-hero__shell">
                <div class="ct-hero__orb" aria-hidden="true"></div>
                <div class="ct-hero__content">
                    <nav class="ct-crumbs" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><span>Contact</span></nav>
                    <span class="ct-kicker">GEIC Indore</span>
                    <h1>{{ $cms['hero_title'] ?? 'Let’s make your next step clear.' }}</h1>
                    <p>{{ $cms['hero_copy'] ?? 'Speak with the Trans Globe Indore team about study destinations, applications, scholarships, test preparation or your student-visa pathway.' }}</p>
                    <div class="ct-hero__actions"><a href="{{ url('/contact') }}#enquiry" class="ct-button">Send an enquiry <span aria-hidden="true">↓</span></a><a href="tel:+919826666886" class="ct-hero__call"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 11.2 19a19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 2.3 4.2 2 2 0 0 1 4.3 2h3a2 2 0 0 1 2 1.7c.12.9.34 1.79.67 2.63a2 2 0 0 1-.45 2.11L8.3 9.66a16 16 0 0 0 6 6l1.25-1.25a2 2 0 0 1 2.11-.45c.84.33 1.73.55 2.63.67A2 2 0 0 1 22 16.9Z"/></svg>+91 98266 66886</a></div>
                </div>
                <figure class="ct-hero__visual">
                    <img src="{{ asset($cms['hero_image'] ?? 'assets/services/expert-counselling.jpg') }}" alt="Students working together during an international education counselling session" width="1200" height="800" loading="eager" fetchpriority="high" decoding="async">
                </figure>
            </div>
            <div class="ct-quick" aria-label="Contact highlights">
                <div class="ct-quick__item"><span class="ct-quick__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-5.9 7-12a7 7 0 1 0-14 0c0 6.1 7 12 7 12Z"/><circle cx="12" cy="9" r="2.4"/></svg></span><span><strong>Visit us in Indore</strong><span>Near Nehru Park 2, Lad Colony</span></span></div>
                <div class="ct-quick__item"><span class="ct-quick__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 4h16v12H8l-4 4V4Z"/><path d="M8 9h8M8 13h5"/></svg></span><span><strong>Talk to an expert</strong><span>Call or submit an enquiry</span></span></div>
                <div class="ct-quick__item"><span class="ct-quick__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M7 11h10M8 15h3"/></svg></span><span><strong>Monday to Saturday</strong><span>10:00 AM to 6:30 PM</span></span></div>
            </div>
        </div>
    </section>

    <section class="ct-section">
        <div class="ct-wrap ct-connect">
            <div>
                <span class="ct-kicker">Contact details</span>
                <h2 class="ct-title">Come by, call us, or <span>write to us.</span></h2>
                <p class="ct-lead">Choose the way that is easiest for you. Our Indore team can help you understand your options before you make a commitment.</p>
                <div class="ct-contact-list">
                    <article class="ct-contact-item"><span class="ct-contact-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-5.9 7-12a7 7 0 1 0-14 0c0 6.1 7 12 7 12Z"/><circle cx="12" cy="9" r="2.4"/></svg></span><div><small>Office</small><strong>Global Education and Immigration Consultants</strong><p>Office No. 503, THE VIEW Tower 1, Yeshwant Niwas Rd, above Jade Blue Showroom, Nehru Park 2, Lad Colony, Indore, Madhya Pradesh 452001</p></div></article>
                    <article class="ct-contact-item"><span class="ct-contact-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 11.2 19a19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 2.3 4.2 2 2 0 0 1 4.3 2h3a2 2 0 0 1 2 1.7c.12.9.34 1.79.67 2.63a2 2 0 0 1-.45 2.11L8.3 9.66a16 16 0 0 0 6 6l1.25-1.25a2 2 0 0 1 2.11-.45c.84.33 1.73.55 2.63.67A2 2 0 0 1 22 16.9Z"/></svg></span><div><small>Phone</small><a href="tel:+919826666886">+91 98266 66886</a><p>For study-abroad counselling enquiries</p></div></article>
                    <article class="ct-contact-item"><span class="ct-contact-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><small>Email</small><a href="mailto:info@geic.in">info@geic.in</a><p>We will help direct your enquiry to the right team</p></div></article>
                    <article class="ct-contact-item"><span class="ct-contact-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3.3 2"/></svg></span><div><small>Office hours</small><strong>Monday to Saturday</strong><p>10:00 AM–6:30 PM</p></div></article>
                </div>
            </div>
            <div class="ct-map"><iframe title="Map to GEIC Indore" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=Office%20No.%20503%2C%20THE%20VIEW%20Tower%201%2C%20Yeshwant%20Niwas%20Rd%2C%20Indore%20452001&output=embed"></iframe></div>
        </div>
    </section>

    <section class="ct-section ct-enquiry" id="enquiry">
        <div class="ct-wrap ct-form-layout">
            <div class="ct-form-intro"><span class="ct-kicker">Free counselling enquiry</span><h2 class="ct-title">Tell us what you’re planning. <span>We’ll help you navigate it.</span></h2><p class="ct-lead">Share a few details so the right counsellor can prepare for your conversation. There is no obligation to proceed.</p></div>
            <form class="ct-form-card" method="post" action="{{ route('contact.enquire') }}" novalidate>
                @csrf
                @if(session('enquiry_success'))<div class="ct-alert" role="status">{{ session('enquiry_success') }}</div>@endif
                @if($errors->any())<div class="ct-alert ct-alert--error" role="alert"><strong>Please check the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <div class="ct-form-grid" @if(session('enquiry_success') || $errors->any()) style="margin-top:18px" @endif>
                    <div class="ct-field"><label for="full_name">Full name *</label><input id="full_name" name="full_name" value="{{ old('full_name') }}" autocomplete="name" required>@error('full_name')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field"><label for="phone">Phone number *</label><input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" required>@error('phone')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field"><label for="email">Email address *</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field"><label for="city">Current city *</label><input id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2" required>@error('city')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field"><label for="study_level">Preferred study level *</label><select id="study_level" name="study_level" required><option value="">Select study level</option>@foreach(['Undergraduate','Postgraduate','Diploma or pathway','Research','Not sure yet'] as $option)<option value="{{ $option }}" @selected(old('study_level')===$option)>{{ $option }}</option>@endforeach</select>@error('study_level')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field"><label for="preferred_intake">Preferred intake *</label><select id="preferred_intake" name="preferred_intake" required><option value="">Select intake</option>@foreach(['Next available intake','February intake','July intake','October intake','Not sure yet'] as $option)<option value="{{ $option }}" @selected(old('preferred_intake')===$option)>{{ $option }}</option>@endforeach</select>@error('preferred_intake')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field"><label for="preferred_course">Preferred course</label><input id="preferred_course" name="preferred_course" value="{{ old('preferred_course') }}" placeholder="e.g. Data Science">@error('preferred_course')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field"><label for="english_test">English-test status *</label><select id="english_test" name="english_test" required><option value="">Select status</option>@foreach(['IELTS','PTE','TOEFL','Planning to take a test','Not sure yet'] as $option)<option value="{{ $option }}" @selected(old('english_test')===$option)>{{ $option }}</option>@endforeach</select>@error('english_test')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field ct-field--full"><label for="message">What can we help with?</label><textarea id="message" name="message" placeholder="Tell us about your study plan, preferred destination or any questions.">{{ old('message') }}</textarea>@error('message')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-field ct-field--full"><label class="ct-consent"><input type="checkbox" name="consent" value="1" @checked(old('consent')) required><span>I agree that GEIC Indore may contact me about this study-abroad enquiry.</span></label>@error('consent')<span class="ct-error">{{ $message }}</span>@enderror</div>
                    <div class="ct-honeypot" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
                    <div class="ct-field ct-field--full"><button class="ct-button" type="submit">Send my enquiry <span aria-hidden="true">→</span></button><p class="ct-form-note">Your details are used only to respond to your counselling enquiry.</p></div>
                </div>
            </form>
        </div>
    </section>
</main>

@include('mirror.partials.footer')
