# Mithooos: Sindh Ki Khushboo

Mithooos is a professional e-commerce platform dedicated to traditional Sindhi and heritage products. It features a lightweight, high-performance architecture built on vanilla PHP and native web technologies.

## Architecture

- **Frontend:** Vanilla HTML, CSS, and JavaScript. No build steps, no heavy frameworks.
- **Backend API:** Vanilla PHP REST API, deployed on Vercel with `vercel-php@0.7.4`.
- **Database:** Remote Turso/libSQL, configured with environment variables.

## Prerequisites

- PHP 8.3 or higher
- A remote Turso/libSQL database and its URL/token

## Local Setup

### 1. Environment Configuration
Copy `.env.example` to `.env` and set the remote Turso credentials and other local
settings:
```bash
cp .env.example .env
```

### 2. Database Initialization
Apply the schema:
   ```bash
   php api/backend/migrate.php
   ```
Do not run the development seed file against production data. Set a strong
`JWT_SECRET` and configure `APP_ENV` before running the app.

### 3. Running the Server
You can use PHP's built-in server for rapid local development. From the project root, run:
```bash
php -S localhost:8000 -t public api/router.php
```
Visit `http://localhost:8000/`; the router serves clean PHP page routes and the custom 404 page.

## Production Deployment

When deploying to production:
1. Set `APP_ENV=production` in Vercel Project Settings → Environment Variables.
2. Generate a secure, 32+ character random string for `JWT_SECRET`.
3. Provide `TURSO_DATABASE_URL` and `TURSO_AUTH_TOKEN` for the remote database.
4. Configure the remaining Vercel variables from `.env.example`.
5. Move legacy `/uploads/...` objects to durable object storage before using them in production.

## Admin Panel
Access the administrative dashboard via `/admin-panel/login.html`. Use the seeded admin credentials to get started.
