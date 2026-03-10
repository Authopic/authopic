-- ============================================
-- Authopic Technologies PLC - Sample Data / Seed Data
-- ============================================
USE `authopic_db`;

-- ============================================
-- Default Admin User (password: Admin@2026!)
-- ============================================
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `full_name`, `role`, `is_active`) VALUES
('admin', 'admin@authopic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 1),
('editor', 'editor@authopic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Content Editor', 'editor', 1);

-- ============================================
-- Site Settings
-- ============================================
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `label`) VALUES
('site_name', 'Authopic Technologies PLC', 'text', 'general', 'Site Name'),
('site_tagline', 'Modern Digital Solutions', 'text', 'general', 'Site Tagline'),
('site_email', 'info@authopic.com', 'email', 'general', 'Contact Email'),
('site_phone', '+251-904-455302', 'text', 'general', 'Phone Number'),
('site_phone2', '+251-922-654321', 'text', 'general', 'Secondary Phone'),
('site_address', 'Bole Road, Addis Ababa, Ethiopia', 'text', 'general', 'Office Address'),
('site_map_lat', '9.0192', 'text', 'general', 'Map Latitude'),
('site_map_lng', '38.7525', 'text', 'general', 'Map Longitude'),
('site_logo', '/assets/images/logo.png', 'image', 'general', 'Site Logo'),
('site_logo_light', '/assets/images/logo-light.png', 'image', 'general', 'Site Logo (Light)'),
('site_favicon', '/assets/images/favicon.ico', 'image', 'general', 'Favicon'),
('social_linkedin', 'https://linkedin.com/company/authopic-technologies', 'url', 'social', 'LinkedIn'),
('social_telegram', 'https://t.me/authopic_tech', 'url', 'social', 'Telegram'),
('social_whatsapp', '+251911123456', 'text', 'social', 'WhatsApp Number'),
('social_facebook', 'https://facebook.com/authopic.comnologies', 'url', 'social', 'Facebook'),
('social_twitter', 'https://twitter.com/authopic_tech', 'url', 'social', 'Twitter/X'),
('social_youtube', '', 'url', 'social', 'YouTube'),
('meta_title', 'Authopic Technologies PLC - Modern Digital Solutions', 'text', 'seo', 'Default Meta Title'),
('meta_description', 'Leading software development company in Addis Ababa, Ethiopia. School Management Systems, ERP Solutions, and Custom Web Development.', 'textarea', 'seo', 'Default Meta Description'),
('meta_keywords', 'software company ethiopia, school management system, erp ethiopia, web development addis ababa', 'textarea', 'seo', 'Default Meta Keywords'),
('google_analytics', '', 'text', 'seo', 'Google Analytics ID'),
('smtp_host', '', 'text', 'email', 'SMTP Host'),
('smtp_port', '587', 'text', 'email', 'SMTP Port'),
('smtp_username', '', 'text', 'email', 'SMTP Username'),
('smtp_password', '', 'text', 'email', 'SMTP Password'),
('smtp_encryption', 'tls', 'text', 'email', 'SMTP Encryption'),
('smtp_from_name', 'Authopic Technologies PLC', 'text', 'email', 'From Name'),
('smtp_from_email', 'noreply@authopic.com', 'email', 'email', 'From Email'),
('office_hours_weekday', '8:30 AM - 5:30 PM', 'text', 'general', 'Weekday Hours'),
('office_hours_saturday', '9:00 AM - 1:00 PM', 'text', 'general', 'Saturday Hours'),
('office_hours_sunday', 'Closed', 'text', 'general', 'Sunday Hours'),
('maintenance_mode', '0', 'boolean', 'general', 'Maintenance Mode');

