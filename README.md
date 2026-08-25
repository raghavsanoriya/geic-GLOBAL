# GEIC Rocket LMS Laravel conversion

This is a Laravel 13 conversion of the downloaded HTML mirror in `../GEIC/lms.rocket-soft.org`.

## Local preview

Docker is the only local prerequisite:

```powershell
cd "D:\GEIC\LMS rocket\GEIC-laravel"
docker compose up -d
```

Open <http://localhost:8085>. Stop it later with `docker compose down`.

## Structure

- `app/Http/Controllers/MirrorPageController.php` resolves the original mirrored URLs.
- `resources/views/mirror` contains the converted Blade pages.
- `public` contains the downloaded CSS, JavaScript, images, fonts, and media.

The HTML mirror contains presentation data only. Forms and account/payment actions that originally depended on the remote Rocket LMS backend are visual previews until corresponding Laravel controllers, models, and database features are implemented.
