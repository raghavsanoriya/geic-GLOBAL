#!/usr/bin/env bash

set -Eeuo pipefail

repository_root="$(git rev-parse --show-toplevel)"
branch_name="$(git rev-parse --abbrev-ref HEAD)"
release_name="$(git rev-parse HEAD)"

deploy_account_home="/home2/geicic3c"
export COMPOSER_HOME="$deploy_account_home/.composer"
mkdir -p "$COMPOSER_HOME"

case "$branch_name" in
    develop)
        deploy_root="/home2/geicic3c/staging"
        ;;
    main)
        deploy_root="/home2/geicic3c/apps/geic-production"
        ;;
    *)
        echo "Branch $branch_name is not deployable." >&2
        exit 64
        ;;
esac

if [[ "$deploy_root" != "/home2/geicic3c/staging" && "$deploy_root" != "/home2/geicic3c/apps/geic-production" ]]; then
    echo "Refusing unsafe deployment root: $deploy_root" >&2
    exit 64
fi

shared_dir="$deploy_root/shared"
releases_dir="$deploy_root/releases"
release_dir="$releases_dir/$release_name"
next_link="$deploy_root/current.next"
current_link="$deploy_root/current"
previous_target=""
release_activated=false
vendor_archive_next=""

cleanup_partial_release() {
    status=$?

    if [[ "$status" -ne 0 && "$release_activated" != true && -d "$release_dir" && "$release_dir" == "$releases_dir/"* ]]; then
        rm -rf -- "$release_dir"
        echo "Removed incomplete release $release_name." >&2
    fi

    if [[ "$status" -ne 0 && -n "$vendor_archive_next" && -f "$vendor_archive_next" && "$vendor_archive_next" == "$shared_dir/vendor-cache/"*.next.* ]]; then
        rm -f -- "$vendor_archive_next"
    fi
}

trap cleanup_partial_release EXIT

if [[ -L "$current_link" ]]; then
    previous_target="$(readlink -f "$current_link")"
fi

if [[ ! -f "$shared_dir/.env" ]]; then
    echo "Missing $shared_dir/.env. Configure the environment before the first deployment." >&2
    exit 78
fi

mkdir -p \
    "$shared_dir/database" \
    "$shared_dir/storage/app/public" \
    "$shared_dir/storage/framework/cache/data" \
    "$shared_dir/storage/framework/sessions" \
    "$shared_dir/storage/framework/testing" \
    "$shared_dir/storage/framework/views" \
    "$shared_dir/storage/logs" \
    "$shared_dir/vendor-cache" \
    "$releases_dir"

if [[ -e "$release_dir" ]]; then
    if [[ "$previous_target" == "$release_dir" && -f "$release_dir/public/release.txt" ]]; then
        echo "Release $release_name is already active."
        exit 0
    fi

    if [[ "$release_dir" != "$releases_dir/"* ]]; then
        echo "Refusing unsafe partial-release cleanup: $release_dir" >&2
        exit 64
    fi

    rm -rf -- "$release_dir"
fi

