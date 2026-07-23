# PROJECT_STATE.md

## 1. EXECUTIVE SUMMARY & TECH STACK

### Purpose & Scope
Production-grade Learning Management System (LMS) blueprint designed for scaled e-learning delivery (serving 10,000+ active learners). Features course management, video/interactive lessons, randomized & timed exam engine, reflective journal submission, auto-generated PDF certificates with QR code verification, voucher redemption with Prakerja (government training program) integration, and admin dashboards for tracking learner progress.

### Tech Stack & System Components

| Layer | Technology | Key Details & Configurations |
|---|---|---|
| **Backend Framework** | Laravel `^8.75` (PHP `^7.3 \| ^8.0`) | RESTful API controllers (`App\Http\Controllers\API`), Eloquent ORM, Form Requests, Sanctum Auth |
| **Frontend Framework** | Vue.js `^2.6.14` | SPA powered by Vue CLI 5, JavaScript ES6+, Options API, Vercel SPA Routing (`vercel.json`) |
| **UI Library** | Vuetify `^2.6.0` | Material Design UI framework with custom SASS configuration |
| **Database** | MySQL `8.0` / PostgreSQL (Supabase) | Dual-driver support for local MySQL 8.0 or cloud-hosted Supabase PostgreSQL (with SSL & pooler support) |
| **Cache & Queue** | Redis `6.x` | Used for caching API responses, session storage, and processing asynchronous jobs |
| **Authentication** | Laravel Sanctum `^2.15` | Bearer token authentication stored via HTTP-only cookies / local storage |
| **Role & Permissions** | Spatie Laravel Permission `^5.5` | RBAC roles: `Super-Admin`, `Admin`, `Expert`, `Learner` |
| **Reverse Proxy** | Nginx / Vercel Edge | Port 80 proxying `/api` to Laravel backend (port 8000) and `/` to Vue.js frontend (port 8080) |
| **File Storage** | AWS S3 (`league/flysystem-aws-s3-v3` `^1.0`) | Presigned video streaming URLs and certificate asset storage |
| **PDF & Exports** | `vue-html2pdf` `^1.8.0`, `json-to-csv-export` | Client-side certificate generation and admin CSV exports |
| **Rich Text Editor** | CKEditor 5 (`@ckeditor/ckeditor5-vue2`) | Custom file upload handling via `CkeditorFileUploadController` |
| **Testing & E2E** | PHPUnit `^9.5`, Cypress `^13.13` | Backend unit/feature testing and frontend E2E specs |
| **Containerization** | Docker & Docker Compose | Multi-container setup (`backend`, `frontend`, `db`, `redis`, `nginx`, `phpmyadmin`) |

---

## 2. RECRUITER & DEMO ACCESS CREDENTIALS

For evaluators, recruiters, and QA testing the live deployment or local database seed (`php artisan db:seed`), pre-seeded demo accounts are available:

| Role / Persona | Email | Password | Access Capabilities |
|---|---|---|---|
| **Recruiter / Evaluator (Admin View)** | `recruiter@email.com` | `Password123123` | Full admin dashboard, user management, course/exam builder, analytics export |
| **Super Admin (System View)** | `superadmin@email.com` | `Password123123` | System configuration, admin creation, full RBAC control |
| **Demo Learner (Student View)** | `learner@email.com` | `Password123123` | Course catalog enrollment, video lesson player, quiz & exam engine, PDF certificate verification |

> **Note:** Seeded automatically via `DatabaseSeeder.php` and `php artisan create:superadmin`.

---

## 3. PROJECT STRUCTURE & ARCHITECTURE

