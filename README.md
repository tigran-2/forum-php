# PHP Forum (Topics + Comments) — Simple Educational Project

## Features
- User registration and login (sessions)
- Registration form validation:
  - Email is required and must be a valid format
  - First/last name: letters only (spaces and hyphens allowed)
  - Date of birth: must be 18+
  - Phone: `+374 00 000 000` format
  - Password and confirmation must match
- Creating topics and posting comments is allowed only for authenticated users
- Reading topics is allowed for everyone

## Requirements
- PHP 8.0+
- MySQL 8+ (or MariaDB)
- PDO MySQL extension enabled

## Setup

### Quick start with Docker Compose
1) Copy `.env` and adjust if needed:
   ```bash
   cp .env .env.local && nano .env.local
   ```
2) Start the stack (PHP-FPM + Nginx + MySQL 8.3):
   ```bash
   docker compose --env-file .env.local up --build
   ```
3) Open http://localhost:8080 (Nginx serves `public/`).

Notes:
- `database/schema.sql` is auto-loaded on first MySQL start.
- Source is bind-mounted; edit locally and refresh.

### Manual (no Docker)
1) Create a database, e.g. `forum_db`.
2) Import `database/schema.sql` in MySQL.
3) Configure DB credentials in `config/config.php`.
4) Start a local server from `public`:
   ```bash
   cd public
   php -S localhost:8000
   ```
   Open: http://localhost:8000

## Structure
- `public/` — entry point and pages
- `app/` — DB, auth, helpers
- `partials/` — header/footer
- `database/schema.sql` — tables

## Minimal security notes
- Passwords are stored using `password_hash()`
- Queries use prepared PDO statements
- A CSRF token is included for POST forms
