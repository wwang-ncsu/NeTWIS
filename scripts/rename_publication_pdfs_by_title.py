from __future__ import annotations

import argparse
import csv
import json
import re
import subprocess
from dataclasses import dataclass
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
DATA_FILE = ROOT / "data" / "publications.php"
DEFAULT_FOLDER = ROOT / "unnamed"
REPORT_FILE = ROOT / "docs" / "unnamed_pdf_rename_map.csv"

STOPWORDS = {
    "a", "an", "and", "as", "at", "based", "by", "can", "does", "for", "from",
    "how", "in", "into", "is", "of", "on", "or", "the", "their", "through", "to",
    "toward", "towards", "under", "understanding", "using", "via", "with",
}

MANUAL_SOURCE_ALIASES = {
    "architecture_of_solid_state_transformer-based_energy_router_and_models_of_energy_traffic.pdf":
        "Architecture of Solid State Transformer-based Energy Router and Energy Traffic",
    "efficient_security_management_for_ad_hoc_networks.pdf":
        "Efficient Security Management in Ad Hoc Networks",
    "performance_assessment_of_data_and_time-sensitive_wireless_distributed_networked-control-systems_in_presence_of_information_security.pdf":
        "Performance Assessment of Data and Time-Sensitive Wireless Distributed Networked-control-Systems in Presence of Information Securty",
    "remedy_or_resource_drain_modeling_and_analysis_of_massive_task_offloading_processes_in_fog.pdf":
        "Remedy or Resource Drain: Modeling and Analysis of Massive Task Offloading Processes in the Fog",
    "talk_to_transformers_an_empirical_study_of_device_communications_for_the_freedm_system.pdf":
        "Talk to Transformers: An Empirical Study of Device Communications for the FREEDM Sysem",
    "toward_fast_and_energy-efficient_access_to_cloudlets_in_hostile_environments.pdf":
        "Towards Fast and Energy-Efficient Access to Cloudlets in Hostile Environments",
    "towards multi-person gesture.pdf":
        "Towards Multi-Person Gesture Recognition using Commodity Wi-Fi",
}


@dataclass
class Publication:
    title: str
    authors: str
    year: int


def load_publications(data_file: Path) -> list[Publication]:
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
    rows = json.loads(result.stdout)
    return [
        Publication(
            title=clean_whitespace(str(row.get("title", ""))),
            authors=clean_whitespace(str(row.get("authors", ""))),
            year=int(row.get("year", 0) or 0),
        )
        for row in rows
    ]


def clean_whitespace(text: str) -> str:
    return re.sub(r"\s+", " ", text).strip()


def normalize_title(text: str) -> str:
    return re.sub(r"[^a-z0-9]+", "", text.lower())


def surname_initials(authors: str, limit: int = 3) -> str:
    normalized = clean_whitespace(authors.replace(" and ", ", "))
    parts = [part.strip() for part in normalized.split(",") if part.strip()]
    initials: list[str] = []
    for author in parts:
        tokens = [token for token in re.split(r"[\s.]+", author) if token]
        if not tokens:
            continue
        letters = re.sub(r"[^A-Za-z]", "", tokens[-1])
        if letters:
            initials.append(letters[0].lower())
        if len(initials) >= limit:
            break
    return "".join(initials) or "x"


def pick_keyword(title: str) -> str:
    for word in re.findall(r"[A-Za-z0-9]+", title.lower()):
        if word in STOPWORDS or len(word) <= 1:
            continue
        return word
    return "paper"


def build_target_name(pub: Publication, used_names: set[str]) -> str:
    base = f"{pub.year % 100:02d}{surname_initials(pub.authors)}-{pick_keyword(pub.title)}"
    candidate = f"{base}.pdf"
    suffix = 2
    while candidate.lower() in used_names:
        candidate = f"{base}-{suffix}.pdf"
        suffix += 1
    used_names.add(candidate.lower())
    return candidate


def score_match(source_title: str, candidate_title: str) -> int:
    source_norm = normalize_title(source_title)
    candidate_norm = normalize_title(candidate_title)
    if not source_norm or not candidate_norm:
        return 0
    if source_norm == candidate_norm:
        return 100
    source_tokens = set(re.findall(r"[a-z0-9]+", source_title.lower()))
    candidate_tokens = set(re.findall(r"[a-z0-9]+", candidate_title.lower()))
    if not source_tokens or not candidate_tokens:
        return 0
    return int(100 * len(source_tokens & candidate_tokens) / len(source_tokens | candidate_tokens))


def resolve_match(pdf_name: str, publications: list[Publication]) -> Publication | None:
    alias = MANUAL_SOURCE_ALIASES.get(pdf_name.lower())
    source_title = alias or Path(pdf_name).stem.replace("_", " ")
    best: Publication | None = None
    best_score = 0
    for pub in publications:
        score = score_match(source_title, pub.title)
        if score > best_score:
            best = pub
            best_score = score
    return best if best is not None and best_score >= 75 else None


def rename_folder(folder: Path, dry_run: bool) -> tuple[list[dict[str, str]], list[str]]:
    publications = load_publications(DATA_FILE)
    rows: list[dict[str, str]] = []
    unmatched: list[str] = []
    used_names = {path.name.lower() for path in folder.glob("*.pdf")}

    for pdf in sorted(folder.glob("*.pdf")):
        match = resolve_match(pdf.name, publications)
        if match is None:
            unmatched.append(pdf.name)
            rows.append(
                {
                    "old_name": pdf.name,
                    "new_name": "",
                    "title": "",
                    "authors": "",
                    "year": "",
                    "status": "skipped",
                }
            )
            continue

        used_names.discard(pdf.name.lower())
        new_name = build_target_name(match, used_names)
        if not dry_run and pdf.name != new_name:
            pdf.rename(pdf.with_name(new_name))
        rows.append(
            {
                "old_name": pdf.name,
                "new_name": new_name,
                "title": match.title,
                "authors": match.authors,
                "year": str(match.year),
                "status": "renamed" if pdf.name != new_name else "unchanged",
            }
        )

    return rows, unmatched


def write_report(rows: list[dict[str, str]], report_file: Path) -> None:
    report_file.parent.mkdir(parents=True, exist_ok=True)
    with report_file.open("w", newline="", encoding="utf-8") as fh:
        writer = csv.DictWriter(fh, fieldnames=["old_name", "new_name", "title", "authors", "year", "status"])
        writer.writeheader()
        writer.writerows(rows)


def main() -> int:
    parser = argparse.ArgumentParser(description="Rename newly added publication PDFs by title.")
    parser.add_argument("--folder", type=Path, default=DEFAULT_FOLDER)
    parser.add_argument("--dry-run", action="store_true")
    parser.add_argument("--report", type=Path, default=REPORT_FILE)
    args = parser.parse_args()

    rows, unmatched = rename_folder(args.folder, dry_run=args.dry_run)
    write_report(rows, args.report)

    print(f"folder={args.folder}")
    print(f"report={args.report}")
    print(f"renamed={sum(1 for row in rows if row['status'] == 'renamed')}")
    print(f"unchanged={sum(1 for row in rows if row['status'] == 'unchanged')}")
    print(f"skipped={sum(1 for row in rows if row['status'] == 'skipped')}")
    if unmatched:
        print("unmatched_files=" + ", ".join(unmatched))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
