#!/usr/bin/env python3
"""Localise and normalise destination photos and university logos.

The university manifest is captured from the rendered Trans Globe destination
pages.  Photography uses existing country imagery plus one Pexels student-life
photo per destination.  This script is intentionally repeatable: existing
outputs are replaced with the same dimensions and quality settings.
"""

from __future__ import annotations

import argparse
import io
import json
import re
import shutil
import subprocess
import tempfile
import urllib.request
from pathlib import Path

from PIL import Image, ImageOps

try:
    import cairosvg
except ImportError as exc:  # pragma: no cover - developer setup guard
    raise SystemExit("Install cairosvg before running this asset sync.") from exc


PHOTO_SOURCES = {
    "australia": (
        "assets/transglobe/destinations/australia/campus-life.webp",
        "assets/transglobe/destinations/australia.jpg",
        "assets/transglobe/destinations/australia/student-community.webp",
    ),
    "new-zealand": (
        "assets/transglobe/destinations/detail/new-zealand-hero.webp",
        "assets/transglobe/destinations/new-zealand.jpg",
        1438072,
    ),
    "uk": (
        "assets/transglobe/destinations/detail/uk-hero.webp",
        "assets/transglobe/destinations/uk.jpg",
        1454360,
    ),
    "ireland": (
        "assets/transglobe/destinations/detail/ireland-hero.webp",
        "assets/transglobe/destinations/ireland.jpg",
        1184578,
    ),
    "germany": (
        "assets/transglobe/destinations/detail/germany-hero.webp",
        "assets/transglobe/destinations/germany.webp",
        267885,
    ),
    "europe": (
        "assets/transglobe/destinations/detail/europe-hero.webp",
        "assets/transglobe/destinations/europe-card.jpg",
        3769021,
    ),
    "usa": (
        "assets/transglobe/destinations/detail/usa-hero.webp",
        "assets/transglobe/destinations/usa.jpg",
        3184325,
    ),
    "canada": (
        "assets/transglobe/destinations/detail/canada-hero.webp",
        "assets/transglobe/destinations/canada.jpg",
        5212345,
    ),
    "singapore": (
        "assets/transglobe/destinations/detail/singapore-hero.webp",
        "assets/transglobe/destinations/singapore.jpg",
        6147369,
    ),
    "dubai": (
        "assets/transglobe/destinations/detail/dubai-hero.webp",
        "assets/transglobe/destinations/dubai-card.jpg",
        8199562,
    ),
    "malaysia": (
        "assets/transglobe/destinations/detail/malaysia-hero.webp",
        "assets/transglobe/destinations/malaysia.webp",
        7683897,
    ),
    "switzerland": (
        "assets/transglobe/destinations/detail/switzerland-hero.webp",
        "assets/transglobe/destinations/switzerland.webp",
        7973073,
    ),
}


def slugify(value: str) -> str:
    return re.sub(r"[^a-z0-9]+", "-", value.lower()).strip("-")


def request_bytes(url: str) -> bytes:
    request = urllib.request.Request(
        url,
        headers={
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/140 Safari/537.36",
            "Accept": "image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8",
        },
    )
    try:
        with urllib.request.urlopen(request, timeout=35) as response:
            return response.read()
    except Exception:
        with tempfile.NamedTemporaryFile(suffix=".asset", delete=False) as temporary:
            temporary_path = Path(temporary.name)
        try:
            subprocess.run(
                [
                    "curl.exe",
                    "-L",
                    "--fail",
                    "--retry",
                    "2",
                    "--retry-delay",
                    "2",
                    "-k",
                    "-A",
                    "Mozilla/5.0 geic.in asset sync contact info@geic.in",
                    "-o",
                    str(temporary_path),
                    url,
                ],
                check=True,
                capture_output=True,
            )
            return temporary_path.read_bytes()
        finally:
            temporary_path.unlink(missing_ok=True)


def open_image(raw: bytes) -> Image.Image:
    if b"<svg" in raw[:2048].lower():
        raw = cairosvg.svg2png(bytestring=raw, output_width=1200)
    return Image.open(io.BytesIO(raw)).convert("RGBA")


def save_photo(source: Path | bytes, output: Path) -> None:
    image = Image.open(source).convert("RGB") if isinstance(source, Path) else open_image(source).convert("RGB")
    image = ImageOps.exif_transpose(image)
    image.thumbnail((1600, 1100), Image.Resampling.LANCZOS)
    output.parent.mkdir(parents=True, exist_ok=True)
    image.save(output, "WEBP", quality=80, method=6)


def save_logo(raw: bytes, output: Path) -> None:
    image = open_image(raw)
    image.thumbnail((320, 112), Image.Resampling.LANCZOS)
    canvas = Image.new("RGBA", (360, 160), (255, 255, 255, 0))
    x = (canvas.width - image.width) // 2
    y = (canvas.height - image.height) // 2
    canvas.alpha_composite(image, (x, y))
    output.parent.mkdir(parents=True, exist_ok=True)
    canvas.save(output, "WEBP", quality=92, method=6, lossless=True)


def sync_photos(public: Path) -> None:
    labels = ("campus", "city", "students")
    for country, sources in PHOTO_SOURCES.items():
        destination = public / "assets" / "transglobe" / "destinations" / country
        for label, source in zip(labels, sources, strict=True):
            output = destination / f"{label}.webp"
            if output.exists():
                continue
            if isinstance(source, int):
                url = f"https://images.pexels.com/photos/{source}/pexels-photo-{source}.jpeg?auto=compress&cs=tinysrgb&w=1800"
                save_photo(request_bytes(url), output)
            else:
                save_photo(public / source, output)
            print(f"photo {country}/{output.name}")


def sync_logos(public: Path, manifest: dict[str, list[dict[str, str]]]) -> None:
    failures: list[str] = []
    for country, universities in manifest.items():
        directory = public / "assets" / "transglobe" / "destinations" / country / "universities"
        for university in universities:
            output = directory / f"{slugify(university['name'])}.webp"
            if output.exists():
                continue
            try:
                save_logo(request_bytes(university["src"]), output)
                print(f"logo  {country}/{output.name}")
            except Exception as exc:  # noqa: BLE001 - report all third-party failures together
                failures.append(f"{country}: {university['name']} ({exc})")
    if failures:
        raise SystemExit("Logo downloads failed:\n" + "\n".join(failures))


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("manifest", type=Path)
    parser.add_argument("--project", type=Path, default=Path(__file__).resolve().parents[1])
    args = parser.parse_args()

    with args.manifest.open(encoding="utf-8") as handle:
        manifest = json.load(handle)

    public = args.project / "public"
    sync_photos(public)
    sync_logos(public, manifest)


if __name__ == "__main__":
    main()
