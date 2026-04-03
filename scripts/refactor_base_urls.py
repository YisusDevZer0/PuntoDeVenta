#!/usr/bin/env python3
"""Reemplaza URLs fijas doctorpez.mx/PuntoDeVenta en PHP del POS (un solo uso)."""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1] / "PuntoDeVenta"
SKIP_DIRS = {"vendor", "node_modules", ".git"}
OLD = "https://doctorpez.mx/PuntoDeVenta/"


def skip(path: Path) -> bool:
    return any(p in SKIP_DIRS for p in path.parts)


def process_php(content: str, rel_path: str) -> str:
    if "doctorpez.mx" not in content:
        return content

    text = content

    # Expiro.php casi solo HTML
    if rel_path.endswith("Expiro.php") and text.lstrip().startswith("<!DOCTYPE"):
        if "config/app.php" not in text[:800]:
            text = "<?php require_once __DIR__ . '/../config/app.php'; ?>\n" + text
        text = text.replace(f'href="{OLD}', 'href="<?= BASE_URL ?>')
        return text

    text = text.replace(f'src="{OLD}', 'src="<?= BASE_URL ?>')
    text = text.replace(f'href="{OLD}', 'href="\' . BASE_URL . \'')

    text = text.replace(f'$.post("{OLD}', '$.post("<?php echo BASE_URL; ?>')
    text = text.replace(f"$.post('{OLD}", "$.post('<?php echo BASE_URL; ?>")
    text = text.replace(f'$.get("{OLD}', '$.get("<?php echo BASE_URL; ?>')
    text = text.replace(f"$.get('{OLD}", "$.get('<?php echo BASE_URL; ?>")

    text = text.replace(f"url: '{OLD}", "url: '<?php echo BASE_URL; ?>")
    text = text.replace(f'url: "{OLD}', 'url: "<?php echo BASE_URL; ?>')

    text = text.replace(f'"sAjaxSource": "{OLD}', '"sAjaxSource": "<?php echo BASE_URL; ?>')
    text = text.replace(f'"ajax": "{OLD}', '"ajax": "<?php echo BASE_URL; ?>')

    text = text.replace(f"window.location.href = '{OLD}", "window.location.href = '<?php echo BASE_URL; ?>")
    text = text.replace(f'window.location.href = "{OLD}', 'window.location.href = "<?php echo BASE_URL; ?>')

    # PHP arrays / strings con comillas dobles que contienen HTML
    text = text.replace(f"src='{OLD}", "src='\" . BASE_URL . \"")
    text = text.replace(f'src="{OLD}', 'src="\' . BASE_URL . \'')  # rare

    # Resto: prefijo en comentarios o PHP — sustituir por concatenación
    while OLD in text:
        text = text.replace(OLD, "' . BASE_URL . '", 1)

    return text


def main():
    for path in ROOT.rglob("*.php"):
        if skip(path):
            continue
        rel = path.relative_to(ROOT).as_posix()
        try:
            raw = path.read_text(encoding="utf-8", errors="replace")
        except OSError:
            continue
        if "doctorpez.mx" not in raw:
            continue
        new = process_php(raw, rel)
        if new != raw:
            path.write_text(new, encoding="utf-8", newline="\n")
            print("OK", rel)


if __name__ == "__main__":
    main()