-- ============================================
-- Navigation Menus
-- ============================================
INSERT INTO `navigation_menus` (`location`, `label_en`, `label_am`, `url`, `parent_id`, `sort_order`, `is_active`) VALUES
('header', 'Home', 'መነሻ', '/', NULL, 1, 1),
('header', 'Products', 'ምርቶች', '#', NULL, 2, 1),
('header', 'School Management', 'የትምህርት ቤት አስተዳደር', '/products/sms', 2, 1, 1),
('header', 'ERP System', 'ኢአርፒ ሲስተም', '/products/erp', 2, 2, 1),
('header', 'Services', 'አገልግሎቶች', '#', NULL, 3, 1),
('header', 'Website Development', 'ዌብሳይት ልማት', '/services/website-development', 5, 1, 1),
('header', 'Web App Development', 'ዌብ አፕ ልማት', '/services/web-application-development', 5, 2, 1),
('header', 'Portfolio', 'ፖርትፎሊዮ', '/portfolio', NULL, 4, 1),
('header', 'Insights', 'ግንዛቤዎች', '/insights', NULL, 5, 1),
('header', 'About', 'ስለ እኛ', '/about', NULL, 6, 1),
('header', 'Contact', 'ያግኙን', '/contact', NULL, 7, 1),
('footer', 'Home', 'መነሻ', '/', NULL, 1, 1),
('footer', 'Products', 'ምርቶች', '#', NULL, 2, 1),
('footer', 'Services', 'አገልግሎቶች', '#', NULL, 3, 1),
('footer', 'Portfolio', 'ፖርትፎሊዮ', '/portfolio', NULL, 4, 1),
('footer', 'Insights', 'ግንዛቤዎች', '/insights', NULL, 5, 1),
('footer', 'About', 'ስለ እኛ', '/about', NULL, 6, 1),
('footer', 'Contact', 'ያግኙን', '/contact', NULL, 7, 1),
('footer', 'Request Demo', 'ዲሞ ይጠይቁ', '/request-demo', NULL, 8, 1),
('footer', 'Privacy Policy', 'የግላዊነት ፖሊሲ', '/privacy', NULL, 9, 1);

