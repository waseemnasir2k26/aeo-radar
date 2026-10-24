# AEO Radar

**A WordPress plugin that scores how quotable each post is for AI answer engines, with fixes, worst first.**

![License: GPL v2+](https://img.shields.io/badge/License-GPLv2%2B-blue) ![WordPress 5.8+](https://img.shields.io/badge/WordPress-5.8%2B-21759B) ![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-777BB4)

"Do I rank?" is no longer the only question. "Would an AI quote this page?" is harder to see. Radar checks the on-page signals that make content easy to lift.

## What it does

- Scores every post 0-100, live in the editor sidebar.
- Checks: answer-first opening (20), heading structure (15), question-style headings (10), lists and tables (15), FAQ section (10), meta description (10), focused title (10), depth of 300+ words (10).
- Site-wide dashboard under **Tools > AEO Radar** ranks all content worst-first with specific fixes.
- The scoring engine (`includes/class-aeor-analyzer.php`) is dependency-free PHP: no network calls, no API key.

## Quickstart

1. Copy `aeo-radar` into `wp-content/plugins/` (or zip it and upload).
2. Activate.
3. Edit any post and read the score in the sidebar; open **Tools > AEO Radar** for the dashboard.

The analyzer is a plain function you can call on its own:

```php
AEOR_Analyzer::analyze($html, $title, $excerpt); // score + per-check results
```

Pair it with [aeo-kit](https://github.com/waseemnasir2k26/aeo-kit), which adds `llms.txt`, schema and FAQ blocks.

## For you if

You publish on WordPress and want a quick, local audit of which pages to fix first.

## Status and limits

- The score is a checklist of content shape. It does not measure real citations in ChatGPT, Perplexity or AI Overviews.
- GPL-2.0-or-later. Last reviewed October 2026.

## Built by SkynetLabs

Built by [SkynetLabs](https://skynetjoe.com). Questions: info@skynetjoe.com

Need this wired into your business? SkynetLabs builds voice agents, n8n back offices and sites that convert → https://skynetjoe.com
