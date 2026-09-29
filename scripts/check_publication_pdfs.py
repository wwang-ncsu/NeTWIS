"""Check publication PDF names, static copies, and crawlable HTML links."""
from __future__ import annotations

import hashlib
import re
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import unquote, urlsplit

ROOT = Path(__file__).resolve().parents[1]
NAME = re.compile(r"\d{2}[a-z]{1,3}-[a-z0-9]+(?:-[a-z0-9]+)*\.pdf")


class Links(HTMLParser):
    def __init__(self):
        super().__init__()
        self.hrefs: list[str] = []

    def handle_starttag(self, tag, attrs):
        if tag == "a":
            self.hrefs.append(dict(attrs).get("href", ""))


def main() -> int:
    errors = []
    papers = sorted((ROOT / "papers").glob("*.pdf"))
    if not papers:
        errors.append("No source PDFs found")
    for paper in papers:
        if not NAME.fullmatch(paper.name):
            errors.append(f"Nonstandard filename: {paper.name}")
        source = paper.read_bytes()
        if not source.startswith(b"%PDF-"):
            errors.append(f"Not a PDF: {paper.name}")
        copy = ROOT / "docs" / "papers" / paper.name
        if not copy.is_file():
            errors.append(f"Missing static PDF: {paper.name}")
        elif hashlib.sha256(source).digest() != hashlib.sha256(copy.read_bytes()).digest():
            errors.append(f"Static PDF differs from source: {paper.name}")

    source_names = {p.name for p in papers}
    for copy in (ROOT / "docs" / "papers").glob("*.pdf"):
        if copy.name not in source_names:
            errors.append(f"Stale static PDF: {copy.name}")

    count = 0
    for page in (ROOT / "docs").rglob("*.html"):
        parser = Links()
        parser.feed(page.read_text(errors="replace"))
        for href in parser.hrefs:
            url = urlsplit(href)
            if "pdf-viewer.html" in url.path:
                errors.append(f"Viewer link in {page.relative_to(ROOT)}")
            if "raw.githubusercontent.com/wwang-ncsu/NeTWIS/" in href:
                errors.append(f"GitHub Raw link in {page.relative_to(ROOT)}")
            if url.scheme or url.netloc:
                continue
            path = unquote(url.path)
            if path.startswith("papers/") and path.endswith(".pdf"):
                count += 1
                if not (page.parent / path).is_file():
                    errors.append(f"Broken PDF link in {page.relative_to(ROOT)}: {href}")
    if not count:
        errors.append("No direct publication PDF links found")
    for error in errors:
        print(f"ERROR: {error}")
    if errors:
        return 1
    print(f"OK: {len(papers)} consistently named PDFs, identical static copies, "
          f"and {count} working direct PDF links.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
