#!/usr/bin/env bash
set -Eeuo pipefail

application_root="/home2/geicic3c/apps/geic-production/current"
if [[ -f "$application_root/storage/framework/down" ]]; then
    exit 0
fi
if [[ ! -f "$application_root/artisan" ]]; then
    echo "The active GEIC Laravel release is unavailable." >&2
    exit 78
fi

for php_candidate in /opt/cpanel/ea-php83/root/usr/bin/php /usr/local/bin/ea-php83 /usr/local/bin/php /usr/bin/php; do
    if [[ -x "$php_candidate" ]]; then
        cd "$application_root"
        exec "$php_candidate" artisan schedule:run --no-interaction
    fi
done

echo "The production PHP CLI is unavailable." >&2
exit 69
