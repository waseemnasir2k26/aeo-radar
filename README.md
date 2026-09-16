# AEO Radar — Answer Engine Audit & Score for WordPress

> Score how quotable your content is by AI answer engines (ChatGPT, Claude, Perplexity, Google AI Overviews, Gemini). One-click on-page audit per post + a worst-first site dashboard, with specific fixes. **100% local — no API, no cloud, no per-use cost.**

[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2%2B-blue.svg)](LICENSE)
![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759B?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![No API cost](https://img.shields.io/badge/API_cost-%240-2EA043)

Part of the **[SkynetLabs AEO Suite](https://github.com/waseemnasir2k26)** — *Radar measures · [AEO Kit](https://github.com/waseemnasir2k26/aeo-kit) emits · [On-Device AI](https://github.com/waseemnasir2k26/on-device-ai) creates.*

---

## Status

Last reviewed: September 2026 · release v2026.09

## Why

Search is moving from blue links to AI answers. The new question isn't "do I rank?" — it's "**does the AI quote me?**" AEO Radar scores exactly that, per page, and tells you what to fix. All analysis runs in PHP on your own server — no key, no cloud, nothing sent anywhere.

## What it scores (0–100)

| Check | Weight | Looks for |
|---|---|---|
| Answer-first opening | 20 | Concise, self-contained answer up top |
| Heading structure | 15 | ≥2 descriptive H2/H3 |
| Question-style headings | 10 | A heading phrased like the user's query |
| Lists & tables | 15 | Structured content AI lifts cleanly |
| FAQ section | 10 | The most-cited content shape |
| Meta description | 10 | Present, 50–160 chars |
| Focused title | 10 | Specific, 20–65 chars |
| Depth | 10 | 300+ words of substance |

Shown **live on every edit screen** (sidebar panel) and in a **site-wide dashboard** (Tools → AEO Radar) that ranks all content **worst-first** — your AEO work queue.

## Install

1. Copy `aeo-radar` into `wp-content/plugins/` (or zip → Plugins → Add New → Upload).
2. Activate.
3. Edit a post → see the **AEO Radar** score in the sidebar. Visit **Tools → AEO Radar** for the dashboard.

## The scoring engine is pure + testable

`includes/class-aeor-analyzer.php` is dependency-free (`AEOR_Analyzer::analyze($html, $title, $excerpt)` → score + checks). No WordPress or network needed to run it — which is how it's unit-tested.

## Structure

```
aeo-radar/
├── aeo-radar.php
├── uninstall.php
├── readme.txt
├── includes/
│   ├── class-aeo-radar.php       # Loader
│   ├── class-aeor-analyzer.php   # Pure scoring engine
│   ├── class-aeor-meta-box.php   # Per-post score panel
│   └── class-aeor-admin.php      # Tools → AEO Radar dashboard
└── assets/
    └── admin.css
```

## License

GPL-2.0-or-later. Built by [SkynetLabs](https://www.skynetjoe.com). PRs welcome.
