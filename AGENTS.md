# AGENTS.md

## Project purpose

This repository is a small PHP facility management dashboard for tracking item loan requests and reservation-like workflow data. The app uses PHP for server-rendered pages, MariaDB for persistence, Nginx for serving the app, and Tailwind CSS for styling.

## Key directories

- `public/` — web-facing PHP pages and static assets
  - `public/dashboard/` — dashboard and item-loan pages
  - `public/config/db.php` — database connection logic
  - `public/assets/` — frontend JavaScript
- `src/` — Tailwind source stylesheet
- `db-schema/` — SQL files used to initialize MariaDB
- `coverage/` — runtime environment settings, including `.env`
- `Dockerfile` and `docker-compose.yml` — local runtime configuration
- `nginx.conf` — server routing for PHP requests

## Development workflow

Use the project as a Dockerized PHP app:

```bash
docker compose up --build
```

The app is served at:

```text
http://localhost:8180
```

If you need to rebuild CSS:

```bash
pnpm install
pnpm run build
```

For live CSS watching:

```bash
pnpm run dev
```

## Architecture expectations

- Keep application logic in PHP files under `public/`.
- Keep database access logic in `public/config/db.php`.
- Keep UI logic and modal interactions in `public/assets/dashboard.js`.
- Update styles in `src/input.css`; do not hand-edit generated CSS in `public/css/` unless the build process intentionally requires it.
- Match the existing database schema names and conventions when adding or editing queries.

## Database conventions

The database schema is initialized from files in `db-schema/`. The current schema includes:

- `users`
- `items`
- `lists`

The common request status values are:

- `pending`
- `approved`
- `rejected`

When adding features, prefer the existing table and column naming patterns already present in `db-schema/schema.sql`.

## Rules for edits

- Prefer minimal, project-consistent changes.
- Do not assume a framework or build system that is not present in this repo.
- Keep the app working as a simple PHP + MariaDB + Nginx application.
- If changing the database schema, update the SQL files in `db-schema/` accordingly.
- If changing the UI, keep it consistent with the existing Tailwind-based styling and dashboard layout.

## Notes

This project is intentionally small and direct. It is not a React, Next.js, or Node service-based application. The main operational pattern is server-rendered PHP pages with Docker-managed infrastructure and a MariaDB-backed data layer.
