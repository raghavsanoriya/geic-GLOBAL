# GEIC Trans Globe Indore LMS Laravel conversion

This is a Laravel 13 conversion of the downloaded HTML mirror in `../GEIC/www.geic.in`.

## Local preview

Docker is the only local prerequisite:

```powershell
cd "D:\GEIC\Trans Globe Indore LMS\GEIC-laravel"
docker compose up -d
```

Open <http://localhost:8085>. Stop it later with `docker compose down`.

## Structure

- `app/Http/Controllers/MirrorPageController.php` resolves the original mirrored URLs.
- `resources/views/mirror` contains the converted Blade pages.
- `public` contains the downloaded CSS, JavaScript, images, fonts, and media.

The HTML mirror contains presentation data only. Forms and account/payment actions that originally depended on the remote Trans Globe Indore LMS backend are visual previews until corresponding Laravel controllers, models, and database features are implemented.
