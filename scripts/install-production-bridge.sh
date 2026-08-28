#!/usr/bin/env bash

set -Eeuo pipefail

public_root="/home2/geicic3c/public_html"
deploy_root="/home2/geicic3c/apps/geic-production"
current_public="$deploy_root/current/public"
bridge_link="$public_root/_geic_release"
backup_root="/home2/geicic3c/backups"
timestamp="$(date -u +%Y%m%dT%H%M%SZ)"
backup_file="$backup_root/public-html-before-git-$timestamp.tar.gz"
refresh_only="${1:-}"

if [[ ! -d "$public_root" || ! -d "$current_public" ]]; then
    echo "Production public directory or active release is missing." >&2
    exit 78
fi

if [[ "$refresh_only" != "--refresh" ]]; then
    mkdir -p "$backup_root"
    tar \
        --exclude='./staging' \
        --exclude='./.well-known' \
        --exclude='./_geic_release' \
        -czf "$backup_file" \
        -C "$public_root" .
fi

rm -f -- "$bridge_link"
ln -s "$current_public" "$bridge_link"

cat > "$public_root/.htaccess" <<'HTACCESS'
Options -Indexes +SymLinksIfOwnerMatch
RewriteEngine On

# Keep the staging subdomain directory independent from the production bridge.
RewriteRule ^staging(?:/|$) - [L]

# Prevent the bridge rewrite from recursing after Apache resolves the symlink.
RewriteRule ^_geic_release(?:/|$) - [L]

# A legacy public_html/landing directory can trigger Apache's DirectorySlash
# before Laravel sees the request. Send the complete landing-page namespace
# straight to the active release front controller so both the page and its
# committed assets are served without leaking _geic_release URLs.
RewriteRule ^landing(?:/.*)?$ _geic_release/index.php [L,QSA]

# Serve every production request from the active atomic Laravel release.
RewriteRule ^(.*)$ _geic_release/$1 [L,QSA]
HTACCESS

if [[ "$refresh_only" == "--refresh" ]]; then
    echo "Production bridge refreshed."
else
    echo "Production bridge installed. Backup: $backup_file"
fi
