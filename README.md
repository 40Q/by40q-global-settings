# 40Q Global Settings

A WordPress plugin that replaces ACF options pages with a native, developer-driven settings panel. Devs register tabs and fields in PHP; editors fill values via a React admin UI. Any plugin or theme reads values with a single helper call.

---

## Requirements

- **40Q Core Plugin** must be active.
- PHP 8.2+, WordPress 6.4+

---

## How it works

1. **Devs register** tabs and fields by hooking into `by40q_register_global_settings` (fires on `init`).
2. **Editors** visit **Global Settings** in the WP admin sidebar and fill in values.
3. Values are stored in a single `wp_options` row (`by40q_global_settings`).
4. **Any plugin/theme** retrieves a value with `by40q_global_setting( 'key' )`.

---

## Registering tabs and fields

Hook into `by40q_register_global_settings`. You can do this in your plugin's main file, a `plugins_loaded` hook, or any `init`-or-earlier hook.

```php
add_action( 'by40q_register_global_settings', function () {
    // 1. Register a tab
    \By40Q\GlobalSettings\Field_Registry::register_tab( [
        'key'   => 'branding',
        'label' => 'Branding',
        'order' => 10,        // lower = appears first; default 10
    ] );

    // 2. Register fields on that tab
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'         => 'site_slogan',
        'label'       => 'Site Slogan',
        'type'        => 'text',
        'tab'         => 'branding',
        'default'     => '',
        'description' => 'Displayed in the hero section.',
    ] );
} );
```

### `register_tab()` parameters

| Parameter | Type   | Required | Description                                     |
|-----------|--------|----------|-------------------------------------------------|
| `key`     | string | ✅       | Unique slug (auto-sanitized via `sanitize_key`) |
| `label`   | string | ✅       | Human-readable tab name shown in the UI         |
| `order`   | int    | —        | Sort order; lower = first. Default `10`         |

### `register_field()` parameters

| Parameter       | Type   | Required | Description                                                       |
|-----------------|--------|----------|-------------------------------------------------------------------|
| `key`           | string | ✅       | Unique slug — this is what you pass to `by40q_global_setting()`  |
| `label`         | string | ✅       | Human-readable label shown in the admin UI                         |
| `type`          | string | ✅       | Field type (see table below)                                       |
| `tab`           | string | ✅       | Key of a registered tab; orphan fields fall under a General tab   |
| `default`       | mixed  | —        | Default value returned before the editor saves for the first time |
| `description`   | string | —        | Helper text displayed below the field                              |
| `choices`       | array  | —        | Required for `select` type — array of `['label' => '', 'value' => '']` |
| `input_type`    | string | —        | HTML input type hint for `text` fields (e.g. `'email'`, `'tel'`, `'number'`) |
| `repeater_type` | string | —        | For `repeater` fields: the sub-field type (e.g. `'text'`, `'url'`). Default `'text'` |
| `sub_label`     | string | —        | For `repeater` fields: label for each item (e.g. `'Link'`). Defaults to field `label` |

---

## Field types

| Type       | Stored as       | Notes                                                                 |
|------------|-----------------|-----------------------------------------------------------------------|
| `text`     | string          | Single-line text input; supports `input_type` for email/tel/number hints |
| `textarea` | string          | Multi-line plain text                                                 |
| `richtext` | string (HTML)   | HTML textarea with live preview; see upgrade note below               |
| `toggle`   | bool            | On/off switch                                                         |
| `image`    | int             | WordPress attachment ID; use `wp_get_attachment_image_url()` in PHP   |
| `url`      | string          | URL input with `https://` placeholder; stored via `esc_url_raw()`    |
| `select`   | string          | Dropdown; requires `choices` array                                    |
| `repeater` | array           | List of items, all of the same sub-type; requires `repeater_type`    |

### `select` example

```php
\By40Q\GlobalSettings\Field_Registry::register_field( [
    'key'     => 'footer_style',
    'label'   => 'Footer Style',
    'type'    => 'select',
    'tab'     => 'layout',
    'default' => 'minimal',
    'choices' => [
        [ 'label' => 'Minimal',   'value' => 'minimal'   ],
        [ 'label' => 'Full',      'value' => 'full'      ],
        [ 'label' => 'Mega',      'value' => 'mega'      ],
    ],
] );
```

### `text` with `input_type` example

```php
\By40Q\GlobalSettings\Field_Registry::register_field( [
    'key'        => 'contact_email',
    'label'      => 'Contact Email',
    'type'       => 'text',
    'input_type' => 'email',  // Triggers browser email validation and mobile keyboard
    'tab'        => 'contact',
] );

\By40Q\GlobalSettings\Field_Registry::register_field( [
    'key'        => 'phone_number',
    'label'      => 'Phone Number',
    'type'       => 'text',
    'input_type' => 'tel',    // Mobile phone keyboard
    'tab'        => 'contact',
] );
```

Supported `input_type` values: `'email'`, `'tel'`, `'number'`, `'url'`, `'date'`, etc. — any valid HTML5 input type.

### `repeater` example