-- ============================================
-- Products
-- ============================================
INSERT INTO `products` (`name_en`, `name_am`, `slug`, `type`, `tagline_en`, `tagline_am`, `description_en`, `description_am`, `features`, `pricing_tiers`, `implementation_steps`, `faq`, `status`) VALUES
(
  'School Management System',
  'የትምህርት ቤት አስተዳደር ሲስተም',
  'sms',
  'sms',
  'Complete Digital Solution for Ethiopian Schools',
  'ለኢትዮጵያ ትምህርት ቤቶች ሙሉ ዲጂታል መፍትሔ',
  'Our School Management System is specifically designed for Ethiopian schools, with full Amharic language support, attendance tracking, exam management, fee collection, parent communication portal, and comprehensive reporting. Trusted by over 50 schools across Addis Ababa and beyond.',
  'የትምህርት ቤት አስተዳደር ሲስተማችን በተለይ ለኢትዮጵያ ትምህርት ቤቶች የተነደፈ ሲሆን ሙሉ የአማርኛ ቋንቋ ድጋፍ፣ የመገኘት ክትትል፣ የፈተና አስተዳደር፣ የክፍያ ስብስብ፣ የወላጆች የግንኙነት ፖርታል እና አጠቃላይ ሪፖርት ያካትታል።',
  '["Student Registration & Enrollment","Attendance Tracking","Exam & Grade Management","Fee Collection & Accounting","Parent Communication Portal","Teacher Dashboard","Automated Report Cards","SMS & Email Notifications","Amharic Language Support","Timetable Management","Library Management","Transport Management"]',
  '[{"name":"Basic","price_setup":50000,"price_monthly":3500,"price_annual":35000,"students":"Up to 300","features":["Student Registration","Attendance","Grade Management","Basic Reports","Email Support"],"popular":false},{"name":"Standard","price_setup":75000,"price_monthly":5500,"price_annual":55000,"students":"Up to 800","features":["All Basic Features","Parent Portal","SMS Notifications","Advanced Reports","Fee Management","Priority Support"],"popular":true},{"name":"Premium","price_setup":150000,"price_monthly":8500,"price_annual":85000,"students":"Unlimited","features":["All Standard Features","Library Management","Transport Tracking","Custom Reports","API Access","Dedicated Account Manager","On-site Training"],"popular":false}]',
  '[{"step":1,"title":"On-site Consultation","description":"We visit your school, understand your workflow, and create a customized plan.","icon":"clipboard-check"},{"step":2,"title":"Data Migration","description":"We migrate your existing student records and data into the system securely.","icon":"database"},{"step":3,"title":"Staff Training","description":"Comprehensive hands-on training for administrators, teachers, and staff.","icon":"academic-cap"},{"step":4,"title":"Go-Live Support","description":"We provide on-site support during the first week and ongoing remote support.","icon":"rocket-launch"}]',
  '[{"question":"Does the system support Amharic language?","answer":"Yes, our system has full Amharic language support including student names, reports, and the entire interface."},{"question":"Can parents access the system?","answer":"Yes, with Standard and Premium plans, parents get a dedicated portal to view grades, attendance, fees, and communicate with teachers."},{"question":"What happens if we lose internet?","answer":"The system can work offline for basic functions and syncs automatically when connection is restored."},{"question":"How long does implementation take?","answer":"Typical implementation takes 2-4 weeks depending on school size and data migration needs."},{"question":"Is our data secure?","answer":"Absolutely. We use industry-standard encryption and your data is backed up daily on secure Ethiopian servers."},{"question":"Can we customize the system?","answer":"Yes, Premium plan includes customization options. We can also build custom modules for specific needs."}]',
  'published'
),
(
  'Enterprise ERP Suite',
  'የድርጅት ኢአርፒ ሲስተም',
  'erp',
  'erp',
  'Integrated Business Management for Ethiopian Enterprises',
  'ለኢትዮጵያ ድርጅቶች የተቀናጀ የንግድ አስተዳደር',
  'Our Enterprise ERP Suite provides comprehensive business management solutions tailored for Ethiopian companies. From HR and Finance to Inventory and CRM, manage your entire operation from a single platform. Integrated with Ethiopian banks and tax systems.',
  'የድርጅት ኢአርፒ ስብስባችን ለኢትዮጵያ ኩባንያዎች የተበጀ አጠቃላይ የንግድ አስተዳደር መፍትሔዎችን ይሰጣል። ከኤችአር እና ፋይናንስ እስከ ኢንቬንቶሪ እና ሲአርኤም ድረስ መላ ስራዎን ከአንድ መድረክ ያስተዳድሩ።',
  '["HR & Payroll Management","Finance & Accounting","Inventory Control","Customer Relationship Management (CRM)","Purchasing & Procurement","Sales & Distribution","Project Management","Business Intelligence & Reporting","Multi-branch Support","Ethiopian Tax Compliance","Bank Integration (CBE, Dashen, Awash)","Mobile Money Integration (Telebirr)"]',
  '[{"name":"Small Business","price_range":"50,000 - 100,000 ETB","modules":"Up to 3 modules","users":"Up to 10 users","features":["Core HR","Basic Accounting","Inventory","Standard Reports","Email Support"]},{"name":"Mid-Market","price_range":"150,000 - 250,000 ETB","modules":"Up to 6 modules","users":"Up to 50 users","features":["All Core Modules","CRM","Advanced Reports","Bank Integration","Priority Support","Training"]},{"name":"Enterprise","price_range":"300,000+ ETB","modules":"All modules","users":"Unlimited","features":["Complete Suite","Custom Modules","API Integration","Dedicated Support","On-site Training","Multi-branch","Custom Reports"]}]',
  '[{"step":1,"title":"Requirements Analysis","description":"Deep dive into your business processes and requirements.","icon":"magnifying-glass"},{"step":2,"title":"System Configuration","description":"Configure modules based on your specific business needs.","icon":"cog"},{"step":3,"title":"Data Migration","description":"Securely migrate your existing business data.","icon":"database"},{"step":4,"title":"User Training","description":"Comprehensive training for all user levels.","icon":"academic-cap"},{"step":5,"title":"Go-Live & Support","description":"Phased go-live with continuous support.","icon":"rocket-launch"}]',
  '[{"question":"Which industries do you serve?","answer":"We serve manufacturing, trading, NGOs, real estate, and service companies across Ethiopia."},{"question":"Can the ERP integrate with Ethiopian banks?","answer":"Yes, we support integration with CBE, Dashen Bank, Awash Bank, and mobile money platforms like Telebirr."},{"question":"Is the system compliant with Ethiopian tax regulations?","answer":"Yes, our system generates reports compliant with Ethiopian Revenue and Customs Authority requirements."},{"question":"How long does ERP implementation take?","answer":"Implementation typically takes 4-12 weeks depending on the number of modules and complexity."},{"question":"Can we start with a few modules and add more later?","answer":"Absolutely. Our modular approach allows you to start small and scale as your needs grow."}]',
  'published'
);

