=== Community AI ===
Contributors: amrosama
Tags: community, ai, summary, cpt
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a Community Discussions CPT with a hardened mock-AI summarization workflow.

== Description ==
Demonstrates clean WordPress architecture: CPT, meta boxes, Settings API, admin-ajax,
and a centralized security helper for nonces, capability checks, sanitize/escape.

The AI service is a deterministic local mock; swap in a real provider by implementing
Community_AI_Service_Interface.

== Installation ==
1. Upload to /wp-content/plugins/community-ai/
2. Activate via the Plugins menu.
3. Visit Discussions → AI Settings to configure summary length.

== Changelog ==
= 1.0.0 =
* Initial release.