```php
\By40Q\GlobalSettings\Field_Registry::register_field( [
    'key'           => 'team_members',
    'label'         => 'Team Members',
    'type'          => 'repeater',
    'repeater_type' => 'text',
    'sub_label'     => 'Name',
    'tab'           => 'team',
    'default'       => [],
] );

\By40Q\GlobalSettings\Field_Registry::register_field( [
    'key'           => 'social_links',
    'label'         => 'Social Media Links',
    'type'          => 'repeater',
    'repeater_type' => 'url',
    'sub_label'     => 'URL',
    'tab'           => 'contact',
    'default'       => [],
] );
```

In PHP:
```php
$links = by40q_global_setting( 'social_links' );  // Returns array of URLs
foreach ( $links as $url ) {
    if ( ! empty( $url ) ) {
        echo '<a href="' . esc_url( $url ) . '">Visit</a>';
    }
}
```

### `image` example

```php
\By40Q\GlobalSettings\Field_Registry::register_field( [
    'key'   => 'og_default_image',
    'label' => 'Default OG Image',
    'type'  => 'image',
    'tab'   => 'seo',
] );
```

In a template:
```php
$attachment_id = by40q_global_setting( 'og_default_image' );
$image_url = $attachment_id
    ? wp_get_attachment_image_url( (int) $attachment_id, 'full' )
    : '';
```

---

## Shortcodes

Text fields can be exposed as WordPress shortcodes. Editors enable shortcodes and choose the slug for each field directly in the admin UI — devs don't hard-code them.

### How it works

1. Editor visits **Global Settings** → a text field
2. Below the input, they see a toggle: **"Enable shortcode"**
3. When toggled on, an editable slug field appears, pre-filled with the field key (e.g. `contact_email`)
4. Editor can change the slug to anything (e.g. `my_contact_email`)
5. On save, the shortcode is registered and ready to use in posts
6. In any post/page, `[my_contact_email]` outputs the saved value

### REST API for shortcodes

Shortcode settings are returned by the GET endpoint and accepted by the POST endpoint:

**GET response** (`/wp-json/by40q/v1/global-settings`):
```json
{
  "schema": [...],
  "shortcodes": {
    "contact_email": { "enabled": true,  "slug": "my_contact_email" },
    "site_slogan":   { "enabled": false, "slug": "" },
    ...
  }
}
```

**POST body** to save shortcode settings:
```json
{
  "values": { "contact_email": "hello@example.com", ... },
  "shortcodes": {
    "contact_email": { "enabled": true, "slug": "company_email" },
    "site_slogan":   { "enabled": false, "slug": "" }
  }
}
```

### Data storage

Shortcode settings are stored in a separate option for safety:

```
wp_options.option_name = 'by40q_shortcode_settings'
```

Inspect via WP CLI:
```bash
wp option get by40q_shortcode_settings --format=json
```

---

## Reading settings in PHP

```php
// Basic usage — returns the saved value, or the field's registered default.
$slogan = by40q_global_setting( 'site_slogan' );

// With explicit fallback (overrides field default).
$show_banner = by40q_global_setting( 'show_top_banner', false );

// Image field — attachment ID → URL.
$logo_id  = by40q_global_setting( 'site_logo' );
$logo_url = $logo_id ? wp_get_attachment_image_url( (int) $logo_id, 'full' ) : '';
```

`by40q_global_setting()` lazy-loads all values from `wp_options` on first call and caches them in memory for the rest of the request — no N+1 queries.

---

## Reading settings in a block (JavaScript / `apiFetch`)

```js
import apiFetch from '@wordpress/api-fetch';

const { schema } = await apiFetch( { path: '/by40q/v1/global-settings' } );

// schema is an array of tabs, each with a fields array.
// Extract all values into a flat map:
const values = {};
schema.forEach( ( tab ) => {
    tab.fields.forEach( ( field ) => {
        values[ field.key ] = field.value;
    } );
} );

console.log( values.site_slogan );
```

> The REST endpoint `/wp-json/by40q/v1/global-settings` requires the `manage_options` capability. For front-end blocks, use server-side rendering and pass values via `wp_localize_script` or `wp_add_inline_script` using the PHP helper.

---

## Adding a new tab

You can call `register_tab()` from any plugin. Tabs are identified purely by their `key`. If two plugins register a tab with the same key, the last one wins — so either coordinate keys or reuse common keys intentionally (e.g. a shared `'general'` tab).

```php
// Plugin A — creates the "SEO" tab at position 20
\By40Q\GlobalSettings\Field_Registry::register_tab( [
    'key'   => 'seo',
    'label' => 'SEO',
    'order' => 20,
] );

// Plugin B — adds a field to the same tab
\By40Q\GlobalSettings\Field_Registry::register_field( [
    'key'  => 'meta_description',
    'label'=> 'Default Meta Description',
    'type' => 'textarea',
    'tab'  => 'seo',
] );
```

---

## Complete multi-tab example

