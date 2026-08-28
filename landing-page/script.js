const header = document.querySelector("[data-header]");
const menuButton = document.querySelector("[data-menu-button]");
const mobileMenu = document.querySelector("[data-mobile-menu]");
const profileForm = document.querySelector("[data-profile-form]");
const countrySelect = document.querySelector("[data-country-select]");
const formStatus = document.querySelector("[data-form-status]");
const toast = document.querySelector("[data-toast]");
const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
const heroSlides = document.querySelectorAll("[data-hero-slide-image]");
const heroSlideButtons = document.querySelectorAll("[data-hero-slide-button]");
const bottomNavLinks = document.querySelectorAll("[data-bottom-nav-link]");

const closeMenu = () => {
  if (!menuButton || !mobileMenu) return;
  menuButton.setAttribute("aria-expanded", "false");
  menuButton.setAttribute("aria-label", "Open navigation");
  mobileMenu.classList.remove("is-open");
  document.body.classList.remove("menu-open");
};

if (menuButton && mobileMenu) {
  menuButton.addEventListener("click", () => {
    const willOpen = menuButton.getAttribute("aria-expanded") !== "true";
    menuButton.setAttribute("aria-expanded", String(willOpen));
    menuButton.setAttribute("aria-label", willOpen ? "Close navigation" : "Open navigation");
    mobileMenu.classList.toggle("is-open", willOpen);
    document.body.classList.toggle("menu-open", willOpen);
  });

  mobileMenu.querySelectorAll("a").forEach((link) => link.addEventListener("click", closeMenu));

  window.addEventListener("resize", () => {
    if (window.innerWidth > 880) closeMenu();
  });
}

const updateHeader = () => {
  if (header) header.dataset.scrolled = String(window.scrollY > 12);
};

updateHeader();
window.addEventListener("scroll", updateHeader, { passive: true });

const revealElements = document.querySelectorAll("[data-reveal]");

if (reducedMotion.matches || !("IntersectionObserver" in window)) {
  revealElements.forEach((element) => element.classList.add("is-revealed"));
} else {
  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-revealed");
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.12, rootMargin: "0px 0px -32px" },
  );

  revealElements.forEach((element) => revealObserver.observe(element));
}

document.querySelectorAll("[data-country]").forEach((countryLink) => {
  countryLink.addEventListener("click", () => {
    if (countrySelect) {
      countrySelect.value = countryLink.dataset.country || "";
      countrySelect.dispatchEvent(new Event("change", { bubbles: true }));
    }
  });
});

if (heroSlides.length && heroSlideButtons.length) {
  let activeHeroSlide = 0;
  let heroSlideTimer;

  const showHeroSlide = (index) => {
    activeHeroSlide = (index + heroSlides.length) % heroSlides.length;
    heroSlides.forEach((slide, slideIndex) => slide.classList.toggle("is-active", slideIndex === activeHeroSlide));
    heroSlideButtons.forEach((button, buttonIndex) => {
      const isActive = buttonIndex === activeHeroSlide;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", String(isActive));
    });
  };

  const startHeroSlider = () => {
    if (reducedMotion.matches) return;
    window.clearInterval(heroSlideTimer);
    heroSlideTimer = window.setInterval(() => showHeroSlide(activeHeroSlide + 1), 6000);
  };

  heroSlideButtons.forEach((button, index) => {
    button.addEventListener("click", () => {
      showHeroSlide(index);
      startHeroSlider();
    });
  });

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) window.clearInterval(heroSlideTimer);
    else startHeroSlider();
  });

  startHeroSlider();
}

const universityMarquee = document.querySelector("[data-university-marquee]");
const universityMarqueeToggle = document.querySelector("[data-marquee-toggle]");

