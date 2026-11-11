# jbmm/db-table-manager

A Laravel package that provides a local development UI to browse and manage database tables using your app’s default `.env` database connection.

## Features

- Route at **`/db-manager`** (configurable)
- Left sidebar: table list; right panel: records, search, pagination
- Create, edit, and delete rows (dynamic forms from schema)
- UI theme: **Tailwind**, **Bootstrap 5**, or **Bootstrap 6** (`auto` detects from the host project)
- **Local-only** by default (`APP_ENV=local`); opt-in elsewhere via `DB_MANAGER_ENABLED=true`

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

## Installation

```bash
composer require jbmm/db-table-manager
```

Optional: publish config

```bash
php artisan vendor:publish --tag=db-manager-config
```

Optional: publish views for customization

```bash
php artisan vendor:publish --tag=db-manager-views
```

Visit `http://localhost/db-manager` while `APP_ENV=local`.

## Configuration

| Key | Env | Default | Description |
|-----|-----|---------|-------------|
| `enabled` | `DB_MANAGER_ENABLED` | `false` | Force enable outside `local` |
| `route_prefix` | `DB_MANAGER_ROUTE_PREFIX` | `db-manager` | URL prefix |
| `ui` | `DB_MANAGER_UI` | `auto` | `auto`, `tailwind`, `bootstrap5`, `bootstrap6` (`bootstrap` = `bootstrap5`) |
| `assets` | `DB_MANAGER_ASSETS` | `project` | `project` (host Vite) or `cdn` |
| `vite_entries` | — | `resources/css/app.css` | Vite entry paths (existing files only) |
| `tailwind_cdn_url` | `DB_MANAGER_TAILWIND_CDN` | — | Tailwind CDN (required when `assets=cdn` + Tailwind theme) |
| `bootstrap_cdn_urls` | `DB_MANAGER_BOOTSTRAP5_CDN`, `DB_MANAGER_BOOTSTRAP6_CDN` | — | Bootstrap CDN when `assets=cdn` |
| `connection` | `DB_MANAGER_CONNECTION` | `null` | DB connection name (default connection if null) |

See [`config/db-manager.php`](config/db-manager.php) for `excluded_tables`, `read_only_tables`, and `hidden_columns`.

## UI theme

| Value | Behavior |
|-------|----------|
| `auto` | Tailwind if the host uses Tailwind (`tailwind.config.js`, Vite, or `package.json`); else Bootstrap **6** when `bootstrap` in `package.json` is `^6` / `>=6` / `6.x`, else **Bootstrap 5** |
| `tailwind` | Tailwind markup classes |
| `bootstrap5` | Bootstrap 5 markup classes |
| `bootstrap6` | Bootstrap 6 markup classes |
| `bootstrap` | Alias for `bootstrap5` |

## Assets (default: project Vite)

By default (`assets=project`), the layout loads your app’s Vite CSS via `@vite` using `vite_entries` (default: `resources/css/app.css` if the file exists). Run `npm run dev` or `npm run build` so styles match the rest of your Laravel app.

**CDN opt-in** — set `DB_MANAGER_ASSETS=cdn` and provide URLs:

```env
DB_MANAGER_ASSETS=cdn
DB_MANAGER_TAILWIND_CDN=https://cdn.tailwindcss.com
# or for Bootstrap themes:
DB_MANAGER_BOOTSTRAP5_CDN=https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
DB_MANAGER_BOOTSTRAP6_CDN=https://cdn.jsdelivr.net/npm/bootstrap@6.0.0/dist/css/bootstrap.min.css
```

Publish config to add JS entries to `vite_entries` if your app requires them for styles.

## Security

This UI performs **direct table CRUD** on your database. Do not enable it in production without authentication and authorization (add middleware in config). Prefer keeping `DB_MANAGER_ENABLED=false` in production and relying on `APP_ENV=local` only on developer machines.

## Limitations (v1)

- Single-column primary keys only (composite PKs show an error)
- Default database connection only (override via `connection` config)
- No foreign-key pickers or multi-database switcher

## License

MIT
