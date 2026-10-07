# Mithooos: Sindh Ki Khushboo

Mithooos is a professional e-commerce platform dedicated to traditional Sindhi and heritage products. It features a lightweight, high-performance architecture built on vanilla PHP and native web technologies.

## Architecture

- **Frontend:** Vanilla HTML, CSS, and JavaScript. No build steps, no heavy frameworks.
- **Backend API:** Vanilla PHP 8.1+ REST API.
- **Database:** PostgreSQL 14+.

## Prerequisites

- PHP 8.1 or higher
- PostgreSQL 14 or higher
- (Optional) Apache/Nginx for production deployment

## Local Setup

### 1. Database Initialization
1. Create a PostgreSQL database named `mithooos`:
   ```bash
   createdb -U postgres mithooos
   ```
2. Import the schema and seed data:
   ```bash
   php backend/migrate.php
   psql -U postgres -d mithooos -f backend/config/seeds/development.sql
   php backend/scripts/create_admin.php
   ```
   *Note: Production initialization should only run `migrate.php` and `create_admin.php`, NOT the seeds file.*

### 2. Environment Configuration
Copy the sample environment file and adjust as needed:
```bash
cp .env.example .env
```
Ensure the database credentials match your local PostgreSQL setup.

### 3. Running the Server
You can use PHP's built-in server for rapid local development. From the project root, run:
```bash
php -S localhost:8000 router.php
```
The router is required so missing URLs are rendered with the site's custom 404 page. Then visit `http://localhost:8000/` in your browser.

## Production Deployment

When deploying to production:
1. Ensure `.env` contains `APP_ENV=production`.
2. Generate a secure, 32+ character random string for `JWT_SECRET`.
3. Provide secure, unique database credentials via `.env`.
4. Ensure the web server (Apache/Nginx) points its root to the repository directory. The included `.htaccess` file will automatically handle static asset caching.
5. Secure the `uploads/` directory to prevent script execution.

## Admin Panel
Access the administrative dashboard via `/admin-panel/login.html`. Use the seeded admin credentials to get started.