-- ============================================
-- Services
-- ============================================
INSERT INTO `services` (`name_en`, `name_am`, `slug`, `tagline_en`, `description_en`, `offerings`, `technologies`, `process_steps`, `status`) VALUES
(
  'Website Development',
  'ዌብሳይት ልማት',
  'website-development',
  'Professional Websites That Drive Results',
  'We design and develop professional, responsive websites that represent your brand, engage visitors, and generate leads. From corporate sites to e-commerce platforms, we build digital experiences that work for Ethiopian businesses.',
  '[{"name":"Corporate Websites","price_range":"80,000 - 150,000 ETB","description":"Professional company websites with modern design, content management, and SEO optimization.","features":["Custom Design","Mobile Responsive","Content Management","SEO Optimized","Contact Forms","Analytics"]},{"name":"E-commerce Platforms","price_range":"150,000 - 300,000 ETB","description":"Full-featured online stores with payment integration, inventory management, and order tracking.","features":["Product Catalog","Shopping Cart","Payment Gateway","Order Management","Customer Accounts","Inventory Sync"]},{"name":"Dynamic Portals","price_range":"120,000 - 250,000 ETB","description":"Interactive web portals for organizations, membership sites, and service platforms.","features":["User Authentication","Dashboard","Role Management","Data Visualization","API Integration","Real-time Updates"]}]',
  '["PHP","Python","Laravel","Django","HTML5","CSS3","Tailwind CSS","JavaScript","MySQL","PostgreSQL"]',
  '[{"step":1,"title":"Discovery & Planning","description":"We learn about your business, goals, and target audience to create a strategic plan.","icon":"lightbulb"},{"step":2,"title":"Design & Prototyping","description":"Modern UI/UX design with interactive prototypes for your approval.","icon":"paint-brush"},{"step":3,"title":"Development","description":"Clean, efficient code following best practices and modern standards.","icon":"code"},{"step":4,"title":"Testing & QA","description":"Comprehensive testing across devices, browsers, and performance benchmarks.","icon":"check-circle"},{"step":5,"title":"Launch","description":"Smooth deployment with domain setup, SSL, and performance optimization.","icon":"rocket"},{"step":6,"title":"Ongoing Support","description":"Continued maintenance, updates, and technical support.","icon":"life-ring"}]',
  'published'
),
(
  'Web Application Development',
  'ዌብ አፕሊኬሽን ልማት',
  'web-application-development',
  'Custom Web Applications Built for Scale',
  'We build powerful, scalable web applications that automate your business processes, improve efficiency, and drive growth. From custom SaaS platforms to legacy system modernization, we deliver solutions that transform operations.',
  '[{"name":"Custom SaaS Platforms","description":"Build your own software-as-a-service product with subscription management, multi-tenancy, and scalable architecture.","features":["Multi-tenant Architecture","Subscription Billing","REST API","Role-based Access","Analytics Dashboard","Scalable Infrastructure"]},{"name":"Business Process Automation","description":"Automate repetitive tasks, workflows, and business processes to improve efficiency and reduce errors.","features":["Workflow Engine","Task Automation","Email Integration","Document Generation","Approval Workflows","Reporting"]},{"name":"Legacy System Modernization","description":"Modernize outdated systems with modern technology, improved UX, and cloud-ready architecture.","features":["Code Refactoring","Database Migration","API Layer","Modern UI","Performance Optimization","Cloud Migration"]},{"name":"API Development","description":"Design and build robust RESTful APIs for mobile apps, third-party integrations, and microservices.","features":["REST API Design","Authentication","Rate Limiting","Documentation","Versioning","Testing"]}]',
  '["PHP","Laravel","Python","Django","Node.js","React","Vue.js","Tailwind CSS","MySQL","PostgreSQL","Redis","Docker"]',
  '[{"step":1,"title":"Requirements Analysis","description":"Deep dive into your needs, user stories, and technical requirements.","icon":"clipboard"},{"step":2,"title":"Architecture Design","description":"System architecture, database design, and technology selection.","icon":"blueprint"},{"step":3,"title":"Agile Development","description":"Iterative development with regular demos and feedback cycles.","icon":"code"},{"step":4,"title":"Quality Assurance","description":"Automated testing, security audits, and performance optimization.","icon":"shield-check"},{"step":5,"title":"Deployment","description":"CI/CD pipeline setup, cloud deployment, and monitoring.","icon":"cloud-upload"},{"step":6,"title":"Maintenance & Scaling","description":"Ongoing support, feature updates, and infrastructure scaling.","icon":"chart-up"}]',
  'published'
);

