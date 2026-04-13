from __future__ import annotations

import argparse
import csv
import json
import re
import subprocess
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
DATA_FILE = ROOT / "data" / "publications.php"
PAPERS_DIR = ROOT / "papers"
RENAME_REPORT = ROOT / "docs" / "publication_pdf_rename_map.csv"
EXTRA_RENAME_REPORT = ROOT / "docs" / "unnamed_pdf_rename_map.csv"

DIRECT_TITLE_MATCHES = {
    "synergizing acoustic and wi-fi signals for device-free gesture recognition": "25ML-Synergizing_Acoustic_and_Wi-Fi_Signals_for_Device-Free_Gesture_Recognition.pdf",
    "dutrack: long-term indoor human tracking with dual-channel sensing and inference": "26ML-DuTrack.pdf",
    "uni-fi: integrated multi-task wi-fi sensing": "26ML-UNI-FI.pdf",
}


def run_php_json_loader(data_file: Path) -> list[dict]:
    php_code = (
        "echo json_encode(require "
        + repr(str(data_file).replace("\\", "/"))
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


def normalize_title(text: str) -> str:
    return re.sub(r"\s+", " ", text).strip().lower()


def php_single_quoted(value: str) -> str:
    return "'" + value.replace("\\", "\\\\").replace("'", "\\'") + "'"


def load_title_to_pdf(*report_files: Path) -> dict[str, str]:
    title_map = dict(DIRECT_TITLE_MATCHES)
    for report_file in report_files:
        if not report_file.exists():
            continue
        with report_file.open(encoding="utf-8", newline="") as fh:
            for row in csv.DictReader(fh):
                title = normalize_title(row.get("title", ""))
                name = row.get("new_name", "").strip()
                if title and name:
                    title_map[title] = name
    return title_map


def format_php_publications(publications: list[dict]) -> str:
    lines = [
        "<?php",
        "// data/publications.php (auto-generated from publications.html)",
        "// id, type, year, area[], selected, title, authors, venue, link, extra",
        "return [",
    ]

    for pub in publications:
        area = pub.get("area", [])
        area_items = ", ".join(php_single_quoted(str(item)) for item in area)
        area_php = f"[{area_items}]"
        selected_php = "true" if pub.get("selected") else "false"
        line = (
            "  ["
            f"'id' => {php_single_quoted(str(pub.get('id', '')))}, "
            f"'type' => {php_single_quoted(str(pub.get('type', '')))}, "
            f"'year' => {int(pub.get('year', 0) or 0)}, "
            f"'area' => {area_php}, "
            f"'selected' => {selected_php}, "
            f"'title' => {php_single_quoted(str(pub.get('title', '')))}, "
            f"'authors' => {php_single_quoted(str(pub.get('authors', '')))}, "
            f"'venue' => {php_single_quoted(str(pub.get('venue', '')))}, "
            f"'link' => {php_single_quoted(str(pub.get('link', '')))}, "
            f"'extra' => {php_single_quoted(str(pub.get('extra', '')))}"
            "],"
        )
        lines.append(line)

    lines.append("];")
    return "\n".join(lines) + "\n"


def sync_links(data_file: Path, papers_dir: Path, report_file: Path) -> tuple[int, int]:
    publications = run_php_json_loader(data_file)
    title_to_pdf = load_title_to_pdf(report_file, EXTRA_RENAME_REPORT)
    existing_pdfs = {path.name for path in papers_dir.glob("*.pdf")}

    updated = 0
    missing = 0

    for pub in publications:
        title_key = normalize_title(str(pub.get("title", "")))
        matched_name = title_to_pdf.get(title_key, "")
        if matched_name and matched_name in existing_pdfs:
            pub["link"] = f"papers/{matched_name}"
            updated += 1
        else:
            pub["link"] = ""
            missing += 1

    data_file.write_text(format_php_publications(publications), encoding="utf-8")
    return updated, missing


def main() -> int:
    parser = argparse.ArgumentParser(description="Sync publication links with files in the papers folder.")
    parser.add_argument("--data-file", type=Path, default=DATA_FILE)
    parser.add_argument("--papers-dir", type=Path, default=PAPERS_DIR)
    parser.add_argument("--report-file", type=Path, default=RENAME_REPORT)
    args = parser.parse_args()

    updated, missing = sync_links(args.data_file, args.papers_dir, args.report_file)
    print(f"updated={updated}")
    print(f"missing={missing}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