### Directory Structure Overview
```
laravel-vue-lms-blueprint/
├── docker-compose.yml              # Root service orchestration (web, api, db, redis, proxy)
├── nginx/
│   └── default.conf                # Nginx reverse proxy configuration (/api -> backend:8000, / -> frontend:8080)
├── lms-backend/                    # Laravel 8 REST API project root
│   ├── app/
│   │   ├── Http/Controllers/API/   # Domain API Controllers (Auth, Course, Lesson, Exam, Voucher, Certificate)
│   │   ├── Models/                 # Eloquent ORM Models (23 models including User, Course, Lesson, Exam, etc.)
│   │   ├── Observers/              # Event observers for domain models
│   │   └── Traits/                 # Reusable backend traits (ResponseFormatter, MoveOrder)
│   ├── database/
│   │   ├── migrations/             # 102 DB migrations defining relational schema & indexes
│   │   └── seeders/                # Database seeders for initial roles, users, and course data
│   ├── routes/
│   │   └── api.php                 # Versioned API routes (/v1/...) with auth & role middleware
│   └── tests/                      # PHPUnit API test suite
└── lms-frontend/
    └── startup-campus/             # Vue.js 2 SPA project root
        ├── vercel.json             # Vercel rewrite configuration for SPA fallback routing
        └── src/
            ├── components/         # Shared Vuetify components (Navbar, Footer, Modals, Loaders)
            ├── views/
            │   ├── website/        # Student-facing pages (Home, Course Catalog, Lesson Player, Exam, Profile)
            │   └── admin/          # Admin/CMS pages (User management, Course Builder, Exam Builder, Vouchers)
            ├── router/             # Vue Router route definitions with navigation guards
            ├── plugins/            # Vuetify & third-party plugin initializations
            └── helpers/            # Axios API wrappers, cookie storage, and date formatters
```

### Architectural Patterns Applied
1. **Decoupled Client-Server (REST API Monorepo):** Backend serves strict JSON API endpoints under `/v1/`, completely isolated from Vue SPA presentation.
2. **Reverse Proxy & SPA Rewrite:** Nginx handles request routing locally (`/api` -> API, `/` -> SPA). Vercel edge rewrite (`vercel.json`) handles production SPA route fallbacks.
3. **Eager Loading & Compound Indexing:** Database queries rely on explicit Eloquent `with()` eager loading and compound SQL indexes (`idx_progress_user_course`, `idx_enrollment_status`) to prevent N+1 query bottlenecks.
4. **RBAC Guarding:** Dual-layer security using Spatie middleware (`role:Super-Admin`) on API routes and Vue Router navigation guards on client-side views.
5. **State & Storage Abstraction:** Stateless authentication tokens stored securely, with presigned AWS S3 URLs used for sensitive media delivery.

---

## 4. PRODUCTION SECURITY & ZERO DATA LEAK STANDARDS

To guarantee security best practices and eliminate data leaks in production (especially on free deployment tiers like Vercel, Supabase, Render, Fly.io):

1. **Strict Origin CORS Scoping (`config/cors.php`):**
   - Configured dynamically via `CORS_ALLOWED_ORIGINS` / `FRONTEND_URL` rather than wildcard `*` when credentials are supported.
   - Includes preview domain patterns (`*.vercel.app`, `*.netlify.app`, `*.onrender.com`).
2. **Sanitized Exception Handling (`BaseController::ressException`):**
   - In production (`APP_DEBUG=false`), API errors return clean, generic messages to clients (`An unexpected server error occurred.`), preventing SQL queries, stack traces, or database connection strings from leaking.
3. **Model Attribute Protection & Safe Search (`User.php`):**
   - Sensitive attributes (`password`, `remember_token`) are strictly hidden from JSON serialization.
   - `scopeSearch` explicitly filters non-sensitive columns (`id`, `fullname`, `email`, `phone`), eliminating potential hash-matching searches against the password column.
4. **Zero Frontend Secret Exposure:**
   - Client JS bundle built strictly with public `VUE_APP_` variables. AWS S3 secret keys and DB credentials remain server-side only.
5. **Environment Isolation:**
   - `.env` files are untracked in git (`.gitignore`), preventing credential leaks in open repositories.

---

## 5. CURRENT IMPLEMENTATION STATE & DATA FLOW

### Primary Built Modules & Active Features
- **Auth & Identity:** Register, Login, Email OTP verification, Password Reset, User Profile management with avatar uploads.
- **Course & Lesson Engine:** Categorized course catalog, course detail pages, structured lesson hierarchies, video playback with presigned S3 URLs, document viewing, and order sorting (`moveup`/`movedown`).
- **Reflective Learning & Feedback:** Journal reflection responses (`UserLessonAnswer`), rating lessons and lesson pages, instructor review & feedback workflows.
- **Exam & Assessment System:** Question bank management, CSV import for question sets, randomized question order per user attempt (`random_seed`), minimum pass grade (KKM), attempt limits, timed exam sessions, auto-evaluation, and answer tracking (`ExamAnswer`).
- **Certificate System:** Automated completion checking, certificate code generation with verification endpoints, PDF render via `vue-html2pdf`, and public QR verification link (`/certificate/read/{id}`).
- **Voucher & Prakerja Redemption:** Voucher code validation, redemption rate limiting, course category enrollment unlocked by redemption code, Prakerja state integration.
- **Admin Dashboard & Analytics:** Full CRUD for tutors, categories, courses, lessons, exam pages, user block/unblock, and export of student progress/exam answers.

