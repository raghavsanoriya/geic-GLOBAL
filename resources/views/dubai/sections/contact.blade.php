@php($oldText = fn (string $key): string => is_string(old($key)) ? old($key) : '')
<section id="contact" class="contact-section-2 tg-contact section-padding pt-0">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="tg-contact-info">
                    <div class="section-title mb-4">
                        <h6 class="text-white">{{ $page['contact_eyebrow'] }}</h6>
                        <h2 class="text-white">{{ $page['contact_title'] }}</h2>
                    </div>
                    <p>{{ $page['contact_copy'] }}</p>
                    <button type="button" class="theme-btn tg-mobile-only tg-open-sheet-inline" data-open-sheet>
                        <i class="fas fa-file-signature" aria-hidden="true"></i> Fill The Enquiry Form
                    </button>
                    <ul class="tg-contact-list">
                        <li><span class="icon"><i class="fas fa-phone-alt"></i></span><div><b>Call us</b><a href="{{ $page['telUrl'] }}">{{ $page['phone'] }}</a></div></li>
                        <li><span class="icon"><i class="fas fa-envelope"></i></span><div><b>Email</b><a href="mailto:{{ $page['email'] }}">{{ $page['email'] }}</a></div></li>
                        <li><span class="icon"><i class="fas fa-clock"></i></span><div><b>Office hours</b>{{ $page['office_hours'] }}</div></li>
                        <li><span class="icon"><i class="fas fa-map-marker-alt"></i></span><div><b>Visit us</b>{{ $page['address'] }}</div></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-form-items tg-form-card" id="enquiry-sheet" aria-labelledby="enquiry-title">
                    <div class="tg-sheet-bar tg-mobile-only">
                        <span class="tg-sheet-handle" aria-hidden="true"></span>
                        <button type="button" class="tg-sheet-close" data-close-sheet aria-label="Close form"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="title">
                        <h2 id="enquiry-title">{{ $page['form_title'] }}</h2>
                        <p>Fields marked * are required.</p>
                    </div>

                    @if (session('enquiry_success'))
                        <div class="tg-alert tg-alert-success" role="status"><i class="fas fa-check-circle"></i><div><b>{{ session('enquiry_success') }}</b></div></div>
                    @endif
                    @if ($errors->any())
                        <div class="tg-alert tg-alert-error" role="alert"><i class="fas fa-exclamation-circle"></i><div><b>Please check the highlighted fields.</b></div></div>
                    @endif

                    <form method="POST" action="{{ route('dubai.enquire') }}" novalidate>
                        @csrf
                        <div class="tg-hp" aria-hidden="true"><label for="enq-website">Website</label><input type="text" id="enq-website" name="website" tabindex="-1" autocomplete="off"></div>
                        <div class="row g-4">
                            @foreach ([
                                ['id' => 'enq-name', 'name' => 'full_name', 'label' => 'Full name *', 'type' => 'text', 'auto' => 'name', 'max' => 120, 'mode' => null],
                                ['id' => 'enq-phone', 'name' => 'phone', 'label' => 'Phone number *', 'type' => 'tel', 'auto' => 'tel', 'max' => 24, 'mode' => 'tel'],
                                ['id' => 'enq-email', 'name' => 'email', 'label' => 'Email address *', 'type' => 'email', 'auto' => 'email', 'max' => 160, 'mode' => 'email'],
                                ['id' => 'enq-city', 'name' => 'city', 'label' => 'City', 'type' => 'text', 'auto' => 'address-level2', 'max' => 100, 'mode' => null],
                            ] as $input)
                                <div class="col-md-6">
                                    <div class="form-clt">
                                        <label for="{{ $input['id'] }}">{{ $input['label'] }}</label>
                                        <input type="{{ $input['type'] }}" id="{{ $input['id'] }}" name="{{ $input['name'] }}" value="{{ $oldText($input['name']) }}" maxlength="{{ $input['max'] }}" autocomplete="{{ $input['auto'] }}" @if ($input['mode']) inputmode="{{ $input['mode'] }}" @endif @if (str_ends_with($input['label'], '*')) required @endif @error($input['name']) aria-invalid="true" aria-describedby="{{ $input['id'] }}-error" @enderror>
                                        @error($input['name'])<span class="tg-field-error" id="{{ $input['id'] }}-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-6">
                                <div class="form-clt">
                                    <label for="enq-level">Study level *</label>
                                    <select id="enq-level" name="study_level" required @error('study_level') aria-invalid="true" @enderror>
                                        <option value="">Select level</option>
                                        @foreach (['Undergraduate' => 'Undergraduate (Bachelor\'s)', 'Postgraduate' => 'Postgraduate (Master\'s)', 'Not sure yet' => 'Not sure yet'] as $value => $label)
                                            <option value="{{ $value }}" @selected($oldText('study_level') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('study_level')<span class="tg-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-clt">
                                    <label for="enq-intake">Preferred intake *</label>
                                    <select id="enq-intake" name="preferred_intake" required @error('preferred_intake') aria-invalid="true" @enderror>
                                        <option value="">Select intake</option>
                                        @foreach (['September intake', 'January intake', 'May intake', 'Not sure yet'] as $value)
                                            <option value="{{ $value }}" @selected($oldText('preferred_intake') === $value)>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('preferred_intake')<span class="tg-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-clt">
                                    <label for="enq-course">Course of interest</label>
                                    <input type="text" id="enq-course" name="preferred_course" value="{{ $oldText('preferred_course') }}" maxlength="160" placeholder="For example: MBA, Computer Science, Hospitality">
                                    @error('preferred_course')<span class="tg-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-clt">
                                    <label for="enq-message">Message</label>
                                    <textarea id="enq-message" name="message" maxlength="1200" placeholder="Tell us your plans or any questions">{{ $oldText('message') }}</textarea>
                                    @error('message')<span class="tg-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="tg-consent-check" for="enq-consent">
                                    <input type="checkbox" id="enq-consent" name="consent" value="1" @checked($oldText('consent') !== '') required @error('consent') aria-invalid="true" @enderror>
                                    <span>I agree to be contacted by Trans Globe Indore about studying in Dubai. *</span>
                                </label>
                                @error('consent')<span class="tg-field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="theme-btn">{{ $page['form_button'] }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
