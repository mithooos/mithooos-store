# Mithooos — Deployment Guide

## Quick Start (Local Development)

### Requirements
- PHP 8.1+
- PHP Extensions: `curl`, `mbstring` (PDO is no longer strictly required for Turso HTTP API)
- Turso (libSQL) Database Account
- Apache/Nginx with mod_rewrite

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
php backend/migrate.php
php backend/scripts/create_admin.php
# DO NOT RUN development.sql in production.
```

4. **Set permissions**
```bash
chmod 755 /var/www/mithooos
chmod -R 644 /var/www/mithooos/css /var/www/mithooos/js
mkdir -p /var/www/mithooos/uploads
chmod 755 /var/www/mithooos/uploads
```

5. **Apache Virtual Host**
```apache
<VirtualHost *:80>
    ServerName mithooos.local
    DocumentRoot /var/www/mithooos
    <Directory /var/www/mithooos>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

6. **Visit**: http://mithooos.local
7. **Admin Panel**: http://mithooos.local/admin-panel/

## Production Deployment (DigitalOcean)

```bash
# 1. Create $12/mo Droplet (Ubuntu 24.04)
# 2. Install stack
sudo apt update && sudo apt install -y apache2 php8.1 php8.1-curl \
    php8.1-mbstring php8.1-zip certbot python3-certbot-apache

# 3. Enable modules
sudo a2enmod rewrite ssl headers deflate expires

# 4. Deploy via git
cd /var/www && git clone https://github.com/yourrepo/mithooos.git

# 5. SSL
sudo certbot --apache -d yourdomain.com

# 6. Set up cron for backups
echo "0 2 * * * /usr/local/bin/pp-backup.sh" | crontab -
```

## Default Admin Credentials
- URL: /admin-panel/
- Email: admin@mithooos.com
- Password: **Change immediately after first login!**

## Project Structure
```
mithooos/
├── index.html              ← Homepage
├── pages/                  ← All frontend pages
│   ├── shop.html
│   ├── product-detail.html
│   ├── cart.html
│   ├── checkout.html
│   ├── account.html
│   ├── login.html
│   ├── about.html
│   ├── contact.html
│   └── blog.html
├── css/                    ← Stylesheets
│   ├── variables.css       ← Design tokens
│   ├── main.css            ← Global styles
│   ├── components.css      ← UI components
│   └── pages.css           ← Page-specific
├── js/                     ← JavaScript modules
│   ├── api-client.js       ← API communication
│   ├── cart.js             ← Cart logic
│   ├── auth.js             ← Authentication
│   ├── search.js           ← Autocomplete search
│   ├── utils.js            ← Utilities & helpers
│   └── main.js             ← Global init
├── images/                 ← Static assets
│   └── logo.svg
├── backend/                ← PHP backend
│   ├── index.php           ← API router
│   ├── api/                ← REST endpoints
│   ├── admin/              ← Admin API
│   ├── config/             ← DB config, schema
│   └── includes/           ← PHP classes
└── admin-panel/            ← Admin UI
    ├── index.html          ← Dashboard
    ├── pages/              ← Admin pages
    ├── css/admin.css
    └── js/admin.js
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
