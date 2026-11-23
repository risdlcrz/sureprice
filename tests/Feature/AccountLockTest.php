<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

function lockEmployee(string $role = 'procurement', string $username = 'pedro'): User
{
    $user = User::factory()->create(['role' => $role, 'user_type' => 'employee', 'username' => $username,
        'email' => "{$username}@x.test", 'name' => ucfirst($username) . ' Cruz', 'password' => Hash::make('correct-horse')]);
    Employee::create(['user_id' => $user->id, 'first_name' => ucfirst($username), 'last_name' => 'Cruz',
        'email' => $user->email, 'username' => $username, 'role' => $role]);

    return $user;
}

function lockAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'user_type' => 'admin', 'name' => 'Ana Admin']);
}

const GENERIC_LOGIN_ERROR = 'These credentials do not match our records.';

beforeEach(fn () => RateLimiter::clear('pedro|127.0.0.1'));

// ---------------------------------------------------------------- automatic lock + safe sign-in errors

test('five consecutive failed sign-ins lock the account automatically, with the same generic error each time', function () {
    $user = lockEmployee();

    foreach (range(1, User::MAX_FAILED_LOGINS) as $attempt) {
        $this->from('/login')->post('/login', ['login' => 'pedro', 'password' => 'wrong-' . $attempt])
            ->assertRedirect('/login')->assertSessionHasErrors(['login' => GENERIC_LOGIN_ERROR]);
        expect($user->fresh()->isLocked())->toBe($attempt === User::MAX_FAILED_LOGINS);
    }

    $user->refresh();
    expect($user->failed_login_attempts)->toBe(User::MAX_FAILED_LOGINS)
        ->and($user->locked_by)->toBeNull()
        ->and($user->lock_reason)->toBe(User::AUTO_LOCK_REASON)
        ->and($user->locked_at)->not->toBeNull();
    $this->assertGuest();
});

test('a locked account cannot sign in even with the right password, and only then learns it is locked', function () {
    $user = lockEmployee();
    $user->lockBy(lockAdmin(), 'Internal: chargeback investigation #4411');

    // wrong password on a locked account: indistinguishable from any other failure
    $this->from('/login')->post('/login', ['login' => 'pedro', 'password' => 'nope'])
        ->assertSessionHasErrors(['login' => GENERIC_LOGIN_ERROR]);

    // right password: told the account is locked, without the internal reason
    $response = $this->from('/login')->post('/login', ['login' => 'pedro', 'password' => 'correct-horse'])
        ->assertSessionHasErrors(['login' => AuthenticatedSessionController::lockedMessage()]);
    expect(session('errors')->first('login'))->not->toContain('chargeback');
    $this->assertGuest();

    // JSON clients get a 403 with the same safe message
    $this->postJson('/login', ['login' => 'pedro', 'password' => 'correct-horse'])
        ->assertForbidden()->assertExactJson(['message' => AuthenticatedSessionController::lockedMessage()]);
});

test('unknown accounts get the same error as wrong passwords', function () {
    lockEmployee();
    $this->from('/login')->post('/login', ['login' => 'nobody-here', 'password' => 'whatever'])
        ->assertSessionHasErrors(['login' => GENERIC_LOGIN_ERROR]);
    $this->postJson('/login', ['login' => 'nobody-here', 'password' => 'whatever'])
        ->assertStatus(422)->assertExactJson(['message' => GENERIC_LOGIN_ERROR]);
});

test('a successful sign-in resets the failed-attempt count', function () {
    $user = lockEmployee();
    foreach (range(1, User::MAX_FAILED_LOGINS - 1) as $i) {
        $this->post('/login', ['login' => 'pedro', 'password' => 'wrong']);
    }
    expect($user->fresh()->failed_login_attempts)->toBe(User::MAX_FAILED_LOGINS - 1);

    $this->post('/login', ['login' => 'pedro', 'password' => 'correct-horse'])->assertRedirect();
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->failed_login_attempts)->toBe(0)->and($user->fresh()->isLocked())->toBeFalse();
});

// ---------------------------------------------------------------- admin lock / unlock endpoints