### Core Database Schema Summary
- **Users & Auth:** `users` (joined with Spatie `roles`/`permissions`), `user_verifications`, `personal_access_tokens`.
- **Course Domain:** `courses`, `course_categories`, `lessons`, `lesson_pages`, `tutors`, `banner_promotions`.
- **User Progress & Enrollments:** `user_courses`, `user_course_categories`, `user_lessons`, `user_lesson_answers`, `lesson_ratings`, `lesson_page_ratings`.
- **Exam Domain:** `exams`, `exam_pages`, `question_banks`, `exam_configs`, `exam_poins` (scores & attempts), `exam_answers`.
- **Certificates & Vouchers:** `certificates`, `vouchers`.

---

## 6. REMAINING TASKS, TODOs & TECHNICAL DEBT

### Pending Enhancements & Technical Debt
- **Framework Modernization:** Upgrade Laravel from `8.x` to `10.x/11.x` and PHP from `7.3/8.0` to `8.2+` to leverage modern type hints, Enums, and Fiber support.
- **Frontend Upgrade:** Vue 2 reached EOL; upgrade `lms-frontend` to Vue 3 (Options/Composition API) or Vite-based build setup for improved HMR and long-term security maintenance.
- **Automated Test Coverage:** Expand unit/feature tests for `ExamPoinController` scoring logic, edge cases in random question assignment, and race conditions during concurrent voucher redemption.
- **Redis Queue Worker Documentation:** Ensure production queue worker configuration (`php artisan queue:work redis`) is documented for high-volume background job processing.
- **API Response Schemas:** Standardize JSON resource classes across all legacy API endpoints to ensure uniform key formatting across backend updates.

---

## 7. AI AGENT CODING GUIDELINES

### Naming Conventions & Placement Rules
- **Backend Controllers:** Place API controllers in `lms-backend/app/Http/Controllers/API/`. Name using `[Domain]Controller.php` (e.g., `CourseController.php`).
- **Eloquent Models:** Place models in `lms-backend/app/Models/`. Singular PascalCase (e.g., `UserLessonAnswer.php`). Explicitly declare `$table`, `$fillable`, and relationship methods.
- **API Routes:** Register in `lms-backend/routes/api.php` under `Route::prefix('v1')`. Use standard resource conventions or explicit action paths (`/create`, `/read/{id}`, `/update/{id}`, `/delete/{id}`).
- **Vue Views:** Place student pages under `lms-frontend/startup-campus/src/views/website/` and admin pages under `src/views/admin/`. Use PascalCase (e.g., `ExamDetail.vue`).
- **Vue Components:** Place reusable UI elements under `lms-frontend/startup-campus/src/components/`. Use PascalCase.

### Step-by-Step Feature Implementation Workflow
1. **Database & Model Layer:**
   - Create migration in `lms-backend/database/migrations/` using timestamped naming.
   - Define table columns, foreign keys, and indexes.
   - Create Eloquent model in `lms-backend/app/Models/` with `$fillable` fields and relation methods (`belongsTo`, `hasMany`).
2. **API Endpoint & Controller Layer:**
   - Add controller method extending `BaseController` or returning standard formatted JSON (`ResponseFormatter`).
   - Use eager loading (`with()`) for related models to eliminate N+1 queries.
   - Register route in `lms-backend/routes/api.php` with appropriate middleware (`auth:sanctum`, `role:...`).
3. **Frontend Integration:**
   - Add API helper function in `lms-frontend/startup-campus/src/helpers/` or view using Axios instance.
   - Create/update Vue view in `src/views/` using Vuetify components (`v-container`, `v-card`, `v-btn`, `v-data-table`).
   - Add route in `src/router/index.js` with required `meta` properties (`requiresAuth`, `role`).
4. **Verification:**
   - Test endpoint payload and status codes.
   - Verify responsive UI layout and role access restrictions.
