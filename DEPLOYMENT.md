# Mithooos — Deployment Guide

## Vercel Deployment

The project uses the community PHP runtime `vercel-php@0.7.4`. PHP entry points
are under `api/`; browser-served files are under `public/`.

1. Import the repository into Vercel and use the project root as the root directory.
2. Add the environment variables listed in `.env.example` in **Project Settings → Environment Variables**.
   `TURSO_DATABASE_URL` must point to a remote Turso database. Do not use localhost.
3. Set `APP_ENV=production`, `APP_URL` to the deployed HTTPS origin, and provide a
   randomly generated `JWT_SECRET` of at least 32 characters.
4. Add the SMTP credentials if the email features are needed.
5. Deploy. The storefront is served at `/`; clean page paths such as `/about` and
   `/shop` are routed to PHP, while static files are served from `public/`.

Vercel function filesystems are not persistent. Product/payment uploads currently
use database-stored data URLs for new image data; any old `/uploads/...` references
need durable object storage (for example, Vercel Blob or S3-compatible storage)
before those existing files can be used reliably in production. Do not put payment
proofs in `public/`.

The app does not call PHP `session_start()` or use `$_SESSION`. Guest identity is a
client-generated ID held in localStorage/session headers, with cart data stored in
the remote database.

## Quick Start (Local Development)

### Requirements
- PHP 8.3+
- PHP Extensions: `curl`, `mbstring` (PDO is no longer strictly required for Turso HTTP API)
- Turso (libSQL) Database Account

### Steps

1. **Clone / Extract project**
```bash
unzip mithooos.zip -d /var/www/mithooos
```

2. **Configure environment & Turso**
```bash
cp .env.example .env
# Edit .env and set:
# TURSO_DATABASE_URL=libsql://your-db-name.turso.io
# TURSO_AUTH_TOKEN=your-auth-token-here
```

3. **Set up database schema**
Run the migration script to apply the SQLite schema to your Turso DB:
```bash
php api/backend/migrate.php
# DO NOT RUN development.sql in production.
```

4. **Run the local PHP server**
```bash
php -S localhost:8000 -t public api/router.php
```
Visit http://localhost:8000/; the admin panel is at `/admin-panel/`.

## Default Admin Credentials
- URL: /admin-panel/
- Email: admin@mithooos.com
- Password: **Change immediately after first login!**

## Project Structure
```
mithooos/
├── api/                    ← PHP function and included PHP source
│   ├── index.php           ← Vercel/local request dispatcher
│   ├── home.php            ← Storefront home
│   ├── pages/              ← Storefront PHP pages
│   ├── includes/           ← Shared storefront includes
│   ├── backend/            ← API, admin, config, and migrations
│   └── errors/             ← Shared error pages
├── public/                 ← Files served statically by Vercel
│   ├── admin-panel/        ← Admin HTML, CSS, and JavaScript
│   ├── css/
│   ├── images/
│   └── js/
├── uploads/                ← Local-only legacy uploads (not deployed)
├── .env.example            ← Environment variable template
└── vercel.json             ← PHP runtime and URL rewrites
```

## API Endpoints
| Method | Endpoint | Auth |
|--------|----------|------|
| POST | /backend/api/auth/register | — |
| POST | /backend/api/auth/login | — |
| GET  | /backend/api/auth/me | Bearer |
| GET  | /backend/api/products | — |
| POST | /backend/api/products | Admin |
| GET  | /backend/api/cart | Session/Bearer |
| POST | /backend/api/cart | Session/Bearer |
| POST | /backend/api/orders | Bearer |
| GET  | /backend/api/orders | Bearer |
| POST | /backend/api/reviews | Bearer |
| POST | /backend/api/wishlist | Bearer |
| GET  | /backend/api/search?q= | — |
| POST | /backend/api/newsletter | — |