```php
add_action( 'by40q_register_global_settings', function () {
    // ── Tabs ──────────────────────────────────────────────────────────────
    \By40Q\GlobalSettings\Field_Registry::register_tab( [ 'key' => 'general',  'label' => 'General',  'order' => 10 ] );
    \By40Q\GlobalSettings\Field_Registry::register_tab( [ 'key' => 'branding', 'label' => 'Branding', 'order' => 20 ] );
    \By40Q\GlobalSettings\Field_Registry::register_tab( [ 'key' => 'contact',  'label' => 'Contact',  'order' => 30 ] );

    // ── General ───────────────────────────────────────────────────────────
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'     => 'maintenance_mode',
        'label'   => 'Maintenance Mode',
        'type'    => 'toggle',
        'tab'     => 'general',
        'default' => false,
    ] );

    // ── Branding ──────────────────────────────────────────────────────────
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'   => 'site_logo',
        'label' => 'Site Logo',
        'type'  => 'image',
        'tab'   => 'branding',
    ] );
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'     => 'site_slogan',
        'label'   => 'Site Slogan',
        'type'    => 'text',
        'tab'     => 'branding',
        'default' => 'Building better.',
    ] );
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'   => 'brand_color',
        'label' => 'Brand Colour',
        'type'  => 'select',
        'tab'   => 'branding',
        'choices' => [
            [ 'label' => 'Blue',  'value' => 'blue'  ],
            [ 'label' => 'Green', 'value' => 'green' ],
        ],
    ] );

    // ── Contact ───────────────────────────────────────────────────────────
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'   => 'contact_email',
        'label' => 'Contact Email',
        'type'  => 'text',
        'tab'   => 'contact',
    ] );
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'   => 'contact_phone',
        'label' => 'Contact Phone',
        'type'  => 'text',
        'tab'   => 'contact',
    ] );
    \By40Q\GlobalSettings\Field_Registry::register_field( [
        'key'   => 'contact_address',
        'label' => 'Address',
        'type'  => 'textarea',
        'tab'   => 'contact',
    ] );
} );
```

---

## Data storage

All field values are stored in a single WordPress option:

```
wp_options.option_name = 'by40q_global_settings'
wp_options.option_value = { "site_slogan": "...", "contact_email": "...", ... }
```

Shortcode settings are stored separately:

```
wp_options.option_name = 'by40q_shortcode_settings'
wp_options.option_value = { "site_slogan": { "enabled": false, "slug": "" }, ... }
```

Inspect via WP CLI:
```bash
wp option get by40q_global_settings --format=json
wp option get by40q_shortcode_settings --format=json
```

Reset all values:
```bash
wp option delete by40q_global_settings
wp option delete by40q_shortcode_settings
```

---

## REST API reference

| Method | Endpoint                            | Auth             | Description                             |
|--------|-------------------------------------|------------------|-----------------------------------------|
| GET    | `/wp-json/by40q/v1/global-settings` | `manage_options` | Returns schema + values + shortcodes    |
| POST   | `/wp-json/by40q/v1/global-settings` | `manage_options` | Saves field values and shortcode toggles |

GET response:
```json
{
  "schema": [
    {
      "key": "general",
      "label": "General",
      "fields": [
        { "key": "site_slogan", "label": "Site Slogan", "type": "text", "value": "..." }
      ]
    }
  ],
  "shortcodes": {
    "site_slogan": { "enabled": true, "slug": "site_slogan" }
  }
}
```

POST body:
```json
{
  "values": {
    "site_slogan": "Building better.",
    "maintenance_mode": false
  },
  "shortcodes": {
    "site_slogan": { "enabled": true, "slug": "site_slogan" }
  }
}
```

> `shortcodes` is optional in the POST body; omit it to save only field values without changing shortcode settings.

---

## Developer notes

### RichText upgrade path

The `richtext` field currently uses a plain HTML textarea. To upgrade to the full block-editor `RichText` component, wrap the app in `<BlockEditorProvider>` inside [src/js/settings/index.tsx](src/js/settings/index.tsx) and replace `RichtextField.tsx` with a `<RichText>` component from `@wordpress/block-editor`.

### Repeater field notes

- Sub-fields are any type *except* `repeater` (no nesting).
- Repeater values are stored as a flat array in `wp_options`; each item is sanitized according to the `repeater_type`.
- Editors can add/remove items in the admin; the UI renders one sub-field per item.
- Default is `[]` (empty array) unless overridden.

### Field key collisions

Field keys are global — if two plugins register a field with the same key, the last one to run wins. Use a unique prefix per plugin to avoid collisions:

```php
'key' => 'my_plugin__hero_title'  // ✅ prefixed
'key' => 'title'                   // ❌ too generic
```

### Adding a new field type

1. Add the type string to `$valid_types` in [includes/class-field-registry.php](includes/class-field-registry.php).
2. Add a sanitizer branch in `sanitize_value()` in the same file. For repeater-like types, handle the `$repeater_type` parameter.
3. Add the TypeScript type to the `FieldType` union in [src/js/settings/types.ts](src/js/settings/types.ts).
4. Create a new component in [src/js/settings/components/fields/](src/js/settings/components/fields/).
5. Add a `case` in [src/js/settings/components/fields/FieldRenderer.tsx](src/js/settings/components/fields/FieldRenderer.tsx).
