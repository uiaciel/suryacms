# SuryaCMS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/uiaciel/suryacms.svg)](https://packagist.org/packages/uiaciel/suryacms)
[![Total Downloads](https://img.shields.io/packagist/dt/uiaciel/suryacms.svg)](https://packagist.org/packages/uiaciel/suryacms)
[![License](https://img.shields.io/packagist/l/uiaciel/suryacms.svg)](https://github.com/uiaciel/suryacms/blob/main/LICENSE)
[![GitHub Stars](https://img.shields.io/github/stars/uiaciel/suryacms?style=social)](https://github.com/uiaciel/suryacms)

**SuryaCMS** is a modular content-management system for Laravel applications. It provides an authenticated administration panel, multilingual SEO-friendly frontend routes, a configurable theme system, page and post management, media tools, backups, and a Livewire-powered user interface.

It is intended for developers, agencies, and product teams building company profiles, landing pages, blogs, school websites, SME websites, and multiple websites from a shared Laravel codebase.

> SuryaCMS is distributed as a Laravel package. It is not a standalone Laravel application; it must be installed into an existing Laravel application.

## Contents

- [Requirements](#requirements)
- [Included capabilities](#included-capabilities)
- [Installation](#installation)
- [Configuration](#configuration)
- [Environment variables](#environment-variables)
- [Admin panel and frontend URLs](#admin-panel-and-frontend-urls)
- [Artisan commands](#artisan-commands)
- [Themes](#themes)
- [Database](#database)
- [Helpers](#helpers)
- [Extending SuryaCMS](#extending-suryacms)
- [Backups and restore](#backups-and-restore)
- [Monitoring API](#monitoring-api)
- [Operational notes](#operational-notes)
- [Development and contribution](#development-and-contribution)
- [License](#license)

## Requirements

- PHP **8.1 or newer**
- Laravel components **11.x or 12.x** (`illuminate/support`)
- Livewire **3.x**
- A Laravel application with a configured database, session, cache, queue, filesystem, and mailer
- The PHP extensions required by Laravel and the package dependencies

The package also uses the following Composer dependencies:

- `maatwebsite/excel` for import/export workflows
- `spatie/pdf-to-image` and `barryvdh/laravel-dompdf` for PDF-related features
- `intervention/image-laravel` for image processing
- `symfony/dom-crawler` and `symfony/http-client` for HTML and HTTP workflows

## Included capabilities

### Content management

- Pages with publish/draft status, slugs, translations, SEO fields, and PDF attachments
- Posts, categories, tags, publishing controls, translations, and view tracking
- Nested menus with ordering and links to pages, posts, and categories
- Galleries, sliders, image uploads, PDF attachments, and media helpers
- YouTube video management with URL-based metadata workflows
- Contact form and administrator inbox
- Comments and visitor statistics

### Site administration

- Authenticated admin dashboard
- User management and profile management
- Site identity, contact information, social links, SEO metadata, analytics snippets, colors, date format, and homepage settings
- Maintenance mode with a frontend maintenance page
- File and system checks
- Import/export support for settings, pages, posts, menus, galleries, and inbox data

### Frontend and themes

- Blade-based frontend themes
- Livewire 3 components with Alpine.js-compatible views
- Single-language and multilingual routing
- SEO-friendly language URLs: the default language has no prefix, while other languages use a two-letter prefix such as `/en`
- Theme selection, theme creation, HTML-to-Blade conversion, theme editor, theme builder, and theme documentation screens
- Built-in `default` and `builder-example` themes
- Bootstrap 5-compatible frontend assets and Bootstrap Icons assets
- PWA manifest and service-worker endpoints
- Optional generated offline page and sitemap

### Reliability and deployment

- Full database, storage, frontend-asset, theme, configuration, and environment backup workflows
- Full and partial restore workflows
- Queueable backup and restore jobs
- Package update and deployment archive commands
- Monitoring endpoints protected by a bearer token

## Installation

### 1. Install the package

From the root of an existing Laravel application:

```bash
composer require uiaciel/suryacms
```

Laravel package discovery registers `Uiaciel\SuryaCms\SuryaCmsServiceProvider` automatically through `composer.json`.

### 2. Publish package files

Publish the package configuration:

```bash
php artisan vendor:publish --tag=suryacms-config
```

Publish the frontend views when you need to customize them:

```bash
php artisan vendor:publish --tag=suryacms-frontend-views
```

Publish the public frontend assets:

```bash
php artisan vendor:publish --tag=suryacms-frontend-assets
```

Optionally publish the migrations explicitly:

```bash
php artisan vendor:publish --tag=suryacms-migrations
```

The service provider also loads the package migrations automatically. Publish them only when your application workflow requires editable migration copies.

### 3. Configure the application

Set the normal Laravel values in `.env`, especially:

```dotenv
APP_NAME="My Website"
APP_URL=https://example.com
APP_LOCALE=id
DB_CONNECTION=mysql
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

Configure the mailer if contact forwarding is enabled, and configure a queue worker if backups or monitoring-triggered backups should run asynchronously.

### 4. Run migrations and create storage links

```bash
php artisan migrate
php artisan storage:link
```

### 5. Run the installation checker

```bash
php artisan suryacms:install
```

The command checks the application environment and can guide the initial administrator setup. The exact prompts may depend on the host application.

### 6. Open the application

- Login: `/login`
- Admin dashboard: `/admin`
- Public homepage: `/`

The admin prefix is configurable; see [Configuration](#configuration).

## Configuration

After publishing, the package creates `config/suryacms.php` and `config/frontend.php` in the host application.

### `config/suryacms.php`

| Key | Default | Purpose |
| --- | --- | --- |
| `admin_prefix` | `admin` | Prefix for the authenticated admin panel. For example, `backoffice` changes the dashboard URL to `/backoffice`. |
| `excluded_slugs` | See the published file | Slugs excluded from the frontend wildcard page route. Add application-specific reserved paths here. |
| `tables` | `categories`, `contacts`, `galleries`, `languages`, `menus`, `pages`, `posts`, `settings`, `youtube_videos`, `users`, `custom_blocks` | Tables considered SuryaCMS tables by the backup configuration. |
| `monitor_token` | `env('SURYACMS_MONITOR_TOKEN', null)` | Bearer token used to protect monitoring API endpoints. |
| `multisite.enabled` | `env('SURYACMS_MULTISITE_ENABLED', false)` | Enables host-based site resolution and site data isolation. |
| `multisite.management_site_id` | `1` | Site ID whose primary domain can manage site mappings at `/{admin_prefix}/sites`. |
| `version` | Package version | SuryaCMS version exposed by the monitoring API and system UI. |

After changing configuration, clear the cached configuration in production:

```bash
php artisan config:clear
php artisan config:cache
```

The `excluded_slugs` list should contain every path that must not be captured by the final `{slug}` frontend route. The built-in list protects paths such as `login`, `register`, `api`, `category`, `media`, `horizon`, and `telescope`.

### `config/frontend.php`

| Key | Default | Purpose |
| --- | --- | --- |
| `active` | `env('FRONTEND_THEME', 'default')` | Fallback active theme when the current site's database setting does not define `active_theme`. |
| `url` | `env('APP_URL', null)` | Frontend URL value used by frontend configuration. |
| `default_locale` | `id` | Default frontend locale value. The database setting may provide the active site language. |
| `themes_path` | `frontend` | Directory below `resources/views` and `public` containing themes. |
| `available_themes` | `default`, `builder-example` | Themes presented to theme-related UI and services. Add custom theme names here when needed. |
| `registration_enabled` | `env('FRONTEND_REGISTRATION_ENABLED', true)` | Enables or disables public registration. When disabled, users should be created from the admin panel. |

### Theme configuration

Each theme can provide a `theme.php` file. SuryaCMS reads theme metadata and asset configuration through `get_theme_config()`. The built-in themes live under:

```text
resources/views/frontend/default/
resources/views/frontend/builder-example/
public/frontend/default/
public/frontend/builder-example/
```

A published theme can be customized in the host application without editing the package. When multisite is enabled, the current site's `settings.active_theme` takes precedence over `frontend.active`.

### Site settings managed from the admin panel

The Settings screen stores values including:

- Site name, tagline, description, keywords, URL, logo, favicon, and social links
- Homepage type and homepage ID
- Active theme
- Default language and multilingual mode
- Google site verification, Analytics, and AdSense snippets
- Primary, secondary, success, danger, warning, info, light, and dark colors
- Maintenance-mode status
- Forwarding email address
- Date format

## Environment variables

| Variable | Default | Used for |
| --- | --- | --- |
| `APP_URL` | Laravel default | Application and frontend URL generation. |
| `FRONTEND_THEME` | `default` | Fallback frontend theme. |
| `FRONTEND_REGISTRATION_ENABLED` | `true` | Public registration switch. |
| `SURYACMS_MONITOR_TOKEN` | `null` | Authentication for the monitoring API. Keep this secret and use HTTPS. |
| `SURYACMS_MULTISITE_ENABLED` | `false` | Enables domain-based site resolution and data isolation. |
| `SURYACMS_MULTISITE_MANAGEMENT_SITE_ID` | `1` | Site ID allowed to manage sites from its primary domain. |
| `TINY_EDITOR_KEY` | Package fallback value | TinyMCE editor loading in page and post forms. Set your own TinyMCE key in production. |

Do not commit secrets to source control. In particular, set `SURYACMS_MONITOR_TOKEN` and `TINY_EDITOR_KEY` through the host application's secret-management process.

## Admin panel and frontend URLs

### Admin routes

With the default prefix, the package provides routes such as:

| URL | Purpose |
| --- | --- |
| `/admin` | Dashboard |
| `/admin/pages` | Page management |
| `/admin/posts` | Post management |
| `/admin/menus` | Menu management |
| `/admin/galleries` | Gallery management |
| `/admin/youtube` | YouTube video management |
| `/admin/contacts` | Contact inbox |
| `/admin/themes` | Theme management |
| `/admin/sites` | Multisite and domain management (available on the management site's primary domain) |
| `/admin/page-builder` | Page builder |
| `/admin/system` | System and file checks |
| `/admin/system/backups` | Backup management |
| `/admin/system/restore` | Full restore UI |
| `/admin/users` | User management |
| `/admin/setting` | Site settings |

All admin routes require the Laravel `web` and `auth` middleware.

### Public routes

The frontend includes:

- `/` for the homepage
- `/category` and `/category/{slug}` for categories
- `/media/{slug}` for published post pages
- `/contact-us` and `/contact-us/send` for the contact form
- `/{slug}` for published pages, registered last as a wildcard route

When multilingual mode is enabled, the default language uses unprefixed URLs and other languages use a prefix:

```text
/about
/en/about
/category/news
/en/category/news
```

### Built-in system endpoints

- `GET /suryacms/test` — simple activation check
- `GET /suryacms/sw.js` — service worker response
- `GET /suryacms/manifest.json` — PWA manifest response
- `GET /manifest.json` — localized PWA manifest for the active site

## Artisan commands

Run `php artisan list` in the host application to confirm the commands registered in the current installation.

### Content and system commands

| Command | Purpose |
| --- | --- |
| `php artisan suryacms:install` | Check the environment and perform initial SuryaCMS setup. |
| `php artisan suryacms:helper` | List available SuryaCMS helper functions. |
| `php artisan suryacms:maintenance --status` | Show maintenance-mode status. |
| `php artisan suryacms:maintenance --check` | Check maintenance configuration. |
| `php artisan suryacms:maintenance --on` | Enable maintenance mode. |
| `php artisan suryacms:maintenance --off` | Disable maintenance mode. |
| `php artisan app:generate-offline` | Generate `offline.html` from the frontend homepage. |
| `php artisan app:generate-sitemap` | Generate the site sitemap, or one `{domain}.xml` sitemap per mapped domain when multisite is enabled. |
| `php artisan suryacms:site:list` | List sites and their mapped domains. |
| `php artisan suryacms:site:create {domain} {name}` | Create a site, map its primary domain, and create its initial settings row. |
| `php artisan suryacms:site:domain {site} {domain}` | Map an additional domain to an existing site ID. |

### Backup and restore commands

```bash
php artisan suryacms:backup
php artisan suryacms:restore storage/app/private/suryacms_backups/backup.zip
```

`restore` accepts a required backup archive path. Backups and restores may touch database records, storage files, themes, configuration, and environment files; verify the archive and create a safe copy before restoring in production.

### Theme and deployment commands

| Command | Arguments and options | Purpose |
| --- | --- | --- |
| `php artisan theme:install {zipfile} {--activate}` | Theme archive and optional activation flag | Install a theme archive from `storage/app/public`. |
| `php artisan suryacms:setup-theme-storage` | None | Create or prepare theme storage directories and permissions. |
| `php artisan suryacms:setup-packages` | None | Prepare the packages repository path used by the package workflow. |
| `php artisan suryacms:package-zip {output=vendor_update.zip}` | Optional output filename | Create a deployment archive containing vendor and Composer files. |
| `php artisan suryacms:package-update {zip} {--backup} {--backup-only} {--migrate} {--force}` | Archive path plus update options | Extract and update a deployment archive; optionally back up, migrate, or skip confirmation. |

The `--backup-only` option creates a backup without applying the update. Treat `--force` as a production deployment option and review the archive before using it.

## Themes

Themes use the configured theme path and are selected in this order:

1. The `active_theme` value in the `settings` table, when available
2. The `frontend.active` configuration value
3. The `default` fallback

A theme normally contains Blade views and may contain public assets:

```text
resources/views/frontend/{theme}/
public/frontend/{theme}/
```

Common theme helpers include:

```blade
{{ themeAsset('css/styles.css') }}
{!! theme_css('styles.css') !!}
{!! theme_js('scripts.js') !!}
{{ get_theme_config('assets.styles') }}
```

For Vite-based themes, use `theme_vite_assets()`. For view resolution with fallback to the default theme, use `theme_view('homepage')` or another view name supported by the theme.

## Database

The package ships migrations for the following core tables and features:

- `settings`
- `categories`
- `languages`
- `pages`
- `posts`
- `menus`
- `galleries`
- `contacts`
- `comments`
- `youtube_videos`
- `visitors`
- `custom_blocks`
- additional migrations for page builders, visitor dates, inbox fields, and gallery PDFs

The package expects the host Laravel application to provide the normal `users` table and authentication stack. Review migration order and existing table names before installing into an application that already contains CMS tables.

## Multisite and shared hosting

Multisite resolves the incoming HTTP `Host` header to a site while every domain shares one Laravel installation and database. On cPanel, point each domain's document root or symlink at the same Laravel `public` directory; the incoming hostname is what selects the site.

1. Set the primary domain in `APP_URL`, enable multisite, then run migrations:

   ```dotenv
   APP_URL=https://example.com
   SURYACMS_MULTISITE_ENABLED=true
   ```

   ```bash
   php artisan migrate
   ```

   The migration creates the initial site, maps the host from `APP_URL`, and assigns existing CMS rows to that site. Keep `APP_URL` set to the canonical primary domain before this first migration.

2. Create each additional site and map optional aliases:

   ```bash
   php artisan suryacms:site:create second.example.com "Second Site"
   php artisan suryacms:site:domain 2 www.second.example.com
   php artisan suryacms:site:list
   ```

3. Configure each site's settings and active theme from its domain's `/{admin_prefix}` panel (`/admin` by default). CMS records are scoped by `site_id` in settings, posts, pages, categories, inbox/contacts, galleries, menus, languages, comments, videos, visitors, custom blocks, and `suryacms_settings`. Site IDs are assigned automatically on model inserts. Admin user accounts remain shared.

The **Kelola Sites** page is available from the primary site's admin menu or at `https://example.com/{admin_prefix}/sites` (`/admin/sites` by default). Access is restricted to the primary domain of `multisite.management_site_id` (site ID `1` by default); set `SURYACMS_MULTISITE_MANAGEMENT_SITE_ID` if another site should own site management. On each additional domain, sign in with the shared admin account and visit `https://domain-anda/{admin_prefix}` to manage that domain's own content, settings, inbox, and theme.

The `sites` and `site_domains` tables contain domain mappings. An unmapped hostname returns HTTP 404 when multisite is enabled, rather than falling back to another site's content. When multisite is disabled (the default), existing single-site installations keep their previous hostname behavior.

When multisite is enabled, run `php artisan app:generate-sitemap` (or use the admin's sitemap generator) to generate a separate `domain.xml` file for each mapped domain. Each file uses the content belonging to that domain's site. The File Check and system information screens show the active site and domain.

New media and site-setting uploads made through SuryaCMS are stored under `storage/app/public/sites/{site_id}/` and served through the normal `public/storage` symlink. Existing uploads are left in place and remain available to the initial site. The selected theme is per-site; theme view and public asset files remain part of the shared application resources.

## Helpers

The package autoloads `src/helpers.php` through Composer. Frequently used helpers include:

| Helper | Purpose |
| --- | --- |
| `setting()` / `get_cms_setting()` | Retrieve the current CMS settings record safely. |
| `active_languages()` / `available_locales()` | Retrieve published language codes or language records. |
| `default_locale()` / `current_locale()` / `is_multilingual()` | Inspect locale and multilingual state. |
| `lang_route($name, $params = [])` | Generate a route URL with SEO-friendly language handling. |
| `url_with_lang($path = '', $lang = null)` | Generate a language-aware URL. |
| `switch_locale_url($newLocale)` | Switch language while preserving the current path. |
| `formatDate($date)` / `get_date_format()` | Format dates using the CMS date format. |
| `text($idText, $enText)` | Select Indonesian or English text from the current session locale. |
| `get_active_theme()` / `getActiveTheme()` | Resolve the active theme. |
| `theme_view($view, $data = [], $mergeData = [])` | Resolve a view from the active theme with default-theme fallback. |
| `themeAsset($path)` / `theme_asset($path)` | Generate a URL for a theme asset. |
| `themePath()` | Get the active theme's view path. |
| `theme_css($path, $attributes = [])` / `theme_js($path, $attributes = [])` | Generate CSS and JavaScript tags. |
| `getAvailableThemes()` | Discover themes in the configured theme directory. |
| `getFirstImage($item, ...)` / `getFirstImageUrl($item)` | Resolve the first image from a model exposing `gambar()`. |
| `get_content_excerpt($item, $length = 150)` | Return a plain-text content excerpt. |
| `seo_meta($seo = null)` | Render the package SEO metadata view. |
| `suryacms_version()` | Read the installed package version. |
| `register_admin_menupackage($packageName, $menus)` | Register additional admin menu entries from another package. |

## Extending SuryaCMS

SuryaCMS is designed to be extended from the host application or another package:

1. Register additional admin navigation with `register_admin_menupackage()`.
2. Add application routes before or after package routes according to the desired precedence.
3. Add custom themes under the configured theme directory and publish matching assets under `public/frontend`.
4. Add migrations and models for application-specific modules.
5. Add custom blocks for the page builder and expose them through the host application's UI.
6. Add custom metrics or integrations around the monitoring API where appropriate.

Because the frontend wildcard route is registered last but matches broadly, reserve custom paths through `suryacms.excluded_slugs` when another route must own a top-level URL.

## Backups and restore

The backup workflow is intended to cover SuryaCMS database tables, application storage, frontend assets, themes, configuration, and environment data. The exact contents depend on the installed package version and the host application's filesystem layout.

Recommended production workflow:

```bash
php artisan suryacms:backup
# Verify the generated archive and keep an external copy.
php artisan suryacms:restore /absolute/path/to/verified-backup.zip
```

Before a restore:

- Put the application into a controlled maintenance window.
- Verify the archive checksum and origin.
- Confirm database and storage credentials.
- Test the restore in a staging environment first.
- Ensure the queue worker is running if the UI starts a queued operation.

## Monitoring API

The package exposes token-protected routes under `/api`:

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `GET` | `/api/system/monitor` | Return system status, package version, storage information, CMS metrics, visitors, active theme, and last backup information. |
| `POST` | `/api/surya-monitor/backup` | Queue a SuryaCMS backup. |
| `POST` | `/api/posts` | Create a post from validated API input. |

Send the configured token as a bearer token:

```http
Authorization: Bearer YOUR_SURYACMS_MONITOR_TOKEN
```

Example health check:

```bash
curl -H "Authorization: Bearer ${SURYACMS_MONITOR_TOKEN}" \
  https://example.com/api/system/monitor
```

Do not expose these endpoints without HTTPS and a strong secret token. The API can create posts and queue backups, so protect it as an operational interface rather than a public API.

## Operational notes

- Run `php artisan optimize:clear` after publishing files or changing package configuration during deployment.
- Configure a queue worker for queued backups and restore operations.
- Configure `MAIL_*` values before enabling contact forwarding.
- Use a custom TinyMCE key through `TINY_EDITOR_KEY` rather than relying on the fallback editor key.
- The package currently has no test suite or PHP coding-standard configuration in this repository. Add host-application integration tests before making a production release.
- The package's backup services currently reference the `suryacms-backup.tables` configuration namespace, while the published configuration file defines `tables` under `suryacms`. Verify this namespace in the installed version before relying on custom backup-table configuration.
- Check the host application's existing routes and migration names before installation, especially when it already contains `settings`, `pages`, `posts`, or `contacts` tables.

## Development and contribution

Clone the repository and install dependencies in a host Laravel application that exercises the package. Before opening a pull request:

1. Run Composer dependency validation.
2. Run the host application's test suite.
3. Run PHP syntax checks and static analysis where configured.
4. Verify migrations on a fresh database and an existing database.
5. Verify published views and assets from a clean installation.
6. Test multilingual URLs, theme switching, backup/restore, and the monitoring API.

Issues and feature requests can be submitted through the [GitHub repository](https://github.com/uiaciel/suryacms).

## License

SuryaCMS is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

**Uia Ciel**

Email: [uiaciel@gmail.com](mailto:uiaciel@gmail.com)

GitHub: [@uiaciel](https://github.com/uiaciel)
