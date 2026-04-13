from __future__ import annotations

import json
import re
import subprocess
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
DATA_FILE = ROOT / "data" / "publications.php"


def load_publications() -> list[dict]:
    php_code = (
        "echo json_encode(require "
        + repr(str(DATA_FILE).replace("\\", "/"))
        + ", JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);"
    )
    result = subprocess.run(
        ["php", "-r", php_code],
        check=True,
        capture_output=True,
        text=True,
        encoding="utf-8",
    )
    return json.loads(result.stdout)


def normalize_whitespace(text: str) -> str:
    text = text.replace("\r", " ").replace("\n", " ")
    text = re.sub(r"\s+", " ", text)
    return text.strip()


def clean_title(text: str) -> str:
    text = normalize_whitespace(text)
    text = re.sub(r"\s+([,.;:?])", r"\1", text)
    return text


def clean_authors(text: str) -> str:
    text = normalize_whitespace(text)
    text = re.sub(r"\s+and\s+", " and ", text, flags=re.IGNORECASE)
    return text


def clean_venue(text: str) -> str:
    text = normalize_whitespace(text)
    text = re.sub(r"\bpdf\b", "", text, flags=re.IGNORECASE)
    text = re.sub(r"\bbib\b", "", text, flags=re.IGNORECASE)
    text = re.sub(r"\(\s*Accepted\s*\)\s*\.", "(Accepted).", text, flags=re.IGNORECASE)
    text = re.sub(r"\bIn Proc\.", "In Proc.", text)
    text = re.sub(r"\s+,", ",", text)
    text = re.sub(r",\s+,", ", ", text)
    text = re.sub(r"\s+\.", ".", text)
    text = re.sub(r"\s{2,}", " ", text)
    text = text.replace("  IEEE", " IEEE")
    text = text.replace(',"  ', '," ')
    text = text.replace('," in ', '," in ')
    text = text.replace('," In ', '," In ')
    text = text.replace("2006 2006", "2006")
    return text.strip()


def php_quote(text: str) -> str:
    return "'" + text.replace("\\", "\\\\").replace("'", "\\'") + "'"


def write_publications(publications: list[dict]) -> None:
    lines = [
        "<?php",
        "// data/publications.php (auto-generated from publications.html)",
        "// id, type, year, area[], selected, title, authors, venue, link, extra",
        "return [",
    ]
    for pub in publications:
        area = pub.get("area", [])
        area_php = "[" + ", ".join(php_quote(str(item)) for item in area) + "]"
        lines.append(
            "  ["
            f"'id' => {php_quote(str(pub.get('id', '')))}, "
            f"'type' => {php_quote(str(pub.get('type', '')))}, "
            f"'year' => {int(pub.get('year', 0) or 0)}, "
            f"'area' => {area_php}, "
            f"'selected' => {'true' if pub.get('selected') else 'false'}, "
            f"'title' => {php_quote(str(pub.get('title', '')))}, "
            f"'authors' => {php_quote(str(pub.get('authors', '')))}, "
            f"'venue' => {php_quote(str(pub.get('venue', '')))}, "
            f"'link' => {php_quote(str(pub.get('link', '')))}, "
            f"'extra' => {php_quote(str(pub.get('extra', '')))}"
            "],"
        )
    lines.append("];")
    DATA_FILE.write_text("\n".join(lines) + "\n", encoding="utf-8")


def main() -> int:
    publications = load_publications()
    for pub in publications:
        pub["title"] = clean_title(str(pub.get("title", "")))
        pub["authors"] = clean_authors(str(pub.get("authors", "")))
        pub["venue"] = clean_venue(str(pub.get("venue", "")))
        pub["extra"] = normalize_whitespace(str(pub.get("extra", "")))
    write_publications(publications)
    print(f"cleaned={len(publications)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