test('admins lock and unlock accounts with a reason', function () {
    $admin = lockAdmin();
    $user = lockEmployee();

    $this->actingAs($admin)->post(route('admin.users.lock', $user), ['reason' => 'Left the company'])
        ->assertRedirect()->assertSessionHas('success');
    $user->refresh();
    expect($user->isLocked())->toBeTrue()->and($user->locked_by)->toBe($admin->id)->and($user->lock_reason)->toBe('Left the company');

    $this->actingAs($admin)->delete(route('admin.users.unlock', $user))->assertRedirect()->assertSessionHas('success');
    $user->refresh();
    expect($user->isLocked())->toBeFalse()->and($user->lock_reason)->toBeNull()->and($user->failed_login_attempts)->toBe(0);
});

test('a reason is required, and admins cannot lock themselves', function () {
    $admin = lockAdmin();
    $user = lockEmployee();

    $this->actingAs($admin)->postJson(route('admin.users.lock', $user), ['reason' => ''])->assertStatus(422)->assertJsonValidationErrors('reason');
    $this->actingAs($admin)->postJson(route('admin.users.lock', $admin), ['reason' => 'oops'])->assertStatus(422);
    expect($admin->fresh()->isLocked())->toBeFalse();
});

test('only admins can lock or unlock', function () {
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee']);
    $user = lockEmployee();

    $this->actingAs($manager)->post(route('admin.users.lock', $user), ['reason' => 'x'])->assertForbidden();
    $user->lockBy(lockAdmin(), 'Locked');
    $this->actingAs($manager)->delete(route('admin.users.unlock', $user))->assertForbidden();
    expect($user->fresh()->isLocked())->toBeTrue();
});

// ---------------------------------------------------------------- enforcement for signed-in users

test('a user locked mid-session is signed out: pages redirect to sign-in, writes and JSON get 403', function () {
    $user = lockEmployee();

    $this->actingAs($user)->get('/account')->assertOk();
    $user->lockBy(lockAdmin(), 'Internal note');

    $this->actingAs($user)->get('/account')->assertRedirect(route('login'))->assertSessionHasErrors('login');
    $this->assertGuest();

    $this->actingAs($user->fresh())->put(route('account.update'), ['name' => 'Changed'])->assertForbidden();
    $this->assertGuest();

    $json = $this->actingAs($user->fresh())->getJson('/account')->assertForbidden()->json();
    expect($json['message'])->toBe(AuthenticatedSessionController::lockedMessage())
        ->and(json_encode($json))->not->toContain('Internal note')->not->toContain('trace');
    expect($user->fresh()->name)->toBe('Pedro Cruz');
});

test('managers see locked accounts but cannot edit or delete them; admins see lock controls', function () {
    $admin = lockAdmin();
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee']);
    $user = lockEmployee();
    $employee = Employee::where('user_id', $user->id)->first();
    $user->lockBy($admin, 'Pending HR review');

    $this->actingAs($manager)->get('/information-management?type=employee')->assertOk()
        ->assertSeeText('Locked')->assertSeeText('Pending HR review')->assertSeeText('By Ana Admin')
        ->assertSee('aria-label="Edit (locked)"', false)
        ->assertDontSee('id="unlockUser' . $user->id . '"', false);

    $this->actingAs($manager)->get(route('information-management.edit', $employee->id))->assertForbidden();
    $this->actingAs($manager)->put(route('information-management.update', $employee->id), ['first_name' => 'X'])->assertForbidden();
    $this->actingAs($manager)->delete(route('information-management.destroy', $employee->id))->assertForbidden();
    expect(Employee::find($employee->id))->not->toBeNull();

    $this->actingAs($admin)->get('/information-management?type=employee')->assertOk()
        ->assertSee('id="unlockUser' . $user->id . '"', false)
        ->assertDontSee('aria-label="Edit (locked)"', false);
    $this->actingAs($admin)->get(route('information-management.edit', $employee->id))->assertOk();
});

test('users:unlock restores access when no admin can sign in', function () {
    $admin = lockAdmin();
    $admin->lockBy(null, User::AUTO_LOCK_REASON);

    $this->artisan('users:unlock', ['login' => $admin->email])->assertSuccessful();
    expect($admin->fresh()->isLocked())->toBeFalse();
});

test('JSON error responses never include exception details, even in debug mode', function () {
    config(['app.debug' => true]);
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee']);
    $user = lockEmployee();
    $user->lockBy(lockAdmin(), 'Locked');
    $employee = Employee::where('user_id', $user->id)->first();

    $json = $this->actingAs($manager)->deleteJson(route('information-management.destroy', $employee->id))->assertForbidden()->json();
    expect(array_keys($json))->toBe(['message'])
        ->and($json['message'])->toBe('This account is locked by an administrator. Only an administrator can change it.');
});
