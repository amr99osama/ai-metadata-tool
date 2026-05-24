# Community AI — WordPress Plugin + Tahaluf Child Theme

A WordPress submission demonstrating:

- A custom post type (**Community Discussions**) with a meta-box-driven mock-AI summary workflow.
- A child theme of Twenty Twenty-Five adding **logo and theme settings** via the Customizer.
- A centralized `Community_AI_Security` helper covering nonces, capability checks, sanitization, and output escaping.

> The "AI" service is a **deterministic local mock** — no API calls, no keys. The mock implements `Community_AI_Service_Interface`, so swapping in a real provider is a one-class change wired in `Community_AI_Plugin::boot_modules()`.

## Repo layout

```
wp-content/
├── plugins/community-ai/                      # The plugin
│   ├── community-ai.php                       # Header + bootstrap
│   ├── uninstall.php                          # Removes options/meta on uninstall
│   ├── includes/
│   │   ├── class-community-ai-plugin.php      # Orchestrator
│   │   ├── class-community-ai-security.php    # Nonces / caps / sanitize / escape
│   │   ├── class-community-ai-cpt.php         # community_discussion CPT + meta
│   │   ├── class-community-ai-settings.php    # Settings API page (length bounds)
│   │   ├── class-community-ai-metabox.php     # AI Summary meta box
│   │   ├── class-community-ai-ajax.php        # admin-ajax handler
│   │   ├── class-community-ai-frontend.php    # Front-end summary render
│   │   ├── interface-community-ai-service.php # AI service contract
│   │   └── class-community-ai-mock-service.php
│   └── assets/                                # admin.js, admin.css, frontend.css
└── themes/tahaluf-twentytwentyfive-child/
    ├── style.css                              # Theme header
    ├── functions.php                          # Enqueue + bootstrap
    ├── screenshot.png
    └── inc/
        ├── customizer.php                     # Customizer panel/sections/settings
        ├── customizer-output.php              # CSS vars + footer render
        └── template-tags.php                  # tahaluf_site_logo()
```

## Install

1. WordPress 6.0+ on PHP 7.4+ (developed on XAMPP / PHP 8.2).
2. Copy `wp-content/plugins/community-ai/` into your site's plugins directory.
3. Copy `wp-content/themes/tahaluf-twentytwentyfive-child/` into your site's themes directory.
4. WP Admin → Plugins → activate **Community AI**.
5. WP Admin → Appearance → Themes → activate **Tahaluf Twenty Twenty-Five Child**.
6. WP Admin → Settings → Permalinks → click **Save** (refreshes rewrite rules for `/discussions/`).

## Manual QA checklist

### Plugin

- [ ] Activate the plugin. No PHP fatal in `error.log`.
- [ ] WP Admin shows **Discussions** menu item with chat icon.
- [ ] Create a new Community Discussion with body content. The "AI Summary" meta box appears on the right.
- [ ] Click **Generate AI Summary**. Spinner appears, textarea fills with `[AI Summary] ...` text, "Generated just now." caption updates.
- [ ] Update the post, reload the editor — summary persists, caption shows time-ago.
- [ ] Visit the public single discussion URL — summary block renders above content.
- [ ] **XSS check:** Paste `<script>alert(1)</script>` into the summary textarea, Update, view front-end. The tags appear as literal text; no alert.
- [ ] **Nonce check:** In devtools, POST to `admin-ajax.php` with `action=community_ai_generate_summary` and a bad `nonce`. Response is `0` (HTTP 403).
- [ ] **Capability check:** Log in as a Subscriber and try the same Ajax POST with a fresh nonce. Response is `{success:false, data:{message:"Insufficient permissions."}}` (HTTP 403).
- [ ] WP Admin → Discussions → AI Settings: set Min=200, Max=100, Save. Validation error shown, values reset to 40/160.
- [ ] Deactivate plugin → reactivate. Settings preserved.
- [ ] Uninstall (delete) the plugin via WP Admin → Plugins. Verify `wp_options` row `community_ai_settings` and any `_community_ai_summary*` post meta are gone.