-- ============================================
-- Portfolio Items (Sample)
-- ============================================
INSERT INTO `portfolio` (`title_en`, `slug`, `client_name`, `type`, `industry`, `completion_date`, `challenge_en`, `solution_en`, `results_en`, `technologies`, `metrics`, `is_featured`, `status`) VALUES
(
  'Brightstar Academy - Digital Transformation',
  'brightstar-academy',
  'Brightstar International Academy',
  'sms',
  'Education',
  '2025-06-15',
  'Brightstar Academy, a leading private school in Bole with 1,200+ students, was drowning in paperwork. Manual attendance tracking, hand-written report cards, and spreadsheet-based fee management consumed 40+ hours per week of administrative time. Parents had no visibility into their children\'s progress between semester report cards.',
  'We implemented our comprehensive School Management System with full customization for Brightstar\'s unique grading system. The solution included real-time attendance via tablet check-in, automated report card generation in both English and Amharic, integrated fee management with CBE and Dashen bank integration, and a parent portal with mobile-responsive design.',
  'The transformation was dramatic. Administrative workload dropped by 60%, fee collection improved by 35% with online payment options, and parent satisfaction scores increased by 45%. The school now processes report cards in hours instead of weeks.',
  '["PHP","Laravel","MySQL","Tailwind CSS","JavaScript","REST API"]',
  '[{"label":"Admin Time Saved","value":"60%"},{"label":"Fee Collection Improvement","value":"35%"},{"label":"Parent Satisfaction Increase","value":"45%"},{"label":"Implementation Time","value":"3 weeks"}]',
  1,
  'published'
),
(
  'Abyssinia Trading PLC - ERP Implementation',
  'abyssinia-trading-erp',
  'Abyssinia Trading PLC',
  'erp',
  'Manufacturing',
  '2025-03-20',
  'Abyssinia Trading, a mid-size import/export company with 85 employees, operated with disconnected systems — separate software for accounting, a spreadsheet for inventory, and manual HR processes. Data inconsistencies led to stock-outs, delayed payroll, and inaccurate financial reports.',
  'We deployed our ERP Suite with HR, Finance, Inventory, and Sales modules. The system integrated with their existing CBE corporate account for automated reconciliation. Custom import/export documentation module was built to handle Ethiopian customs requirements. Multi-branch support connected their Addis Ababa HQ with Dire Dawa warehouse.',
  'Within 3 months, inventory accuracy improved from 72% to 98%, monthly financial closing reduced from 5 days to 1 day, and payroll processing became fully automated. The ROI was achieved within 8 months of implementation.',
  '["PHP","Laravel","MySQL","Vue.js","REST API","Tailwind CSS"]',
  '[{"label":"Inventory Accuracy","value":"98%"},{"label":"Financial Close Time","value":"1 day"},{"label":"ROI Period","value":"8 months"},{"label":"Process Automation","value":"75%"}]',
  1,
  'published'
),
(
  'Hope Medical Center - Corporate Website',
  'hope-medical-website',
  'Hope Medical Center',
  'website',
  'Healthcare',
  '2025-08-10',
  'Hope Medical Center needed a modern web presence to attract patients and provide online appointment booking. Their existing website was outdated, not mobile-friendly, and had no dynamic content management capabilities.',
  'We designed and developed a modern, fully responsive website with an integrated appointment booking system, doctor profiles with availability calendars, patient resource library, and a content management system for easy updates. The site supports both English and Amharic with automatic language detection.',
  'Website traffic increased by 200% within 2 months of launch. Online appointment bookings now account for 40% of all appointments, reducing phone call volume by 50%. The bilingual content has significantly improved accessibility for all patients.',
  '["PHP","MySQL","Tailwind CSS","JavaScript","HTML5","CSS3"]',
  '[{"label":"Traffic Increase","value":"200%"},{"label":"Online Bookings","value":"40%"},{"label":"Phone Calls Reduced","value":"50%"},{"label":"Page Load Time","value":"1.2s"}]',
  1,
  'published'
),
(
  'Ethiopian Coffee Export Association - Web Portal',
  'coffee-export-portal',
  'Ethiopian Coffee Export Association',
  'webapp',
  'Agriculture',
  '2025-01-15',
  'The association needed a centralized platform for member management, export documentation, quality certifications, and market price updates. Manual processes were causing delays and communication gaps between 200+ member exporters.',
  'We built a custom web application featuring member registration and dashboard, real-time coffee price feeds, export documentation generator, quality certification tracking, and a communication hub. The system includes role-based access for members, inspectors, and administrators.',
  'Member communication time reduced by 70%, documentation processing went from 3 days to 30 minutes, and member satisfaction increased dramatically. The platform now serves as the central hub for the entire association.',
  '["PHP","Laravel","MySQL","Vue.js","REST API","Chart.js"]',
  '[{"label":"Communication Time Saved","value":"70%"},{"label":"Document Processing","value":"30 min"},{"label":"Active Members","value":"200+"},{"label":"Export Docs Generated","value":"5000+"}]',
  0,
  'published'
);

