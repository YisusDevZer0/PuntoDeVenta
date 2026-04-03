#!/usr/bin/env python3
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1] / "PuntoDeVenta"
SKIP = {"vendor", "node_modules", ".git", "lib"}


def skip(path: Path) -> bool:
    return any(p in SKIP for p in path.parts)


def process_js(text: str) -> str:
    if "doctorpez.mx" not in text:
        return text
    text = re.sub(
        r"'https://doctorpez\.mx/PuntoDeVenta/([^']*)'",
        r"(window.__FDP_BASE_URL__||'')+'\1'",
        text,
    )
    text = re.sub(
        r'"https://doctorpez\.mx/PuntoDeVenta/([^"]*)"',
        r'(window.__FDP_BASE_URL__||"")+"\1"',
        text,
    )
    return text


def main():
    for path in ROOT.rglob("*.js"):
        if skip(path):
            continue
        raw = path.read_text(encoding="utf-8", errors="replace")
        if "doctorpez.mx" not in raw:
            continue
        new = process_js(raw)
        if new != raw:
            path.write_text(new, encoding="utf-8", newline="\n")
            print("OK", path.relative_to(ROOT))


if __name__ == "__main__":
    main()
