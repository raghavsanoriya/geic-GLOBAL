# Trans Globe landing page

A responsive static landing page based on the supplied study-abroad brief. It uses plain HTML, CSS, and JavaScript, so it can be uploaded directly to cPanel without a Node.js server or build step.

## Preview locally

Run any static file server in this folder, for example:

```bash
python3 -m http.server 4173
```

Then open `http://localhost:4173`.

## Deploy to cPanel

Upload these files and folders to `public_html`:

- `index.html`
- `styles.css`
- `script.js`
- `assets/hero-campus.webp`

## Replace before launch

- Brand name and logo if the final brand is not “Trans Globe”
- Verified student names, testimonials, photos, and videos
- Team photographs, full names, and official roles
- WhatsApp business number and both counsellor phone numbers
- Email address, office address, privacy policy, and terms links
- The preview-only form handler with a cPanel PHP mail handler, CRM, or form service

The original generated PNG is kept in `assets/hero-campus-source.png` as a source asset; only the optimized WebP is needed on the live site.
