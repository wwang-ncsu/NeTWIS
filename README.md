Source code of NetWis Lab lead by Prof. Wenye Wang

## Publication PDFs

Keep source PDFs in `papers/`. Use lowercase filenames in the form
`YYauthors-keyword.pdf`: two-digit publication year, the surname initials of
the first three authors (or all authors if fewer), and a short title keyword.
Use hyphens for compound keywords and a numeric suffix for collisions.
Examples: `24lw-hybrid.pdf`, `26lw-dutrack.pdf`, `26lw-uni-fi.pdf`.
Use the publication year, or the manuscript year for an unpublished paper.
Keep existing compliant names stable.

Set each local paper's `link` in `data/publications.php` to `papers/NAME.pdf`.
The static generator copies PDFs into `docs/papers/` and preserves these direct
links. Do not replace them with JavaScript viewers or GitHub Raw URLs. Relative
links work both at a domain root and under a project path such as `/NeTWIS/`.

Build and verify with PHP 8+ and Python 3:

```sh
php render.php . docs
python3 scripts/check_publication_pdfs.py
```

`data/pdf-renames.json` records the five filenames normalized in September 2026.
The legacy `pdf-viewer.html` handles bookmarked viewer links, including those
old filenames, by opening the local PDF. Old direct GitHub Raw URLs cannot be
redirected by this static site. Future renames must update references and the
legacy viewer mapping together.

The numeric filename `10185834.pdf` was identified from its contents as
*Prevention and Mitigation of Catastrophic Failures in Demand-Supply
Interdependent Networks*. Its new name uses the 2020 journal publication year
listed in the [author's institutional publication record](https://researchconnect.buffalo.edu/en/publications/prevention-and-mitigation-of-catastrophic-failures-in-demand-supp/).

PDFs are copied byte-for-byte; naming changes do not alter paper contents.
Direct links improve discoverability but do not guarantee Scholar indexing.
