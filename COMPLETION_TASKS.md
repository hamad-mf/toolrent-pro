# ToolRent Pro — Completion Tasks & Missing Features

> **Purpose of this document:** This is a complete, actionable specification of everything that is missing, broken, or incomplete in the ToolRent Pro codebase when compared against `PowerToolsRental_ProjectPlan.md`. It is written so that an AI agent (Gemini) or a developer can pick up each task and implement it without re-analyzing the whole project.
>
> **Current completion estimate:** ~70–75% of the project plan.
>
> **Stack reminder (do NOT introduce Node.js/npm):** Laravel 12 (PHP 8.2+), Blade, Bootstrap 5.3 via CDN, Alpine.js via CDN, Chart.js, Flatpickr, SweetAlert2, DomPDF (`barryvdh/laravel-dompdf`), QR codes (`simplesoftwareio/simple-qrcode`). All assets are loaded via `<link>`/`<script>` tags — there is no build step.

---

## How to use this document

1. Work top-to-bottom. **Section A (Critical Bugs)** must be done first — these cause runtime errors.
2. Each task has: a unique ID, the affected files, the problem, and the exact fix expected.
3. After each task, run `php artisan route:list` and manually hit the affected page to verify.
4. Do not break existing multi-tenancy (`tenant_id` scoping via `App\Scopes\TenantScope` and `App\Traits\BelongsToTenant`).

---

## Table of Contents

