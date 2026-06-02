#!/usr/bin/env python3
import os
import sys

REPLACEMENTS = [
    ("fas fa-angle-double-left", "fa-solid fa-angles-left"),
    ("fas fa-angle-double-right", "fa-solid fa-angles-right"),
    ("fas fa-angle-right", "fa-solid fa-chevron-right"),
    ("fas fa-angle-left", "fa-solid fa-chevron-left"),
    ("fa fa-angle-double-left", "fa-solid fa-angles-left"),
    ("fa fa-angle-double-right", "fa-solid fa-angles-right"),
    ("fa fa-angle-right", "fa-solid fa-chevron-right"),
    ("fa fa-angle-left", "fa-solid fa-chevron-left"),
    ("fas fa-calendar-alt", "fa-solid fa-calendar-days"),
    ("fa fa-calendar-alt", "fa-solid fa-calendar-days"),
    ("fa fa-clock-o", "fa-solid fa-clock"),
    ("fa fa-calendar-check-o", "fa-solid fa-calendar-check"),
    ("fa fa-delete", "fa-solid fa-trash"),
    ("fa fa-times", "fa-solid fa-xmark"),
    ("fa fa-arrow-up", "fa-solid fa-chevron-up"),
    ("fa fa-arrow-down", "fa-solid fa-chevron-down"),
]

SKIP_DIRS = {"node_modules", ".git", "lib/tempusdominus"}
EXTENSIONS = {".php", ".js", ".html"}


def should_skip(path: str) -> bool:
    parts = path.replace("\\", "/").split("/")
    for skip in SKIP_DIRS:
        if skip in parts:
            return True
    return "icons-config.js" in path


def migrate_tree(root: str) -> int:
    changed = 0
    for dirpath, _, filenames in os.walk(root):
        for name in filenames:
            ext = os.path.splitext(name)[1].lower()
            if ext not in EXTENSIONS:
                continue
            path = os.path.join(dirpath, name)
            if should_skip(path):
                continue
            try:
                with open(path, "r", encoding="utf-8", errors="ignore") as f:
                    content = f.read()
            except OSError:
                continue
            original = content
            for old, new in REPLACEMENTS:
                content = content.replace(old, new)
            if content != original:
                with open(path, "w", encoding="utf-8", newline="") as f:
                    f.write(content)
                changed += 1
    return changed


def main():
    base = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    targets = [
        os.path.join(base, "PuntoDeVenta", "PuntoDeVentaFarmacias"),
        os.path.join(base, "PuntoDeVenta", "ControlYAdministracion"),
    ]
    total = 0
    for t in targets:
        if os.path.isdir(t):
            n = migrate_tree(t)
            print(f"{t}: {n} archivos actualizados")
            total += n
    print(f"Total: {total}")


if __name__ == "__main__":
    main()
