# AI Agent Development Guidelines (agent.md)

Welcome! This document defines the standard coding conventions, workspace configuration, and developer guidelines for building and maintaining the **Print Form Application** (`print_form`).

---

## 1. Coding Standards & Conventions

All contributions to this project must follow these backend and frontend standards.

### 1.1 Backend PHP/Laravel Standards
* **PSR Compliance:** Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding styles.
* **Laravel Best Practices:**
  * Keep controllers thin. Delegate complex business logic to services or models.
  * Use **Eloquent ORM** for database interaction. Raw SQL queries should be avoided unless strictly necessary for performance.
  * Define relationship methods in models explicitly (e.g., `$this->belongsTo(...)`).
* **Type Safety:**
  * Declare parameter types and return types on all controller methods, service methods, and model scopes where applicable (e.g., `public function login(Request $request): RedirectResponse`).
* **Validation:**
  * Always validate incoming request data using `$request->validate([...])` or Form Requests.
  * Keep validation rules clean and concise.
* **Database Transactions:**
  * For operations affecting multiple tables or performing complex database mutations, always wrap statements in database transactions:
    ```php
    use Illuminate\Support\Facades\DB;

    DB::beginTransaction();
    try {
        // DB changes
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        // handle or rethrow exception
    }
    ```
* **Soft Deletes:**
  * The tables `users`, `profiles`, `profile_templates`, and `form_templates` utilize Laravel's `SoftDeletes` trait. When executing deletes, ensure they are marked as deleted instead of using hard deletion (unless requested explicitly).

### 1.2 Frontend Standards
* **Tech Stack:** TailwindCSS is NOT utilized. Styling is strictly done using **Bootstrap 5**, **Bootstrap Icons**, and custom variables defined in `resources/css/app.css`.
* **CSS Variable Tokens:** Use the custom variables defined in `:root` of `resources/css/app.css` to keep colors consistent:
  * `--primary-color`: `#556ee6` (Primary theme color)
  * `--topbar-bg`: `#556ee6` (Topbar blue)
  * `--sidebar-bg`: `#2a3042` (Dark gray/navy sidebar)
  * `--card-shadow`: `0 0.75rem 1.5rem rgba(18, 38, 63, .03)`
* **DataTables Localization:**
  * When initializing DataTables, always configure Thai language translation elements for consistency (see example in `resources/js/ex_declaration/script.js`).
* **Vite Integration:**
  * Do not reference public folders directly for custom scripts. Custom scripts must be placed in `resources/js/` and imported in Blade views via `@vite(['resources/js/path/to/script.js'])`.

---

## 2. Docker & Local Development Setup

The application is containerized using Docker Compose. Use the following commands to interact with the environment:

### 2.1 Essential Commands

| Purpose | Command |
| :--- | :--- |
| **Start Environment** | `docker compose up -d` |
| **Stop Environment** | `docker compose down` |
| **Rebuild Containers** | `docker compose up -d --build` |
| **Access PHP CLI Bash** | `docker compose exec app sh` |
| **Run Artisan Command** | `docker compose exec app php artisan <command>` |
| **Run Composer Install** | `docker compose exec app composer install` |
| **Run Migrations** | `docker compose exec app php artisan migrate` |
| **Run Seeds** | `docker compose exec app php artisan db:seed` |
| **Run Unit/Feature Tests** | `docker compose exec app php artisan test` |

### 2.2 Asset Compilation (Vite)
Vite is configured to run automatically in hot-reload mode within the `vite` container.
If manual compilation is needed:
* **Production Build:** `docker compose exec vite npm run build`
* **Restart Vite Dev Server:** `docker compose restart vite`

---

## 3. API Design Standards

All RESTful APIs located in `routes/api.php` and handled by `app/Http/Controllers/Api/` must adhere to these response formats:

### 3.1 Successful Responses (HTTP 200)
Return a standard JSON structure with `status` indicating success (usually 1 or true):
```json
{
    "status": 1,
    "message": "Action completed successfully",
    "data": { ... }
}
```

### 3.2 Error Responses (HTTP 200 or 4xx/5xx)
If validation or business logic fails, return standard status indicator along with details:
```json
{
    "status": 0,
    "message": "Brief error description",
    "description": "System exception or detailed validation messages"
}
```

---

## 4. Guidelines for AI Agents modifying code

When asked to implement new features or modify existing code:
1. **Analyze Dependencies:** Always inspect `composer.json` and `package.json` to leverage existing libraries (e.g. Sanctum, Spatie Permission) before installing new ones.
2. **Reuse Layouts:** Do not write boilerplate HTML. Use layout templates like `layouts.app` or include UI components like `layouts.topbar` directly.
3. **Validate Database Types:** Note that some models (e.g. `ProfileTemplate`) use string UUIDs or custom string IDs as primary keys (`profile_template_id`), while others use standard auto-incrementing integers. Always check the primary keys configuration in the Model files before writing database queries.
4. **Preserve Code Style:** Do not remove user-written comments or alter unrelated configurations. Use clean and well-commented code, referencing context files where applicable.
5. **Git Commit & Push Guidelines:**
   - ห้ามสั่ง `git push` หรือ `git commit` เองโดยพลการหากผู้ใช้ไม่ได้สั่งหรือร้องขอ
   - **เมื่อผู้ใช้สั่ง `git push`:** ให้ดำเนินการทำ `git commit` ให้ด้วยเสมอ โดยต้องแยก commit ตาม function หรือ module ให้เรียบร้อยก่อน แล้วจึงทำการ `git push` ไปยัง remote repository (ห้ามรวมการแก้ไขต่างฟังก์ชันไว้ใน commit เดียว)
6. **Language for Documentation and Plans:** Always write implementation plans (`implementation_plan.md`), walkthroughs (`walkthrough.md`), and work summaries in Thai language (ภาษาไทย).
7. **Pre-approved Operations (การทำงานที่อนุญาตให้ทำได้ทันทีโดยไม่ต้องขออนุญาต):**
   The AI agent is explicitly permitted to execute the following diagnostic and inspection commands directly without needing prior confirmation or approval from the user:
   - **Check PHP version:** (เช่น `php -v`, `docker compose exec app php -v`)
   - **Check Docker version:** (เช่น `docker --version`, `docker compose version`)
   - **Check Git status:** (เช่น `git status`)
   - **Check diff of docker-compose.yml:** (เช่น `git diff docker-compose.yml`)

