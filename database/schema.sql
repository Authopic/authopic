-- ============================================
-- Authopic Technologies PLC - Complete Database Schema
-- Version: 1.0.0
-- Compatible with MySQL 5.7+ / MariaDB 10.3+
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+03:00"; -- East Africa Time

-- ============================================
-- TABLE: admin_users
-- ============================================
CREATE TABLE `admin_users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin','editor','viewer') NOT NULL DEFAULT 'editor',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `remember_token` VARCHAR(255) DEFAULT NULL,
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `reset_expires` DATETIME DEFAULT NULL,
  `login_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: pages
-- ============================================
CREATE TABLE `pages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title_en` VARCHAR(255) NOT NULL,
  `title_am` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content_en` LONGTEXT DEFAULT NULL,
  `content_am` LONGTEXT DEFAULT NULL,
  `excerpt_en` TEXT DEFAULT NULL,
  `excerpt_am` TEXT DEFAULT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `template` VARCHAR(50) DEFAULT 'default',
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `sort_order` INT DEFAULT 0,
  `views` INT UNSIGNED DEFAULT 0,
  `author_id` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FULLTEXT KEY `ft_pages` (`title_en`, `content_en`, `excerpt_en`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: page_revisions
-- ============================================
CREATE TABLE `page_revisions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_id` INT UNSIGNED NOT NULL,
  `content_en` LONGTEXT DEFAULT NULL,
  `content_am` LONGTEXT DEFAULT NULL,
  `revised_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_page` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: products
-- ============================================
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name_en` VARCHAR(255) NOT NULL,
  `name_am` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `type` ENUM('sms','erp') NOT NULL,
  `tagline_en` VARCHAR(255) DEFAULT NULL,
  `tagline_am` VARCHAR(255) DEFAULT NULL,
  `description_en` LONGTEXT DEFAULT NULL,
  `description_am` LONGTEXT DEFAULT NULL,
  `features` JSON DEFAULT NULL,
  `pricing_tiers` JSON DEFAULT NULL,
  `gallery` JSON DEFAULT NULL,
  `implementation_steps` JSON DEFAULT NULL,
  `faq` JSON DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
  `views` INT UNSIGNED DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_slug` (`slug`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: services
-- ============================================
CREATE TABLE `services` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name_en` VARCHAR(255) NOT NULL,
  `name_am` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `tagline_en` VARCHAR(255) DEFAULT NULL,
  `tagline_am` VARCHAR(255) DEFAULT NULL,
  `description_en` LONGTEXT DEFAULT NULL,
  `description_am` LONGTEXT DEFAULT NULL,
  `offerings` JSON DEFAULT NULL,
  `technologies` JSON DEFAULT NULL,
  `process_steps` JSON DEFAULT NULL,
  `features` JSON DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
  `views` INT UNSIGNED DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: portfolio
-- ============================================
CREATE TABLE `portfolio` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title_en` VARCHAR(255) NOT NULL,
  `title_am` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `client_name` VARCHAR(255) DEFAULT NULL,
  `client_logo` VARCHAR(255) DEFAULT NULL,
  `type` ENUM('sms','erp','website','webapp') NOT NULL,
  `industry` VARCHAR(100) DEFAULT NULL,
  `completion_date` DATE DEFAULT NULL,
  `challenge_en` LONGTEXT DEFAULT NULL,
  `challenge_am` LONGTEXT DEFAULT NULL,
  `solution_en` LONGTEXT DEFAULT NULL,
  `solution_am` LONGTEXT DEFAULT NULL,
  `results_en` LONGTEXT DEFAULT NULL,
  `results_am` LONGTEXT DEFAULT NULL,
  `technologies` JSON DEFAULT NULL,
  `metrics` JSON DEFAULT NULL,
  `gallery` JSON DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `testimonial_quote` TEXT DEFAULT NULL,
  `testimonial_name` VARCHAR(100) DEFAULT NULL,
  `testimonial_position` VARCHAR(100) DEFAULT NULL,
  `testimonial_photo` VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
  `views` INT UNSIGNED DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FULLTEXT KEY `ft_portfolio` (`title_en`, `challenge_en`, `solution_en`),
  KEY `idx_slug` (`slug`),
  KEY `idx_type` (`type`),
  KEY `idx_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog_categories
-- ============================================
CREATE TABLE `blog_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name_en` VARCHAR(100) NOT NULL,
  `name_am` VARCHAR(100) DEFAULT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description_en` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog_posts
-- ============================================
CREATE TABLE `blog_posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title_en` VARCHAR(255) NOT NULL,
  `title_am` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content_en` LONGTEXT DEFAULT NULL,
  `content_am` LONGTEXT DEFAULT NULL,
  `excerpt_en` TEXT DEFAULT NULL,
  `excerpt_am` TEXT DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `tags` VARCHAR(500) DEFAULT NULL,
  `author_id` INT UNSIGNED DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_gated` TINYINT(1) DEFAULT 0,
  `gated_pdf` VARCHAR(255) DEFAULT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `status` ENUM('draft','published','scheduled','archived') NOT NULL DEFAULT 'draft',
  `publish_date` DATETIME DEFAULT NULL,
  `views` INT UNSIGNED DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FULLTEXT KEY `ft_blog` (`title_en`, `content_en`, `excerpt_en`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_category` (`category_id`),
  KEY `idx_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: team_members
-- ============================================
CREATE TABLE `team_members` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name_en` VARCHAR(100) NOT NULL,
  `name_am` VARCHAR(100) DEFAULT NULL,
  `position_en` VARCHAR(100) NOT NULL,
  `position_am` VARCHAR(100) DEFAULT NULL,
  `bio_en` TEXT DEFAULT NULL,
  `bio_am` TEXT DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `linkedin` VARCHAR(255) DEFAULT NULL,
  `department` VARCHAR(50) DEFAULT NULL,
  `is_leadership` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_leadership` (`is_leadership`),
  KEY `idx_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: testimonials
