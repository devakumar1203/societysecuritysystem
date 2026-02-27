Society Security System
=======================

The Society Security System is a management tool for housing societies. It handles
visitor logging, resident approvals, and maintenance record tracking.

Quick Start
-----------

* Setup Database: Import `db.sql` into MySQL
* Build Tables: Run `insert_users.sql` for initial setup
* Configure App: Copy `config.php.example` to `config.php`
* Run System: Use `php -S localhost:8000`

Default Credentials (Testing Only)
----------------------------------
Use these accounts to test the system after importing `insert_users.sql`:

* Admin: `admin` / `admin123`
* Supervisor: `supervisor` / `super123`
* Resident: `resident1` / `res123`

Essential Documentation
-----------------------

All users should be familiar with:

* System Requirements: PHP 8.x and MySQL
* Core Logic: `api.php`
* Configuration template: `config.php.example`
* Database Schema: `db.sql`


System Architecture
===================

The system follows a lightweight monolithic architecture:

* FRONTEND: Vanilla CSS and JS for lightweight performance.
* BACKEND: Procedural PHP with modular functions in `config.php`.
* DATA FLOW: DASHBOARD -> API -> DATABASE -> DASHBOARD.


Security Implementation
=======================

Core security principles applied to the codebase:

* SQL INJECTION PREVENTION: All dynamic queries use MySQLi prepared
  statements with parameter binding.
* PASSWORD SECURITY: Passwords are never stored in plain text. The system
  uses `password_hash()` with the Argon2 or BCRYPT algorithm.
* ACCESS CONTROL: Role-based session verification is enforced on every
  page and API endpoint via `require_role_simple()`.
* CREDENTIAL HYGIENE: A `.gitignore` is provided to ensure local
  `config.php` settings are never pushed to the repository.


Who Are You?
============

Find your role below:

* Administrator - Managing buildings and flat infrastructure
* Supervisor - Handling daily visitor entries and staff management
* Resident - Approving visitor requests and tracking payments
* Developer - Extending the API or customizing the dashboards


For Specific Users
==================

Administrator
-------------

Manage the foundational data of the society:

* Infrastructure Setup: `admin.php`
* Database Structure: `db.sql`
* User Seeding: `insert_users.sql`

Supervisor
----------

Handle the day-to-day security operations:

* Visitor Management: `supervisor.php`
* Actions API: `api.php`
* UI Components: `styles.css`

Resident
--------

Monitor your flat's security and finances:

* Resident Portal: `resident.php`
* Login System: `index.php`
* Notification Flow: `api.php`

Developer
---------

Understand and modify the system internals:

* Database Logic: `db.sql`
* Request Handling: `api.php`
* Session & Auth: `config.php`
* Style System: `styles.css`
