# Project Context: Print Form Application

This document provides a comprehensive overview of the **Print Form Application** (`print_form`). It outlines the system architecture, tech stack, database schemas, directory structure, and key workflows. It serves as a single source of truth for developers and AI agents to understand the codebase.

---

## 1. System Overview & Architecture

The **Print Form Application** is a web-based platform built on **Laravel 12** and **PHP 8.2**. Its primary purpose is to manage and print document templates, specifically focusing on **Export Declarations** and user/profile templates configuration.

The application uses:
* **Backend:** Laravel 12 (MVC framework) with Sanctum for API token authentication and Spatie Laravel-Permission for role/permission management.
* **Frontend:** Blade templates, styled with Bootstrap 5, powered by jQuery, DataTables for table operations, and bundled using Vite.
* **Containerization:** Docker Compose setup orchestrating:
  * `app`: PHP 8.2 CLI/FPM environment.
  * `web`: Nginx web server acting as a reverse proxy.
  * `vite`: Node 20 environment compiling/serving frontend assets.
  * `redis`: Cache, session, and queue driver.

---

## 2. Directory Structure

Below is the directory structure focusing on custom files and folders relevant to development:

```text
print_form/
├── app/
│   ├── Enums/                 # Application enums (e.g., Status.php)
│   ├── Http/
│   │   ├── Controllers/       # MVC Controllers
│   │   │   ├── Api/           # API endpoints (Sanctum protected)
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── UserController.php
│   │   │   └── AuthController.php
│   │   │   └── ExportDeclarationController.php
│   │   │   └── UserController.php
│   │   │   └── RoleController.php
│   ├── Models/                # Eloquent ORM Models
│   │   ├── ApplicationPartner.php
│   │   ├── FormTemplate.php
│   │   ├── Profile.php
│   │   ├── ProfileTemplate.php
│   │   └── User.php
│   └── Providers/             # Service Providers
├── config/                    # Laravel Configuration files
├── database/
│   ├── factories/             # Database model factories
│   ├── migrations/            # Migration scripts (standard Laravel schema)
│   └── seeders/               # Database seeders
├── docker/                    # Docker configuration files (PHP & Nginx configurations)
├── public/                    # Compiled assets & public entry points
├── resources/
│   ├── css/                   # Custom stylesheets (app.css)
│   ├── js/                    # JS files and page-specific scripts (ex_declaration/script.js)
│   └── views/                 # Blade templates
│       ├── admin/
│       ├── auth/              # Login views
│       ├── ex_declaration/    # Export Declaration template management UI
│       ├── form/              # Printing form templates
│       └── layouts/           # Master layouts (admin, app, topbar)
├── routes/
│   ├── api.php                # API Routes (sanctum auth group)
│   ├── console.php            # Console commands
│   └── web.php                # Web (browser) Routes
├── docker-compose.yml         # Docker orchestration file
├── vite.config.js             # Vite asset compiler configuration
└── composer.json              # Backend dependencies
```

---

## 3. Database Schema & Models

The core application data structures are managed via Eloquent Models:

### 3.1 `User` (`app/Models/User.php`)
Represents system administrators and clients who manage templates.
* **Traits used:** `HasApiTokens`, `HasFactory`, `Notifiable`, `HasRoles`, `SoftDeletes`.
* **Key Attributes:**
  * `username` (string, required)
  * `password` (string, hashed)
  * `profile_id` (string, maps to a profile reference)
  * `comp_tax` (company tax identifier)
  * `status` (integer status, e.g. Active/Inactive)
  * `lsp_tax_no`, `lsp_comp_nmt`, `lsp` (LSP/logistics-related tax metadata)
  * `email` (string)
  * `temporary_token` (used for auto-login/SSO redirects)

### 3.2 `Profile` (`app/Models/Profile.php`)
Represents customer/business entity profiles.
* **Traits used:** `HasFactory`, `Notifiable`, `SoftDeletes`.
* **Key Attributes:**
  * `profile_id` (string, primary identifier)
  * `profile_tax` (tax number unique to the profile)
  * `status` (integer, e.g. 1 = Active, 0 = Inactive)
  * `remark` (text)

### 3.3 `ProfileTemplate` (`app/Models/ProfileTemplate.php`)
Represents templates configured for specific profiles.
* **Traits used:** `SoftDeletes`.
* **Configuration:**
  * Primary Key: `profile_template_id` (string, non-incrementing).
  * Table: `profile_templates`.
* **Key Attributes:**
  * `profile_id` (string, maps to Profile)
  * `profile_template_name` (string)
  * `description` (text)
  * `status` (integer status)
  * Audit fields: `created_by`, `updated_by`, `deleted_by`.

### 3.4 `FormTemplate` (`app/Models/FormTemplate.php`)
Represents default base layouts and templates for printing forms.
* **Traits used:** `SoftDeletes`.
* **Configuration:**
  * Primary Key: `template_id`.
* **Key Attributes:**
  * `template_name`, `description`, `status`.

### 3.5 `ApplicationPartner` (`app/Models/ApplicationPartner.php`)
Handles external system integration clients authorized to issue print requests or sync data.
* **Traits used:** `HasApiTokens`, `HasFactory`, `Notifiable`.
* **Key Attributes:**
  * `application_id` (primary key)
  * `application_name`, `application_code`
  * `token` (stored secret, returned via `getAuthPassword`)

---

## 4. Key Workflows & Mechanisms

### 4.1 Authentication & Auto-Login
* **Standard Web Login:** Handled in `AuthController@login`. Requires `username`, `password`, and `profile_id`. Checks against `Status::Active` (value = 1).
* **Auto-Login (SSO/Token Redirect):** Handled in `AuthController@autoLogin`.
  * External portals redirect users with a `temporary_token`.
  * The application searches for the user, logs them in using `Auth::login($user)`, invalidates the `temporary_token` (sets to `null` to prevent replay attacks), and redirects to `/dashboard`.
* **API Authentication:** Handled in `Api/AuthController`. Protected via Laravel Sanctum (`auth:sanctum`). Uses bearer tokens for authorization.

### 4.2 API Integration (`app/Http/Controllers/Api`)
* API routes are prefix-grouped under `routes/api.php`.
* CRUD controllers (`UserController`, `ProfileController`) execute mutations wrapped in database transactions (`DB::beginTransaction()`, `DB::commit()`, `DB::rollBack()`) to ensure atomicity.
* Validation is performed explicitly on incoming requests, returning standardized JSON responses.

### 4.3 Export Declaration Templates Management
* Managed through the web view at `/ex-declaration` via `ExportDeclarationController`.
* The front-end renders templates using jQuery DataTables inside `resources/views/ex_declaration/index.blade.php`.
* Create, Edit, and Delete actions are executed asynchronously using AJAX requests.
* Modals are reused for both creation and editing, swapping data dynamically via HTML5 `data-*` attributes.