-- ============================================
-- Blog Categories
-- ============================================
INSERT INTO `blog_categories` (`name_en`, `name_am`, `slug`) VALUES
('Technology', 'ቴክኖሎጂ', 'technology'),
('Education', 'ትምህርት', 'education'),
('Business', 'ንግድ', 'business'),
('Product Updates', 'የምርት ማሻሻያዎች', 'product-updates'),
('Guides & Tutorials', 'መመሪያዎች', 'guides'),
('Industry News', 'የኢንዱስትሪ ዜና', 'industry-news');

-- ============================================
-- Blog Posts (Sample)
-- ============================================
INSERT INTO `blog_posts` (`title_en`, `slug`, `content_en`, `excerpt_en`, `category_id`, `tags`, `author_id`, `is_featured`, `status`, `publish_date`, `views`) VALUES
(
  'Why Ethiopian Schools Need Digital Management Systems in 2026',
  'why-ethiopian-schools-need-digital-management',
  '<p>The Ethiopian education sector is undergoing a massive transformation. With over 500 private schools in Addis Ababa alone, the need for efficient digital management has never been more critical.</p><h2>The Challenge</h2><p>Most Ethiopian schools still rely on paper-based record keeping, manual attendance tracking, and spreadsheet-based financial management. This leads to inefficiency, data loss, and poor communication with parents.</p><h2>The Solution</h2><p>Modern School Management Systems (SMS) offer comprehensive digital solutions that automate administrative tasks, improve data accuracy, and enhance parent-teacher communication.</p><h2>Key Benefits</h2><ul><li><strong>Time Savings:</strong> Reduce administrative workload by up to 60%</li><li><strong>Accuracy:</strong> Eliminate manual data entry errors</li><li><strong>Communication:</strong> Real-time updates for parents via portal and SMS</li><li><strong>Financial Management:</strong> Automated fee collection and tracking</li><li><strong>Compliance:</strong> Easy generation of Ministry of Education required reports</li></ul><h2>The Future</h2><p>As Ethiopia moves toward a digital economy, schools that adopt technology early will have a significant competitive advantage. The time to digitize is now.</p>',
  'Discover why Ethiopian private schools are rapidly adopting digital management systems and how technology is transforming education administration in Addis Ababa.',
  2, 'education,school-management,digital-transformation,ethiopia', 1, 1, 'published', '2026-02-15 09:00:00', 234
),
(
  'Top 5 ERP Features Every Ethiopian Business Needs',
  'top-5-erp-features-ethiopian-business',
  '<p>Choosing the right ERP system for your Ethiopian business can be challenging. With so many options available globally, it is essential to find a solution that understands the Ethiopian business environment.</p><h2>1. Ethiopian Tax Compliance</h2><p>Your ERP must generate reports compliant with ERCA requirements, handle withholding tax calculations, and produce proper VAT invoices.</p><h2>2. Multi-Currency Support with ETB Focus</h2><p>While supporting multiple currencies is important for importers/exporters, the system must handle ETB as the primary currency with proper formatting and rounding.</p><h2>3. Local Bank Integration</h2><p>Direct integration with Ethiopian banks like CBE, Dashen, and Awash for automated reconciliation and payment processing.</p><h2>4. Amharic Language Support</h2><p>Full Amharic interface and document generation capability is essential for team adoption and customer-facing documents.</p><h2>5. Offline Capability</h2><p>Given occasional internet connectivity challenges in Ethiopia, the ability to work offline and sync later is crucial.</p>',
  'Learn about the essential ERP features that Ethiopian businesses need for efficient operations, tax compliance, and growth in the local market.',
  3, 'erp,business,ethiopia,enterprise-software', 1, 0, 'published', '2026-02-10 09:00:00', 187
),
(
  'Building a Strong Web Presence for Ethiopian Businesses',
  'building-web-presence-ethiopian-businesses',
  '<p>In 2026, having a professional website is no longer optional for Ethiopian businesses. With internet penetration growing rapidly and mobile usage skyrocketing, your online presence directly impacts your bottom line.</p><h2>Why Your Business Needs a Website</h2><p>Ethiopian consumers are increasingly searching online before making purchasing decisions. If your business is not online, you are invisible to a growing segment of your market.</p><h2>Key Elements of an Effective Business Website</h2><ul><li><strong>Mobile-First Design:</strong> Over 80% of Ethiopian internet users access the web via mobile devices</li><li><strong>Fast Loading:</strong> Optimized for Ethiopian internet speeds</li><li><strong>Bilingual Content:</strong> English and Amharic to reach all customers</li><li><strong>Local SEO:</strong> Optimized for local search terms and Google Maps</li><li><strong>Lead Generation:</strong> Contact forms, WhatsApp integration, and call-to-action buttons</li></ul>',
  'A comprehensive guide to establishing and strengthening your Ethiopian business\'s web presence in 2026, from design principles to local SEO strategies.',
  1, 'web-development,business,digital-marketing,ethiopia', 1, 0, 'published', '2026-02-05 09:00:00', 156
);