if (universityMarquee) {
  const track = universityMarquee.querySelector("[data-university-track]");
  const group = universityMarquee.querySelector("[data-university-group]");
  const toggleLabel = universityMarqueeToggle?.querySelector("[data-marquee-toggle-label]");
  const toggleIcon = universityMarqueeToggle?.querySelector("[data-marquee-toggle-icon]");
  let clonedGroup;

  universityMarquee.querySelectorAll("img").forEach((image) => {
    image.loading = "eager";
  });

  const ensureMarqueeClone = () => {
    if (!track || !group || clonedGroup) return;

    clonedGroup = group.cloneNode(true);
    clonedGroup.classList.add("university-partners-group-clone");
    clonedGroup.setAttribute("aria-hidden", "true");
    clonedGroup.querySelectorAll("img").forEach((image) => {
      image.alt = "";
    });
    track.appendChild(clonedGroup);
  };

  const removeMarqueeClone = () => {
    clonedGroup?.remove();
    clonedGroup = undefined;
  };

  const setMarqueePaused = (paused) => {
    universityMarquee.classList.toggle("is-paused", paused);
    universityMarqueeToggle?.setAttribute("aria-pressed", String(paused));

    if (toggleLabel) toggleLabel.textContent = paused ? "Resume logos" : "Pause logos";
    if (toggleIcon) {
      toggleIcon.setAttribute("d", paused ? "M6 4l10 6-10 6z" : "M6 4h3v12H6zM11 4h3v12h-3z");
    }
  };

  const syncMarqueeMotionPreference = () => {
    if (reducedMotion.matches) {
      removeMarqueeClone();
      setMarqueePaused(true);
      if (universityMarqueeToggle) universityMarqueeToggle.hidden = true;
      return;
    }

    ensureMarqueeClone();
    setMarqueePaused(false);
    if (universityMarqueeToggle) universityMarqueeToggle.hidden = false;
  };

  universityMarqueeToggle?.addEventListener("click", () => {
    setMarqueePaused(!universityMarquee.classList.contains("is-paused"));
  });

  syncMarqueeMotionPreference();

  if (typeof reducedMotion.addEventListener === "function") {
    reducedMotion.addEventListener("change", syncMarqueeMotionPreference);
  } else {
    reducedMotion.addListener(syncMarqueeMotionPreference);
  }
}

if (bottomNavLinks.length && "IntersectionObserver" in window) {
  const bottomNavSections = [...bottomNavLinks]
    .map((link) => document.querySelector(link.getAttribute("href")))
    .filter(Boolean);

  const setActiveBottomNav = (sectionId) => {
    bottomNavLinks.forEach((link) => {
      const isActive = link.getAttribute("href") === `#${sectionId}`;
      link.classList.toggle("is-active", isActive);
      if (isActive) link.setAttribute("aria-current", "page");
      else link.removeAttribute("aria-current");
    });
  };

  const bottomNavObserver = new IntersectionObserver(
    (entries) => {
      const visibleEntry = entries
        .filter((entry) => entry.isIntersecting)
        .sort((first, second) => second.intersectionRatio - first.intersectionRatio)[0];
      if (visibleEntry) setActiveBottomNav(visibleEntry.target.id);
    },
    { rootMargin: "-25% 0px -45%", threshold: [0.1, 0.3, 0.6] },
  );

  bottomNavSections.forEach((section) => bottomNavObserver.observe(section));
}

const googleReviewSource = "https://www.google.com/search?q=geic+indore#lrd=0x3962fd400e5c61eb:0x6db8cf73bcf20625,1,,,,";
const reviews = [
  { name: "Arun Rawat", date: "3 weeks ago", text: "The staff was friendly, and the overall environment was positive and motivating." },
  { name: "Anuj Joshi", date: "a month ago", text: "Excellent service and a very supportive team throughout my journey." },
  { name: "Gurshan Singh", date: "2 weeks ago", text: "Their team demonstrated deep expertise, complete transparency, and a genuine commitment to my goals." },
  { name: "Siddhi Janve", date: "6 months ago", text: "The team offers clear guidance and takes time to explain each step of the process." },
  { name: "Mustafa Nalwala", date: "6 months ago", text: "Helpful and attentive counselling team made the whole process seamless." },
  { name: "Kundan Sharma", date: "6 months ago", text: "The team is very supportive and gives genuine guidance at every step." },
  { name: "Aaniya Bhavsar", date: "4 months ago", text: "I had so many doubts regarding study abroad and he cleared everything." },
  { name: "Antar Singh", date: "4 months ago", text: "They explained everything. You must visit once if you are planning for studying abroad." },
  { name: "Mohammed Safdari", date: "9 months ago", text: "Everything was managed smoothly and efficiently." },
  { name: "Rishika Katara", date: "7 months ago", text: "The counselor was knowledgeable, patient, and clearly explained the process while addressing all my doubts." },
];

