# Pelakom FM

Pelakom FM is a small facility management dashboard built as a PHP application served through Nginx and backed by MariaDB. The project currently focuses on a user dashboard and an item-loan request flow with tracking for request status.

## Overview

This application includes:

- A dashboard landing page at `/dashboard/`
- An item loan management page at `/dashboard/items.php`
- User requests stored in a MariaDB database
- Request statuses: `pending`, `approved`, and `rejected`
- Docker-based local setup for PHP, Nginx, and MariaDB
- Tailwind CSS styling compiled from the project source

## Stack

- PHP 8.4 FPM
- Nginx
- MariaDB
- Tailwind CSS 4
- pnpm
- Docker Compose

## Project structure

- `public/` — served application files and PHP pages
  - `public/dashboard/` — dashboard pages
  - `public/config/db.php` — PDO database connection setup
  - `public/assets/` — frontend JS and static assets
  - `public/css/` — generated stylesheet output
- `src/` — source styling input for Tailwind
- `db-schema/` — SQL initialization files for the database
- `coverage/` — environment config area for local runtime settings
- `Dockerfile` — multi-stage container build for app, PHP, and Nginx
- `docker-compose.yml` — local runtime orchestration for PHP, MariaDB, and Nginx
- `nginx.conf` — Nginx config that routes PHP requests to the PHP-FPM container
- `package.json` — build and dev scripts for Tailwind

## Database model

The schema in `db-schema/schema.sql` defines these tables:

- `users`
- `items`
- `lists`

The `lists` table stores loan requests and includes:

- `item_id`
- `user_id`
- `total_amount`
- `purpose`
- `status`
- `created_at`
- `duration`

## Local setup

### 1. Install JavaScript dependencies

```bash
pnpm install
```

### 2. Prepare the environment file

The PHP app reads environment values from `coverage/.env`. Use the example file as a starting point if needed:

```bash
cp coverage/.env.example coverage/.env
```

Then verify the values match your local setup, especially:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

### 3. Start the project with Docker Compose

```bash
docker compose up --build
```

This starts:

- PHP app container
- MariaDB container
- Nginx container on port `8180`

Open the app at:

```text
http://localhost:8180
```

## Development and styling

The front-end styles are generated from `src/input.css` using Tailwind.

Build the stylesheet:

```bash
pnpm run build
```

Watch for style changes while developing:

```bash
pnpm run dev
```

## Important implementation notes

- The app is a server-rendered PHP project, not a modern JS framework app.
- The Docker build copies the `public/` directory into the PHP and Nginx containers.
- `nginx.conf` routes all PHP files through the `php` container on port `9000`.
- Database initialization scripts under `db-schema/` are mounted into MariaDB’s startup directory, so schema and seed scripts are loaded automatically when the database container starts.
- The project does not currently define a working test suite in `package.json`.

## Common entry points

- Dashboard: `public/dashboard/index.php`
- Item loan page: `public/dashboard/items.php`
- Database connection: `public/config/db.php`
- Frontend scripts: `public/assets/dashboard.js`
- Tailwind source: `src/input.css`

## Notes

This repository is a small, focused internal app for managing facility requests and loan records. It is designed for local Docker-based development and simple PHP page rendering rather than a distributed or framework-heavy architecture.