-- ============================================
-- Team Members
-- ============================================
INSERT INTO `team_members` (`name_en`, `name_am`, `position_en`, `position_am`, `bio_en`, `department`, `is_leadership`, `sort_order`, `is_active`) VALUES
('Yisak Alemayehu', 'ይሳክ አለማየሁ', 'Founder & CEO', 'መስራች እና ዋና ስራ አስፈፃሚ', 'Visionary tech entrepreneur with 10+ years of experience in software development and business management. Passionate about leveraging technology to solve Ethiopian business challenges.', 'Leadership', 1, 1, 1),
('Meron Tadesse', 'ሜሮን ታደሰ', 'CTO', 'ዋና ቴክኖሎጂ ኃላፊ', 'Full-stack developer turned technology leader. Expert in PHP, Python, and enterprise architecture. 8 years of experience building scalable systems.', 'Leadership', 1, 2, 1),
('Abebe Kebede', 'አበበ ከበደ', 'Lead Developer', 'ዋና ገንቢ', 'Senior software engineer specializing in web applications and database design. 6 years of experience in PHP and Laravel development.', 'Engineering', 0, 3, 1),
('Sara Hailu', 'ሣራ ኃይሉ', 'UI/UX Designer', 'ዩአይ/ዩኤክስ ዲዛይነር', 'Creative designer focused on user-centered design. Expert in Figma, Adobe XD, and modern web design trends.', 'Design', 0, 4, 1),
('Daniel Getachew', 'ዳንኤል ጌታቸው', 'Project Manager', 'የፕሮጀክት ሥራ አስኪያጅ', 'PMP-certified project manager with expertise in agile methodologies. Ensures projects are delivered on time and within budget.', 'Management', 0, 5, 1),
('Hiwot Bekele', 'ሕይወት በቀለ', 'Business Development', 'የንግድ ልማት', 'Experienced business development professional focused on building lasting client relationships and identifying growth opportunities.', 'Sales', 0, 6, 1);

