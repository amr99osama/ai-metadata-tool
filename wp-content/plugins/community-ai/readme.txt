=== Community AI ===
Contributors: amrosama
Tags: community, ai, summary, cpt
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds community discussions with editable AI-style summaries for editors.

== Description ==
Community AI gives editors a Community Discussions post type and a local summary
generator they can use as a starting point before publishing.

The included AI service is a deterministic local mock, so no API keys or network
calls are needed for review. Developers can replace it by implementing
Community_AI_Service_Interface and filtering community_ai_service.

== Installation ==
1. Upload to /wp-content/plugins/community-ai/
2. Activate via the Plugins menu.
3. Visit Discussions → AI Settings to configure summary length.

== Changelog ==
= 1.0.0 =
* Initial release.