const reviewSlider = document.querySelector("[data-review-slider]");

if (reviewSlider) {
  const reviewText = reviewSlider.querySelector("[data-review-text]");
  const reviewName = reviewSlider.querySelector("[data-review-name]");
  const reviewDate = reviewSlider.querySelector("[data-review-date]");
  const reviewInitials = reviewSlider.querySelector("[data-review-initials]");
  const reviewPosition = reviewSlider.querySelector("[data-review-position]");
  const reviewSource = reviewSlider.querySelector("[data-review-source]");
  const previousReview = document.querySelector("[data-review-previous]");
  const nextReview = document.querySelector("[data-review-next]");
  let activeReview = 0;

  const updateReview = () => {
    const review = reviews[activeReview];
    const initials = review.name
      .split(/\s+/)
      .slice(0, 2)
      .map((part) => part.charAt(0))
      .join("")
      .toUpperCase();

    reviewText.textContent = review.text;
    reviewName.textContent = review.name;
    reviewDate.textContent = `Google review · ${review.date}`;
    reviewInitials.textContent = initials;
    reviewPosition.textContent = String(activeReview + 1).padStart(2, "0");
    reviewSource.href = googleReviewSource;
  };

  previousReview?.addEventListener("click", () => {
    activeReview = (activeReview - 1 + reviews.length) % reviews.length;
    updateReview();
  });

  nextReview?.addEventListener("click", () => {
    activeReview = (activeReview + 1) % reviews.length;
    updateReview();
  });

  updateReview();
}

let toastTimer;

const showToast = (message) => {
  if (!toast) return;
  window.clearTimeout(toastTimer);
  toast.textContent = message;
  toast.classList.add("is-visible");
  toastTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 4500);
};

document.querySelectorAll("[data-whatsapp]").forEach((button) => {
  button.addEventListener("click", () => {
    showToast("WhatsApp is ready to connect—replace the placeholder with your verified business number before launch.");
  });
});

if (profileForm && formStatus) {
  profileForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (!profileForm.checkValidity()) {
      profileForm.reportValidity();
      formStatus.textContent = "Please complete the required fields before continuing.";
      formStatus.classList.add("is-visible");
      return;
    }

    const submitButton = profileForm.querySelector("button[type='submit']");
    const submitLabel = submitButton?.querySelector("span");

    if (submitButton) submitButton.disabled = true;
    if (submitLabel) submitLabel.textContent = "Sending enquiry...";
    formStatus.textContent = "";
    formStatus.classList.remove("is-visible");

    try {
      const response = await fetch(profileForm.dataset.endpoint || "/landing/form-handler.php", {
        method: "POST",
        headers: {
          Accept: "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
        },
        body: new FormData(profileForm),
      });
      const result = await response.json().catch(() => ({}));

      if (!response.ok || !result.success) {
        throw new Error(result.message || "We could not send your enquiry. Please try again.");
      }

      profileForm.reset();
      formStatus.textContent = result.message || "Thank you. Your profile evaluation request has been received.";
    } catch (error) {
      formStatus.textContent = error.message || "We could not send your enquiry. Please try again.";
    }

    formStatus.classList.add("is-visible");
    formStatus.scrollIntoView({ behavior: reducedMotion.matches ? "auto" : "smooth", block: "nearest" });
    if (submitButton) submitButton.disabled = false;
    if (submitLabel) submitLabel.textContent = "Get my free profile evaluation";
  });

  profileForm.addEventListener("input", () => {
    if (!formStatus.classList.contains("is-visible")) return;
    formStatus.classList.remove("is-visible");
    formStatus.textContent = "";
  });
}

document.querySelectorAll(".faq-list details").forEach((detail) => {
  detail.addEventListener("toggle", () => {
    if (!detail.open) return;
    document.querySelectorAll(".faq-list details").forEach((otherDetail) => {
      if (otherDetail !== detail) otherDetail.open = false;
    });
  });
});

const year = document.querySelector("[data-year]");
if (year) year.textContent = String(new Date().getFullYear());
