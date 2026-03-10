# Authopic Technologies PLC — Full Project Documentation

> **Document Version:** 1.0  
> **Created:** February 27, 2026  
> **Project Name:** Authopic Technologies PLC — Modern Digital Solutions  
> **Project Type:** Company Website + Content Management System (CMS)  
> **Client:** Authopic Technologies PLC (Software Solutions Company — Addis Ababa, Ethiopia)  
> **Status:** Active Development

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Project Goals & Objectives](#2-project-goals--objectives)
3. [Technology Stack](#3-technology-stack)
4. [System Architecture](#4-system-architecture)
5. [File & Directory Structure](#5-file--directory-structure)
6. [Database Design](#6-database-design)
7. [Feature Specifications](#7-feature-specifications)
8. [Public Website Pages](#8-public-website-pages)
9. [Admin Panel (CMS)](#9-admin-panel-cms)
10. [API Endpoints](#10-api-endpoints)
11. [Authentication & Authorization](#11-authentication--authorization)
12. [Security Specifications](#12-security-specifications)
13. [Progressive Web App (PWA)](#13-progressive-web-app-pwa)
14. [Internationalization (i18n)](#14-internationalization-i18n)
15. [Frontend Specifications](#15-frontend-specifications)
16. [SEO & Analytics](#16-seo--analytics)
17. [Email & Notifications](#17-email--notifications)
18. [File Upload System](#18-file-upload-system)
19. [Development Checklist](#19-development-checklist)
20. [Deployment Guide](#20-deployment-guide)
21. [Environment Configuration](#21-environment-configuration)
22. [Testing Plan](#22-testing-plan)
23. [Risk Assessment](#23-risk-assessment)
24. [Glossary](#24-glossary)

---

## 1. Project Overview

### 1.1 Description

Authopic Technologies PLC is a full-stack company website and content management system for a modern technology company based in Addis Ababa, Ethiopia. The platform serves as both a public-facing marketing website and an internal CMS for managing content, leads, and business operations.

### 1.2 Key Highlights

| Aspect | Detail |
|---|---|
| **Type** | Company Website + CMS + CRM (Lite) |
| **Architecture** | Monolithic PHP application, single entry point |
| **Languages Supported** | English & Amharic (አማርኛ) |
| **Target Audience** | Ethiopian businesses, schools, enterprises |
| **Monetization** | Software products (School Management System, Enterprise ERP) + Services |
| **Currency** | Ethiopian Birr (ETB) |
| **Timezone** | East Africa Time (EAT, UTC+03:00) |

### 1.3 Core Capabilities

- **Marketing Website** — Product showcase, service listings, portfolio, blog, team page
- **Lead Management** — Contact forms, demo requests, lead scoring, follow-up tracking
- **Content Management** — Pages, blog posts, media library with full CRUD
- **Newsletter System** — Subscriber management with CSV export
- **Analytics Dashboard** — Page views, device stats, traffic metrics
- **PWA Support** — Offline capability, installable, push notification ready
- **Bilingual Content** — Every piece of content supports English and Amharic

---

## 2. Project Goals & Objectives

### 2.1 Business Goals

1. Establish a professional online presence for Authopic Technologies PLC
2. Showcase software products (SMS & ERP) with pricing and demo request flow
3. Generate qualified leads through contact forms and demo scheduling
4. Publish technical blog content to drive organic traffic
5. Display portfolio of completed projects as social proof
6. Enable newsletter subscription for marketing outreach

### 2.2 Technical Goals

1. Build a fast, SEO-optimized, mobile-first website
2. Implement a custom CMS that non-technical admins can operate
3. Support bilingual content (English + Amharic) throughout
4. Make the site installable as a PWA with offline capability
5. Implement robust security (CSRF, rate limiting, input validation)
6. Track analytics without third-party dependencies

### 2.3 Success Metrics

| Metric | Target |
|---|---|
| Page Load Time | < 2 seconds (first contentful paint) |
| Lighthouse Score | > 90 (Performance, Accessibility, SEO) |
| Mobile Responsiveness | 100% functional on 320px+ screens |
| Offline Support | Core pages accessible without network |
| Form Conversion | Contact & demo forms functional with <3 fields required |
| Admin Usability | All CRUD operations completable via admin panel |

---

## 3. Technology Stack

### 3.1 Backend

| Component | Technology | Version |
|---|---|---|
| Language | PHP | 8.0+ (Procedural) |
| Database | MySQL / MariaDB | 8.0+ / 10.6+ |
| Character Set | UTF-8 (`utf8mb4`) | — |
| Server | Apache | 2.4+ (with `mod_rewrite`) |
| Session Management | PHP Native Sessions | — |
| Password Hashing | `password_hash()` / `password_verify()` | `PASSWORD_DEFAULT` (bcrypt) |

### 3.2 Frontend

| Component | Technology | Version |
|---|---|---|
| CSS Framework | Tailwind CSS | v4.2 |
| JavaScript | Vanilla JS (ES6+) | — |
| Fonts | Plus Jakarta Sans + Noto Sans Ethiopic | Google Fonts |
| Icons | Inline SVG | — |
| Build Tool | Tailwind CLI | v4.2 |

### 3.3 Infrastructure

| Component | Technology |
|---|---|
| Web Server | Apache with `.htaccess` URL rewriting |
| PWA | Service Worker + Web App Manifest |
| Package Manager | npm (Node.js) |
| Version Control | Git |

### 3.4 No External Dependencies

This project intentionally avoids:
- No PHP frameworks (Laravel, Symfony, etc.)
- No JavaScript frameworks (React, Vue, etc.)
- No ORM (raw SQL queries via `mysqli`)
- No Composer dependencies
- No third-party analytics (custom-built)

---

## 4. System Architecture

### 4.1 Architecture Pattern

```
Single Entry Point (Front Controller)
┌──────────────────────────────────────────────────────┐
│                     index.php                        │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────┐  │
│  │  Public Site │  │  Admin Panel │  │  REST API   │  │
│  │  /pages/*    │  │  /admin/*    │  │  /api/*     │  │
│  └──────┬──────┘  └──────┬───────┘  └──────┬─────┘  │
│         │                │                  │        │
│  ┌──────┴────────────────┴──────────────────┴─────┐  │
│  │           includes/functions.php               │  │
│  │         (Core Helper Library — 780+ lines)     │  │
│  └──────────────────┬────────────────────────────┘   │
│                     │                                │
│  ┌──────────────────┴────────────────────────────┐   │
│  │           config/database.php                 │   │
│  │          (DB Connection + Site Config)         │   │
│  └──────────────────┬────────────────────────────┘   │
│                     │                                │
│              ┌──────┴──────┐                         │
│              │   MySQL DB  │                         │
│              └─────────────┘                         │
└──────────────────────────────────────────────────────┘
```

### 4.2 Request Flow

```
Browser Request
    ↓
Apache (.htaccess rewrite)
    ↓
index.php (Front Controller)
    ↓
├── Load config/database.php (DB connection + constants)
├── Load includes/functions.php (helper library)
├── Initialize session (secure settings)
├── Detect language (cookie/GET/session → EN or AM)
├── Parse URL into segments
├── Route matching:
│   ├── /admin/*  →  admin/router.php  →  admin/{page}.php
│   ├── /api/*    →  api/router.php    →  JSON response
│   └── /*        →  pages/{page}.php  →  HTML response
├── Track analytics (public pages only)
└── Close DB connection
```

### 4.3 Layer Responsibilities

| Layer | Responsibility |
|---|---|
| **index.php** | Routing, session init, language detection, analytics |
| **config/database.php** | Database connection, site constants, configuration |
| **includes/functions.php** | All shared logic: DB helpers, security, auth, content, uploads, email |
| **includes/header.php** | Public site HTML head, navbar, language toggle, theme toggle |
| **includes/footer.php** | Public site footer, newsletter, WhatsApp button, PWA install, JS |
| **pages/*.php** | Public page templates — each self-contained with queries + HTML |
| **admin/router.php** | Admin routing + auth gating + layout wrapping |
| **admin/*.php** | Admin panel page logic — CRUD operations per module |
| **admin/includes/*.php** | Admin layout: header (top bar), sidebar (nav), footer (bottom nav) |
| **api/router.php** | AJAX JSON endpoints for forms and search |
| **assets/** | Static files — compiled CSS, JavaScript, images |

---

## 5. File & Directory Structure

```
AUTHOPIC_TECHNOLOGIES/
│
├── index.php                    # Main entry point / front controller / router
├── manifest.json                # PWA Web App Manifest
├── package.json                 # Node.js config (Tailwind CSS build)
├── sw.js                        # Service Worker (caching, offline support)
├── README.md                    # Project README
├── .htaccess                    # Apache URL rewriting (to be created)
│
├── config/
│   └── database.php             # Database connection + site configuration constants
│
├── database/
│   ├── schema.sql               # Full database schema (18 tables)
│   └── seed.sql                 # Sample/seed data for development
│
├── includes/
│   ├── functions.php            # Core helper function library (780+ lines)
│   ├── header.php               # Public site header template
│   └── footer.php               # Public site footer template
│
├── pages/
│   ├── home.php                 # Homepage / landing page
│   ├── about.php                # About company page
│   ├── contact.php              # Contact page with form
│   ├── portfolio.php            # Portfolio listing with filters
│   ├── portfolio-single.php     # Single portfolio project detail
│   ├── product-single.php       # Single product detail page
│   ├── service-single.php       # Single service detail page
│   ├── blog-single.php          # Single blog post page
│   ├── insights.php             # Blog listing / insights index
│   ├── search.php               # Global search results page
│   ├── request-demo.php         # Demo request form page
│   ├── privacy.php              # Privacy policy page
│   ├── thank-you.php            # Post-submission confirmation page
│   ├── 404.php                  # Custom 404 error page
│   └── offline.php              # PWA offline fallback page
│
├── admin/
│   ├── router.php               # Admin panel route handler
│   ├── login.php                # Admin login page
│   ├── logout.php               # Admin logout handler
│   ├── dashboard.php            # Admin dashboard overview
│   ├── pages.php                # CMS page management
│   ├── products.php             # Product management
│   ├── services.php             # Service management
│   ├── portfolio.php            # Portfolio management
│   ├── blog.php                 # Blog post management
│   ├── team.php                 # Team member management
│   ├── testimonials.php         # Testimonial management
│   ├── leads.php                # Lead / CRM management
│   ├── demos.php                # Demo request management
│   ├── subscribers.php          # Newsletter subscriber management
│   ├── media.php                # Media library
│   ├── settings.php             # Site settings management
│   ├── profile.php              # Admin profile & password
│   ├── analytics.php            # Analytics dashboard
│   └── includes/
│       ├── header.php           # Admin layout header + top bar
│       ├── sidebar.php          # Admin sidebar navigation
│       └── footer.php           # Admin layout footer + bottom nav
│
├── api/
│   └── router.php               # API endpoints (newsletter, contact, demo, search)
│
├── assets/
│   ├── css/
│   │   ├── app.css              # Tailwind CSS source (input file)
│   │   ├── tailwind.css         # Compiled Tailwind CSS (output)
│   │   └── style.css            # Additional custom styles
│   ├── js/
│   │   └── app.js               # Main JavaScript file (911 lines)
│   └── images/
│       ├── icons/               # PWA icons (72px–512px + maskable)
│       └── screenshots/         # PWA screenshots
│
├── uploads/                     # User-uploaded files directory
│   ├── general/                 # General uploads
│   ├── blog/                    # Blog post images
│   ├── portfolio/               # Portfolio project images
│   ├── team/                    # Team member photos
│   ├── testimonials/            # Testimonial client photos
│   └── products/                # Product images
│
└── docs/                        # Project documentation
    └── PROJECT_DOCUMENTATION.md # This file
```

---

## 6. Database Design

### 6.1 Overview

- **Engine:** MySQL / MariaDB
- **Character Set:** `utf8mb4` (full Unicode including Amharic, emojis)
- **Collation:** `utf8mb4_unicode_ci`
- **Total Tables:** 18
- **Timezone:** `+03:00` (EAT)

### 6.2 Entity Relationship Summary

```
admin_users ──────┬──── activity_log
                  ├──── blog_posts ──── blog_categories
                  ├──── page_revisions ──── pages
                  ├──── lead_followups ──── leads
                  └──── media

products (standalone, JSON fields)
services ──── portfolio
testimonials (standalone)
demo_requests (standalone)
newsletter_subscribers (standalone)
site_settings (key-value store)
navigation_menus (self-referencing hierarchy)
site_analytics (standalone)
rate_limits (standalone)
csrf_tokens (standalone)
```

### 6.3 Table Specifications

#### 6.3.1 `admin_users` — Admin User Accounts

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Unique admin user ID |
| `username` | VARCHAR(50) UNIQUE | Login username |
| `email` | VARCHAR(100) UNIQUE | Admin email address |
| `password` | VARCHAR(255) | Bcrypt-hashed password |
| `full_name` | VARCHAR(100) | Display name |
| `role` | ENUM('admin','editor','viewer') | Role (default: editor) |
| `avatar` | VARCHAR(255) NULL | Profile image path |
| `is_active` | TINYINT(1) DEFAULT 1 | Account active flag |
| `last_login` | DATETIME NULL | Last login timestamp |
| `login_count` | INT DEFAULT 0 | Total login count |
| `password_reset_token` | VARCHAR(255) NULL | Password reset token |
| `password_reset_expires` | DATETIME NULL | Token expiration |
| `created_at` | TIMESTAMP DEFAULT CURRENT | Record creation time |
| `updated_at` | TIMESTAMP ON UPDATE | Last update time |

#### 6.3.2 `pages` — CMS Pages

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Unique page ID |
| `title_en` | VARCHAR(255) | English page title |
| `title_am` | VARCHAR(255) NULL | Amharic page title |
| `slug` | VARCHAR(255) UNIQUE | URL-friendly slug |
| `content_en` | LONGTEXT NULL | English HTML content |
| `content_am` | LONGTEXT NULL | Amharic HTML content |
| `template` | VARCHAR(50) DEFAULT 'default' | Page template type |
| `meta_title` | VARCHAR(255) NULL | SEO meta title |
| `meta_description` | TEXT NULL | SEO meta description |
| `featured_image` | VARCHAR(255) NULL | Featured image path |
| `status` | ENUM('draft','published','archived') | Publication status |
| `sort_order` | INT DEFAULT 0 | Display ordering |
| `created_by` | INT FK → admin_users | Author |
| `created_at` | TIMESTAMP | Creation time |
| `updated_at` | TIMESTAMP | Update time |

#### 6.3.3 `page_revisions` — Page Version History

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Revision ID |
| `page_id` | INT FK → pages | Associated page |
| `content_en` | LONGTEXT NULL | English content snapshot |
| `content_am` | LONGTEXT NULL | Amharic content snapshot |
| `revised_by` | INT FK → admin_users | Revising admin |
| `revision_note` | VARCHAR(255) NULL | Revision description |
| `created_at` | TIMESTAMP | Revision timestamp |

#### 6.3.4 `products` — Software Products

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Product ID |
| `name_en` | VARCHAR(255) | English product name |
| `name_am` | VARCHAR(255) NULL | Amharic product name |
| `slug` | VARCHAR(255) UNIQUE | URL slug |
| `icon` | VARCHAR(100) NULL | Product icon identifier |
| `tagline_en` | VARCHAR(255) NULL | English tagline |
| `tagline_am` | VARCHAR(255) NULL | Amharic tagline |
| `short_description_en` | TEXT NULL | English short description |
| `short_description_am` | TEXT NULL | Amharic short description |
| `description_en` | LONGTEXT NULL | English full description |
| `description_am` | LONGTEXT NULL | Amharic full description |
| `features` | JSON NULL | Feature list (JSON array) |
| `pricing_tiers` | JSON NULL | Pricing plans (JSON array of objects) |
| `user_types` | JSON NULL | Target user roles (JSON) |
| `implementation_steps` | JSON NULL | Implementation process (JSON) |
| `faq` | JSON NULL | FAQ items (JSON array) |
| `demo_url` | VARCHAR(255) NULL | Live demo URL |
| `gallery` | JSON NULL | Image gallery paths (JSON) |
| `type` | ENUM('sms','erp','custom') | Product type |
| `status` | ENUM('draft','active','archived') | Status |
| `sort_order` | INT DEFAULT 0 | Display order |
| `views` | INT DEFAULT 0 | View counter |
| `created_at` | TIMESTAMP | Creation time |
| `updated_at` | TIMESTAMP | Update time |

#### 6.3.5 `services` — Service Offerings

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Service ID |
| `title_en` | VARCHAR(255) | English service title |
| `title_am` | VARCHAR(255) NULL | Amharic service title |
| `slug` | VARCHAR(255) UNIQUE | URL slug |
| `icon` | VARCHAR(100) NULL | Service icon |
| `tagline_en` | VARCHAR(255) NULL | English tagline |
| `tagline_am` | VARCHAR(255) NULL | Amharic tagline |
| `short_description_en` | TEXT NULL | Short description (EN) |
| `short_description_am` | TEXT NULL | Short description (AM) |
| `description_en` | LONGTEXT NULL | Full description (EN) |
| `description_am` | LONGTEXT NULL | Full description (AM) |
| `offerings` | JSON NULL | Service offerings list |
| `technologies` | JSON NULL | Technologies used |
| `process_steps` | JSON NULL | Development process steps |
| `price_range` | VARCHAR(100) NULL | Price range text |
| `timeline` | VARCHAR(100) NULL | Estimated timeline |
| `status` | ENUM('draft','active','archived') | Status |
| `sort_order` | INT DEFAULT 0 | Display order |
| `views` | INT DEFAULT 0 | View counter |
| `created_at` | TIMESTAMP | Creation time |
| `updated_at` | TIMESTAMP | Update time |

#### 6.3.6 `portfolio` — Portfolio / Case Studies

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Portfolio item ID |
| `title_en` | VARCHAR(255) | English title |
| `title_am` | VARCHAR(255) NULL | Amharic title |
| `slug` | VARCHAR(255) UNIQUE | URL slug |
| `type` | ENUM('web','mobile','desktop','system') | Project type |
| `service_id` | INT FK → services NULL | Related service |
| `client_name` | VARCHAR(255) NULL | Client name |
| `client_logo` | VARCHAR(255) NULL | Client logo path |
| `description_en` | TEXT NULL | Description (EN) |
| `description_am` | TEXT NULL | Description (AM) |
| `challenge_en` | TEXT NULL | Challenge description (EN) |
| `challenge_am` | TEXT NULL | Challenge description (AM) |
| `solution_en` | TEXT NULL | Solution description (EN) |
| `solution_am` | TEXT NULL | Solution description (AM) |
| `results_en` | TEXT NULL | Results description (EN) |
| `results_am` | TEXT NULL | Results description (AM) |
| `technologies` | JSON NULL | Technologies used |
| `metrics` | JSON NULL | Project metrics/stats |
| `gallery` | JSON NULL | Image gallery |
| `featured_image` | VARCHAR(255) NULL | Main project image |
| `project_url` | VARCHAR(255) NULL | Live project URL |
| `completion_date` | DATE NULL | Project completion date |
| `industry` | VARCHAR(100) NULL | Client industry |
| `client_testimonial` | TEXT NULL | Client quote |
| `client_testimonial_author` | VARCHAR(100) NULL | Quote author |
| `is_featured` | TINYINT(1) DEFAULT 0 | Featured project flag |
| `status` | ENUM('draft','published') | Status |
| `sort_order` | INT DEFAULT 0 | Display order |
| `views` | INT DEFAULT 0 | View counter |
| `created_at` | TIMESTAMP | Creation time |
| `updated_at` | TIMESTAMP | Update time |

#### 6.3.7 `blog_categories` — Blog Post Categories

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Category ID |
| `name_en` | VARCHAR(100) | English category name |
| `name_am` | VARCHAR(100) NULL | Amharic category name |
| `slug` | VARCHAR(100) UNIQUE | URL slug |
| `description_en` | TEXT NULL | Description (EN) |
| `description_am` | TEXT NULL | Description (AM) |
| `created_at` | TIMESTAMP | Creation time |

#### 6.3.8 `blog_posts` — Blog Posts / Articles

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Post ID |
| `title_en` | VARCHAR(255) | English title |
| `title_am` | VARCHAR(255) NULL | Amharic title |
| `slug` | VARCHAR(255) UNIQUE | URL slug |
| `category_id` | INT FK → blog_categories NULL | Category |
| `author_id` | INT FK → admin_users | Post author |
| `excerpt_en` | TEXT NULL | Excerpt (EN) |
| `excerpt_am` | TEXT NULL | Excerpt (AM) |
| `content_en` | LONGTEXT NULL | Full content (EN) |
| `content_am` | LONGTEXT NULL | Full content (AM) |
| `featured_image` | VARCHAR(255) NULL | Featured image path |
| `tags` | VARCHAR(255) NULL | Comma-separated tags |
| `meta_title` | VARCHAR(255) NULL | SEO title |
| `meta_description` | TEXT NULL | SEO description |
| `status` | ENUM('draft','published','archived') | Status |
| `is_featured` | TINYINT(1) DEFAULT 0 | Featured post flag |
| `is_gated` | TINYINT(1) DEFAULT 0 | Gated content flag |
| `views` | INT DEFAULT 0 | View counter |
| `read_time` | INT DEFAULT 0 | Estimated read time (min) |
| `published_at` | DATETIME NULL | Scheduled publish date |
| `created_at` | TIMESTAMP | Creation time |
| `updated_at` | TIMESTAMP | Update time |

#### 6.3.9 `team_members` — Team / Staff

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Team member ID |
| `full_name_en` | VARCHAR(100) | Name (EN) |
| `full_name_am` | VARCHAR(100) NULL | Name (AM) |
| `role_en` | VARCHAR(100) | Job title (EN) |
| `role_am` | VARCHAR(100) NULL | Job title (AM) |
| `bio_en` | TEXT NULL | Bio (EN) |
| `bio_am` | TEXT NULL | Bio (AM) |
| `photo` | VARCHAR(255) NULL | Profile photo path |
| `email` | VARCHAR(100) NULL | Email address |
| `linkedin` | VARCHAR(255) NULL | LinkedIn URL |
| `github` | VARCHAR(255) NULL | GitHub URL |
| `twitter` | VARCHAR(255) NULL | Twitter URL |
| `department` | VARCHAR(50) NULL | Department |
| `is_leadership` | TINYINT(1) DEFAULT 0 | Leadership team flag |
| `status` | ENUM('active','inactive') | Status |
| `sort_order` | INT DEFAULT 0 | Display order |
| `created_at` | TIMESTAMP | Creation time |
| `updated_at` | TIMESTAMP | Update time |

#### 6.3.10 `testimonials` — Client Testimonials

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Testimonial ID |
| `client_name` | VARCHAR(100) | Client name |
| `client_role` | VARCHAR(100) NULL | Client job title |
| `company` | VARCHAR(100) NULL | Client company |
| `content_en` | TEXT | Testimonial text (EN) |
| `content_am` | TEXT NULL | Testimonial text (AM) |
| `photo` | VARCHAR(255) NULL | Client photo |
| `rating` | TINYINT DEFAULT 5 | Rating (1-5 stars) |
| `is_featured` | TINYINT(1) DEFAULT 0 | Featured flag |
| `status` | ENUM('active','inactive') | Status |
| `sort_order` | INT DEFAULT 0 | Display order |
| `created_at` | TIMESTAMP | Creation time |

#### 6.3.11 `leads` — Contact Form Submissions / Leads

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Lead ID |
| `name` | VARCHAR(100) | Contact name |
| `email` | VARCHAR(100) | Email address |
| `phone` | VARCHAR(20) NULL | Phone number |
| `company` | VARCHAR(100) NULL | Company name |
| `subject` | VARCHAR(255) NULL | Subject/topic |
| `message` | TEXT NULL | Message body |
| `source` | VARCHAR(50) DEFAULT 'website' | Lead source |
| `interest` | VARCHAR(100) NULL | Product/service interest |
| `status` | ENUM('new','contacted','qualified','converted','closed') | Pipeline status |
| `priority` | ENUM('low','medium','high') DEFAULT 'medium' | Priority level |
| `assigned_to` | INT FK → admin_users NULL | Assigned admin |
| `score` | INT DEFAULT 0 | Lead score |
| `utm_source` | VARCHAR(100) NULL | UTM source tracking |
| `utm_medium` | VARCHAR(100) NULL | UTM medium tracking |
| `utm_campaign` | VARCHAR(100) NULL | UTM campaign tracking |
| `ip_address` | VARCHAR(45) NULL | Submitter IP |
| `user_agent` | TEXT NULL | Browser user agent |
| `created_at` | TIMESTAMP | Submission time |
| `updated_at` | TIMESTAMP | Last update time |

#### 6.3.12 `lead_followups` — Lead Follow-up Notes

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Follow-up ID |
| `lead_id` | INT FK → leads | Associated lead |
| `admin_id` | INT FK → admin_users | Admin who created |
| `type` | ENUM('note','call','email','meeting') | Follow-up type |
| `note` | TEXT | Follow-up details |
| `next_followup_date` | DATE NULL | Next action date |
| `created_at` | TIMESTAMP | Creation time |

#### 6.3.13 `demo_requests` — Product Demo Requests

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Request ID |
| `name` | VARCHAR(100) | Requester name |
| `email` | VARCHAR(100) | Email address |
| `phone` | VARCHAR(20) NULL | Phone number |
| `organization` | VARCHAR(100) NULL | Organization name |
| `organization_size` | VARCHAR(50) NULL | Organization size range |
| `role` | VARCHAR(100) NULL | Requester's role |
| `product_interest` | VARCHAR(100) NULL | Interested product |
| `preferred_date` | DATE NULL | Preferred demo date |
| `preferred_time` | VARCHAR(50) NULL | Preferred time slot |
| `message` | TEXT NULL | Additional notes |
| `status` | ENUM('pending','scheduled','completed','cancelled') | Status |
| `ip_address` | VARCHAR(45) NULL | Submitter IP |
| `created_at` | TIMESTAMP | Submission time |
| `updated_at` | TIMESTAMP | Update time |

#### 6.3.14 `newsletter_subscribers` — Email Subscribers

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Subscriber ID |
| `email` | VARCHAR(100) UNIQUE | Email address |
| `status` | ENUM('active','unsubscribed') DEFAULT 'active' | Status |
| `confirmation_token` | VARCHAR(255) NULL | Email confirmation token |
| `confirmed_at` | DATETIME NULL | Confirmation timestamp |
| `ip_address` | VARCHAR(45) NULL | Subscriber IP |
| `created_at` | TIMESTAMP | Subscribe time |

#### 6.3.15 `media` — Media Library

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Media ID |
| `filename` | VARCHAR(255) | Stored filename |
| `original_name` | VARCHAR(255) | Original upload name |
| `mime_type` | VARCHAR(100) | File MIME type |
| `file_size` | INT | File size in bytes |
| `folder` | VARCHAR(50) DEFAULT 'general' | Storage folder |
| `width` | INT NULL | Image width (px) |
| `height` | INT NULL | Image height (px) |
| `uploaded_by` | INT FK → admin_users | Uploader |
| `created_at` | TIMESTAMP | Upload time |

#### 6.3.16 `site_settings` — Key-Value Configuration

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Setting ID |
| `setting_key` | VARCHAR(100) UNIQUE | Setting identifier |
| `setting_value` | TEXT NULL | Setting value |
| `setting_group` | VARCHAR(50) DEFAULT 'general' | Group (general/social/seo/email) |
| `updated_at` | TIMESTAMP | Last update time |

#### 6.3.17 `navigation_menus` — Navigation Menu Items

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Menu item ID |
| `label_en` | VARCHAR(100) | English label |
| `label_am` | VARCHAR(100) NULL | Amharic label |
| `url` | VARCHAR(255) | Menu item URL |
| `location` | ENUM('header','footer') | Menu location |
| `parent_id` | INT FK → navigation_menus NULL | Parent item (hierarchy) |
| `sort_order` | INT DEFAULT 0 | Display order |
| `is_active` | TINYINT(1) DEFAULT 1 | Active flag |
| `created_at` | TIMESTAMP | Creation time |

#### 6.3.18 `site_analytics` — Page View Tracking

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Analytics record ID |
| `page_url` | VARCHAR(255) | Visited page URL |
| `page_title` | VARCHAR(255) NULL | Page title |
| `ip_address` | VARCHAR(45) NULL | Visitor IP |
| `user_agent` | TEXT NULL | Browser user agent |
| `referrer` | VARCHAR(255) NULL | Referrer URL |
| `device_type` | ENUM('desktop','tablet','mobile') | Device type |
| `browser` | VARCHAR(50) NULL | Browser name |
| `os` | VARCHAR(50) NULL | Operating system |
| `country` | VARCHAR(50) NULL | Country (placeholder) |
| `session_id` | VARCHAR(100) NULL | PHP session ID |
| `created_at` | TIMESTAMP | Visit timestamp |

#### 6.3.19 Additional Support Tables

| Table | Purpose |
|---|---|
| `activity_log` | Admin action audit trail (user_id, action, description, IP, timestamp) |
| `rate_limits` | IP-based rate limiting (identifier, action, attempts, window, IP) |
| `csrf_tokens` | CSRF token storage and validation |

### 6.4 Key Database Relationships

```
admin_users (1) ──→ (N) blog_posts          [author_id]
admin_users (1) ──→ (N) page_revisions      [revised_by]
admin_users (1) ──→ (N) lead_followups      [admin_id]
admin_users (1) ──→ (N) media               [uploaded_by]
admin_users (1) ──→ (N) activity_log        [user_id]
admin_users (1) ──→ (N) leads               [assigned_to]
pages (1)       ──→ (N) page_revisions      [page_id]
blog_categories (1) ──→ (N) blog_posts      [category_id]
services (1)    ──→ (N) portfolio            [service_id]
leads (1)       ──→ (N) lead_followups      [lead_id]
navigation_menus (1) ──→ (N) navigation_menus [parent_id] (self-ref)
```

---

## 7. Feature Specifications

### 7.1 Core Helper Functions (includes/functions.php)

The application relies on a single procedural helper library (~780 lines) organized into the following function groups:

#### Database Functions
| Function | Purpose |
|---|---|
| `db_escape($str)` | Escape string for SQL queries |
| `db_query($sql)` | Execute SQL query, return result |
| `db_fetch_one($sql)` | Fetch single row as associative array |
| `db_fetch_all($sql)` | Fetch all rows as array of arrays |
| `db_insert_id()` | Get last insert ID |
| `db_affected_rows()` | Get affected row count |
| `db_count($table, $where)` | Count rows with optional WHERE clause |

#### Security Functions
| Function | Purpose |
|---|---|
| `e($str)` | HTML entity encoding (XSS prevention) |
| `csrf_token()` | Generate and store CSRF token |
| `csrf_field()` | Output hidden CSRF form field |
| `csrf_verify()` | Validate submitted CSRF token |
| `rate_limit_check($action, $max, $window)` | IP-based rate limiting |
| `get_client_ip()` | Get client IP (proxy-aware) |
| `is_valid_email($email)` | Email format validation |
| `is_valid_phone($phone)` | Ethiopian phone format validation |
| `is_spam_honeypot()` | Detect spam via honeypot field |

#### URL & Routing Functions
| Function | Purpose |
|---|---|
| `url($path)` | Generate full URL from path |
| `asset($path)` | Generate asset URL |
| `upload_url($path)` | Generate upload file URL |
| `redirect($url)` | HTTP redirect |
| `current_path()` | Get current URL path |
| `is_active($path)` | Check if path matches current URL |
| `create_slug($string)` | Generate URL-safe slug |

#### Content & Localization Functions
| Function | Purpose |
|---|---|
| `get_setting($key)` | Get site setting value (cached) |
| `get_text($row, $field)` | Get localized text (EN or AM) |
| `current_lang()` | Get current language code |
| `truncate($text, $length)` | Truncate text with ellipsis |
| `format_date($date)` | Format date for display |
| `format_etb($amount)` | Format Ethiopian Birr currency |
| `get_nav_menu($location)` | Get navigation menu items |
| `build_menu_tree($items)` | Build hierarchical menu structure |

#### Authentication Functions
| Function | Purpose |
|---|---|
| `is_admin_logged_in()` | Check if admin is authenticated |
| `get_current_admin()` | Get current admin user data |
| `admin_has_permission($perm)` | Check role-based permission |
| `require_admin_auth()` | Enforce authentication (redirect if not) |
| `log_activity($action, $desc)` | Log admin action to activity_log |

#### File Upload Functions
| Function | Purpose |
|---|---|
| `handle_upload($field, $folder)` | Process file upload with validation |

Upload configuration:
- Max image size: 5MB
- Max document size: 20MB
- Allowed image types: JPEG, PNG, GIF, WebP, SVG
- Allowed document types: PDF, DOC, DOCX, XLS, XLSX, ZIP
- Files stored in: `uploads/{folder}/{unique_name}.{ext}`
- Records stored in `media` table with dimensions

#### Utility Functions
| Function | Purpose |
|---|---|
| `set_flash($type, $message)` | Set flash message (success/error/warning/info) |
| `get_flash()` | Retrieve and clear flash messages |
| `render_flash_messages()` | Output styled flash message HTML |
| `paginate($total, $perPage, $page)` | Calculate pagination data |
| `render_pagination($data, $baseUrl)` | Output pagination HTML |
| `send_email($to, $subject, $body)` | Send email via `mail()` |
| `notify_admin($subject, $message)` | Send notification to admin email |
| `email_template($title, $body)` | Wrap content in HTML email template |
| `track_page_view($url, $title)` | Record analytics page view |
| `get_json($str)` | Safely decode JSON string |
| `is_ajax()` | Check if request is AJAX |
| `json_response($data, $code)` | Output JSON response |
| `post($key)` | Get sanitized POST value |
| `get($key)` | Get sanitized GET value |
| `random_string($length)` | Generate random alphanumeric string |
| `time_ago($datetime)` | Convert timestamp to relative time |

---

## 8. Public Website Pages

### 8.1 Homepage (`pages/home.php`)

| Section | Description |
|---|---|
| **Hero** | Animated background with gradient orbs, grid pattern, headline, subtitle, dual CTA buttons (Explore Solutions / Request Demo) |
| **Trust Bar** | Client/partner logo display |
| **Products** | Grid of published products with name, description, icon, CTA |
| **Services** | Grid of published services with icon, description, "Learn More" |
| **Stats Counter** | Animated counters (clients, projects, team size, etc.) |
| **Featured Portfolio** | 3 featured case studies with image overlays |
| **Testimonials** | Carousel with star ratings, client info, prev/next navigation, autoplay |
| **Blog Highlights** | 3 most recent published posts |
| **Contact CTA** | Bottom call-to-action band |

**Data Sources:** `products`, `services`, `portfolio`, `testimonials`, `blog_posts`

### 8.2 About Page (`pages/about.php`)

| Section | Description |
|---|---|
| **Hero** | Page title and introduction |
| **Mission & Vision** | Two cards with company mission and vision statements |
| **Core Values** | 4 value cards: Quality, Client Partnership, Innovation, Local Impact |
| **Story Timeline** | Vertical timeline — milestones from 2023 to 2026 |
| **Stats** | 4 animated counters |
| **Team Members** | Grid of active team members with photos, names, roles, social links |
| **Join CTA** | "Join Our Team" call-to-action |

**Data Sources:** `team_members`, `products` (count), `portfolio` (count)

### 8.3 Contact Page (`pages/contact.php`)

| Section | Description |
|---|---|
| **Hero** | Contact heading |
| **Info Cards** | Address, phone, email, office hours (from site_settings) |
| **Contact Form** | Name*, email*, phone*, company, subject dropdown, message* |
| **Google Maps** | Embedded map iframe |
| **Social Links** | Facebook, Telegram, LinkedIn, Instagram |

**Form Details:**
- Fields: name (required), email (required), phone (required, Ethiopian format), company, subject (dropdown: general/sms/erp/web-dev/support/career/partnership), message (required)
- Security: CSRF token, honeypot field (`website_url_hp`), rate limiting (3/hour)
- On success: saves to `leads` table, notifies admin, redirects to `/thank-you/contact`
- Validation: email format, Ethiopian phone pattern, all server-side

### 8.4 Portfolio Listing (`pages/portfolio.php`)

| Section | Description |
|---|---|
| **Hero** | Portfolio page heading |
| **Filters** | Type filter buttons (All/SMS/ERP), service-based filter buttons |
| **Portfolio Grid** | 3-column grid of projects with images, technology tags, hover overlay |
| **Pagination** | Standard numbered pagination |
| **CTA** | Bottom call-to-action |

**Features:** URL-based filtering (`?type=web&service=slug`), pagination, featured badge

### 8.5 Portfolio Detail (`pages/portfolio-single.php`)

| Section | Description |
|---|---|
| **Breadcrumb** | Home → Portfolio → Project Name |
| **Hero** | Project title, client info, type badge, featured image |
| **Overview** | Challenge and solution descriptions |
| **Results** | Metrics grid (JSON-decoded) |
| **Technologies** | Technology tag pills |
| **Gallery** | Image gallery with lightbox |
| **Client Quote** | Client testimonial block |
| **Related Projects** | 3 related portfolio items (same type) |

### 8.6 Product Detail (`pages/product-single.php`)

| Section | Description |
|---|---|
| **Hero** | Product name, tagline, description, icon |
| **Features** | Feature grid (from JSON) |
| **Feature Tabs** | Tabbed content for different user roles |
| **Pricing** | Pricing tier cards with monthly/annual toggle |
| **Implementation** | Numbered implementation steps |
| **FAQ** | Accordion-style FAQ section |
| **Demo Form** | Inline demo request form |
| **Case Studies** | Related portfolio projects |

**Data:** All structured content from JSON columns (`features`, `pricing_tiers`, `user_types`, `implementation_steps`, `faq`)
**Currency:** Ethiopian Birr (ETB) with `format_etb()`

### 8.7 Service Detail (`pages/service-single.php`)

| Section | Description |
|---|---|
| **Hero** | Service title, tagline, icon |
| **Description** | Full service description with price range and timeline |
| **Offerings** | Service offerings grid (from JSON) |
| **Technologies** | Technology pills |
| **Process** | Numbered development process steps |
| **Portfolio** | Related portfolio projects (by service_id) |
| **CTA** | Get a quote CTA |

### 8.8 Blog / Insights Listing (`pages/insights.php`)

| Section | Description |
|---|---|
| **Hero + Search** | Page title with search form |
| **Category Filters** | Category pill buttons with post counts |
| **Featured Post** | Hero-sized card for first post (page 1 only) |
| **Post Grid** | 3-column grid of blog posts |
| **Pagination** | Numbered pagination preserving filters |
| **Newsletter CTA** | Subscribe form at bottom |

**Features:** Search by keyword, category filter by slug, pagination

### 8.9 Blog Post Detail (`pages/blog-single.php`)

| Section | Description |
|---|---|
| **Breadcrumb** | Home → Insights → Post Title |
| **Header** | Category badge, title, excerpt, author info, read time, views |
| **Featured Image** | Full-width featured image |
| **Content** | Prose-styled HTML content |
| **Tags** | Post tags |
| **Share Buttons** | Twitter, Facebook, LinkedIn, copy link |
| **Related Posts** | 3 related articles (same category) |

### 8.10 Search Results (`pages/search.php`)

- Cross-table search across: products, services, portfolio, blog_posts, pages
- LIKE queries on title/description/content fields
- Minimum 2 character query
- Each result has type icon, label, and link to appropriate page
- Empty state with navigation suggestions

### 8.11 Request Demo (`pages/request-demo.php`)

**Form Fields (3 sections):**

| Section | Fields |
|---|---|
| **Your Info** | Name*, email*, phone*, role (dropdown) |
| **Organization** | Organization name, organization size (dropdown), interested product* (dynamic from DB + "Custom Solution") |
| **Demo Time** | Preferred date (date picker, min tomorrow), preferred time (morning/afternoon/flexible), additional notes |

**Sidebar:** "What to Expect" steps, quick chat (phone/WhatsApp), trust badges
**Security:** CSRF, honeypot, rate limiting (3/hour)
**On Success:** Save to `demo_requests`, notify admin, redirect to `/thank-you/demo`

### 8.12 Other Pages

| Page | Description |
|---|---|
| **Privacy Policy** | Static 8-section legal page, settings-driven company info |
| **Thank You** | Dynamic confirmation — 3 types: contact, demo, newsletter |
| **404** | Custom error page with navigation buttons |
| **Offline** | PWA offline fallback — standalone HTML with retry button |

---

## 9. Admin Panel (CMS)

### 9.1 Authentication

| Feature | Detail |
|---|---|
| **Login** | Username/email + password with show/hide toggle |
| **Rate Limiting** | Max login attempts with 15-minute lockout |
| **Session** | Regenerated on login, httponly, strict mode, SameSite |
| **Logout** | Full session destruction, activity logged |
| **Password** | Bcrypt hashing via `password_hash()`/`password_verify()` |
| **Roles** | admin, editor, viewer (defined but not enforced per-page) |

### 9.2 Dashboard (`admin/dashboard.php`)

| Component | Description |
|---|---|
| **Welcome Banner** | Time-of-day greeting with admin name |
| **Stat Cards** | Total leads, demo requests, subscribers, today's page views, blog posts, portfolio projects |
| **Quick Actions** | Links to key admin pages |
| **Recent Leads** | Latest 5 contact submissions |
| **Recent Demos** | Latest 5 demo requests |
| **Top Pages** | Most viewed pages today |
| **Activity Log** | Last 10 admin actions |

### 9.3 Content Management Modules

#### Pages (`admin/pages.php`)
- **CRUD:** Create, Read, Update, Delete
- **Bilingual:** English + Amharic content fields
- **Revision History:** Auto-saved, displays last 10 revisions
- **SEO:** Meta title, meta description fields
- **Templates:** default, full-width, sidebar
- **Status:** draft, published, archived
- **HTML Toolbar:** H2, H3, P, Bold, Link buttons

#### Products (`admin/products.php`)
- **CRUD:** Create, Read (paginated), Update, Delete
- **JSON Fields:** features, pricing_tiers, user_types, implementation_steps, faq
- **Pagination:** 20 items per page
- **View Count:** Displayed in list view
- **Slug Validation:** Uniqueness check

#### Services (`admin/services.php`)
- **CRUD:** Create, Read (paginated), Update, Delete
- **JSON Fields:** offerings, technologies, process_steps
- **Display:** Price range, timeline in list view

#### Portfolio (`admin/portfolio.php`)
- **CRUD:** Create, Read (paginated + filtered), Update, Delete
- **Image Upload:** Featured image via `handle_upload()`
- **Type Filters:** All, Web, Mobile, Desktop, System tabs
- **Service Link:** Dropdown to associate with service
- **Featured Flag:** Star indicator
- **Gallery:** Comma-separated image paths

#### Blog (`admin/blog.php`)
- **CRUD:** Create, Read (paginated + filtered), Update, Delete
- **Image Upload:** Featured image
- **Auto Calculation:** read_time (words/200)
- **Status Filters:** draft, published, archived tabs
- **HTML Toolbar:** H2, H3, Bold, Italic, Link, UL, Quote
- **SEO:** Meta title, meta description fields
- **Author Tracking:** Auto-assigned from session

#### Team (`admin/team.php`)
- **CRUD:** Create, Read (card grid), Update, Delete
- **Photo Upload:** Circular preview
- **Social Links:** LinkedIn, GitHub, Twitter URLs
- **Sort Order:** Manual ordering

#### Testimonials (`admin/testimonials.php`)
- **CRUD:** Create, Read (card list), Update, Delete
- **Photo Upload:** Circular preview
- **Star Rating:** 1-5 star visual selector
- **Featured Flag:** Highlighted testimonials
- **Sort Order:** Manual ordering

### 9.4 Lead Management

#### Leads (`admin/leads.php`)
- **Pipeline:** new → contacted → qualified → converted → closed
- **Auto-Update:** "new" leads marked "contacted" on first view
- **Follow-ups:** Add notes with type (note/call/email/meeting), next follow-up date
- **Search:** By name, email, or interest
- **Status Filters:** Tabs for each pipeline stage
- **Pagination:** 20 per page
- **Admin Attribution:** Follow-ups linked to creating admin

#### Demo Requests (`admin/demos.php`)
- **Pipeline:** pending → scheduled → completed → cancelled
- **Read-Only Data:** No edit form (data from public submission)
- **Status Update:** Via action links
- **Detail View:** Full submission details
- **Pagination:** 20 per page

### 9.5 System Modules

#### Newsletter Subscribers (`admin/subscribers.php`)
- **Read + Delete:** No editing (subscribers come from public)
- **CSV Export:** Download active subscribers as CSV
- **Stats Cards:** Active, unsubscribed, this month counts
- **Search:** By email
- **Status Filter:** Active/unsubscribed

#### Media Library (`admin/media.php`)
- **Multi-File Upload:** Multiple files at once
- **Folder Categories:** general, blog, portfolio, team, testimonials, products
- **Grid View:** Image thumbnails with hover overlay
- **Copy URL:** Clipboard copy for each file
- **Type Filters:** All, Images, Documents
- **Search:** By filename
- **Storage Stats:** Total files, image count, total size
- **Physical Deletion:** Removes file + DB record
- **Pagination:** 30 per page

#### Site Settings (`admin/settings.php`)

| Group | Settings |
|---|---|
| **General** | Site name, tagline, description, keywords, footer text, maintenance mode |
| **Company** | Company name, email, phone, address, city, country |
| **Social Media** | Facebook, Twitter, LinkedIn, GitHub, Instagram, YouTube, Telegram, WhatsApp URLs |
| **Integrations** | Google Analytics ID, Google Maps embed code |

#### Admin Profile (`admin/profile.php`)
- **Profile Info:** Full name, email (with uniqueness validation)
- **Password Change:** Current password verification, min 8 chars, confirmation field
- **Activity History:** Last 20 actions

#### Analytics Dashboard (`admin/analytics.php`)
- **Period Filters:** Today, 7 days, 30 days, 90 days, 1 year
- **Growth Metrics:** Percentage change vs previous period
- **Charts:** CSS bar chart for daily page views
- **Top Pages:** Top 15 most visited pages
- **Referrers:** Top 10 referring domains
- **Device Breakdown:** Desktop/tablet/mobile with progress bars
- **Browser Stats:** Browser name breakdown
- **OS Stats:** Operating system breakdown
- **Combined Metrics:** Views + leads + demos + subscribers per period
- **Unique Visitors:** Count by distinct IP address

### 9.6 Admin UI Components

| Component | Description |
|---|---|
| **Top Bar** | Mobile sidebar toggle, page title, theme toggle, "View Site" link, admin avatar |
| **Sidebar** | 16 nav items in 4 groups (Content, People, Leads, System), badge counts, logout |
| **Bottom Nav** | Mobile – 5 quick items: Home, Leads, Demos, Stats, More |
| **Flash Messages** | Styled alerts with icons (success/error/warning/info) |
| **Pagination** | Standard numbered pagination component |
| **HTML Toolbar** | Basic rich text editing buttons |

---

## 10. API Endpoints

### 10.1 `POST /api/newsletter`

**Purpose:** Newsletter email subscription

| Parameter | Type | Required | Description |
|---|---|---|---|
| `email` | string | Yes | Subscriber email address |
| `csrf_token` | string | Yes | CSRF verification token |

**Rate Limit:** 5 requests/hour per IP  
**Logic:** Checks for existing subscriber, resubscribes if unsubscribed, creates new if not found  
**Response:** JSON `{ success: true/false, message: "..." }`

### 10.2 `POST /api/contact`

**Purpose:** Contact form submission

| Parameter | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | Contact name |
| `email` | string | Yes | Email address |
| `phone` | string | Yes | Phone number |
| `company` | string | No | Company name |
| `subject` | string | No | Subject/topic |
| `message` | string | Yes | Message body |
| `csrf_token` | string | Yes | CSRF token |
| `website_url_hp` | string | No | Honeypot field (must be empty) |

**Rate Limit:** 3 requests/hour per IP  
**Logic:** Validates inputs, checks honeypot, saves to `leads` table, notifies admin via email  
**Response:** JSON `{ success: true/false, message: "..." }`

### 10.3 `POST /api/demo`

**Purpose:** Demo request submission

| Parameter | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | Requester name |
| `email` | string | Yes | Email address |
| `phone` | string | No | Phone number |
| `organization` | string | No | Organization name |
| `organization_size` | string | No | Size range |
| `role` | string | No | Job role |
| `product_interest` | string | No | Interested product |
| `preferred_date` | date | No | Preferred date |
| `preferred_time` | string | No | Preferred time slot |
| `message` | string | No | Additional notes |
| `csrf_token` | string | Yes | CSRF token |
| `website_url_hp` | string | No | Honeypot field |

**Rate Limit:** 3 requests/hour per IP  
**Response:** JSON `{ success: true/false, message: "..." }`

### 10.4 `GET /api/search`

**Purpose:** Site-wide search

| Parameter | Type | Required | Description |
|---|---|---|---|
| `q` | string | Yes | Search query (min 2 chars) |

**Logic:** Searches across products, services, blog_posts using LIKE queries  
**Response:** JSON `{ success: true, results: [...] }`

---

## 11. Authentication & Authorization

### 11.1 Authentication Flow

```
User visits /admin
    ↓
is_admin_logged_in()?
    ├── YES → Load requested admin page
    └── NO  → Redirect to /admin/login
                ↓
        Submit credentials
                ↓
        rate_limit_check() → Too many attempts? → Show lockout
                ↓
        csrf_verify() → Invalid? → Show error
                ↓
        Query admin_users by username OR email
                ↓
        password_verify() → Incorrect? → Log failure, increment attempts
                ↓
        Check is_active = 1
                ↓
        session_regenerate_id()
        Set $_SESSION['admin_id', 'admin_name', 'admin_role']
        Update last_login, login_count
        log_activity('login')
                ↓
        Redirect to /admin/dashboard
```

### 11.2 Session Security Configuration

| Setting | Value |
|---|---|
| `cookie_httponly` | true |
| `cookie_samesite` | Strict |
| `use_strict_mode` | 1 |
| `gc_maxlifetime` | 1800 (30 min) |
| `cookie_lifetime` | 1800 (30 min) |

### 11.3 Role Definitions

| Role | Intended Permissions |
|---|---|
| **admin** | Full access to all modules |
| **editor** | Content management (pages, blog, portfolio) |
| **viewer** | Read-only access |

> **Note:** Role-based permissions are defined in the schema but currently not enforced per-page. All authenticated admins have equal access.

---

## 12. Security Specifications

### 12.1 CSRF Protection

- Tokens generated per session via `csrf_token()`
- Hidden field output via `csrf_field()` in all forms
- Verified server-side via `csrf_verify()` on all POST actions
- Token stored in `$_SESSION['csrf_token']` and optionally in `csrf_tokens` table

### 12.2 Rate Limiting

| Action | Max Attempts | Window |
|---|---|---|
| Login attempts | 5 | 15 minutes |
| Contact form | 3 | 1 hour |
| Demo request | 3 | 1 hour |
| Newsletter subscribe | 5 | 1 hour |
| General form | 10 | 1 hour |

Implementation: IP-based tracking in `rate_limits` table via `rate_limit_check()`

### 12.3 Input Validation & Sanitization

| Protection | Implementation |
|---|---|
| **XSS Prevention** | `e()` function (htmlspecialchars with ENT_QUOTES, UTF-8) |
| **SQL Injection** | `db_escape()` using `mysqli_real_escape_string()` |
| **Email Validation** | `is_valid_email()` + `filter_var(FILTER_VALIDATE_EMAIL)` |
| **Phone Validation** | `is_valid_phone()` — Ethiopian format regex |
| **Spam Prevention** | Honeypot hidden fields (`website_url_hp`) |
| **File Upload** | MIME type checking, file extension whitelist, size limits |
| **Direct Access** | `if (!defined('BASE_PATH')) exit;` guard on all includes |

### 12.4 Password Security

- Hashing: `password_hash($password, PASSWORD_DEFAULT)` (bcrypt)
- Verification: `password_verify()` — timing-safe comparison
- Minimum length: 8 characters
- Password reset tokens: Random string with expiration datetime

---

## 13. Progressive Web App (PWA)

### 13.1 Web App Manifest (`manifest.json`)

| Property | Value |
|---|---|
| `name` | Authopic Technologies PLC - Modern Digital Solutions |
| `short_name` | Authopic Technologies |
| `display` | standalone |
| `theme_color` | #0066FF |
| `background_color` | #0f172a |
| `orientation` | any |
| `scope` | / |
| `start_url` | / |

**Icons:** 8 sizes (72, 96, 128, 144, 152, 192, 384, 512px) + maskable variants  
**Screenshots:** Desktop and mobile screenshots  
**Shortcuts:** Products, Contact, Request Demo

### 13.2 Service Worker (`sw.js`)

| Strategy | Applied To |
|---|---|
| **Cache First** | Google Fonts, images, CSS/JS files |
| **Stale While Revalidate** | Static assets |
| **Network First** | HTML pages (offline fallback) |
| **Skip** | Admin routes, API routes |

**Named Caches:**

| Cache | Purpose | Max Size |
|---|---|---|
| `static-v2` | Pre-cached core assets | 8 items |
| `dynamic-v2` | Dynamic page content | 80 items |
| `images-v2` | Cached images | 150 items |
| `fonts-v2` | Google Fonts | 30 items |

**Pre-cached Assets:** Homepage, offline page, manifest, app.css, tailwind.css, app.js, core images

**Offline Behavior:** Falls back to `/offline` page when network unavailable

### 13.3 Install Prompt (in app.js)

- Captures `beforeinstallprompt` event
- Shows custom bottom sheet install prompt
- iOS detection with separate install instructions
- Install button in navbar
- Dismiss + confirm buttons in prompt
- Handles `appinstalled` event

---

## 14. Internationalization (i18n)

### 14.1 Language System

| Feature | Implementation |
|---|---|
| **Supported Languages** | English (en), Amharic (am / አማርኛ) |
| **Default Language** | English |
| **Language Detection** | `$_GET['lang']` → cookie → session → default |
| **Storage** | `lang` cookie (365 days) + session |
| **Toggle** | Navbar language switch button (EN/አማ) |

### 14.2 Database Bilingual Pattern

All content tables use dual columns:
```
title_en VARCHAR(255)       -- English content
title_am VARCHAR(255) NULL  -- Amharic content (nullable)
```

The `get_text($row, $field)` function automatically selects the correct language column:
```php
function get_text($row, $field) {
    $lang = current_lang();
    $localized = $field . '_' . $lang;
    if (isset($row[$localized]) && !empty($row[$localized])) {
        return $row[$localized];
    }
    return $row[$field . '_en'] ?? '';  // Fallback to English
}
```

### 14.3 Bilingual Content Scope

| Content Area | Bilingual Support |
|---|---|
| **Page titles & content** | ✅ Full |
| **Product names & descriptions** | ✅ Full |
| **Service titles & descriptions** | ✅ Full |
| **Portfolio titles & descriptions** | ✅ Full |
| **Blog titles, excerpts, content** | ✅ Full |
| **Team member names, roles, bios** | ✅ Full |
| **Testimonial content** | ✅ Full |
| **Navigation menu labels** | ✅ Full |
| **Blog category names** | ✅ Full |
| **UI labels & buttons** | ✅ Hardcoded ternary (get_text or inline) |
| **Form labels & errors** | ✅ Hardcoded ternary |
| **Offline page** | ❌ English only |

### 14.4 Font Support

- **English Font:** Plus Jakarta Sans (Google Fonts)
- **Amharic Font:** Noto Sans Ethiopic (Google Fonts) — full Ethiopic Unicode support

---

## 15. Frontend Specifications

### 15.1 Design System

| Aspect | Specification |
|---|---|
| **Framework** | Tailwind CSS v4 with custom theme |
| **Color Scheme** | Dark/Light mode with toggle |
| **Primary Color** | `#0066FF` (blue) with full scale |
| **Secondary Color** | `#06B6D4` (cyan) |
| **Typography** | Plus Jakarta Sans (Latin) + Noto Sans Ethiopic (Amharic) |
| **Approach** | Mobile-first responsive |
| **Dark Mode** | Class-based (`.dark`) with localStorage persistence |

### 15.2 JavaScript Features (app.js — 911 lines)

| Feature | Description |
|---|---|
| **Theme Toggle** | Dark/light mode with system preference detection |
| **Mobile Menu** | Hamburger menu with body scroll lock |
| **Scroll Navbar** | Hide on scroll down, show on scroll up |
| **Dropdown Menus** | Desktop hover dropdowns with timeout cleanup |
| **Scroll Animations** | IntersectionObserver-based `[data-animate]` entrance effects |
| **Animated Counters** | Number counting animation `[data-counter]` |
| **Testimonial Carousel** | Dots, prev/next, touch/swipe, autoplay |
| **Tab System** | `[data-tab-group]` with active state management |
| **FAQ Accordion** | Expand/collapse with smooth animation |
| **Pricing Toggle** | Monthly/annual pricing switch |
| **Image Lightbox** | Full-screen image viewer |
| **Copy to Clipboard** | URL and code copy with toast feedback |
| **Toast Notifications** | Temporary floating messages |
| **Form Validation** | Client-side required, email, phone validation |
| **Newsletter Form** | AJAX submission without page reload |
| **Smooth Scroll** | Anchor link smooth scrolling |
| **PWA Install** | Install prompt handling with bottom sheet UI |
| **Language Toggle** | Cookie-based language switch |
| **Back to Top** | Floating scroll-to-top button |
| **Lazy Loading** | Image lazy load with `[data-src]` |
| **Search Toggle** | Expandable search input |
| **Portfolio Filters** | Animated filter transition for portfolio grid |
| **Character Counter** | Textarea character count display |
| **Admin Utilities** | Image preview, confirm delete, slug generator, rich text toolbar, table sorting |

### 15.3 Custom CSS Animations

| Animation | Effect |
|---|---|
| `float` | Gentle floating movement |
| `glow` | Pulsing glow shadow effect |
| `slideUp` | Slide up entrance |
| `slideDown` | Slide down entrance |
| `fadeIn` | Fade in opacity |

### 15.4 Responsive Breakpoints

Following Tailwind CSS defaults:
| Breakpoint | Size |
|---|---|
| `sm` | 640px |
| `md` | 768px |
| `lg` | 1024px |
| `xl` | 1280px |
| `2xl` | 1536px |

---

## 16. SEO & Analytics

### 16.1 SEO Features

| Feature | Implementation |
|---|---|
| **Meta Tags** | Dynamic title, description per page |
| **Open Graph** | `og:title`, `og:description`, `og:image`, `og:url`, `og:type` |
| **Twitter Card** | `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image` |
| **Canonical URL** | Dynamic canonical tag |
| **Sitemap** | To be implemented |
| **robots.txt** | To be implemented |
| **Admin Pages** | `noindex, nofollow` on all admin routes |
| **Semantic HTML** | `<main>`, `<article>`, `<nav>`, `<section>` tags |
| **Alt Text** | Image alt attributes throughout |
| **Slugs** | SEO-friendly URL slugs for all content |

### 16.2 Analytics System

**Built-in analytics tracking (no third-party scripts):**

| Data Captured | Method |
|---|---|
| Page URL | Current request URL |
| Page Title | `$page_title` variable |
| IP Address | `get_client_ip()` |
| User Agent | `$_SERVER['HTTP_USER_AGENT']` |
| Referrer | `$_SERVER['HTTP_REFERER']` |
| Device Type | User agent parsing (mobile/tablet/desktop) |
| Browser | User agent parsing |
| OS | User agent parsing |
| Session ID | PHP session ID |

**Privacy:** Respects `DNT` (Do Not Track) header  
**Admin Dashboard:** Period-based viewing (today/7d/30d/90d/1y) with growth metrics

### 16.3 Google Analytics Integration

- Setting available: `google_analytics_id` in site_settings
- Placeholder for GA script injection in header

---

## 17. Email & Notifications

### 17.1 Email System

| Feature | Status |
|---|---|
| **Send Method** | PHP `mail()` function |
| **SMTP Support** | Configured in settings (disabled by default) |
| **HTML Templates** | `email_template()` wraps content in styled HTML |
| **Admin Notifications** | `notify_admin()` sends to configured admin email |

### 17.2 Email Triggers

| Event | Email Sent To |
|---|---|
| Contact form submitted | Admin |
| Demo request submitted | Admin |
| Newsletter subscription | Admin |
| New lead created | Admin |

### 17.3 Configurable Settings

| Setting | Key |
|---|---|
| SMTP Host | `smtp_host` |
| SMTP Port | `smtp_port` |
| SMTP Username | `smtp_username` |
| SMTP Password | `smtp_password` |
| From Email | `smtp_from_email` |
| From Name | `smtp_from_name` |

---

## 18. File Upload System

### 18.1 Configuration

| Setting | Value |
|---|---|
| Max Image Size | 5MB |
| Max Document Size | 20MB |
| Upload Directory | `uploads/` |
| Naming | Unique generated filename |

### 18.2 Allowed File Types

| Category | Extensions | MIME Types |
|---|---|---|
| **Images** | jpg, jpeg, png, gif, webp, svg | image/jpeg, image/png, image/gif, image/webp, image/svg+xml |
| **Documents** | pdf, doc, docx, xls, xlsx, zip | application/* |

### 18.3 Upload Process

```
File submitted via form
    ↓
Validate file exists
    ↓
Check MIME type against whitelist
    ↓
Check file extension against whitelist
    ↓
Check file size against limits
    ↓
Generate unique filename: random_string(20) + extension
    ↓
Create folder if not exists (uploads/{folder}/)
    ↓
Move uploaded file to destination
    ↓
Get image dimensions (if image)
    ↓
Record in `media` table
    ↓
Return relative file path
```

### 18.4 Storage Folders

| Folder | Purpose |
|---|---|
| `uploads/general/` | General-purpose uploads |
| `uploads/blog/` | Blog post featured images |
| `uploads/portfolio/` | Portfolio project images and galleries |
| `uploads/team/` | Team member profile photos |
| `uploads/testimonials/` | Client testimonial photos |
| `uploads/products/` | Product-related images |

---

## 19. Development Checklist

### 19.1 Environment & Infrastructure Setup

- [ ] Set up Apache web server with PHP 8.0+
- [ ] Install MySQL/MariaDB 8.0+
- [ ] Configure virtual host (`authopic.com` or production domain)
- [ ] Create `.htaccess` file with URL rewrite rules
- [ ] Install Node.js and npm
- [ ] Run `npm install` for Tailwind CSS
- [ ] Configure `config/database.php` with production credentials
- [ ] Set appropriate file permissions on `uploads/` directory
- [ ] Create upload subdirectories (general, blog, portfolio, team, testimonials, products)

### 19.2 Database Implementation

- [ ] Create MySQL database (`dev_group`) with `utf8mb4` charset
- [ ] Execute `database/schema.sql` — create all 18 tables
- [ ] Execute `database/seed.sql` — insert sample data
- [ ] Verify all foreign key constraints
- [ ] Verify all indexes are created
- [ ] Test database connection from PHP

### 19.3 Core Infrastructure Files

- [ ] Implement `config/database.php` — database connection + configuration constants
- [ ] Implement `includes/functions.php` — all helper functions
  - [ ] Database helper functions (db_escape, db_query, db_fetch_one, db_fetch_all, etc.)
  - [ ] Security functions (e, csrf_token, csrf_field, csrf_verify, rate_limit_check)
  - [ ] URL/routing functions (url, asset, redirect, create_slug)
  - [ ] Content functions (get_setting, get_text, current_lang, format_date, format_etb)
  - [ ] Authentication functions (is_admin_logged_in, require_admin_auth, log_activity)
  - [ ] File upload function (handle_upload)
  - [ ] Flash message functions (set_flash, get_flash, render_flash_messages)
  - [ ] Analytics function (track_page_view)
  - [ ] Pagination functions (paginate, render_pagination)
  - [ ] Email functions (send_email, notify_admin, email_template)
  - [ ] Utility functions (get_json, is_ajax, json_response, post, get, etc.)
- [ ] Implement `index.php` — front controller with routing logic

### 19.4 Public Site — Layout & Templates

- [ ] Implement `includes/header.php` — full HTML head, navbar, language/theme toggles
- [ ] Implement `includes/footer.php` — footer, newsletter form, WhatsApp button, PWA prompt
- [ ] Design and implement responsive navigation with dropdown menus
- [ ] Implement dark/light theme toggle
- [ ] Implement language toggle (EN/AM)
- [ ] Implement mobile hamburger menu

### 19.5 Public Site — Pages

- [ ] Implement `pages/home.php` — Hero, trust bar, products, services, stats, portfolio, testimonials, blog
- [ ] Implement `pages/about.php` — Mission/vision, values, timeline, stats, team grid
- [ ] Implement `pages/contact.php` — Info cards, contact form with validation, maps, social links
- [ ] Implement `pages/portfolio.php` — Portfolio grid with type/service filters, pagination
- [ ] Implement `pages/portfolio-single.php` — Project detail with gallery, metrics, related projects
- [ ] Implement `pages/product-single.php` — Features, tabs, pricing, FAQ, implementation steps, demo form
- [ ] Implement `pages/service-single.php` — Offerings, technologies, process steps, related portfolio
- [ ] Implement `pages/insights.php` — Blog listing with search, category filters, featured post, pagination
- [ ] Implement `pages/blog-single.php` — Article content, author info, share buttons, related posts
- [ ] Implement `pages/search.php` — Cross-table search with type-labeled results
- [ ] Implement `pages/request-demo.php` — Multi-section demo form with sidebar
- [ ] Implement `pages/privacy.php` — Privacy policy static content
- [ ] Implement `pages/thank-you.php` — Dynamic confirmation page (contact/demo/newsletter types)
- [ ] Implement `pages/404.php` — Custom error page
- [ ] Implement `pages/offline.php` — Standalone PWA offline fallback page

### 19.6 Admin Panel — Core

- [ ] Implement `admin/router.php` — Admin routing with auth gating
- [ ] Implement `admin/includes/header.php` — Admin top bar, theme toggle, setup
- [ ] Implement `admin/includes/sidebar.php` — 16-item navigation with badges, mobile overlay
- [ ] Implement `admin/includes/footer.php` — Mobile bottom nav, JS include
- [ ] Implement `admin/login.php` — Login form with rate limiting, brute-force protection
- [ ] Implement `admin/logout.php` — Session destruction, activity logging

### 19.7 Admin Panel — Content Management

- [ ] Implement `admin/dashboard.php` — Stats cards, recent leads/demos, top pages, activity log
- [ ] Implement `admin/pages.php` — Full CRUD with revisions, bilingual, SEO, HTML toolbar
- [ ] Implement `admin/products.php` — CRUD with JSON fields, pagination, slug validation
- [ ] Implement `admin/services.php` — CRUD with JSON fields, pagination
- [ ] Implement `admin/portfolio.php` — CRUD with image upload, type filters, service linking
- [ ] Implement `admin/blog.php` — CRUD with image upload, categories, SEO, read time calculation
- [ ] Implement `admin/team.php` — CRUD with photo upload, social links, card grid view
- [ ] Implement `admin/testimonials.php` — CRUD with photo upload, star ratings, featured flag

### 19.8 Admin Panel — Lead Management

- [ ] Implement `admin/leads.php` — Pipeline view, follow-ups, search, status filters
- [ ] Implement `admin/demos.php` — Status workflow, detail view, status updates

### 19.9 Admin Panel — System Modules

- [ ] Implement `admin/subscribers.php` — List view, search, CSV export, stats
- [ ] Implement `admin/media.php` — Multi-file upload, grid view, copy URL, storage stats, deletion
- [ ] Implement `admin/settings.php` — Key-value settings management (general, company, social, integrations)
- [ ] Implement `admin/profile.php` — Profile edit, password change, activity history
- [ ] Implement `admin/analytics.php` — Period filtering, charts, top pages, referrers, device/browser/OS breakdown

### 19.10 API Endpoints

- [ ] Implement `api/router.php` — API endpoint routing
- [ ] Implement `POST /api/newsletter` — Email subscription with duplicate handling
- [ ] Implement `POST /api/contact` — Contact form with CSRF, honeypot, rate limiting
- [ ] Implement `POST /api/demo` — Demo request with CSRF, honeypot, rate limiting
- [ ] Implement `GET /api/search` — Cross-table search returning JSON results

### 19.11 Frontend Assets

- [ ] Create `assets/css/app.css` — Tailwind v4 source with custom theme tokens
- [ ] Build `assets/css/tailwind.css` — Compiled production CSS
- [ ] Create `assets/css/style.css` — Additional custom styles
- [ ] Implement `assets/js/app.js` — All JavaScript features:
  - [ ] Theme toggle (dark/light mode)
  - [ ] Mobile menu (hamburger, scroll lock)
  - [ ] Scroll-aware navbar (hide on scroll down)
  - [ ] Desktop dropdown menus
  - [ ] Scroll animations (IntersectionObserver)
  - [ ] Animated counters
  - [ ] Testimonial carousel (dots, touch/swipe, autoplay)
  - [ ] Tab system
  - [ ] FAQ accordion
  - [ ] Pricing toggle (monthly/annual)
  - [ ] Image lightbox
  - [ ] Copy to clipboard + toast
  - [ ] Newsletter AJAX form
  - [ ] Client-side form validation
  - [ ] Smooth anchor scrolling
  - [ ] PWA install prompt
  - [ ] Language toggle
  - [ ] Back to top button
  - [ ] Lazy load images
  - [ ] Search toggle
  - [ ] Portfolio filter animation
  - [ ] Character counter for textareas
  - [ ] Admin utilities (image preview, confirm delete, slug generator, rich text toolbar, table sort)

### 19.12 PWA Implementation

- [ ] Create `manifest.json` — App manifest with icons, shortcuts, screenshots
- [ ] Create PWA icons (72, 96, 128, 144, 152, 192, 384, 512px) + maskable variants
- [ ] Create PWA screenshots (desktop + mobile)
- [ ] Implement `sw.js` — Service worker with caching strategies:
  - [ ] Install: pre-cache static assets
  - [ ] Activate: clean old caches, claim clients
  - [ ] Fetch: cache-first for fonts/images, network-first for pages
  - [ ] Skip admin and API routes
  - [ ] Offline fallback to `/offline` page
  - [ ] Push notification handler (placeholder)
- [ ] Add service worker registration script in footer
- [ ] Implement install prompt bottom sheet in footer
- [ ] Add iOS install instructions

### 19.13 Security Implementation

- [ ] CSRF token generation and verification on all forms
- [ ] Rate limiting on all public form submissions
- [ ] Honeypot fields on all public forms
- [ ] XSS prevention via `e()` on all output
- [ ] SQL injection prevention via `db_escape()` on all queries
- [ ] Input validation (email, phone, required fields)
- [ ] Direct access prevention on all include files
- [ ] Session security settings (httponly, SameSite, strict mode)
- [ ] Password hashing with bcrypt
- [ ] Admin login rate limiting with lockout

### 19.14 Internationalization (i18n)

- [ ] Database columns with `_en` and `_am` suffix pairs
- [ ] `get_text()` function with language detection and English fallback
- [ ] Language toggle in navbar (EN ↔ አማ)
- [ ] Cookie-based language persistence (365 day expiry)
- [ ] All public page labels and UI text bilingual
- [ ] Contact form labels and error messages bilingual
- [ ] Navigation menu labels bilingual
- [ ] Admin panel: English only (acceptable)

### 19.15 SEO & Performance

- [ ] Dynamic meta tags on all pages (title, description)
- [ ] Open Graph tags on all pages
- [ ] Twitter Card tags on all pages
- [ ] Canonical URLs
- [ ] SEO-friendly URL slugs
- [ ] `noindex, nofollow` on admin pages
- [ ] Semantic HTML5 elements
- [ ] Image alt attributes
- [ ] Lazy loading for off-screen images
- [ ] Minified production CSS
- [ ] Service worker caching for performance
- [ ] Google Fonts preconnect

### 19.16 Testing & QA

- [ ] Test all public pages render correctly (desktop + mobile)
- [ ] Test dark mode on all pages
- [ ] Test Amharic language on all pages
- [ ] Test contact form submission and validation
- [ ] Test demo request form submission and validation
- [ ] Test newsletter subscription (new + resubscribe + duplicate)
- [ ] Test search functionality across all content types
- [ ] Test admin login (valid + invalid + rate limited)
- [ ] Test all admin CRUD operations (create, read, update, delete)
- [ ] Test file upload (valid + invalid types + oversized)
- [ ] Test admin analytics dashboard with data
- [ ] Test CSV export of subscribers
- [ ] Test pagination on all list pages
- [ ] Test filters (portfolio type, blog category, lead status, demo status)
- [ ] Test PWA installation (Chrome, Edge, mobile browsers)
- [ ] Test offline mode (service worker fallback)
- [ ] Test CSRF protection (expired/missing tokens)
- [ ] Test rate limiting (exceed limits)
- [ ] Test honeypot spam detection
- [ ] Test 404 page for invalid URLs
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] Mobile responsive testing (320px — 1920px)
- [ ] Accessibility audit (keyboard navigation, screen readers)
- [ ] Lighthouse audit (Performance, Accessibility, Best Practices, SEO, PWA)

### 19.17 Deployment

- [ ] Change debug mode to OFF in production
- [ ] Update database credentials for production
- [ ] Update `SITE_URL` to production domain
- [ ] Configure SMTP settings for production email
- [ ] Set up SSL certificate (HTTPS)
- [ ] Configure production `.htaccess`
- [ ] Build production CSS (`npm run build:css`)
- [ ] Set file permissions (uploads writable, config read-only)
- [ ] Remove or protect seed data
- [ ] Change default admin passwords
- [ ] Set up database backups
- [ ] Set up error logging (disable display_errors)
- [ ] Test all features in production environment
- [ ] Submit sitemap to search engines
- [ ] Set up monitoring / uptime checks

---

## 20. Deployment Guide

### 20.1 Server Requirements

| Requirement | Minimum |
|---|---|
| PHP | 8.0+ |
| MySQL/MariaDB | 8.0+ / 10.6+ |
| Apache | 2.4+ with `mod_rewrite` |
| Node.js | 16+ (build only) |
| HTTPS | Required (SSL certificate) |
| Storage | 1GB+ (for uploads) |

### 20.2 Apache `.htaccess` Configuration

```apache
RewriteEngine On
RewriteBase /

# Redirect to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Don't rewrite existing files and directories
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Route everything through index.php
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"

# Deny access to sensitive files
<FilesMatch "\.(sql|md|json|lock)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Deny access to config directory
<IfModule mod_rewrite.c>
    RewriteRule ^config/ - [F,L]
    RewriteRule ^database/ - [F,L]
</IfModule>
```

### 20.3 Deployment Steps

1. **Clone repository** to web server document root
2. **Create database** and import `database/schema.sql`
3. **Import seed data** (optional): `database/seed.sql`
4. **Configure** `config/database.php`:
   - Set production DB credentials
   - Set `SITE_URL` to production domain
   - Set `DEBUG_MODE` to `false`
   - Configure SMTP settings
5. **Install npm dependencies:** `npm install`
6. **Build CSS:** `npm run build:css`
7. **Set permissions:**
   - `uploads/` — writable (755 or 775)
   - `config/` — read-only (644)
8. **Configure Apache:** Enable `mod_rewrite`, set up virtual host
9. **SSL Certificate:** Install via Let's Encrypt or hosting provider
10. **Security:** Change default admin passwords, review rate limits
11. **Test:** Verify all pages, forms, and admin functions work

---

## 21. Environment Configuration

### 21.1 Configuration Constants (`config/database.php`)

| Constant | Description | Dev Default |
|---|---|---|
| `DB_HOST` | Database hostname | `localhost` |
| `DB_NAME` | Database name | `dev_group` |
| `DB_USER` | Database username | `root` |
| `DB_PASS` | Database password | `0000` |
| `SITE_URL` | Base URL | `https://authopic.com` |
| `SITE_NAME` | Display name | `Authopic Technologies PLC` |
| `SITE_VERSION` | App version | `1.0.0` |
| `BASE_PATH` | File system root path | `__DIR__ . '/..'` |
| `UPLOAD_PATH` | Upload directory | `BASE_PATH . '/uploads'` |
| `MAX_IMAGE_SIZE` | Image upload limit | `5 * 1024 * 1024` (5MB) |
| `MAX_FILE_SIZE` | Document upload limit | `20 * 1024 * 1024` (20MB) |
| `SESSION_LIFETIME` | Session timeout | `1800` (30 min) |
| `MAX_LOGIN_ATTEMPTS` | Login rate limit | `5` |
| `LOGIN_LOCKOUT_TIME` | Lockout duration | `900` (15 min) |
| `MAX_FORM_SUBMISSIONS` | Form rate limit | `10` per hour |
| `DEBUG_MODE` | Show errors | `true` (disable in prod) |

### 21.2 Build Commands

| Command | Purpose |
|---|---|
| `npm install` | Install Tailwind CSS v4 dependency |
| `npm run build:css` | Compile & minify: `app.css` → `tailwind.css` |
| `npm run dev:css` | Watch mode development |

---

## 22. Testing Plan

### 22.1 Unit Testing Areas

| Area | Test Cases |
|---|---|
| `db_escape()` | Special characters, Unicode, injection attempts |
| `csrf_token()` / `csrf_verify()` | Token generation, validation, expiration |
| `rate_limit_check()` | Counting, window expiry, IP tracking |
| `is_valid_email()` | Valid/invalid email formats |
| `is_valid_phone()` | Ethiopian phone formats (+251, 09, 07) |
| `is_spam_honeypot()` | Filled vs empty honeypot field |
| `create_slug()` | Unicode, special chars, spaces, duplicates |
| `get_text()` | EN/AM selection, fallback to English |
| `format_etb()` | Number formatting with ETB symbol |
| `handle_upload()` | Valid/invalid files, MIME checks, size limits |

### 22.2 Integration Testing Areas

| Flow | Test Steps |
|---|---|
| **Contact submission** | Fill form → validate → save to DB → notify admin → redirect |
| **Demo request** | Fill form → validate → save → notify → redirect |
| **Newsletter subscribe** | Submit email → check duplicate → save/resubscribe → notify |
| **Admin login** | Enter credentials → verify → create session → redirect |
| **Admin CRUD** | Create item → verify in list → edit → verify changes → delete → verify removed |
| **File upload** | Upload image → verify saved → verify in media library → delete → verify removed |
| **Search** | Enter query → verify results from multiple tables → verify links |
| **Language switch** | Toggle language → verify cookie set → verify content changes → refresh → verify persists |

### 22.3 Browser Compatibility

| Browser | Versions |
|---|---|
| Chrome | 90+ |
| Firefox | 88+ |
| Safari | 14+ |
| Edge | 90+ |
| Mobile Chrome | Latest |
| Mobile Safari | Latest |

---

## 23. Risk Assessment

### 23.1 Technical Risks

| Risk | Severity | Mitigation |
|---|---|---|
| **SQL injection via string interpolation** | High | Use `db_escape()` consistently; consider migrating to prepared statements |
| **No role enforcement in admin** | Medium | Implement per-page permission checks using `admin_has_permission()` |
| **Single-file function library** | Medium | Manageable at current scale; consider splitting if >1000 lines |
| **No automated tests** | Medium | Manual testing plan defined; consider adding PHPUnit |
| **SMTP not configured** | Low | Email notifications won't send until SMTP is set up |
| **No content versioning rollback** | Low | Revisions are stored but no UI to restore them |
| **No image resizing/optimization** | Low | Large uploads may affect performance; consider server-side resizing |
| **No CAPTCHA** | Low | Honeypot + rate limiting provide basic protection; add reCAPTCHA for higher security |

### 23.2 Business Risks

| Risk | Severity | Mitigation |
|---|---|---|
| **Single admin can access everything** | Medium | Implement role-based access control enforcement |
| **No automated backups** | High | Set up cron-based database + file backups |
| **No staging environment** | Medium | Set up staging server for testing before production changes |
| **Manual content entry** | Low | Admin CMS provides sufficient content management tools |

---

## 24. Glossary

| Term | Definition |
|---|---|
| **CMS** | Content Management System — admin panel for managing website content |
| **CRM** | Customer Relationship Management — lead tracking and follow-up system |
| **CSRF** | Cross-Site Request Forgery — attack prevented via per-session tokens |
| **XSS** | Cross-Site Scripting — attack prevented via output encoding |
| **PWA** | Progressive Web App — installable web app with offline support |
| **ERP** | Enterprise Resource Planning — business management software product |
| **SMS** | School Management System — education management software product |
| **ETB** | Ethiopian Birr — Ethiopian currency unit |
| **EAT** | East Africa Time — UTC+03:00 timezone |
| **i18n** | Internationalization — multilingual support (English + Amharic) |
| **EN** | English language content |
| **AM** | Amharic (አማርኛ) language content |
| **Honeypot** | Hidden form field used to detect automated spam submissions |
| **Rate Limiting** | Restricting the number of requests within a time window |
| **Slug** | URL-friendly version of a title (lowercase, hyphens, no special chars) |
| **CRUD** | Create, Read, Update, Delete — the four basic data operations |
| **Seed Data** | Sample/test data inserted into the database for development |
| **Flash Message** | One-time notification displayed after a page redirect |
| **Bcrypt** | Password hashing algorithm (used via `password_hash()`) |
| **Service Worker** | JavaScript running in background for caching and offline support |
| **Web Manifest** | JSON file that tells the browser how to install the app as PWA |
| **Stale While Revalidate** | Caching strategy: serve cached version while fetching fresh copy |

---

## Document History

| Version | Date | Author | Changes |
|---|---|---|---|
| 1.0 | 2026-02-27 | Development Team | Initial full documentation |

---

*This document serves as the complete pre-development planning reference for the Authopic Technologies PLC project. All features, database schemas, file structures, and implementation details listed above should be developed and verified against this specification.*
