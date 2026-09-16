# Changelog

All notable changes to this project are documented in this file.

## [2026.09] - 2026-09-16

- Maintenance review of aeo-radar — a public GPLv2+ WordPress plugin ("AEO Radar", v1.0.0) that scores how quotable a page is by AI answer engines and lists specific fixes.
- Status: plain-PHP plugin (WordPress 5.8+, PHP 7.4+) made of `aeo-radar.php` plus four `includes/` classes — a dependency-free analyzer (`AEOR_Analyzer::analyze`), an editor meta box showing a live 0-100 score across eight weighted checks, and a worst-first site dashboard under Tools → AEO Radar. Runs entirely locally with no API calls. Presented as part of the SkynetLabs AEO Suite alongside aeo-kit and on-device-ai.
- Reviewed September 2026: README Status section added and a CHANGELOG created; the repo is versioned as v2026.09. No PHP or asset changes — the plugin's own `Version: 1.0.0` header and `readme.txt` were left untouched so the WordPress-side version is not misreported.
- Known gaps: no CHANGELOG before this release; the README describes the analyzer as unit-tested but the repository contains no test directory or test runner; no CI, no PHPCS config; not on the WordPress.org plugin directory; last functional commit was May 2026.
