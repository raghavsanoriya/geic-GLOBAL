#!/usr/bin/env bash

set -Eeuo pipefail

staging_root="/home2/geicic3c/staging"
current_public="$staging_root/current/public"
webroot="$staging_root/webroot"
next_webroot="$staging_root/webroot.next"
previous_webroot="$staging_root/webroot.previous"

if [[ ! -d "$staging_root" || ! -d "$current_public" ]]; then
    echo "Staging directory or active release is missing." >&2
    exit 78
fi

if [[ "$webroot" != "$staging_root/webroot" || "$next_webroot" != "$staging_root/webroot.next" ]]; then
    echo "Refusing unsafe staging webroot path." >&2
    exit 64
fi

rm -rf -- "$next_webroot"
mkdir "$next_webroot"

tar -cf - -C "$current_public" . | tar -xf - -C "$next_webroot"

cat > "$next_webroot/index.php" <<'PHP'
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../current/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../current/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../current/bootstrap/app.php';

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
