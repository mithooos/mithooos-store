# MITHOS PHASE 0: BASELINE REPORT

## A. Project Structure
The project is structured with a vanilla PHP backend and static HTML/JS frontend.
- `index.html` - Storefront entry
- `pages/` - Static HTML views (`about.html`, `account.html`, `shop.html`, `cart.html`, `checkout.html`, `login.html`, `product-detail.html`, etc.)
- `js/` - Vanilla JavaScript modules
- `css/` - Styling
- `images/` - Static images
- `uploads/` - User/Admin uploads
- `admin-panel/` - Admin interface
- `backend/` - PHP backend logic
  - `api/` - Resource-specific PHP scripts (`auth.php`, `products.php`, `cart.php`, `orders.php`, etc.)
  - `config/` - `schema.sql`, `constants.php`, `database.php`
  - `includes/` - Business logic classes (`Auth.php`, `Cart.php`, `Order.php`, etc.)
  - `admin/` - Admin API router
  - `index.php` - Main backend API router

## B. Environment
- **PHP**: Not detected globally in current shell, likely running via Docker, XAMPP, or specialized local server. 
- **Database**: PostgreSQL (v14+ target as per `schema.sql`).
- **Entry Points**: 
  - Frontend: `/index.html`
  - Backend API: `/backend/index.php`
- **Dependencies**: Native PHP standard libraries. No Composer/Node packages found at root.

## C. Database
**Type**: PostgreSQL
**Schema**: Documented in `backend/config/schema.sql`
**Tables Found**: 
- `users`, `addresses`, `wishlists`, `carts`, `newsletter_subscribers`, `contact_messages`, `blog_posts`
- `categories`, `products`, `product_images`, `product_reviews`, `product_attributes`
- `orders`, `order_items`, `payments`, `coupons`
- `settings`, `audit_log`
**Migration Status**: Found `migration-phase5.sql` suggesting past migrations. Seed data exists for admin and base products.

## D. API
**Router**: `backend/index.php`
- `GET /api/csrf-token`
- `/api/auth` (POST register, login, etc.)
- `/api/products` 
- `/api/categories`
- `/api/cart`
- `/api/orders`
- `/api/addresses`
- `/api/reviews`
- `/api/wishlist`
- `/api/search`
- `/api/newsletter`
- `/api/contact`
- `/admin/*` (Admin routes)

## E. Customer Workflow
1. Browse (`index.html`, `pages/shop.html`, `pages/product-detail.html`)
2. Auth (`pages/login.html`)
3. Cart (`pages/cart.html`)
4. Checkout (`pages/checkout.html`)
5. Order Status & History (`pages/order-tracking.html`, `pages/account.html`)

## F. Admin Workflow
Routed via `admin-panel/` handling Dashboard, Products, Categories, Orders, Customers, Coupons, Analytics.
Admin API bypasses standard CSRF token checks and relies on JWT authorization exclusively.

## G. Security Baseline
- **CSRF**: Enforced on `/api/*` state-changing methods, but intentionally bypassed for `/admin/*` routes.
- **Auth Tokens**: JWTs likely stored in `localStorage` in JS client.
- **Passwords**: Hashed with Argon2id.
- **Error Handling**: Custom global handler returns raw `RuntimeException` messages to client but shields internal SQL errors in production.

## H. Application Health
| Area     | Test              | Result    | Notes |
| -------- | ----------------- | --------- | ----- |
| Homepage | Load              | UNTESTED  | Read-only check requires browser setup |
| Products | Listing           | UNTESTED  | Read-only check requires browser setup |
| Product  | Detail            | UNTESTED  | Read-only check requires browser setup |
| Search   | Search            | UNTESTED  | Read-only check requires browser setup |
| Cart     | Add/update/remove | UNTESTED  | Read-only check requires browser setup |
| Auth     | Login/register    | UNTESTED  | Read-only check requires browser setup |
| Checkout | Validation        | UNTESTED  | Read-only check requires browser setup |
| Orders   | Creation/read     | UNTESTED  | Read-only check requires browser setup |
| Admin    | Login             | UNTESTED  | Read-only check requires browser setup |
| Admin    | Product CRUD      | UNTESTED  | Read-only check requires browser setup |
| Admin    | Orders            | UNTESTED  | Read-only check requires browser setup |
| Database | Connection/schema | UNTESTED  | Needs credentials |
| API      | Core endpoints    | UNTESTED  | Needs active server |

## I. Backup Status
- **Git**: Not initialized (`fatal: not a git repository`).
- **Recommendation**: Initialize a Git repository `git init`, `git add .`, `git commit -m "Phase 0 Baseline"` before starting Phase 1. 
- **Database Backup**: Recommend `pg_dump` of the PostgreSQL database before proceeding.

## J. Known Issues
- `admin` API routes currently bypass CSRF protection.
- No source control versioning active.

## K. Baseline Timestamp
Created at: 2026-09-17T02:10:24+05:00
