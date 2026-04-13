from __future__ import annotations

import argparse
import csv
import re
import subprocess
from dataclasses import dataclass
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
DATA_FILE = ROOT / "data" / "publications.php"
DEFAULT_FOLDER = ROOT / "netwis_publication_pdfs"
REPORT_FILE = ROOT / "docs" / "publication_pdf_rename_map.csv"

STOPWORDS = {
    "a",
    "an",
    "and",
    "as",
    "at",
    "based",
    "by",
    "can",
    "for",
    "from",
    "how",
    "in",
    "into",
    "is",
    "of",
    "on",
    "or",
    "the",
    "their",
    "through",
    "to",
    "toward",
    "towards",
    "under",
    "understanding",
    "with",
}

MANUAL_OVERRIDES = {
    "21rw-gc.pdf": {
        "year": 2021,
        "authors": "Rui Zou, Wenye Wang, and Huaiyu Dai",
        "title": "Temporal and Spectral Analysis of Spectrum Hole Distributions in an LTE Cell",
        "source": "manual-override",
    },
    "competing_epidemics_on_graphs_-_global_convergence_and_coexistence.pdf": {
        "year": 2021,
        "authors": "Prajwal N. Doshi, Saurabh Mallick, and Do Young Eun",
        "title": "Competing Epidemics on Graphs: Global Convergence and Coexistence",
        "source": "manual-override",
    },
    "controlling_metastable_infection_patterns_in_multilayer_networks_via_interlink_design.pdf": {
        "year": 2021,
        "authors": "Ayan Chattopadhyay, Huaiyu Dai, and Do Young Eun",
        "title": "Controlling Metastable Infection Patterns in Multilayer Networks via Interlink Design",
        "source": "manual-override",
    },
    "differential_privacy_and_prediction_uncertainty_of_gossip_protocols_in_general_networks.pdf": {
        "year": 2020,
        "authors": "Mohammad Hossein Jafari, Huaiyu Dai, and Do Young Eun",
        "title": "Differential Privacy and Prediction Uncertainty of Gossip Protocols in General Networks",
        "source": "manual-override",
    },
}


@dataclass
class Publication:
    year: int
    authors: str
    title: str
    source: str


def parse_publications(data_file: Path) -> dict[str, Publication]:
    php_loader = (
        "echo json_encode(require "
        + repr(str(data_file).replace("\\", "/"))
        + ", JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);"
    )
    result = subprocess.run(
        ["php", "-r", php_loader],
        check=True,
        capture_output=True,
        text=True,
        encoding="utf-8",
    )
    entries = __import__("json").loads(result.stdout)
    pubs: dict[str, Publication] = {}

    for entry in entries:
        link = clean_whitespace(str(entry.get("link", "")))
        if not link.lower().endswith(".pdf"):
            continue
        basename = Path(link).name.lower()
        if basename in pubs:
            # Keep the first mapping from the site data and let manual overrides fix anomalies.
            continue
        pubs[basename] = Publication(
            year=int(entry.get("year", 0) or 0),
            authors=clean_whitespace(str(entry.get("authors", ""))),
            title=clean_whitespace(str(entry.get("title", ""))),
            source="data/publications.php",
        )

    for basename, override in MANUAL_OVERRIDES.items():
        pubs[basename] = Publication(
            year=override["year"],
            authors=override["authors"],
            title=override["title"],
            source=override["source"],
        )

    return pubs


def clean_whitespace(text: str) -> str:
    return re.sub(r"\s+", " ", text).strip()


def surname_initials(authors: str, limit: int = 3) -> str:
    normalized = clean_whitespace(authors.replace(" and ", ", "))
    parts = [part.strip() for part in normalized.split(",") if part.strip()]
    initials: list[str] = []

    for author in parts:
        tokens = [token for token in re.split(r"[\s.]+", author) if token]
        if not tokens:
            continue
        surname = tokens[-1]
        letters = re.sub(r"[^A-Za-z]", "", surname)
        if letters:
            initials.append(letters[0].lower())
        if len(initials) >= limit:
            break

    return "".join(initials) or "x"


def pick_keyword(title: str) -> str:
    words = re.findall(r"[A-Za-z0-9]+", title.lower())
    for word in words:
        if word in STOPWORDS:
            continue
        if len(word) == 1:
            continue
        return word
    return "paper"


def build_target_name(pub: Publication, used_names: set[str]) -> str:
    year = f"{pub.year % 100:02d}" if pub.year else "00"
    base = f"{year}{surname_initials(pub.authors)}-{pick_keyword(pub.title)}"
    candidate = f"{base}.pdf"
    suffix = 2
    while candidate.lower() in used_names:
        candidate = f"{base}-{suffix}.pdf"
        suffix += 1
    used_names.add(candidate.lower())
    return candidate


def rename_pdfs(folder: Path, dry_run: bool = False) -> tuple[list[dict[str, str]], list[str]]:
    pubs = parse_publications(DATA_FILE)
    rows: list[dict[str, str]] = []
    unmatched: list[str] = []

    existing_names = {path.name.lower() for path in folder.glob("*.pdf")}
    reserved_targets = {name for name in existing_names}

    for pdf in sorted(folder.glob("*.pdf")):
        pub = pubs.get(pdf.name.lower())
        if pub is None:
            unmatched.append(pdf.name)
            rows.append(
                {
                    "old_name": pdf.name,
                    "new_name": "",
                    "year": "",
                    "authors": "",
                    "title": "",
                    "source": "unmatched",
                    "status": "skipped",
                }
            )
            continue

        reserved_targets.discard(pdf.name.lower())
        new_name = build_target_name(pub, reserved_targets)
        target = pdf.with_name(new_name)

        if not dry_run and pdf.name != new_name:
            pdf.rename(target)

        rows.append(
            {
                "old_name": pdf.name,
                "new_name": new_name,
                "year": str(pub.year),
                "authors": pub.authors,
                "title": pub.title,
                "source": pub.source,
                "status": "renamed" if pdf.name != new_name else "unchanged",
            }
        )

    return rows, unmatched


def write_report(rows: list[dict[str, str]], report_file: Path) -> None:
    report_file.parent.mkdir(parents=True, exist_ok=True)
    with report_file.open("w", newline="", encoding="utf-8") as fh:
        writer = csv.DictWriter(
            fh,
            fieldnames=["old_name", "new_name", "year", "authors", "title", "source", "status"],
        )
        writer.writeheader()
        writer.writerows(rows)


def main() -> int:
    parser = argparse.ArgumentParser(description="Rename NetWIS publication PDFs.")
    parser.add_argument("--folder", type=Path, default=DEFAULT_FOLDER, help="Folder that stores the PDFs.")
    parser.add_argument("--dry-run", action="store_true", help="Preview the new names without renaming files.")
    parser.add_argument(
        "--report",
        type=Path,
        default=REPORT_FILE,
        help="CSV report path for the old/new name mapping.",
    )
    args = parser.parse_args()

    rows, unmatched = rename_pdfs(args.folder, dry_run=args.dry_run)
    write_report(rows, args.report)

    renamed = sum(1 for row in rows if row["status"] == "renamed")
    unchanged = sum(1 for row in rows if row["status"] == "unchanged")
    skipped = sum(1 for row in rows if row["status"] == "skipped")

    print(f"folder={args.folder}")
    print(f"report={args.report}")
    print(f"renamed={renamed}")
    print(f"unchanged={unchanged}")
    print(f"skipped={skipped}")
    if unmatched:
        print("unmatched_files=" + ", ".join(unmatched))

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
