#!/usr/bin/env bash

set -Eeuo pipefail

public_root="/home2/geicic3c/public_html"
deploy_root="/home2/geicic3c/apps/geic-production"
current_public="$deploy_root/current/public"
bridge_link="$public_root/_geic_release"
backup_root="/home2/geicic3c/backups"
timestamp="$(date -u +%Y%m%dT%H%M%SZ)"
backup_file="$backup_root/public-html-before-git-$timestamp.tar.gz"

if [[ ! -d "$public_root" || ! -d "$current_public" ]]; then
    echo "Production public directory or active release is missing." >&2
    exit 78
fi

mkdir -p "$backup_root"
tar \
    --exclude='./staging' \
    --exclude='./.well-known' \
    --exclude='./_geic_release' \
    -czf "$backup_file" \
    -C "$public_root" .

rm -f -- "$bridge_link"
ln -s "$current_public" "$bridge_link"

cat > "$public_root/.htaccess" <<'HTACCESS'
Options -Indexes +SymLinksIfOwnerMatch
RewriteEngine On

# Keep the staging subdomain directory independent from the production bridge.
RewriteRule ^staging(?:/|$) - [L]

# Prevent the bridge rewrite from recursing after Apache resolves the symlink.
RewriteRule ^_geic_release(?:/|$) - [L]

# Serve every production request from the active atomic Laravel release.
RewriteRule ^(.*)$ _geic_release/$1 [L,QSA]
HTACCESS

echo "Production bridge installed. Backup: $backup_file"
