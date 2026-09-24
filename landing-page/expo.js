(function () {
  "use strict";
  var endpoint = "https://script.google.com/macros/s/AKfycbyHn9J57MrkRPLzUF4WCPh8eogwPP4RrU6-tCo4z1p6LTrfLZuerDgW12x2TGeF8I7F/exec";
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
    var errors = { name: values.name && values.name.trim() ? "" : "Please enter your name.", email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email || "") ? "" : "Enter a valid email address.", mobile: /^\d{10}$/.test(mobile) ? "" : "Enter a valid 10-digit mobile number.", country: values.country ? "" : "Please select a study destination." };
    Object.keys(errors).forEach(function (field) { showError(field, errors[field]); });
    return !Object.keys(errors).some(function (field) { return errors[field]; });
  }
  form.addEventListener("submit", function (event) {
    event.preventDefault();
    if (form.classList.contains("is-loading")) return;
    var values = Object.fromEntries(new FormData(form).entries());
    if (!valid(values)) { status.textContent = "Please correct the highlighted fields."; status.className = "form-status is-error"; return; }
    var button = form.querySelector("button[type=submit]");
    var payload = Object.assign(values, { eventCity: "Indore", eventDate: "2026-10-24", submittedAt: new Date().toISOString() });
    form.classList.add("is-loading"); button.disabled = true; status.textContent = "Submitting your registration…"; status.className = "form-status";
    fetch(endpoint, { method: "POST", mode: "no-cors", headers: { "Content-Type": "text/plain;charset=utf-8" }, body: JSON.stringify(payload) })
      .then(function () { form.reset(); status.textContent = "Thank you. Your spot at Global Uni Expo 2026 is reserved."; status.className = "form-status is-success"; })
      .catch(function () { status.textContent = "We could not submit your registration. Please try again."; status.className = "form-status is-error"; })
      .finally(function () { form.classList.remove("is-loading"); button.disabled = false; });
  });
}());