### Child theme

- [ ] Activate child theme. Front-end loads with parent theme styles.
- [ ] Appearance → Customize → **Tahaluf Settings** panel exists with Branding + Footer sections.
- [ ] Upload a logo. Logo `<img>` URL in HTML source is escaped (`esc_url`).
- [ ] Change accent colour. `<style id="tahaluf-vars">` in `<head>` reflects new colour. Summary block left-border on a discussion picks up the new colour.
- [ ] Set footer text to `Hi <strong>world</strong> <script>alert(2)</script>`. Front-end shows bold "world", no alert.
- [ ] Toggle "Show AI-assisted badge". Front-end footer reflects the toggle.

## Security model

Single source of truth: `includes/class-community-ai-security.php`.

| Boundary | Nonce | Capability | Sanitize on input | Escape on output |
|---|---|---|---|---|
| Meta box save (`save_post_community_discussion`) | `community_ai_metabox_save` | `edit_post` | `sanitize_text_field` via `Security::sanitize_plain_text` | `esc_html` / `esc_textarea` |
| Admin-ajax `community_ai_generate_summary` | `community_ai_generate` (`check_ajax_referer`) | `edit_post` | `absint` post_id, `wp_kses_post` content | `wp_send_json_*` (auto-encoded) |
| Settings API form | `settings_fields()` issues nonce | `manage_options` | per-field sanitize callbacks | `esc_attr` on rendered inputs |
| Customizer save | Core | `edit_theme_options` | `sanitize_callback` per setting (`absint`, `sanitize_hex_color`, `wp_kses_post`, `rest_sanitize_boolean`) | `esc_url`, `esc_attr`, `wp_kses_post` on render |
| Front-end render | n/a | n/a | n/a | `esc_html` for summary, `esc_url` for logo, `wp_kses_post` for footer |

### How to use the security helpers

Every entry point routes through `Community_AI_Security`:

```php
// Nonce verification (form post)
if ( ! Community_AI_Security::verify_post_nonce( 'community_ai_metabox_save', 'community_ai_metabox_nonce' ) ) {
    return;
}

// Nonce verification (ajax)
Community_AI_Security::require_ajax_nonce( 'community_ai_generate', 'nonce' );

// Capability
if ( ! Community_AI_Security::user_can_edit_post( $post_id ) ) {
    wp_send_json_error( ..., 403 );
}

// Sanitize plain text input (with hard length cap)
$clean = Community_AI_Security::sanitize_plain_text( $_POST['community_ai_summary'] );

// Sanitize rich-text post content
$clean_html = Community_AI_Security::sanitize_rich_text( $post->post_content );

// Sanitize a numeric range with min/max clamping
$len = Community_AI_Security::sanitize_summary_length( $input, 10, 500 );
```

The helper enforces a hard upper bound (`MAX_TEXT_BYTES = 100000`) on any string before sanitization to prevent DoS via oversize payloads.

## Architecture

Each module has a single responsibility and a registration entry point. The orchestrator (`Community_AI_Plugin`) wires them up:

```
Community_AI_Plugin::boot_modules()
├── Community_AI_CPT::register()         // registers post type + meta
├── Community_AI_Settings::register()    // Settings API hooks
├── Community_AI_MetaBox::register()     // meta box + save_post + admin enqueue
├── Community_AI_Ajax::register($mock)   // injects an AI service implementation
└── Community_AI_Frontend::register()    // the_content filter + frontend enqueue
```

Swap the mock for a real provider in one line — implement `Community_AI_Service_Interface::summarize()` and inject it in `boot_modules()`.

## Notes on authenticity

Per the Tahaluf brief, AI tools were used for inspiration only. Every architectural choice, security boundary, and verification step was reviewed by hand. The implementation plan that produced this code was deliberately kept out of the public repo to keep the diff focused on the deliverable.
