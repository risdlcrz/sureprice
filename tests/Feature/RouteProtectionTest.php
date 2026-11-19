<?php

use Illuminate\Support\Facades\Route;

// Routes deliberately reachable without logging in. Anything else must reject guests.
const PUBLIC_ROUTES = [
    '/', 'login', 'logout', 'register', 'register/company', 'pending-approval', 'privacy-policy', 'terms-conditions',
    'client/quotation/create', 'client/quotation/store', 'invitations/{code}', 'up', 'sanctum/csrf-cookie',
    'forgot-password', 'reset-password', 'reset-password/{token}',
];

test('every non-public route requires authentication', function () {
    $unprotected = collect(Route::getRoutes()->getRoutes())
        ->reject(fn ($r) => in_array($r->uri(), PUBLIC_ROUTES, true) || str_starts_with($r->uri(), 'storage/'))
        ->reject(fn ($r) => collect($r->gatherMiddleware())->contains(fn ($m) => is_string($m) && (str_starts_with($m, 'auth') || str_starts_with($m, 'guest') || str_contains($m, 'Authenticate'))))
        ->map(fn ($r) => implode('|', $r->methods()) . ' ' . $r->uri())
        ->values()->all();

    expect($unprotected)->toBe([]);
});

test('guests are redirected away from internal pages', function (string $uri) {
    $this->get($uri)->assertRedirect();
    $this->assertGuest();
})->with(['/notifications', '/purchase-orders', '/contracts', '/admin/logs', '/search/users?q=a', '/material-requests']);
