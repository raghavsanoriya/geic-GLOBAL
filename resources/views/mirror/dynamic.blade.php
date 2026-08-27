@include('mirror.partials.header', ['siteCms' => $cms])

@php
    $heroImage = $cms['hero_image'] ?? 'assets/services/expert-counselling.jpg';
    $heroImageUrl = str_starts_with($heroImage, 'http://') || str_starts_with($heroImage, 'https://') ? $heroImage : asset($heroImage);
@endphp

<style>
    .custom-page{background:#f6f8fc}.custom-page__hero{padding:64px 20px 88px}.custom-page__hero-card{position:relative;max-width:1240px;min-height:510px;margin:0 auto;overflow:hidden;border-radius:34px;background:#0e2145;box-shadow:0 28px 70px rgba(14,33,69,.18)}.custom-page__hero-image{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.custom-page__hero-overlay{position:absolute;inset:0;background:linear-gradient(90deg,rgba(7,20,48,.95) 0%,rgba(7,20,48,.76) 48%,rgba(7,20,48,.18) 100%)}.custom-page__hero-copy{position:relative;z-index:2;display:flex;max-width:700px;min-height:510px;justify-content:center;padding:70px;flex-direction:column}.custom-page__eyebrow{color:#ff8e93;font-size:13px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.custom-page__hero h1{margin:16px 0 0;color:#fff;font-size:54px;line-height:1.08;font-weight:700;text-wrap:balance}.custom-page__hero p{margin:22px 0 0;color:rgba(255,255,255,.8);font-size:18px;line-height:1.7}.custom-page__content{padding:24px 20px 96px}.custom-page__content-card{display:grid;max-width:1040px;margin:0 auto;padding:52px;border:1px solid #e5eaf2;border-radius:28px;background:#fff;box-shadow:0 18px 45px rgba(14,33,69,.07);gap:20px}.custom-page__content-card h2{margin:0;color:#0e2145;font-size:36px}.custom-page__content-card p{margin:0;color:#61708a;font-size:17px;line-height:1.75}.custom-page__cta{display:flex;align-items:center;justify-content:space-between;margin-top:20px;padding:28px;border-radius:20px;background:#0e2145;gap:24px}.custom-page__cta h3{margin:0;color:#fff;font-size:25px}.custom-page__cta p{margin-top:8px;color:rgba(255,255,255,.7);font-size:14px}.custom-page__cta a{display:inline-flex;min-height:48px;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;background:#e31e24;color:#fff;font-weight:700;white-space:nowrap}.custom-page__cta a:hover{background:#f3951e;color:#fff}@media(max-width:767px){.custom-page__hero{padding:24px 14px 54px}.custom-page__hero-card,.custom-page__hero-copy{min-height:470px}.custom-page__hero-card{border-radius:24px}.custom-page__hero-overlay{background:linear-gradient(180deg,rgba(7,20,48,.34),rgba(7,20,48,.96))}.custom-page__hero-copy{justify-content:flex-end;padding:30px}.custom-page__hero h1{font-size:36px}.custom-page__hero p{font-size:16px}.custom-page__content{padding:0 14px 70px}.custom-page__content-card{padding:27px;border-radius:22px}.custom-page__content-card h2{font-size:28px}.custom-page__cta{align-items:flex-start;flex-direction:column}.custom-page__cta a{width:100%}}
</style>

<main class="custom-page">
    <section class="custom-page__hero">
        <div class="custom-page__hero-card">
            <img class="custom-page__hero-image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">
            <div class="custom-page__hero-overlay"></div>
            <div class="custom-page__hero-copy">
                <span class="custom-page__eyebrow">{{ $customPage->name }}</span>
                <h1>{{ $cms['hero_title'] ?? $customPage->name }}</h1>
                <p>{{ $cms['hero_copy'] ?? $customPage->description }}</p>
            </div>
        </div>
    </section>
    <section class="custom-page__content">
        <article class="custom-page__content-card">
            <h2>{{ $cms['content_title'] ?? 'How we can help' }}</h2>
            <p>{!! nl2br(e($cms['content_copy'] ?? 'Add the key information visitors need to understand this page and confidently take their next step.')) !!}</p>
            <div class="custom-page__cta">
                <div><h3>{{ $cms['cta_title'] ?? 'Ready to take the next step?' }}</h3><p>{{ $cms['cta_copy'] ?? 'Speak with our Indore counselling team for clear, personal guidance.' }}</p></div>
                <a href="{{ url('/contact#enquiry') }}">{{ $cms['cta_label'] ?? 'Speak to a Counsellor' }}</a>
            </div>
        </article>
    </section>
</main>

@include('mirror.partials.footer', ['siteCms' => $cms])
