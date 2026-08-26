#!/usr/bin/env bash

set -Eeuo pipefail

repository_root="$(git rev-parse --show-toplevel)"
branch_name="$(git branch --show-current)"
release_name="$(git rev-parse HEAD)"

case "$branch_name" in
    develop)
        deploy_root="/home2/geicic3c/public_html/staging"
        ;;
    main)
        deploy_root="/home2/geicic3c/apps/geic-production"
        ;;
    *)
        echo "Branch $branch_name is not deployable." >&2
        exit 64
        ;;
esac

if [[ "$deploy_root" != "/home2/geicic3c/public_html/staging" && "$deploy_root" != "/home2/geicic3c/apps/geic-production" ]]; then
    echo "Refusing unsafe deployment root: $deploy_root" >&2
    exit 64
fi

shared_dir="$deploy_root/shared"
releases_dir="$deploy_root/releases"
release_dir="$releases_dir/$release_name"
next_link="$deploy_root/current.next"
current_link="$deploy_root/current"

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
    echo "Release $release_name already exists; refusing to overwrite it." >&2
    exit 73
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

ln -s "$release_dir" "$next_link"
mv -Tf "$next_link" "$current_link"

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
