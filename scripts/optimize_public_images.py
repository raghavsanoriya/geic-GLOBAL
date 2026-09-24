#!/usr/bin/env python3
"""Create visually lossless WebP delivery variants for public website assets.

Source images are not modified. A generated sibling such as ``photo.jpg.webp``
is only kept when it is smaller than the JPG/PNG source. The web server can
serve that sibling to WebP-capable browsers while legacy clients use the source.
"""

from __future__ import annotations

import argparse
import json
import sys
from dataclasses import asdict, dataclass
from pathlib import Path

from PIL import Image, ImageOps, UnidentifiedImageError


IMAGE_SUFFIXES = {".jpg", ".jpeg", ".png"}
HERO_MARKERS = ("hero", "banner", "background", "cover")
SMALL_MARKERS = ("card", "thumb", "thumbnail", "avatar", "flag", "icon", "logo")


@dataclass
class Result:
    source: str
    output: str | None
    status: str
    original_bytes: int
    generated_bytes: int | None
    saved_bytes: int | None
    source_dimensions: tuple[int, int] | None
    output_dimensions: tuple[int, int] | None
    reason: str | None = None


def target_dimension(path: Path) -> int:
    name = path.as_posix().lower()
    if any(marker in name for marker in HERO_MARKERS):
        return 2560
    if any(marker in name for marker in SMALL_MARKERS):
        return 960
    return 1600


def has_alpha(image: Image.Image) -> bool:
    return image.mode in {"RGBA", "LA"} or (image.mode == "P" and "transparency" in image.info)


def optimise(source: Path, force: bool) -> Result:
    output = source.with_name(f"{source.name}.webp")
    original_bytes = source.stat().st_size

    if output.exists() and not force and output.stat().st_mtime >= source.stat().st_mtime:
        with Image.open(source) as original, Image.open(output) as generated:
            return Result(
                str(source), str(output), "existing", original_bytes, output.stat().st_size,
                original_bytes - output.stat().st_size, original.size, generated.size,
            )

    temporary = output.with_suffix(f"{output.suffix}.tmp")
    try:
        with Image.open(source) as opened:
            image = ImageOps.exif_transpose(opened)
            source_size = image.size
            maximum = target_dimension(source)
            image.thumbnail((maximum, maximum), Image.Resampling.LANCZOS)
            output_size = image.size

            if has_alpha(image):
                image.convert("RGBA").save(temporary, "WEBP", lossless=True, method=6)
            else:
                image.convert("RGB").save(temporary, "WEBP", quality=92, method=6)

        generated_bytes = temporary.stat().st_size
        if generated_bytes >= original_bytes:
            temporary.unlink(missing_ok=True)
            return Result(
                str(source), None, "skipped", original_bytes, generated_bytes,
                None, source_size, output_size, "WebP was not smaller than the source",
            )

        temporary.replace(output)
        return Result(
            str(source), str(output), "generated", original_bytes, generated_bytes,
            original_bytes - generated_bytes, source_size, output_size,
        )
    except (OSError, UnidentifiedImageError) as exception:
        temporary.unlink(missing_ok=True)
        return Result(str(source), None, "skipped", original_bytes, None, None, None, None, str(exception))


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--project", type=Path, default=Path(__file__).resolve().parents[1])
    parser.add_argument("--report", type=Path, default=Path("storage/app/image-optimization-report.json"))
    parser.add_argument("--force", action="store_true", help="Regenerate existing WebP variants.")
    args = parser.parse_args()

    project = args.project.resolve()
    roots = (project / "public", project / "landing-page")
    sources = sorted(
        path for root in roots if root.is_dir() for path in root.rglob("*")
        if path.is_file() and path.suffix.lower() in IMAGE_SUFFIXES
    )
    results = [optimise(path, args.force) for path in sources]
    report_path = (project / args.report).resolve() if not args.report.is_absolute() else args.report
    report_path.parent.mkdir(parents=True, exist_ok=True)
    report_path.write_text(json.dumps({
        "summary": {
            "sources": len(results),
            "generated": sum(item.status == "generated" for item in results),
            "existing": sum(item.status == "existing" for item in results),
            "skipped": sum(item.status == "skipped" for item in results),
            "original_bytes": sum(item.original_bytes for item in results),
            "webp_bytes": sum(item.generated_bytes or 0 for item in results if item.status != "skipped"),
            "saved_bytes": sum(item.saved_bytes or 0 for item in results),
        },
        "images": [asdict(item) for item in results],
    }, indent=2), encoding="utf-8")

    print(
        f"Processed {len(results)} images; generated "
        f"{sum(item.status == 'generated' for item in results)} variants; saved "
        f"{sum(item.saved_bytes or 0 for item in results) / 1024 / 1024:.2f} MiB."
    )
    print(f"Report: {report_path}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
