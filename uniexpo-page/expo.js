(function () {
  "use strict";
  var endpoint = window.EXPO_CONFIG && window.EXPO_CONFIG.appsScriptUrl;
  var form = document.getElementById("registration-form");
  var status = document.getElementById("form-status");
  if (!form) return;
  function showError(field, message) {
    var input = form.elements[field];
    var output = document.getElementById(field + "-error");
    if (input) input.setAttribute("aria-invalid", message ? "true" : "false");
    if (output) output.textContent = message;
  }
  function valid(values) {
    var mobile = String(values.mobile || "").replace(/\D/g, "");
    var errors = {
      name: values.name && values.name.trim() ? "" : "Please enter your name.",
      email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email || "") ? "" : "Enter a valid email address.",
      mobile: /^\d{10}$/.test(mobile) ? "" : "Enter a valid 10-digit mobile number.",
      country: values.country ? "" : "Please select a study destination."
    };
    Object.keys(errors).forEach(function (field) { showError(field, errors[field]); });
    return !Object.keys(errors).some(function (field) { return errors[field]; });
  }
  function showConfirmation() {
    var style = document.createElement("style");
    style.textContent = ".expo-success-backdrop{position:fixed;inset:0;z-index:1000;display:grid;place-items:center;padding:24px;background:rgba(4,18,50,.72);backdrop-filter:blur(5px);animation:expoFade .24s ease-out}.expo-success-dialog{width:min(100%,430px);padding:40px 30px 30px;border-radius:18px;background:#fff;color:#263957;text-align:center;box-shadow:0 28px 90px rgba(0,0,0,.35);animation:expoPop .38s cubic-bezier(.2,.9,.2,1)}.expo-success-check{width:78px;height:78px;margin:0 auto 18px;border-radius:50%;display:grid;place-items:center;background:#e8f8e8;color:#1c9a4b;font:800 43px/1 Arial}.expo-success-dialog h2{margin:0 0 10px;color:#071c4d;font:800 27px/1.1 Montserrat,sans-serif}.expo-success-dialog p{margin:0;color:#53627a;font:16px/1.55 'Nunito Sans',sans-serif}.expo-success-dialog button{margin-top:24px;padding:12px 28px;border:0;border-radius:5px;background:#e5252a;color:#fff;font:700 12px Montserrat,sans-serif;letter-spacing:.05em;cursor:pointer}@keyframes expoFade{from{opacity:0}to{opacity:1}}@keyframes expoPop{from{opacity:0;transform:scale(.86) translateY(16px)}to{opacity:1;transform:none}}@media(prefers-reduced-motion:reduce){.expo-success-backdrop,.expo-success-dialog{animation:none}}";
    document.head.appendChild(style);
    var popup = document.createElement("div");
    popup.className = "expo-success-backdrop";
    popup.innerHTML = '<section class="expo-success-dialog" role="dialog" aria-modal="true" aria-labelledby="expo-success-title"><div class="expo-success-check" aria-hidden="true">✓</div><h2 id="expo-success-title">Registration Complete</h2><p>Your seat for <strong>Global Uni Expo 2026</strong> has been registered successfully.</p><button type="button">DONE</button></section>';
    function close() { popup.remove(); style.remove(); }
    popup.querySelector("button").addEventListener("click", close);
    popup.addEventListener("click", function (event) { if (event.target === popup) close(); });
    document.body.appendChild(popup);
    popup.querySelector("button").focus();
  }
  form.addEventListener("submit", function (event) {
    event.preventDefault();
    if (form.classList.contains("is-loading")) return;
    var values = Object.fromEntries(new FormData(form).entries());
    if (!valid(values)) {
      status.textContent = "Please correct the highlighted fields.";
      status.className = "form-status is-error";
      return;
    }
    var button = form.querySelector("button[type=submit]");
    var payload = Object.assign(values, { eventCity: "Indore", eventDate: "2026-10-24", submittedAt: new Date().toISOString() });
    form.classList.add("is-loading");
    button.disabled = true;
    button.textContent = "REGISTRATION RECEIVED ✓";
    status.textContent = "Registration received. Saving your details…";
    status.className = "form-status is-success";
    showConfirmation();
    window.setTimeout(function () {
      if (!endpoint) {
        status.textContent = "We could not submit your registration. Please try again.";
        status.className = "form-status is-error";
        button.textContent = "REGISTER NOW";
        button.disabled = false;
        form.classList.remove("is-loading");
        return;
      }
      fetch(endpoint, { method: "POST", mode: "no-cors", headers: { "Content-Type": "text/plain;charset=utf-8" }, body: JSON.stringify(payload) })
        .then(function () {
          form.reset();
          button.textContent = "REGISTRATION COMPLETED ✓";
          status.textContent = "Done! Your registration for Global Uni Expo 2026 is complete.";
          status.className = "form-status is-success";
        })
        .catch(function () {
          status.textContent = "We could not submit your registration. Please try again.";
          status.className = "form-status is-error";
          button.textContent = "REGISTER NOW";
          button.disabled = false;
        })
        .finally(function () { form.classList.remove("is-loading"); });
    }, 0);
  });
}());
