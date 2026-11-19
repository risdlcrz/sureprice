<?php

use App\Models\User;

test('signed-in users opening the login page go to their own home page', function (string $role, string $home) {
    $user = User::factory()->create(['role' => $role, 'user_type' => 'employee']);

    $this->actingAs($user)->get(route('login'))->assertRedirect(route($home));
})->with([
    'admin' => ['admin', 'admin.dashboard'],
    'procurement' => ['procurement', 'procurement.dashboard'],
    'finance' => ['finance', 'finance.dashboard'],
]);