- [A. Critical Bugs (Runtime Errors — fix first)](#a-critical-bugs-runtime-errors--fix-first)
- [B. White-Label / Multi-Tenant Gaps](#b-white-label--multi-tenant-gaps)
- [C. Missing Features vs. Project Plan](#c-missing-features-vs-project-plan)
- [D. Role & Permission Gaps](#d-role--permission-gaps)
- [E. Data Integrity & Model Gaps](#e-data-integrity--model-gaps)
- [F. UI / Library Gaps](#f-ui--library-gaps)
- [G. Nice-to-Have / Polish](#g-nice-to-have--polish)
- [H. Verification Checklist](#h-verification-checklist)

---

## A. Critical Bugs (Runtime Errors — fix first)

These will throw 500 errors or silently swallow user feedback. They must be resolved before anything else.

### TASK-A1 — Missing view `tools/show.blade.php`
- **Files:** `app/Http/Controllers/ShopAdmin/ToolController.php` (method `show`), route `shop-admin.tools.show` (registered via `Route::resource('tools', ...)`), new file `resources/views/shop-admin/tools/show.blade.php`.
- **Problem:** `ToolController@show` returns `view('shop-admin.tools.show', ...)` but that Blade file does not exist. Visiting `/shop/tools/{id}` throws `InvalidArgumentException: View [shop-admin.tools.show] not found`.
- **Fix:** Create `resources/views/shop-admin/tools/show.blade.php` extending `layouts.admin`. It should display: tool name, category, brand, model number, serial number, description, daily rate, status badge, image (if present), and the QR code (if the tenant has the `qrcode` feature). Include Edit and Back buttons.

### TASK-A2 — Missing view `rentals/show.blade.php`
- **Files:** `app/Http/Controllers/ShopAdmin/RentalController.php` (method `show`), route `shop-admin.rentals.show`, new file `resources/views/shop-admin/rentals/show.blade.php`.
- **Problem:** `RentalController@show` returns `view('shop-admin.rentals.show', ...)` but the file does not exist. Visiting `/shop/rentals/{id}` errors.
- **Fix:** Create `resources/views/shop-admin/rentals/show.blade.php` extending `layouts.admin`. Display: customer details, tool details, staff member who processed it, checkout/due/returned timestamps, daily rate, computed/total price, status, notes. If status is `Active`, show a "Return Tool" button (POST to `shop-admin.rentals.return`). If status is `Returned` and tenant has `invoicing` feature, show an "Download Invoice" button.

### TASK-A3 — `error` flash messages are never displayed
- **Files:** `resources/views/layouts/admin.blade.php`.
- **Problem:** Controllers set `->with('error', '...')` in at least 6 places (tool not available, tool already returned, cannot delete rented tool, cannot delete category with tools, cannot delete self, etc.), but the layout only renders `@if(session('success'))`. All error feedback silently disappears, leaving users confused.
- **Fix:** Add an error alert block right after the existing success block:
  ```blade
  @if(session('error'))
      <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif
  ```
  Also consider rendering validation errors (`$errors->any()`) globally here.

### TASK-A4 — Settings sidebar link broken for non-shop-admin roles
- **Files:** `resources/views/layouts/admin.blade.php` (Settings nav link).
- **Problem:** The Settings link uses `Auth::user()->isShopAdmin() ? route('shop-admin.settings.edit') : '#'`. Managers and counter-staff get a dead `#` link. Super Admin's settings route (`super-admin.settings.index`) is never linked in the sidebar at all.
- **Fix:** Branch the Settings link by role: Super Admin → `super-admin.settings.index`; Shop Admin → `shop-admin.settings.edit`; other roles → hide the link entirely (or route appropriately once permissions are defined in TASK-D2).

---

## B. White-Label / Multi-Tenant Gaps

The plan (§10 Design System, §11 Authentication) promises per-tenant branding. Only the logo and shop name are currently applied.

### TASK-B1 — Inject tenant primary color into the admin layout
- **Files:** `resources/views/layouts/admin.blade.php`, optionally `app/Providers/AppServiceProvider.php` or a `View::composer`.
- **Problem:** Tenants store `primary_color` (and `secondary_color`) in the DB, but `layouts/admin.blade.php` hardcodes `#0d6efd` throughout the inline `<style>` block. The stored color is never used, so white-labeling colors does nothing.
- **Fix:**
  1. Read the tenant color from session (`session('tenant_primary_color')`) with a fallback to `#0d6efd`.
  2. Inject a CSS variable on `:root`, e.g. `<style>:root{ --bs-primary: {{ $color }}; --tr-primary: {{ $color }}; }</style>`.
  3. Replace hardcoded `#0d6efd` references in sidebar `.nav-link.active`, hover states, and `.navbar-brand` icon with `var(--tr-primary)`.
  4. Override Bootstrap's `.btn-primary`, `.text-primary`, `.bg-primary` to use the variable so buttons/badges follow the tenant theme.
- **Note:** This task overlaps with the new design system in `DESIGN_SYSTEM.md`. If implementing the new design, do the color injection there instead of duplicating.

### TASK-B2 — Tenant-branded login screen
- **Files:** `resources/views/auth/login.blade.php`, `resources/views/layouts/app.blade.php`.
- **Problem:** Plan §11 says: "Tenant-specific logos and colors on the login screen." The current login page is the stock Laravel/UI Bootstrap scaffold with no tenant branding.
- **Fix:** When the request resolves to a tenant (subdomain → `session('tenant_id')`), display that tenant's logo and shop name, and apply its primary color to the login button. Fall back to the generic "ToolRent Pro" branding when there is no tenant context.

### TASK-B3 — `custom_css` field is unused
- **Files:** `tenants` migration (field exists), `resources/views/layouts/admin.blade.php`.
- **Problem:** `tenants.custom_css` column exists and is fillable on the `Tenant` model but is never rendered.
- **Fix:** If present, inject `{!! $tenant->custom_css !!}` inside a `<style>` tag in the layout `@yield('styles')` area. **Security note:** This is raw CSS injected by tenants — document that only trusted Super Admin / Shop Admin should edit it, and never allow untrusted user input here.

### TASK-B4 — `system_name` and `favicon` unused
- **Files:** `tenants` migration, `layouts/admin.blade.php`, `layouts/app.blade.php`.
- **Problem:** `tenants.system_name` (custom product name per shop) and `tenants.favicon` are stored but never used. The `<title>` is hardcoded to "ToolRent Pro Admin".
- **Fix:** Use `session('tenant_name')` / `system_name` in the `<title>` and render the tenant favicon `<link rel="icon">` when available.

---

## C. Missing Features vs. Project Plan

### TASK-C1 — Tool image upload not handled
- **Files:** `app/Http/Controllers/ShopAdmin/ToolController.php` (`store`, `update`), `resources/views/shop-admin/tools/create.blade.php`, `edit.blade.php`.
- **Problem:** The `tools` table and `Tool` model both have an `image` column (plan mentions "Tool photos"), but the controller never validates or stores an uploaded file, and the forms have no file input.
- **Fix:**
  1. Add `enctype="multipart/form-data"` and a file `<input name="image">` to the create/edit forms.
  2. In `store`/`update`, validate `'image' => 'nullable|image|max:2048'` and, when present, `$request->file('image')->store('tools', 'public')`, saving the path to the model. Mirror the existing logo upload pattern in `ShopAdmin\SettingsController@update`.
  3. Display the image on the tools index and the new `show` view.
  4. Ensure `php artisan storage:link` is documented in setup (the `public/storage` symlink).

### TASK-C2 — Super Admin global settings does nothing
- **Files:** `app/Http/Controllers/SuperAdmin/SettingsController.php`, `resources/views/super-admin/settings/index.blade.php`.
- **Problem:** `update()` just returns `redirect()->back()->with('success', ...)` with a comment "Global system settings logic would go here." Nothing is persisted.
- **Fix:** Define what global settings are needed (e.g., default plan limits, platform name, default theme, maintenance mode). Persist them — simplest approach is a `settings` key/value table or Laravel's cache/config. Wire the form to actually save and reload values. If global settings are out of scope, remove the page to avoid a misleading no-op.

### TASK-C3 — No reporting module
- **Files:** new controller(s) under `app/Http/Controllers/ShopAdmin/`, new views, routes in `routes/web.php`.
- **Problem:** Plan §4 states Managers "can access reports," and §5/§14 (Phase 3) call for reporting. There are no reports anywhere.
- **Fix (minimum viable):** Add a Reports section for Shop Admin/Manager with: revenue over a date range (sum of `rentals.total_price`), most-rented tools, currently overdue rentals, utilization rate (rented vs. total tools). Use Chart.js for visuals (already used on the dashboard). Add a sidebar link gated to `shop-admin` + `manager`.

### TASK-C4 — Overdue rental handling is passive
- **Files:** `app/Models/Rental.php`, `app/Http/Controllers/ShopAdmin/RentalController.php`, optional console command in `routes/console.php`.
- **Problem:** The `rentals.status` enum includes `Overdue`, and the index view shows red text when `due_at->isPast()`, but no rental is ever actually set to `Overdue` status — it stays `Active`. The dashboard and any reporting can't count overdue items by status.
- **Fix:** Add a scheduled command (or an accessor + a daily job) that flips `Active` rentals past their `due_at` to `Overdue`. Add an `isOverdue()` helper on the model. Register a schedule entry if using a command.

### TASK-C5 — Pricing/billing is minimal
- **Files:** `app/Http/Controllers/ShopAdmin/RentalController.php` (`calculatePrice`), invoice view.
- **Problem:** Price is `days * daily_rate` with a 1-day minimum. No deposits, late fees, discounts, taxes, or partial-day rules — the plan references billing and Manager "approve discounts."
- **Fix (scope with owner):** At minimum add optional late-fee calculation for overdue returns and a discount field on checkout. Document any business rules. If full billing is out of scope, note it explicitly here so it isn't mistaken for complete.

---

## D. Role & Permission Gaps

### TASK-D1 — No way to create staff users (Manager / Counter Staff / Floor Staff)
- **Files:** new `app/Http/Controllers/ShopAdmin/StaffController.php` (or `UserController`), new views, routes, `routes/web.php`.
- **Problem:** Roles `manager`, `counter-staff`, `floor-staff` are defined in `App\Models\Role`, but the only way to create any non-super-admin user is via seeders. Super Admin's user resource is `->only(['index', 'destroy'])` and there is no Shop-Admin user management at all. The entire staff hierarchy in plan §4 is unusable in practice.
- **Fix:**
  1. Add Shop-Admin staff management: list users belonging to the current tenant, create/edit/deactivate, assign a role (manager / counter-staff / floor-staff). Enforce the tenant's `max_users` limit.
  2. New users must be auto-scoped to the current `tenant_id` (the `BelongsToTenant` trait sets this on create when `session('tenant_id')` is present — verify it works for `User`).
  3. Hash passwords (`Hash::make`) and respect `is_active`.

### TASK-D2 — Granular permissions per role are not enforced
- **Files:** `routes/web.php`, `app/Http/Middleware/CheckRole.php`, controllers.
- **Problem:** The plan defines distinct capabilities (Manager: reports + approve discounts; Counter Staff: rentals/returns/billing/customers; Floor Staff: view + update condition + mark maintenance). Currently the shop route group allows `shop-admin,manager,counter-staff` uniformly, and **floor-staff is locked out of everything**. There is no per-action authorization.
- **Fix:** Introduce policies or per-route role middleware so each role only sees/does what §4 specifies. At minimum: give floor-staff access to tool status/maintenance updates; restrict settings + staff management to shop-admin; restrict reports to shop-admin + manager.

### TASK-D3 — Floor Staff "mark maintenance / update condition" flow missing
- **Files:** `ToolController` (or a dedicated action), tool views.
- **Problem:** Plan §4: Floor Staff "Can view tool status, update condition, mark maintenance." No such action exists; tool status is only changed implicitly by rental checkout/return or full edit.
- **Fix:** Add a lightweight action to toggle a tool to/from `Maintenance` and update a condition note, accessible to floor-staff. Block setting `Maintenance` on a tool that is currently `Rented`.

---

## E. Data Integrity & Model Gaps

### TASK-E1 — `Customer::rentals()` relationship is a broken stub
- **Files:** `app/Models/Customer.php`.
- **Problem:** The `rentals()` method has only a commented-out body referencing a non-existent `Booking` model and `return`s nothing. Any call to `$customer->rentals` returns null and cannot load history.
- **Fix:** Implement `return $this->hasMany(Rental::class);`. Then surface rental history on the customer edit/show page.

### TASK-E2 — `last_login_at` never recorded
- **Files:** login flow — `app/Http/Controllers/Auth/LoginController.php` (override `authenticated()`), or an event listener.
- **Problem:** `users.last_login_at` exists and is fillable but is never written. Login tracking implied by the schema is non-functional.
- **Fix:** On successful login, set `$user->last_login_at = now(); $user->save();` (e.g., override `authenticated(Request $request, $user)` in `LoginController`, or listen for the `Login` event).

### TASK-E3 — Customer deletion does not check active rentals
- **Files:** `app/Http/Controllers/ShopAdmin/CustomerController.php` (`destroy`).
- **Problem:** A code comment admits "Should check for active rentals before deleting." Deleting a customer with active rentals can orphan/cascade-delete rental records (FK `onDelete('cascade')`), losing history.
- **Fix:** Before delete, block if the customer has any `Active`/`Overdue` rentals (after TASK-E1 makes the relationship usable). Show an `error` flash.

### TASK-E4 — Tool deletion vs. rental history
- **Files:** `ToolController@destroy`, `rentals` FK.
- **Problem:** `tools` is referenced by `rentals.tool_id` with `onDelete('cascade')`. Deleting a tool (when not currently rented) silently deletes its historical rental records. The controller only blocks deletion when status is `Rented`, not when historical rentals exist.
- **Fix:** Prefer soft-deletes for tools (and possibly rentals/customers) to preserve history, or block hard-delete when any rental rows reference the tool. Decide and document the policy.

---

## F. UI / Library Gaps

### TASK-F1 — DataTables.js not integrated
- **Files:** index views (`tools`, `customers`, `rentals`, `categories`, `super-admin/tenants`, `super-admin/users`), `layouts/admin.blade.php`.
- **Problem:** Plan §2 lists DataTables.js for "Searchable, sortable, paginated tables." All tables are plain Bootstrap with server-side `paginate()` and no search/sort.
- **Fix:** Either (a) add DataTables via CDN and initialize on `.datatable` tables, or (b) implement server-side search/sort/filter controls. Note: DataTables client-side pagination conflicts with Laravel's `paginate()`+`links()`; pick one approach per table and be consistent. (If the new design system is adopted, align this with it.)

### TASK-F2 — SweetAlert2 loaded but not used for confirmations
- **Files:** all views using `onclick="return confirm(...)"`.
- **Problem:** SweetAlert2 is included in the layout (plan §2 wants "clean, non-blocking alerts"), but destructive actions still use the native browser `confirm()`.
- **Fix:** Replace `confirm()` delete/return prompts with SweetAlert2 confirmation dialogs that submit the form on confirm. Optionally show success/error flashes as SweetAlert toasts.

### TASK-F3 — Flatpickr loaded but verify usage on date inputs
- **Files:** `resources/views/shop-admin/rentals/create.blade.php`.
- **Problem:** Flatpickr is included globally; confirm the rental `due_at` field actually initializes it (the `store` validates `due_at` as `date|after:today`). If not wired, the date UX is plain.
- **Fix:** Ensure the `due_at` input is a Flatpickr instance with `minDate: "today"`.

---

## G. Nice-to-Have / Polish

- **TASK-G1 — Dashboard "second card" is a placeholder.** The shop dashboard's right column is a static "Ready for business" marketing card. Replace with something useful: recent rentals, overdue list, or revenue sparkline.
- **TASK-G2 — Super Admin dashboard lacks charts.** Only three stat numbers; plan emphasizes dashboard analytics (Chart.js). Add tenant growth / active-vs-inactive charts.
- **TASK-G3 — `home.blade.php` fallback.** `HomeController` falls back to `home` view for unmapped roles (e.g., floor-staff). Confirm this view is meaningful or redirect appropriately once TASK-D2 is done.
- **TASK-G4 — Validation error display.** Most forms show per-field `@error` blocks, but a global validation summary in the layout would help (tie into TASK-A3).
- **TASK-G5 — `.env` / setup docs.** `composer.json` `setup` script still references `npm install` / `npm run build` even though the project is explicitly "No Node.js." Remove those lines to match the plan and avoid setup failures.
- **TASK-G6 — Seeded credentials.** Document the seeded logins for testing: Super Admin `admin@toolrent.com / admin123`, Shop Admin `shop@test.com / shop123` (tenant slug `testshop`). Consider forcing a password change in production.
- **TASK-G7 — Tests.** No feature/unit tests exist for the tenancy scope, role middleware, or rental lifecycle. Add coverage for tenant isolation (critical for a multi-tenant SaaS).

---

## H. Verification Checklist

After implementing, confirm each item:

- [ ] Visiting `/shop/tools/{id}` renders (TASK-A1).
- [ ] Visiting `/shop/rentals/{id}` renders (TASK-A2).
- [ ] Triggering an error path (e.g., delete a category with tools) shows a red alert (TASK-A3).
- [ ] Changing a tenant's primary color visibly re-themes buttons/sidebar (TASK-B1).
- [ ] Login page on a tenant subdomain shows that tenant's logo/colors (TASK-B2).
- [ ] Uploading a tool image saves and displays it (TASK-C1).
- [ ] Shop Admin can create a Counter Staff user who can then log in (TASK-D1).
- [ ] Floor Staff can log in and mark a tool for maintenance (TASK-D2/D3).
- [ ] `$customer->rentals` returns rentals (TASK-E1).
- [ ] `last_login_at` updates after login (TASK-E2).
- [ ] Tables support search/sort, or this is consciously deferred (TASK-F1).
- [ ] `php artisan migrate:fresh --seed` runs clean.
- [ ] `php artisan route:list` shows no routes pointing to missing views.
- [ ] Multi-tenancy still isolates data: log in as two different tenants and confirm no cross-tenant leakage.

---

## Appendix — Confirmed-working (do NOT redo)

- Multi-tenancy core: `tenant_id` columns, `App\Scopes\TenantScope`, `App\Traits\BelongsToTenant`, subdomain + session resolution in `App\Http\Middleware\TenantScope`.
- Role middleware `App\Http\Middleware\CheckRole` (alias `role`) and role constants in `App\Models\Role`.
- CRUD for Categories, Tools, Customers (index/create/edit views present).
- Rental lifecycle: checkout (`create`/`store`), return (`returnTool`), PDF invoice (`invoice` via DomPDF).
- QR code generation (`ToolController@qrcode` + `qrcode.blade.php`) via simple-qrcode.
- Super Admin: tenant CRUD with feature flags, user list/delete, dashboard stats.
- Feature flags: `Tenant::hasFeature()` gating `invoicing` and `qrcode` in views.
- Asset stack via CDN (Bootstrap, Alpine, Chart.js, Flatpickr, SweetAlert2) — no build step, matching the "No-Node" goal.
