            @php($siteCms = $siteCms ?? [])
            @php($contactPhone = $siteCms['contact_phone'] ?? '+91 98266 66886')
            @php($contactPhoneLink = preg_replace('/[^0-9+]/', '', $contactPhone))
            @php($contactEmail = $siteCms['contact_email'] ?? 'info@geic.in')
            <div id="appFooterArea">
            <style>
                #appFooterArea,
                #appFooterArea .theme-footer-1,
                #appFooterArea .theme-footer-1__section,
                #appFooterArea .theme-footer-1__section-bg-wrapper {
                    background-color: #0e2145 !important;
                }
                #appFooterArea .theme-footer-1__bottom-section-divider {
                    border-color: rgba(255,255,255,.14) !important;
                }
                #appFooterArea .tg-footer-bottom {
                    width: 100%;
                    background-color: #0e2145 !important;
                    /* Keep the divider spacing inside the dark footer surface. */
                    padding-top: 0;
                    margin-top: 0;
                }
                #appFooterArea .theme-footer-1__bottom-section-divider {
                    margin-top: 0;
                }
                /* Hidden cart UI is positioned outside the viewport until it
                   is opened. Keep it from creating a mobile page scrollbar. */
                html {
                    overflow-x: clip !important;
                    overflow-y: auto !important;
                }
                body {
                    overflow: visible !important;
                }
            </style>
            <div class="theme-footer-1 position-relative has-newsletter">
        <div class="theme-footer-1__section position-relative">
            <div class="theme-footer-1__section-bg-wrapper light-only" style="background-color: #0e2145; background-image: url(store/themes/footers/2/footer_background_7gn.png); "></div>
            <div class="theme-footer-1__section-bg-wrapper dark-only" style="background-color: #0e2145; background-image: url(store/themes/footers/2/footer_background_7gn.png); "></div>



                            <div class="theme-footer-1__newsletter">
    <div class="container position-relative">
        <div class="theme-footer-1__newsletter-mask"></div>

        <div class="position-relative z-index-2 bg-white p-16 rounded-24">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="">
                        <div class="d-flex align-items-center gap-4">
                                                            <h4 class="font-20">{{ $siteCms['footer_newsletter_title'] ?? 'Stay in the Study-Abroad Loop' }}</h4>

                                                            <div class="theme-footer-1__newsletter-emoji">
                                    <img src="store/themes/footers/2/happy_emoji_zoa.svg" alt="emoji" class="img-fluid" width="20px" height="20px">
                                </div>
                                                    </div>

                                                    <div class="mt-8 font-14 text-gray-500">{{ $siteCms['footer_newsletter_copy'] ?? 'Get visa updates, scholarship alerts and honest study-abroad advice in your inbox.' }}</div>

                    </div>
                </div>

                <div class="col-12 col-lg-6 mt-16 mt-lg-0 d-flex justify-content-end">
                    <div class="js-newsletter-form newsletter-form d-flex align-items-center justify-content-between p-12 rounded-12 border-gray-200">
                        <div class="form-group mb-0 flex-1">
                            <div class="d-flex align-items-center gap-8 px-12 flex-1">
                                <svg width="24px" height="24px" class="icons text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 20.5H7c-3 0-5-1.5-5-5v-7c0-3.5 2-5 5-5h10c3 0 5 1.5 5 5v7c0 3.5-2 5-5 5z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 9l-3.13 2.5c-1.03.82-2.72.82-3.75 0L7 9"/>
