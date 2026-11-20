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
constrained globally. Who may use what (tested in `AccessControlTest`):
- **Staff** — `role:admin,manager,procurement,contractor`: materials, inventory, purchase requests, quotations,
  inquiries, invitations, suppliers, projects/tasks, material requests, transactions, warranty management, search.
  Contractors are treated as procurement (`User::hasRole('procurement')` is true for them; same area & home).
- **Contracts / project timeline** — staff manage; clients view only their own (`Contract::visibleTo()` /
  `isVisibleTo()`), finance views all read-only. Check ownership in the controller for any new client-facing
  contract/payment/quotation action.
- **Payments** — `role:admin,manager,finance`; clients use `/client/payments`. Purchase orders are checked per
  action in `PurchaseOrderController` (staff incl. finance; the order's supplier ships; warehouse receives).
- Links to restricted pages must be hidden from roles that can't open them. Login/register are behind `guest` (signed-in users are redirected to their home route,
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

**Design system** — `resources/css/app-layout.css` holds the tokens (`--sp-*`: brand greens, neutrals, radii,
shadows), the app shell (sidebar, mobile drawer) and the Bootstrap component layer (cards, tables, buttons, forms,
soft badges, alerts, modals, KPI tiles). It loads before `@stack('styles')`, so page CSS refines it — but page CSS
must not restyle global elements (`body`, `.container`, `h1`, `.card`, `.btn-*`, `.badge`, form controls); use a
page-specific class instead. Brand: `#1b5e20` (sidebar, primary), `#198754` (success); no blue gradients.
Page titles use `.page-title` (or `<x-page-header>`). Tailwind (`resources/css/app.css`) is only for the Breeze guest
layout — it breaks Bootstrap, so keep it out of `layouts/app`. Sidebars: `include/sidebars/*`, sharing `_brand`
(logo + user card) and `_footer` (logout); the active link is highlighted by `app-layout.js` when not set in markup.

**CSS / JS** — no inline `<style>` or `<script>` logic in views. Page code lives in `resources/js/pages/*.js`
and `resources/css/pages/*.css`; every file under `resources/css` and `resources/js` is a Vite entry automatically
(`vite.config.js`), so any `@vite('resources/...')` resolves in production builds. Load with `@vite('resources/js/pages/<name>.js')`. Pass server data through
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
(cached per request); supplier on-time/late figures use `purchase_orders.is_on_time` (set when an order is completed);
supplier rankings: `SupplierRankingService::cachedRankings()` (cache cleared by model
events).

## Known open items

- Apache DocumentRoot should point at `public/`, and `APP_DEBUG=false` in production.
- The rejected-account appeal address is `SUPPORT_EMAIL` (config `app.support_email`); its default is still a personal address — set it in `.env`.
- `LogAdminActivityMiddleware` exists but is not registered.
- Some analytics queries use MySQL-only functions (`YEAR`, `MONTH`, `DATE_FORMAT`) — supplier dashboard sales chart,
  warehouse reports, feedback and budget analytics — so those pages don't run on SQLite (tests, scratch DBs).
