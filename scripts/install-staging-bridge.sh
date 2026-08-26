#!/usr/bin/env bash

set -Eeuo pipefail

staging_root="/home2/geicic3c/staging"
current_public="$staging_root/current/public"
bridge_link="$staging_root/_geic_release"
next_link="$staging_root/_geic_release.next"

if [[ ! -d "$staging_root" || ! -d "$current_public" ]]; then
    echo "Staging directory or active release is missing." >&2
    exit 78
fi

rm -f -- "$next_link"
ln -s "$current_public" "$next_link"
mv -Tf "$next_link" "$bridge_link"

cat > "$staging_root/.htaccess" <<'HTACCESS'
Options -Indexes +SymLinksIfOwnerMatch
RewriteEngine On

# Prevent recursion after Apache resolves the stable staging bridge.
RewriteRule ^_geic_release(?:/|$) - [L]

# Serve every staging request from the active atomic Laravel release.
RewriteRule ^(.*)$ _geic_release/$1 [L,QSA]
HTACCESS

echo "Staging bridge installed."