</svg>                                <input type="email" name="newsletter_email" class="js-ajax-newsletter_email flex-1" placeholder="Enter your email address here">
                            </div>

                            <div class="invalid-feedback d-block position-absolute position-bottom-0"></div>
                        </div>

                        <button type="button" class="js-submit-newsletter-btn btn btn-primary btn-lg text-white">{{ $siteCms['footer_newsletter_button'] ?? 'Join' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="position-relative z-index-2">

                <div class="container position-relative">
                    <div class="row">
                        <div class="col-12 col-lg-5">
                                                            <img src="assets/transglobe/trans-globe-logo-white.png" alt="Trans Globe Indore managed by Global Education and Immigration Consultants" class="img-fluid mb-24" style="width: 100%; max-width: 340px; height: auto;">
                                                            <div class="d-inline-flex-center gap-8 border-2 border-white rounded-32 bg-white-10 text-white px-16 py-12">
                                                                            <div class="size-24">
                                            <img src="store/themes/footers/2/power_emoji_42t.svg" alt="footer cta btn icon" class="img-fluid" width="24px" height="24px">
                                        </div>

                                                                            <span class="">{{ $siteCms['footer_badge'] ?? 'Your journey with Trans Globe Indore starts here' }}</span>
                                                                    </div>

                                                                    <h3 class="mt-16 font-44 text-white mr-0 mr-lg-48">{{ $siteCms['footer_title'] ?? 'Start With GEIC Indore' }}</h3>

                                                                    <a href="{{ $siteCms['footer_cta_url'] ?? url('/contact#enquiry') }}" class="btn-flip-effect btn btn-xlg btn-primary gap-8 mt-32" data-text="{{ $siteCms['footer_cta_label'] ?? 'Book Free Counselling' }}">
                                                                                    <svg width="24px" height="24px" class="icons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
  <path d="M18.38 12.84v4.93c0 1.27-.99 2.63-2.18 3.03l-3.19 1.06c-.56.19-1.47.19-2.02 0L7.8 20.8c-1.2-.4-2.18-1.76-2.18-3.03l.01-4.93 4.42 2.88c1.08.71 2.86.71 3.94 0l4.39-2.88z" opacity=".4"/>
  <path d="M19.98 6.46l-5.99-3.93c-1.08-.71-2.86-.71-3.94 0L4.03 6.46c-1.93 1.25-1.93 4.08 0 5.34l1.6 1.04 4.42 2.88c1.08.71 2.86.71 3.94 0l4.39-2.88 1.37-.9V15c0 .41.34.75.75.75s.75-.34.75-.75v-4.92c.4-1.29-.01-2.79-1.27-3.62z"/>
</svg>
                                        <span class="btn-flip-effect__text">{{ $siteCms['footer_cta_label'] ?? 'Book Free Counselling' }}</span>
                                    </a>
                                                                                    </div>

                        <div class="col-6 col-lg-2 mt-32 mt-lg-0">
                                                            <h4 class="font-16 text-white">Explore</h4>

                                                                                                                                        <a href="{{ url('/destinations') }}" class="d-block font-16 text-white opacity-70 mt-16">
                                            <span class="">Study Destinations</span>
                                        </a>
                                                                                <a href="{{ url('/services') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Our Services</span>
                                        </a>
                                                                                <a href="{{ url('/events') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Events</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/scholarships') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Scholarships</span>
                                        </a>
                                                                                <a href="{{ url('/tests') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Test Preparation</span>
                                        </a>
                                                                                <a href="{{ url('/compare-destinations') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Compare Destinations</span>
                                        </a>
                                                                                <a href="{{ url('/emi-calculator') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">EMI Calculator</span>
                                        </a>
                                                                                <a href="{{ url('/education-loans') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Education Loans</span>
                                        </a>
                                                                                <a href="{{ url('/ai-agents') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">AI Agents</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/#why-trans-globe') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Why Trans Globe Indore</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/#faq') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">FAQs</span>
                                        </a>
                                                                                <a href="{{ url('/study-planner') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Study Planner</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/pages/terms') }}" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Terms and Policies</span>
                                        </a>
                                                                                                                                                                                            </div>

                        <div class="col-6 col-lg-2 mt-32 mt-lg-0">
                                                            <h4 class="font-16 text-white">Top Destinations</h4>

                                                                                                                                        <a href="{{ url('/destinations/australia') }}" class="d-block font-16 text-white opacity-70 mt-16">
                                            <span class="">Australia</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/destinations/uk') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">United Kingdom</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/destinations/usa') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">United States</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/destinations/canada') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Canada</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/destinations/germany') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Germany</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/destinations/new-zealand') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">New Zealand</span>
                                        </a>
                                                                                                                                                <a href="{{ url('/destinations/ireland') }}" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Ireland</span>
                                        </a>
                                                                                                                                                                                            </div>

                        <div class="col-12 col-lg-3 mt-32 mt-lg-0">
                                                                                                <h4 class="font-16 text-white">{{ $siteCms['footer_contact_heading'] ?? 'Contact Us' }}</h4>

                                                                    <div class="d-flex align-items-start gap-8 mt-20">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-width="1.5" d="M12 13.43a3.12 3.12 0 100-6.24 3.12 3.12 0 000 6.24z"/>
  <path stroke-width="1.5" d="M3.62 8.49c1.97-8.66 14.8-8.65 16.76.01 1.15 5.08-2.01 9.38-4.78 12.04a5.193 5.193 0 01-7.21 0c-2.76-2.66-5.92-6.97-4.77-12.05z"/>
</svg>                                        </div>
                                        <span class="font-16 text-white opacity-70">{{ $siteCms['contact_address'] ?? 'Office No. 503, THE VIEW Tower 1, Yeshwant Niwas Rd, above Jade Blue Showroom, Nehru Park 2, Lad Colony, Indore, Madhya Pradesh 452001' }}</span>
                                    </div>

                                                                    <div class="d-flex align-items-start gap-8 mt-16">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-miterlimit="10" stroke-width="1.5" d="M21.97 18.33c0 .36-.08.73-.25 1.09-.17.36-.39.7-.68 1.02-.49.54-1.03.93-1.64 1.18-.6.25-1.25.38-1.95.38-1.02 0-2.11-.24-3.26-.73s-2.3-1.15-3.44-1.98a28.75 28.75 0 01-3.28-2.8 28.414 28.414 0 01-2.79-3.27c-.82-1.14-1.48-2.28-1.96-3.41C2.24 8.67 2 7.58 2 6.54c0-.68.12-1.33.36-1.93.24-.61.62-1.17 1.15-1.67C4.15 2.31 4.85 2 5.59 2c.28 0 .56.06.81.18.26.12.49.3.67.56l2.32 3.27c.18.25.31.48.4.7.09.21.14.42.14.61 0 .24-.07.48-.21.71-.13.23-.32.47-.56.71l-.76.79c-.11.11-.16.24-.16.4 0 .08.01.15.03.23.03.08.06.14.08.2.18.33.49.76.93 1.28.45.52.93 1.05 1.45 1.58.54.53 1.06 1.02 1.59 1.47.52.44.95.74 1.29.92.05.02.11.05.18.08.08.03.16.04.25.04.17 0 .3-.06.41-.17l.76-.75c.25-.25.49-.44.72-.56.23-.14.46-.21.71-.21.19 0 .39.04.61.13.22.09.45.22.7.39l3.31 2.35c.26.18.44.39.55.64.1.25.16.5.16.78z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.5 9c0-.6-.47-1.52-1.17-2.27-.64-.69-1.49-1.23-2.33-1.23M22 9c0-3.87-3.13-7-7-7"/>
</svg>                                        </div>
                                        <a href="tel:{{ $contactPhoneLink }}" class="font-16 text-white opacity-70">{{ $contactPhone }}</a>
                                    </div>

                                                                    <div class="d-flex align-items-start gap-8 mt-16">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 22" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 6v10c0 4-1 5-5 5H6c-4 0-5-1-5-5V6c0-4 1-5 5-5h6c4 0 5 1 5 5zM11 4.5H7"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18.1A1.55 1.55 0 109 15a1.55 1.55 0 000 3.1z"/>
</svg>                                        </div>
                                        <span class="font-16 text-white opacity-70">{{ $siteCms['footer_hours'] ?? 'Mon to Sat: 10:00 AM–6:30 PM' }}</span>
                                    </div>

                                                                    <div class="d-flex align-items-start gap-8 mt-16">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 20.5H7c-3 0-5-1.5-5-5v-7c0-3.5 2-5 5-5h10c3 0 5 1.5 5 5v7c0 3.5-2 5-5 5z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 9l-3.13 2.5c-1.03.82-2.72.82-3.75 0L7 9"/>
</svg>                                        </div>
                                        <a href="mailto:{{ $contactEmail }}" class="font-16 text-white opacity-70">{{ $contactEmail }}</a>
                                    </div>
                                                                                    </div>


                    </div>
                </div>

                <div class="tg-footer-bottom">
                <div class="theme-footer-1__bottom-section-divider"></div>

                <div class="container d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between py-24 px-16 gap-16">
                                            <div class="font-14 text-white opacity-70">{{ $siteCms['footer_copyright'] ?? '© 2026 Trans Globe Indore, managed by GEIC. Your trusted partner for global education.' }}</div>

                    <div class="d-flex align-items-center justify-content-center gap-16 gap-lg-24">

                                                                                                                                        <a href="https://www.instagram.com/geicindore1/" target="_blank" rel="nofollow noopener" title="GEIC Indore on Instagram" class="d-flex-center size-24">
                                            <img src="store/1/default_images/social/instagram.svg" alt="Instagram" class="img-cover">
                                        </a>
                                                                                                                                                                                                            <a href="https://www.youtube.com/@geicindore5721" target="_blank" rel="nofollow noopener" title="GEIC Indore on YouTube" class="d-flex-center size-24 text-white opacity-70">
                                            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.2 3.6-6.2 3.6Z"/></svg>
                                        </a>
                                                                                                                                                                                                            <a href="http://linkedin.com/in/global-education-752796283" target="_blank" rel="nofollow noopener" title="GEIC Indore on LinkedIn" class="d-flex-center size-24 text-white opacity-70">
                                            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true"><path d="M5.3 7.9H1.1V21h4.2V7.9ZM3.2 1.3A2.45 2.45 0 1 0 3.2 6.2a2.45 2.45 0 0 0 0-4.9ZM21 13.5c0-4-2.1-5.9-4.9-5.9-2.3 0-3.3 1.2-3.8 2.1V7.9H8.1V21h4.2v-6.5c0-1.7.3-3.4 2.5-3.4 2.2 0 2.2 2 2.2 3.5V21h4.2l-.2-7.5Z"/></svg>
                                        </a>
                                                                                                                                                                                                            <a href="https://www.facebook.com/GEICINDORE1" target="_blank" rel="nofollow noopener" title="GEIC Indore on Facebook" class="d-flex-center size-24">
                                            <img src="store/1/default_images/social/facebook.svg" alt="Facebook" class="img-cover">
                                        </a>
                                                                                                                                            </div>

                </div>
                </div>
            </div>
        </div>
    </div>
        </div>




    <div class="cart-drawer no-footer bg-white py-16">
    <div class="d-flex align-items-center pb-16 border-bottom-gray-bg px-16">
        <button type="button" class="js-cart-drawer-close d-flex btn-transparent">
            <svg width="25px" height="25px" class="icons text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M14.43 5.93L20.5 12l-6.07 6.07M3.5 12h16.83"/>
</svg>        </button>

        <span class="font-14 font-weight-bold ml-8">Cart</span>
    </div>

    <div class="cart-drawer__body pb-32" data-simplebar >

    </div>

    <div class="cart-drawer__footer pt-16 border-top-gray-bg d-none px-16">
        <div class="d-flex align-items-center justify-content-between">
            <span class="text-gray-500">Subtotal</span>
            <span class="js-side-cart-subtotal text-dark font-weight-bold"></span>
        </div>

        <div class="mt-12">
            <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-block">View Cart</a>
        </div>
    </div>
</div>
<div class="cart-drawer-mask"></div>

</div>

@include('mirror.partials.study-assistant')

<!-- Template JS File -->
<script>
    var siteDomain = '{{ url('/') }}';
    var deleteAlertTitle = 'Are you sure?';
    var deleteAlertHint = 'This action cannot be undone!';
    var deleteAlertConfirm = 'Delete';
    var deleteAlertCancel = 'Cancel';
    var deleteAlertSuccess = 'Success';
    var deleteAlertFail = 'Failed';
    var deleteAlertFailHint = 'Failed to delete item!';
    var deleteAlertSuccessHint = 'Item deleted successfully.';
    var forbiddenRequestToastTitleLang = 'Forbidden Request';
    var forbiddenRequestToastMsgLang = 'You do not have access to this content.';
    var priceInvalidHintLang = 'Invalid price. Only numbers and decimals are accepted.';
    var clearLang = 'clear';
    var requestSuccessLang = 'Request completed successfully!';
    var saveSuccessLang = 'Item added successfully.';
    var requestFailedLang = 'Request Failed';
    var oopsLang = 'Oops...';
    var somethingWentWrongLang = 'Something went wrong...';
    var loadingDataPleaseWaitLang = 'Loading data. Please wait...';
    var deleteRequestLang = 'Content Deletion Request';
    var deleteRequestTitleLang = 'Are you sure to delete content?';
    var deleteRequestDescriptionLang = 'If you wish to remove your content, please provide a clear and detailed explanation.';
    var requestDetailsLang = 'Request Details';
    var sendRequestLang = 'Submit Request';
    var closeLang = 'Close';
    var generatedContentLang = 'Generated Content';
    var copyLang = 'Copy';
    var doneLang = 'Completed';
    var jsCurrentCurrency = '$';
    var defaultLocale = 'en';
    var appLocale = 'en';
    var dangerCloseIcon = `<svg width="24" height="24" class="icons text-danger" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12h12M12 18V6"/>
</svg>`;
    var directSendIcon = `<svg width="24" height="24" class="icons text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9V2l-2 2M12 2l2 2M1.98 13h4.41c.38 0 .72.21.89.55l1.17 2.34A2 2 0 0010.24 17h3.53a2 2 0 001.79-1.11l1.17-2.34a1 1 0 01.89-.55h4.36"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 5.13c-3.54.52-5 2.6-5 6.87v3c0 5 2 7 7 7h6c5 0 7-2 7-7v-3c0-4.27-1.46-6.35-5-6.87"/>
</svg>`;
    var closeIcon = `<svg width="25px" height="25px" class="close-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12h12M12 18V6"/>
</svg>`;
    var bulDangerIcon = `<svg width="32px" height="32px" class="icons text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
  <path d="M21.76 15.92L15.36 4.4C14.5 2.85 13.31 2 12 2s-2.5.85-3.36 2.4l-6.4 11.52c-.81 1.47-.9 2.88-.25 3.99.65 1.11 1.93 1.72 3.61 1.72h12.8c1.68 0 2.96-.61 3.61-1.72.65-1.11.56-2.53-.25-3.99z" opacity=".4"/>
  <path d="M12 14.75c-.41 0-.75-.34-.75-.75V9c0-.41.34-.75.75-.75s.75.34.75.75v5c0 .41-.34.75-.75.75zM12 18c-.06 0-.13-.01-.2-.02a.636.636 0 01-.18-.06.757.757 0 01-.18-.09l-.15-.12c-.18-.19-.29-.45-.29-.71 0-.26.11-.52.29-.71l.15-.12c.06-.04.12-.07.18-.09.06-.03.12-.05.18-.06.13-.03.27-.03.39 0 .07.01.13.03.19.06.06.02.12.05.18.09l.15.12c.18.19.29.45.29.71 0 .26-.11.52-.29.71l-.15.12c-.06.04-.12.07-.18.09-.06.03-.12.05-.19.06-.06.01-.13.02-.19.02z"/>
</svg>`;
    var defaultAvatarPath = "store/1/default_images/default_profile.jpg";
    var themeColorsMode = {"light":{"primary":"#E31E24","primary_saturated":"#67a9ff","secondary":"#0e2145","accent":"#fe6257","success":"#3fcd82","info":"#67a9ff","warning":"#ffa200","danger":"#f63c3c","dark":"#121f3e","black":"#000000","white":"#ffffff","gray_100":"#fafcff","gray_200":"#f0f4f9","gray_300":"#e9edf3","gray_400":"#cdd5e2","gray_500":"#97a7bf","gray":"#f5f8f9","section_bg":"#eaf0f3"},"dark":{"primary":"#3e93ff","primary_saturated":"#8dbeff","secondary":"#2658b7","accent":"#ff8077","success":"#5ade98","info":"#8dbeff","warning":"#ffb32d","danger":"#fe6363","dark":"#aab8c5","black":"#e1eaf6","white":"#1e1f26","gray_100":"#272832","gray_200":"#30313e","gray_300":"#3e404e","gray_400":"#5d5f72","gray_500":"#8391a2","gray":"#17181e","section_bg":"#2d323a"}};
</script>


<script type="text/javascript" src="assets/design_1/js/app.min.js"></script>
<script type="text/javascript" src="assets/default/vendors/simplebar/simplebar.min.js"></script>
<script defer src="assets/design_1/js/parts/content_delete.min.js"></script>





                                            <script>
                                                var twoColumnsHeroHighlightWords = ["Learning","Growing","Mastering","Building","Upskilling"];

                                                $(document).ready(function () {
                                                    if ($('.js-two-columns-hero-highlight-words-card').length) {
                                                        handleHighlightWords(twoColumnsHeroHighlightWords, 'js-two-columns-hero-highlight-words-card')
                                                    }
                                                })
                                            </script>
                                            <script src="assets/vendors/counterup/jquery.counterup.min.js"></script>

    <script src="assets/design_1/landing_builder/js/components/statistics.min.js"></script>

    <script src="assets/default/vendors/swiper/swiper-bundle.min.js"></script>
    <script src="assets/design_1/js/parts/swiper_slider.min.js"></script>
    <script type="text/javascript" src="assets/default/vendors/simplebar/simplebar.min.js"></script>


    <script src="assets/vendors/typed/typedjs.js"></script>

    <script src="assets/vendors/plyr.io/plyr.min.js"></script>
    <script src="assets/design_1/js/parts/time-counter-down.min.js"></script>
    <script src="assets/design_1/js/parts/video_player_helpers.min.js"></script>
    <script src="assets/design_1/landing_builder/js/front.min.js"></script>



<script>



</script>

<script src="assets/design_1/js/parts/general.min.js"></script>

@include('mirror.partials.site-analytics')

</body>

<!-- Mirrored from www.geic.in/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 25 Aug 2026 16:21:50 GMT -->
</html>
