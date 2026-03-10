# Authopic Technologies PLC — Company Website

Full-stack dynamic website for **Authopic Technologies PLC**, a modern technology company based in Addis Ababa, Ethiopia. Built with procedural PHP, MySQL, Tailwind CSS, and vanilla JavaScript.

---

## Features

- **Public Website** — Home, Products, Services, Portfolio, About, Contact, Blog/Insights, Request Demo, Search, Privacy
- **Admin Dashboard** — Full CMS with CRUD for all content types, leads/demo management, media library, analytics, site settings
- **PWA** — Installable progressive web app with offline support and service worker caching
- **Bilingual** — English and Amharic (አማርኛ) language toggle
- **Dark / Light Mode** — Theme toggle with localStorage persistence
- **Modern UI** — Glass-morphism, gradient orbs, scroll animations, responsive design via Tailwind CSS CDN
- **Security** — CSRF tokens, rate limiting, honeypot spam detection, password hashing, session management

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 7.4+ (procedural, no framework) |
| Database | MySQL 5.7+ / MariaDB (mysqli) |
| Frontend | Tailwind CSS (CDN), Vanilla JS |
| Fonts | Plus Jakarta Sans, Noto Sans Ethiopic |
| Server | Apache with mod_rewrite |

---

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite` enabled
- PHP extensions: `mysqli`, `mbstring`, `json`, `fileinfo`

---

## Installation

### 1. Clone / Upload Files

Upload all files to your web server's document root or a subdirectory.

### 2. Create Database

```sql
CREATE DATABASE authopic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Import Schema & Seed Data

```bash
mysql -u root -p authopic_db < database/schema.sql
mysql -u root -p authopic_db < database/seed.sql
```

### 4. Configure Database Connection

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'authopic_db');
define('SITE_URL', 'https://authopic.com');
```

### 5. Set Permissions

```bash
chmod 755 uploads/
chmod 644 .htaccess
```

### 6. Access the Site

- **Website**: `https://yourdomain.com`
- **Admin Panel**: `https://yourdomain.com/admin`

---

## Default Admin Credentials

| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `Admin@2026!` |

**Change the password immediately after first login.**

---

## File Structure

```
├── .htaccess                  # URL rewriting & security headers
├── index.php                  # Front controller / router
├── manifest.json              # PWA manifest
├── sw.js                      # Service worker
├── config/
│   └── database.php           # Database & site configuration
├── database/
│   ├── schema.sql             # All table definitions
│   └── seed.sql               # Default data & admin user
├── includes/
│   ├── functions.php          # 50+ helper functions
│   ├── header.php             # Public header template
│   └── footer.php             # Public footer template
├── pages/
│   ├── home.php               # Homepage
│   ├── about.php              # About page
│   ├── contact.php            # Contact form
│   ├── portfolio.php          # Portfolio listing
│   ├── portfolio-single.php   # Portfolio detail
│   ├── product-single.php     # Product detail
│   ├── service-single.php     # Service detail
│   ├── insights.php           # Blog listing
│   ├── blog-single.php        # Blog detail
│   ├── request-demo.php       # Demo request form
│   ├── search.php             # Search results
│   ├── privacy.php            # Privacy policy
│   ├── thank-you.php          # Thank you pages
│   ├── 404.php                # 404 error page
│   └── offline.php            # PWA offline fallback
├── admin/
│   ├── login.php              # Admin login
│   ├── logout.php             # Admin logout
│   ├── router.php             # Admin route handler
│   ├── dashboard.php          # Dashboard overview
│   ├── pages.php              # Pages CRUD
│   ├── products.php           # Products CRUD
│   ├── services.php           # Services CRUD
│   ├── portfolio.php          # Portfolio CRUD
│   ├── blog.php               # Blog posts CRUD
│   ├── team.php               # Team members CRUD
│   ├── testimonials.php       # Testimonials CRUD
│   ├── leads.php              # Leads management
│   ├── demos.php              # Demo requests management
│   ├── subscribers.php        # Newsletter subscribers
│   ├── media.php              # Media library
│   ├── settings.php           # Site settings
│   ├── profile.php            # Admin profile
│   ├── analytics.php          # Analytics dashboard
│   └── includes/
│       ├── header.php         # Admin layout header
│       ├── sidebar.php        # Admin sidebar nav
│       └── footer.php         # Admin layout footer
├── api/
│   └── router.php             # AJAX API endpoints
├── assets/
│   ├── css/
│   │   └── style.css          # Custom styles
│   └── js/
│       └── app.js             # Client-side JavaScript
└── uploads/                   # User uploads (auto-created)
    └── .htaccess              # Blocks PHP execution
```

---

## Deployment on Yegara Hosting (Ethiopia)

1. Log in to cPanel
2. Create MySQL database via **MySQL Databases**
3. Import `schema.sql` then `seed.sql` via **phpMyAdmin**
4. Upload files via **File Manager** to `public_html/`
5. Update `config/database.php` with your cPanel DB credentials
6. Update `SITE_URL` to your domain
7. Ensure `.htaccess` is present (may need to show hidden files)

---

## API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| POST | `/api/newsletter` | Subscribe to newsletter |
| POST | `/api/contact` | Submit contact form (AJAX) |
| POST | `/api/demo` | Submit demo request (AJAX) |
| GET | `/api/search?q=term` | Search content (JSON) |

---

## Admin Modules

| Module | Features |
|--------|----------|
| Dashboard | Stats, recent activity, quick links |
| Pages | CRUD with revisions, bilingual content |
| Products | CRUD with JSON features/pricing/FAQ |
| Services | CRUD with offerings/technologies/process |
| Portfolio | CRUD with challenges/solutions/gallery |
| Blog | CRUD with categories, SEO fields, toolbar |
| Team | CRUD with photo upload, social links |
| Testimonials | CRUD with ratings, featured flag |
| Leads | View, status management, follow-ups |
| Demos | View, status management |
| Subscribers | List, search, CSV export |
| Media | Upload, grid view, delete |
| Settings | Company info, social links, SEO, integrations |
| Profile | Update info, change password |
| Analytics | Page views, devices, browsers, referrers |

---

## Security Features

- CSRF token protection on all forms
- Rate limiting on login, contact, demo, newsletter
- Honeypot fields for spam detection
- Password hashing with `password_hash()` / `password_verify()`
- Session timeout (30 min) with regeneration
- SQL injection prevention via `mysqli_real_escape_string()`
- XSS prevention via `htmlspecialchars()` escaping
- PHP execution blocked in uploads directory
- Admin routes protected with authentication check
- Security headers via `.htaccess` (X-Frame-Options, X-Content-Type-Options, etc.)

---

## License

Proprietary — Authopic Technologies PLC. All rights reserved.