mkdir "$release_dir"
tar \
    --exclude=.git \
    --exclude=.env \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=storage/logs/* \
    --exclude=storage/framework/cache/data/* \
    --exclude=storage/framework/sessions/* \
    --exclude=storage/framework/views/* \
    -cf - \
    -C "$repository_root" . \
    | tar -xf - -C "$release_dir"

# The archive's root entry inherits cPanel's private repository mode (0700),
# so correct the release directory after extraction for Apache traversal.
chmod 0755 "$release_dir"

ln -s "$shared_dir/.env" "$release_dir/.env"
rm -rf "$release_dir/storage"
ln -s "$shared_dir/storage" "$release_dir/storage"

# Keep SQLite data outside immutable releases. This is harmless for MySQL
# environments and lets a SQLite-configured production environment survive
# atomic release switches.
touch "$shared_dir/database/database.sqlite"
rm -f -- "$release_dir/database/database.sqlite"
ln -s "$shared_dir/database/database.sqlite" "$release_dir/database/database.sqlite"

php_bin=""
for candidate in \
    /opt/cpanel/ea-php83/root/usr/bin/php \
    /usr/local/bin/ea-php83 \
    /usr/local/bin/php \
    /usr/bin/php; do
    if [[ -x "$candidate" ]]; then
        php_bin="$candidate"
        break
    fi
done

if [[ -z "$php_bin" ]]; then
    echo "PHP 8.3 CLI was not found on the cPanel server." >&2
    exit 69
fi

composer_bin=""
for candidate in \
    /opt/cpanel/composer/bin/composer \
    /usr/local/bin/composer \
    /usr/bin/composer; do
    if [[ -f "$candidate" || -x "$candidate" ]]; then
        composer_bin="$candidate"
        break
    fi
done

if [[ -z "$composer_bin" ]]; then
    echo "Composer was not found on the cPanel server." >&2
    exit 69
fi

cd "$release_dir"
dependency_hash="$(sha256sum composer.json composer.lock | sha256sum | cut -d' ' -f1)"
vendor_archive="$shared_dir/vendor-cache/$dependency_hash.tar"
dependencies_restored=false

if [[ -f "$vendor_archive" ]]; then
    if tar -tf "$vendor_archive" >/dev/null 2>&1; then
        echo "Restoring verified PHP dependencies from cache."
        tar -xf "$vendor_archive" -C "$release_dir"
        dependencies_restored=true
    else
        echo "Discarding an invalid dependency cache archive." >&2
        rm -f -- "$vendor_archive"
    fi
fi

if [[ "$dependencies_restored" == true && -f "$release_dir/vendor/autoload.php" ]]; then
    "$php_bin" "$composer_bin" dump-autoload \
        --no-dev \
        --classmap-authoritative \
        --no-interaction \
        --no-scripts
    "$php_bin" artisan package:discover --ansi
else
    rm -rf -- "$release_dir/vendor"
    echo "Installing PHP dependencies for dependency set $dependency_hash."
    "$php_bin" "$composer_bin" install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --classmap-authoritative

    vendor_archive_next="$vendor_archive.next.$$"
    tar -cf "$vendor_archive_next" -C "$release_dir" vendor
    mv -f -- "$vendor_archive_next" "$vendor_archive"
    vendor_archive_next=""
fi

"$php_bin" artisan migrate --force
"$php_bin" artisan admin:bootstrap --no-interaction

# Keep the production lead store synchronized with the historical WordPress
# submissions. The command upserts by the source submission identifier, so it
# is safe to run again during every production release without creating
# duplicate enquiries. Staging stays isolated from production customer data.
if [[ "$branch_name" == "main" ]]; then
    wordpress_config="/home2/geicic3c/public_html/wp-config.php"

    if [[ -f "$wordpress_config" ]]; then
        "$php_bin" artisan legacy:import-wordpress-leads \
            --wp-config="$wordpress_config" \
            --new-only \
            --no-interaction
    else
        echo "WordPress configuration not found at $wordpress_config; legacy lead import skipped." >&2
    fi
fi

"$php_bin" artisan optimize:clear
"$php_bin" artisan storage:link
"$php_bin" artisan config:cache
"$php_bin" artisan route:cache
"$php_bin" artisan view:cache

printf '%s' "$release_name" > "$release_dir/public/release.txt"

rm -f -- "$next_link"
ln -s "$release_dir" "$next_link"
mv -Tf "$next_link" "$current_link"
release_activated=true

if [[ "$branch_name" == "develop" ]]; then
    "$release_dir/scripts/install-staging-bridge.sh" "$release_dir"
elif [[ "$branch_name" == "main" && ! -L "/home2/geicic3c/public_html/_geic_release" ]]; then
    "$release_dir/scripts/install-production-bridge.sh"
fi

app_url="$("$php_bin" -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo rtrim((string) config("app.url"), "/");
')"

if [[ ! "$app_url" =~ ^https?:// ]]; then
    echo "APP_URL is invalid or missing: $app_url" >&2
    health_ok=false
else
    health_ok=true

    for path in /up / /destinations /destinations/australia /assets/design_1/css/app.min.css; do
        if ! curl --fail --silent --show-error --max-time 20 --output /dev/null "$app_url$path"; then
            echo "Health check failed: $app_url$path" >&2
            health_ok=false
            break
        fi
    done
fi

if [[ "$health_ok" != true ]]; then
    if [[ -n "$previous_target" && -d "$previous_target" ]]; then
        rm -f -- "$next_link"
        ln -s "$previous_target" "$next_link"
        mv -Tf "$next_link" "$current_link"

        if [[ "$branch_name" == "develop" ]]; then
            "$release_dir/scripts/install-staging-bridge.sh" "$previous_target"
        fi

        echo "Restored previous release: $previous_target" >&2
    else
        rm -f -- "$current_link"
        echo "No previous release was available for rollback." >&2
    fi

    release_activated=false
    exit 70
fi

# Dependency archives are immutable and keyed by composer.json/composer.lock.
# Retaining three avoids repeated network installs during a rollback while
# preventing the shared cache from growing indefinitely.
mapfile -t old_vendor_archives < <(
    find "$shared_dir/vendor-cache" -mindepth 1 -maxdepth 1 -type f -name '*.tar' -printf '%T@ %p\n' \
        | sort -rn \
        | tail -n +4 \
        | cut -d' ' -f2-
)

for old_vendor_archive in "${old_vendor_archives[@]}"; do
    if [[ "$old_vendor_archive" == "$shared_dir/vendor-cache/"*.tar ]]; then
        rm -f -- "$old_vendor_archive"
    fi
done

mapfile -t old_releases < <(
    find "$releases_dir" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' \
        | sort -rn \
        | tail -n +6 \
        | cut -d' ' -f2-
)

for old_release in "${old_releases[@]}"; do
    if [[ "$old_release" == "$releases_dir/"* && "$old_release" != "$release_dir" ]]; then
        rm -rf -- "$old_release"
    fi
done

echo "Activated release $release_name"
