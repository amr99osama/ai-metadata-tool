# Community AI - WordPress Plugin + Tahaluf Child Theme

A compact WordPress build for Tahaluf: editors can publish community discussions, generate a local AI-style summary, adjust it by hand, and tune the child theme from the Customizer.

## What's implemented

**Plugin: `Community AI`**

- Custom post type **Community Discussions** (`community_discussion`), public, REST-enabled, with archive at `/discussions/`.
- **AI Summary** meta box on the discussion editor with a one-click "Generate AI Summary" button.
- Deterministic mock AI service behind `Community_AI_Service_Interface`, with a `community_ai_service` filter for real providers.
- Admin-ajax endpoint `community_ai_generate_summary` with nonce + capability + resource checks before any state change.
- Settings page (Discussions → AI Settings) with **min/max summary length** + cross-field validation (min < max).
- Summary is stored as post meta (`_community_ai_summary`) and rendered as an escaped block above the post body on the front-end.
- Centralised `Community_AI_Security` helper for nonces, capability checks, sanitization, and output escaping.
- Clean `uninstall.php` removes the plugin's option row and post meta.

**Child theme: `Tahaluf Twenty Twenty-Five Child`**

- Overrides the parent header (`parts/header.html`) so the **Site Logo** uploaded via Customize → Site Identity actually appears in the header.
- Customizer panel **Tahaluf Settings** with four sections:
  - **Branding** - accent colour, logo max width (40-800 px), logo max height (20-400 px).
  - **Header** - header background, header text colour, sticky header toggle, show-site-title toggle.
  - **Layout & Typography** - content max width (800-2400 px), body font size (12-24 px).
  - **Footer** - footer text (HTML allowed via `wp_kses_post`), "AI-assisted" badge toggle.
- All Customizer settings have explicit `sanitize_callback` and `esc_*` on render.

## Repo layout

```
wp-content/
├── plugins/community-ai/
│   ├── community-ai.php                       # Header + bootstrap
│   ├── uninstall.php                          # Removes options/meta on uninstall
│   ├── readme.txt                             # WP.org-style readme
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
    ├── parts/header.html                      # Overrides parent header with Site Logo block
    └── inc/
        ├── customizer.php                     # Panel / sections / settings
        ├── customizer-output.php              # Dynamic CSS for the settings
        └── template-tags.php                  # tahaluf_site_logo()
```

## Install

1. WordPress 6.0+ on PHP 7.4+ (developed on XAMPP / PHP 8.2).
2. Copy `wp-content/plugins/community-ai/` into your site's plugins directory.
3. Copy `wp-content/themes/tahaluf-twentytwentyfive-child/` into your site's themes directory.
4. WP Admin → **Plugins** → activate **Community AI**.
5. WP Admin → **Appearance → Themes** → activate **Tahaluf Twenty Twenty-Five Child**.
6. WP Admin → **Settings → Permalinks** → click **Save** (refreshes rewrite rules for `/discussions/`).


## Quick manual QA

- [ ] Activate the plugin, then the child theme. No PHP fatals in `wp-content/debug.log`.
- [ ] Create a **Community Discussion** with body content → click **Generate AI Summary** → textarea fills with a plain-text summary and the caption updates.
- [ ] Visit the public discussion URL → summary block appears above the post body.
- [ ] Paste `<script>alert(1)</script>` into the summary → save → view front-end → tags render as literal text (no alert).
- [ ] Discussions → **AI Settings** → set Min=200, Max=100 → save → validation explains the issue and adjusts the maximum.
- [ ] Customize → **Site Identity → Logo** → upload an image → it appears in the header.
- [ ] Customize → **Tahaluf Settings** → tune logo dimensions, header colours, sticky toggle, font size, content width, footer text, and badge toggle → changes reflected on the front-end.

## Security model

Single source of truth: [`includes/class-community-ai-security.php`](wp-content/plugins/community-ai/includes/class-community-ai-security.php).

| Boundary | Nonce | Capability | Sanitize | Escape |
|---|---|---|---|---|
| Meta box save | `community_ai_metabox_save` | `edit_post` | `sanitize_textarea_field` | `esc_textarea` |
| Admin-ajax generate | `community_ai_generate` | `edit_post` | `absint` + `wp_kses_post` | `wp_send_json_*` |
| Settings API form | `settings_fields()` | `manage_options` | per-field callback | `esc_attr` |
| Customizer save | core | `edit_theme_options` | per-setting callback | `esc_url` / `esc_attr` / `wp_kses_post` |
| Front-end render | n/a | n/a | n/a | `esc_html` / `esc_url` / `wp_kses_post` |

