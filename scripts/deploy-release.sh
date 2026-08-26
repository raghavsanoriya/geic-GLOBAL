#!/usr/bin/env bash

set -Eeuo pipefail

repository_root="$(git rev-parse --show-toplevel)"
branch_name="$(git rev-parse --abbrev-ref HEAD)"
release_name="$(git rev-parse HEAD)"

export HOME="/home2/geicic3c"
export COMPOSER_HOME="$HOME/.composer"
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

if [[ -L "$current_link" ]]; then
    previous_target="$(readlink -f "$current_link")"
fi

if [[ ! -f "$shared_dir/.env" ]]; then
    echo "Missing $shared_dir/.env. Configure the environment before the first deployment." >&2
    exit 78
fi

mkdir -p \
    "$shared_dir/storage/app/public" \
    "$shared_dir/storage/framework/cache/data" \
    "$shared_dir/storage/framework/sessions" \
    "$shared_dir/storage/framework/testing" \
    "$shared_dir/storage/framework/views" \
    "$shared_dir/storage/logs" \
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

ln -s "$shared_dir/.env" "$release_dir/.env"
rm -rf "$release_dir/storage"
ln -s "$shared_dir/storage" "$release_dir/storage"

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
"$php_bin" "$composer_bin" install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

"$php_bin" artisan optimize:clear
"$php_bin" artisan migrate --force
"$php_bin" artisan storage:link
"$php_bin" artisan config:cache
"$php_bin" artisan route:cache
"$php_bin" artisan view:cache

printf '%s' "$release_name" > "$release_dir/public/release.txt"

rm -f -- "$next_link"
ln -s "$release_dir" "$next_link"
mv -Tf "$next_link" "$current_link"

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
        echo "No previous release was available for rollback." >&2
    fi

    exit 70
fi

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
