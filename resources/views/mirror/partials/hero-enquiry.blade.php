@php
    $heroFormId = $formId ?? 'hero-enquiry';
@endphp

<style>
    .hero-enquiry { position:absolute; z-index:4; top:34px; right:34px; width:min(326px,27vw); padding:23px; border:1px solid rgba(255,255,255,.3); border-radius:22px; background:linear-gradient(145deg,rgba(255,255,255,.2),rgba(7,22,49,.54)); box-shadow:0 18px 42px rgba(3,12,31,.25); color:#fff; backdrop-filter:blur(18px) saturate(1.2); -webkit-backdrop-filter:blur(18px) saturate(1.2); }
    .hero-enquiry__eyebrow { display:flex; align-items:center; gap:8px; color:rgba(255,255,255,.76); font-size:10px; font-weight:800; letter-spacing:.11em; text-transform:uppercase; }
    .hero-enquiry__eyebrow::before { width:20px; height:2px; background:#ff626a; content:''; }
    .hero-enquiry h2 { margin:9px 0 3px; color:#fff; font-size:22px; line-height:1.12; font-weight:800; letter-spacing:-.035em; }
    .hero-enquiry__copy { margin:0 0 15px; color:rgba(255,255,255,.72); font-size:12px; line-height:1.48; }
    .hero-enquiry__field { display:grid; gap:5px; margin-top:10px; }
    .hero-enquiry label { color:rgba(255,255,255,.84); font-size:11px; font-weight:700; }
    .hero-enquiry input { width:100%; height:42px; padding:0 12px; border:1px solid rgba(255,255,255,.28); border-radius:11px; outline:0; background:rgba(255,255,255,.13); color:#fff; font:inherit; font-size:13px; transition:border-color .18s ease,background .18s ease; }
    .hero-enquiry input::placeholder { color:rgba(255,255,255,.55); }
    .hero-enquiry input:focus { border-color:#fff; background:rgba(255,255,255,.2); }
    .hero-enquiry button { display:flex; width:100%; min-height:46px; align-items:center; justify-content:center; gap:8px; margin-top:15px; border:0; border-radius:12px; background:#ef2630; color:#fff; font:inherit; font-size:13px; font-weight:800; cursor:pointer; box-shadow:0 9px 18px rgba(227,30,36,.24); transition:background .18s ease,transform .18s ease; }
    .hero-enquiry button:hover { background:#c91820; transform:translateY(-1px); }
    .hero-enquiry__note { display:flex; align-items:center; gap:6px; margin:11px 0 0; color:rgba(255,255,255,.62); font-size:10px; line-height:1.35; }
    .hero-enquiry__note svg { width:13px; height:13px; flex:0 0 13px; fill:none; stroke:currentColor; stroke-width:2; }
    .hero-enquiry__alert,.hero-enquiry__error { margin-top:9px; color:#fff; font-size:11px; line-height:1.4; }
    .hero-enquiry__alert { padding:9px 10px; border-radius:10px; background:rgba(53,180,109,.28); }
    .hero-enquiry__error { color:#ffd6d8; }
    .hero-enquiry__honeypot { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0 0 0 0); white-space:nowrap; }
    @media (max-width:1120px) { .hero-enquiry { right:24px; width:296px; padding:20px; } }
    @media (max-width:900px) { .hero-enquiry { position:relative; top:auto; right:auto; width:auto; max-width:440px; margin:0 24px 24px; padding:20px; } }
    @media (max-width:767px) { .hero-enquiry { margin:0 18px 18px; border-radius:18px; } .hero-enquiry h2 { font-size:20px; } }
    @media (prefers-reduced-motion:reduce) { .hero-enquiry * { transition:none!important; } }
</style>

<form class="hero-enquiry" action="{{ route('hero.enquire') }}" method="post" aria-label="Quick counselling enquiry">
    @csrf
    <input type="hidden" name="source_context" value="{{ $sourceContext }}">
    <input type="hidden" name="return_to" value="{{ $returnTo }}">
    <div class="hero-enquiry__eyebrow">Free counselling</div>
    <h2>Plan your next step.</h2>
    <p class="hero-enquiry__copy">Leave your details and a GEIC Indore expert will contact you.</p>
    @if(session('enquiry_success'))<div class="hero-enquiry__alert" role="status">{{ session('enquiry_success') }}</div>@endif
    <div class="hero-enquiry__field"><label for="{{ $heroFormId }}_name">Full name</label><input id="{{ $heroFormId }}_name" name="full_name" value="{{ old('full_name') }}" autocomplete="name" placeholder="Your name" required>@error('full_name')<span class="hero-enquiry__error">{{ $message }}</span>@enderror</div>
    <div class="hero-enquiry__field"><label for="{{ $heroFormId }}_phone">Phone number</label><input id="{{ $heroFormId }}_phone" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" placeholder="+91 00000 00000" required>@error('phone')<span class="hero-enquiry__error">{{ $message }}</span>@enderror</div>
    <div class="hero-enquiry__field"><label for="{{ $heroFormId }}_email">Email address</label><input id="{{ $heroFormId }}_email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required>@error('email')<span class="hero-enquiry__error">{{ $message }}</span>@enderror</div>
    <div class="hero-enquiry__honeypot" aria-hidden="true"><label for="{{ $heroFormId }}_website">Website</label><input id="{{ $heroFormId }}_website" name="website" tabindex="-1" autocomplete="off"></div>
    <button type="submit">Request a callback <span aria-hidden="true">→</span></button>
    <p class="hero-enquiry__note"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>Your details stay private and secure.</p>
</form>
