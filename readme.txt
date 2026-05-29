=== AEO Radar — Answer Engine Audit & Score ===
Contributors: skynetlabs
Tags: aeo, seo, ai, content-analysis, schema
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Score how quotable your content is by AI answer engines. One-click on-page AEO audit + site dashboard with fixes. 100% local — no API, no cost.

== Description ==

Search is shifting from "ten blue links" to AI answers. **AEO Radar** scores how ready each page is to be *quoted* by answer engines (ChatGPT, Claude, Perplexity, Google AI Overviews, Gemini) — and tells you exactly what to fix.

Everything is analyzed **locally on your own server**. No API key, no cloud, no per-use cost, nothing sent anywhere.

= What it checks =

* **Answer-first opening** — does the page lead with a concise, quotable answer?
* **Heading structure** — clear H2/H3 sections engines can extract.
* **Question-style headings** — headings phrased like the queries users ask.
* **Lists & tables** — structured content AI can lift accurately.
* **FAQ section** — the most-cited content shape.
* **Meta description** — present and well-sized.
* **Focused title** — specific, right length.
* **Depth** — enough substance to be worth citing.

Each check is weighted into a **0–100 AEO score** with a letter grade, shown:

* **On every edit screen** — a live score panel with pass/fail and specific advice.
* **In a site-wide dashboard** (Tools → AEO Radar) — all content ranked worst-first, so you fix the pages that move the needle most.

= Part of the SkynetLabs AEO Suite =

Pairs with **AEO Kit** (emits llms.txt + JSON-LD schema) and **On-Device AI** (writes content with on-device AI). Radar measures, Kit emits, On-Device AI creates.

= Why local-only matters =

* $0 forever — no metered API.
* Private — your drafts and content never leave the server.

== Installation ==

1. Upload the `aeo-radar` folder to `/wp-content/plugins/`, or install via the Plugins screen.
2. Activate.
3. Edit any post — the **AEO Radar** score panel appears in the sidebar.
4. Visit **Tools → AEO Radar** for the site-wide ranked dashboard.

== Frequently Asked Questions ==

= Does it send my content anywhere? =

No. All analysis runs in PHP on your own server. No external requests, no API key.

= How is the score calculated? =

Eight weighted on-page checks (answer-first opening, headings, question headings, lists/tables, FAQ, meta description, title, depth) sum to a 0–100 score.

= Does it change my content? =

No. AEO Radar only reads and scores. You make the edits.

= Does it work with the Classic Editor? =

The site-wide dashboard works regardless. The live score panel appears on the standard edit screen for both editors.

== Changelog ==

= 1.0.0 =
* Initial release: local AEO scoring engine, per-post score panel, and a worst-first site dashboard.

== Upgrade Notice ==

= 1.0.0 =
First release.
