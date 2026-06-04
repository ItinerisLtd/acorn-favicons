# Copilot Instructions

## Commands

```bash
composer run-script style:check   # PHP_CodeSniffer lint (phpcs)
composer run-script style:fix     # Auto-fix with phpcbf
```

There is no test suite — PR CI only runs the linter.

## Architecture

This is a [Roots Acorn](https://roots.io/acorn/) package for WordPress/Bedrock that injects favicon `<link>` and `<meta>` tags into the `<head>` and serves a dynamic `site.webmanifest`.

**Bootstrap flow:**
1. `AcornFaviconsServiceProvider` (extends `SageServiceProvider`) registers the `AcornFavicons` singleton and calls `$this->app->make('AcornFavicons')` in `boot()`, which triggers instantiation immediately.
2. `AcornFavicons::__construct()` wires up WordPress hooks: `wp_head` + `login_head` (or `site_icon_meta_tags` if a site icon is set), and `template_redirect` to intercept the manifest URL.
3. The service provider auto-registers via `extra.acorn.providers` in `composer.json` — no manual registration needed by consumers.

**Config:** Two layered files are published to the consumer's config directory:
- `favicons.json` — the user-editable config (theme_color, background_color, manifest_path). Has a JSON Schema reference.
- `favicons.php` — reads and decodes `favicons.json`, falling back to the package default.

**Multisite:** When `multisite.use-shared-assets` is `false` (default), favicon filenames are prefixed with the site slug (e.g. `mysite-favicon.ico`) to avoid collisions. The prefix is empty for single-site installs.

**Favicon file expectations:** Physical files must exist at `ABSPATH/../` (i.e. `bedrock/web/`). `getPublicPath()` returns `null` for missing files. `buildMetaTag()` skips empty attributes, so `<link>` tags may still be emitted without an `href`. The `msapplication-config` meta tag is an exception — `generateWindowsMetaTags()` explicitly filters it out when `content` is empty.

## Key Conventions

- All `src/` PHP files use `declare(strict_types=1)` (config files such as `config/favicons.php` do not).
- Namespace root: `ItinerisLtd\AcornFavicons\`.
- Coding standard: `itinerisltd/itineris-wp-coding-standards` (extends WPCS). Text domain is `itineris`.
- `phpcs.xml` scans only the `src/` directory.
- PHP minimum: **8.1**. Acorn compatibility: **^4.3** (conflicts with >=5.0).
- The Facade accessor uses `AcornFaviconsClass::class` (the FQCN) while the container binds under the string key `'AcornFavicons'` — these intentionally differ. If the class is renamed, update the Facade accessor; if the binding key changes, update `$this->app->make()` in the service provider.
