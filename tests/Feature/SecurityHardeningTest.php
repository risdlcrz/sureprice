<?php

use App\Models\Company;
use App\Models\User;

// Self-registration, company approval, password policy, throttles and HTTPS (security audit fixes).

function shCompanyUser(string $designation, string $status): User
{
    $user = User::factory()->create(['role' => $designation, 'user_type' => 'company']);
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username,
        'company_name' => "Acme $status", 'designation' => $designation, 'status' => $status]);

    return $user;
}

test('nobody can self-register a staff account through the public sign-up', function () {
    foreach (['procurement', 'finance', 'warehousing'] as $role) {
        $this->post(route('register.company'), [
            'type' => 'employee', 'role' => $role, 'firstname' => 'Eve', 'lastname' => 'Mallory',
            'username' => "eve_$role", 'email' => "eve_$role@x.test",
            'password' => 'secret-pass-1', 'password_confirmation' => 'secret-pass-1',
        ])->assertSessionHasErrors('type');
    }

    expect(User::where('username', 'like', 'eve_%')->exists())->toBeFalse();
    $this->assertDatabaseCount('employees', 0);
});

test('companies that are not approved get no client or supplier access', function () {
    foreach (['pending', 'rejected'] as $status) {
        $this->actingAs(shCompanyUser('client', $status))->get('/contracts')->assertForbidden();
        $this->actingAs(shCompanyUser('client', $status))->get(route('client.dashboard'))->assertRedirect();
        $this->actingAs(shCompanyUser('supplier', $status))->get(route('supplier.dashboard'))->assertRedirect();
    }

    $this->actingAs(shCompanyUser('client', 'approved'))->get('/contracts')->assertOk();
});

test('passwords need letters and numbers', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->from('/account')->put(route('password.update'), [
        'current_password' => 'password', 'password' => 'onlyletters', 'password_confirmation' => 'onlyletters',
    ])->assertSessionHasErrorsIn('updatePassword', 'password');
});

test('sign-up and password reset requests are throttled per client', function () {
    foreach (range(1, 5) as $_) {
        $this->post(route('register.company'), ['type' => 'company'])->assertStatus(302);
    }
    $this->post(route('register.company'), ['type' => 'company'])->assertStatus(429);

    foreach (range(1, 5) as $_) {
        $this->post(route('password.email'), ['email' => 'nobody@x.test'])->assertStatus(302);
    }
    $this->post(route('password.email'), ['email' => 'nobody@x.test'])->assertStatus(429);
});

test('plain-HTTP requests are redirected to HTTPS in production only', function () {
    $this->get('/login')->assertOk();

    app()->detectEnvironment(fn () => 'production');
    $this->get('http://sureprice.test/login')->assertRedirect('https://sureprice.test/login')->assertStatus(301);
});