-- ============================================
-- Testimonials
-- ============================================
INSERT INTO `testimonials` (`client_name`, `client_position`, `company_name`, `quote_en`, `rating`, `is_featured`, `status`, `related_product`, `sort_order`) VALUES
('Ato Tesfaye Mekonnen', 'School Director', 'Brightstar International Academy', 'Authopic Technologies transformed how we manage our school. The School Management System saved us countless hours and parents love the portal. Our administrative staff went from drowning in paperwork to having time for what matters — education.', 5, 1, 'approved', 'sms', 1),
('W/ro Almaz Gebre', 'Operations Manager', 'Abyssinia Trading PLC', 'The ERP system from Authopic Technologies has been a game-changer for our business. Inventory accuracy went from 72% to 98%, and our monthly financial closing now takes just one day instead of five. The return on investment was clear within months.', 5, 1, 'approved', 'erp', 2),
('Dr. Kidane Woldemariam', 'CEO', 'Hope Medical Center', 'Our new website has dramatically increased our online visibility. We now receive 40% of appointments through online booking, and the bilingual support has been invaluable for serving all our patients effectively.', 5, 1, 'approved', 'website', 3),
('Ato Bereket Solomon', 'General Manager', 'Unity Manufacturing', 'Working with Authopic Technologies was a pleasure from start to finish. They understood our unique needs as an Ethiopian manufacturer and delivered an ERP system that truly fits our operations. Highly recommended.', 5, 0, 'approved', 'erp', 4),
('W/ro Tigist Ayele', 'Principal', 'Sunshine Academy', 'The SMS system has brought our school into the digital age. Teachers find it easy to use, and the Amharic language support means everyone on our team can use it comfortably. A truly local solution.', 4, 0, 'approved', 'sms', 5);

COMMIT;
