# SurePrice — working notes

Laravel 12 procurement / contracts app (XAMPP, MySQL). Roles: `admin`, `manager`, `procurement`, `warehousing`,
`finance` (employees, `user_type = employee`) and client / supplier companies (`user_type = company`,
`companies.designation`, must be `approved`). Where each user lands after login: `User::homeRoute()`.

## Run / verify

```bash
composer install && npm install
php artisan migrate            # MySQL must be running (XAMPP)
npm run dev                    # Vite dev server (public/hot); `npm run build` for production assets
php artisan test               # Pest, in-memory SQLite — must stay green
php artisan view:cache && php artisan view:clear   # compiles every Blade template
php artisan schedule:run       # run daily via Task Scheduler/cron (purchase-order penalties)
```

Before committing a change, run all of the above that apply. Business-logic changes get a feature test in
`tests/Feature` (see `PurchaseOrderLifecycleTest`, `ModelRelationsTest` for fixture patterns).

## Structure and conventions

**Routing / access** — everything is in `routes/web.php` (`routes/auth.php` for password/verification flows;
there is no API routes file). Protect routes with `auth` + `role:a,b` middleware; numeric route params are
constrained globally. Login/register are behind `guest` (signed-in users are redirected to their home route,
`bootstrap/app.php`). Middleware aliases and the redirect are configured in `bootstrap/app.php`; schedules in
`routes/console.php`.

**Views** — one layout (`layouts/app.blade.php`, `layouts/guest.blade.php` for auth). Admin and procurement
share the same page where they used to have copies; create/edit use one form view (`*/form.blade.php` or
`editor.blade.php`); large pages are split into `partials/`. Reuse the components in
`resources/views/components` instead of hand-writing markup:

| Component | Use |
|---|---|
| `<x-page-header title icon>` | page title row (slot = action buttons) |
| `<x-field name label type value>` | label + input + validation error (slot for custom control) |
| `<x-status-badge :status kind>` | coloured badge; colours/labels in `App\Support\Status` |
| `<x-modal-dialog id dialog-class>` | Bootstrap modal shell, `<x-slot:title>` |
| `<x-confirm-modal id title :action confirm tone>` | "are you sure" modal that submits a form |
| `<x-empty-row cols>` | "no records" table row |
| `<x-progress :value>` | progress bar |
| `<x-flash>` | session success/error messages (already in the layout) |
| `<x-nav-count kind>` | live unread counter in the sidebar |
| `<x-messages.new-message-modal>` | compose-message modal |

**CSS / JS** — no inline `<style>` or `<script>` logic in views. Page code lives in `resources/js/pages/*.js`
and `resources/css/pages/*.css`; files there are registered with Vite automatically (`vite.config.js`,
`pageAssets`). Load with `@vite('resources/js/pages/<name>.js')`. Pass server data through
`window.__page`, using `{{ Js::from($value) }}` (not `@json` with comma-containing expressions):

```blade
<script>window.__page = Object.assign(window.__page || {}, { itemsUrl: {{ Js::from(route('contracts.items', $c)) }} });</script>
```

No `console.log` / `Log::info` debugging left in committed code; log errors with `Log::error` only, and never
log `$request->all()` (passwords, personal data).

**Models / queries** — keep `$fillable` / relations in line with the actual schema (there were many stale
ones). Shared queries belong in scopes/helpers, e.g. `User::role('admin', 'manager')`,
`Notification::forUser($user)`, `QuotationResponse::forSupplier()`, `ProjectTask::rules()`,
`Project::STATUSES`. Dashboard counts: `App\Support\DashboardStats`; unread badges: `App\Support\UnreadCounts`
(cached per request); supplier rankings: `SupplierRankingService::cachedRankings()` (cache cleared by model
events).

## Known open items

- Apache DocumentRoot should point at `public/`, and `APP_DEBUG=false` in production.
- The rejected-account appeal address is `SUPPORT_EMAIL` (config `app.support_email`); its default is still a personal address — set it in `.env`.
- `LogAdminActivityMiddleware` exists but is not registered.
- Some analytics queries use MySQL-only functions (`YEAR`, `DATE_FORMAT`, `DATEDIFF`), so those pages
  don't run on SQLite.
- `SupplierDashboardController::ranking()` computes "on-time" as `delivery_date <= delivery_date` (always true);
  it needs an expected-delivery column to compare against.