-- ============================================
CREATE TABLE `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `client_name` VARCHAR(100) NOT NULL,
  `client_position` VARCHAR(100) DEFAULT NULL,
  `company_name` VARCHAR(100) DEFAULT NULL,
  `quote_en` TEXT NOT NULL,
  `quote_am` TEXT DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `rating` TINYINT UNSIGNED DEFAULT 5,
  `is_featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `related_product` VARCHAR(50) DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_featured` (`is_featured`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: leads
-- ============================================
CREATE TABLE `leads` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `company` VARCHAR(150) DEFAULT NULL,
  `interest` VARCHAR(100) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `source` VARCHAR(50) DEFAULT 'website',
  `source_page` VARCHAR(255) DEFAULT NULL,
  `utm_source` VARCHAR(100) DEFAULT NULL,
  `utm_medium` VARCHAR(100) DEFAULT NULL,
  `utm_campaign` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('new','contacted','qualified','converted','lost') NOT NULL DEFAULT 'new',
  `assigned_to` INT UNSIGNED DEFAULT NULL,
  `lead_score` INT DEFAULT 0,
  `budget_range` VARCHAR(50) DEFAULT NULL,
  `timeline` VARCHAR(50) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_created` (`created_at`),
  KEY `idx_assigned` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: lead_followups
-- ============================================
CREATE TABLE `lead_followups` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT UNSIGNED NOT NULL,
  `admin_id` INT UNSIGNED NOT NULL,
  `type` ENUM('call','email','meeting','note') NOT NULL DEFAULT 'note',
  `notes` TEXT NOT NULL,
  `next_followup` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_lead` (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: demo_requests
-- ============================================
CREATE TABLE `demo_requests` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `company` VARCHAR(150) DEFAULT NULL,
  `product` ENUM('sms','erp','both') NOT NULL DEFAULT 'sms',
  `preferred_date` DATE NOT NULL,
  `preferred_time` TIME NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `status` ENUM('pending','confirmed','completed','cancelled','rescheduled') NOT NULL DEFAULT 'pending',
  `admin_notes` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_status` (`status`),
  KEY `idx_date` (`preferred_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: newsletter_subscribers
-- ============================================
CREATE TABLE `newsletter_subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `name` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
  `is_confirmed` TINYINT(1) DEFAULT 0,
  `confirm_token` VARCHAR(255) DEFAULT NULL,
  `unsubscribe_token` VARCHAR(255) DEFAULT NULL,
  `subscribed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at` DATETIME DEFAULT NULL,
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: media
-- ============================================
CREATE TABLE `media` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_type` VARCHAR(50) NOT NULL,
  `file_size` INT UNSIGNED DEFAULT 0,
  `mime_type` VARCHAR(100) DEFAULT NULL,
  `alt_text_en` VARCHAR(255) DEFAULT NULL,
  `alt_text_am` VARCHAR(255) DEFAULT NULL,
  `width` INT UNSIGNED DEFAULT NULL,
  `height` INT UNSIGNED DEFAULT NULL,
  `uploaded_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_type` (`file_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: site_settings
-- ============================================
CREATE TABLE `site_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `setting_type` ENUM('text','textarea','image','boolean','json','email','url') NOT NULL DEFAULT 'text',
  `setting_group` VARCHAR(50) DEFAULT 'general',
  `label` VARCHAR(100) DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: navigation_menus
-- ============================================
CREATE TABLE `navigation_menus` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `location` ENUM('header','footer') NOT NULL DEFAULT 'header',
  `label_en` VARCHAR(100) NOT NULL,
  `label_am` VARCHAR(100) DEFAULT NULL,
  `url` VARCHAR(255) NOT NULL,
  `parent_id` INT UNSIGNED DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `open_new_tab` TINYINT(1) DEFAULT 0,
  KEY `idx_location` (`location`),
  KEY `idx_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: site_analytics
-- ============================================
CREATE TABLE `site_analytics` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_url` VARCHAR(500) NOT NULL,
  `page_title` VARCHAR(255) DEFAULT NULL,
  `referrer` VARCHAR(500) DEFAULT NULL,
  `utm_source` VARCHAR(100) DEFAULT NULL,
  `utm_medium` VARCHAR(100) DEFAULT NULL,
  `utm_campaign` VARCHAR(100) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `device_type` ENUM('desktop','mobile','tablet') DEFAULT 'desktop',
  `browser` VARCHAR(50) DEFAULT NULL,
  `os` VARCHAR(50) DEFAULT NULL,
  `country` VARCHAR(50) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `session_id` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_page` (`page_url`(191)),
  KEY `idx_created` (`created_at`),
  KEY `idx_session` (`session_id`(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: activity_log
-- ============================================
CREATE TABLE `activity_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `admin_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(50) NOT NULL,
  `entity_type` VARCHAR(50) DEFAULT NULL,
  `entity_id` INT UNSIGNED DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_admin` (`admin_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: rate_limits
-- ============================================
CREATE TABLE `rate_limits` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `attempts` INT UNSIGNED DEFAULT 1,
  `first_attempt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_attempt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_ip_action` (`ip_address`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: csrf_tokens
-- ============================================
CREATE TABLE `csrf_tokens` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(128) NOT NULL,
  `token` VARCHAR(128) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_session` (`session_id`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
