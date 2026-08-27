#!/usr/bin/env bash

set -Eeuo pipefail

staging_root="/home2/geicic3c/staging"
release_root="${1:-$staging_root/current}"
current_public="$release_root/public"
public_staging_root="/home2/geicic3c/public_html/staging"
webroot="$public_staging_root/webroot"
next_webroot="$public_staging_root/webroot.next"
previous_webroot="$public_staging_root/webroot.previous"

if [[ ! -d "$release_root" || ! -d "$current_public" ]]; then
    echo "Staging directory or active release is missing." >&2
    exit 78
fi

if [[ "$webroot" != "$public_staging_root/webroot" || "$next_webroot" != "$public_staging_root/webroot.next" ]]; then
    echo "Refusing unsafe staging webroot path." >&2
    exit 64
fi

mkdir -p "$public_staging_root"
chmod 0755 "$public_staging_root"
rm -rf -- "$next_webroot"
mkdir "$next_webroot"

tar -cf - -C "$current_public" . | tar -xf - -C "$next_webroot"
printf '%s' "$(basename "$release_root")" > "$next_webroot/release.txt"

cat > "$next_webroot/index.php" <<'PHP'
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = '/home2/geicic3c/staging/current/storage/framework/maintenance.php')) {
    require $maintenance;
}

require '/home2/geicic3c/staging/current/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once '/home2/geicic3c/staging/current/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP

cat > "$next_webroot/.htaccess" <<'HTACCESS'
Options -Indexes
RewriteEngine On

RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

RewriteCond %{HTTP:X-XSRF-TOKEN} .
RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-TOKEN}]

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
HTACCESS

rm -rf -- "$previous_webroot"
if [[ -d "$webroot" ]]; then
    mv "$webroot" "$previous_webroot"
fi
mv "$next_webroot" "$webroot"
rm -rf -- "$previous_webroot"

echo "Staging webroot installed."
