<?php

use App\Models\Company;
use App\Models\Employee;
use App\Models\QuotationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function asCompanyUser(string $designation, ?string $companyName = 'Mabini Realty'): User
{
    $user = User::factory()->create(['role' => $designation, 'user_type' => 'company', 'password' => Hash::make('old-password-1')]);
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username, 'company_name' => $companyName,
        'contact_person' => 'Juan Dela Cruz', 'mobile_number' => '09171234567', 'designation' => $designation, 'status' => 'approved',
        'street' => '1 Rizal St', 'barangay' => 'Poblacion', 'city' => 'City of Makati', 'state' => 'Metro Manila', 'postal' => '1210']);

    return $user;
}

test('clients land on their dashboard after signing in, with their quotations and next steps', function () {
    $client = asCompanyUser('client');
    expect($client->homeRoute())->toBe('client.dashboard');

    $this->post('/login', ['login' => $client->email, 'password' => 'old-password-1'])->assertRedirect(route('client.dashboard'));

    QuotationRequest::create(['user_id' => $client->id, 'request_number' => 'QR-READY', 'status' => 'reviewed']);
    $this->get('/client/dashboard')
        ->assertOk()
        ->assertSee('Welcome, Juan')
        ->assertSee('Supplier quotes are ready for QR-READY')
        ->assertSee('Open quotation requests')
        ->assertSee('Upcoming payments')
        ->assertSee('My contracts')
        ->assertSee('href="' . route('client.dashboard') . '"', false)   // sidebar "Dashboard" goes to the real dashboard
        ->assertSee('Account settings');
});

test('every kind of account can open its settings page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);
    $employeeUser = User::factory()->create(['role' => 'procurement', 'user_type' => 'employee']);
    Employee::create(['user_id' => $employeeUser->id, 'username' => $employeeUser->username, 'first_name' => 'Pia', 'last_name' => 'Reyes', 'email' => $employeeUser->email, 'role' => 'procurement']);

    $this->actingAs($admin)->get('/account')->assertOk()->assertSee('Full name')->assertSee('Change password');
    $this->actingAs($employeeUser)->get('/account')->assertOk()->assertSee('First name')->assertSee('Pia');
    $this->actingAs(asCompanyUser('client'))->get('/account')->assertOk()->assertSee('Contact details')->assertSee('data-ph-address', false);
    $this->actingAs(asCompanyUser('supplier', 'Acme Supply'))->get('/account')->assertOk()
        ->assertSee(route('supplier.profile.edit'))->assertDontSee('Contact details');
    $this->get('/account')->assertOk();
});

test('employees update their name, username and email, kept in sync with the employee record', function () {
    $user = User::factory()->create(['role' => 'procurement', 'user_type' => 'employee']);
    $employee = Employee::create(['user_id' => $user->id, 'username' => $user->username, 'first_name' => 'Pia', 'last_name' => 'Reyes', 'email' => $user->email, 'role' => 'procurement']);

    $this->actingAs($user)->put('/account', ['first_name' => 'Pia Marie', 'last_name' => 'Reyes-Cruz', 'username' => 'pia.reyes', 'email' => 'pia@sureprice.test'])
        ->assertRedirect(route('account.edit'))->assertSessionHas('success');

    expect($user->fresh()->only(['name', 'username', 'email']))->toBe(['name' => 'Pia Marie Reyes-Cruz', 'username' => 'pia.reyes', 'email' => 'pia@sureprice.test'])
        ->and($employee->fresh()->only(['first_name', 'last_name', 'username', 'email']))->toBe(['first_name' => 'Pia Marie', 'last_name' => 'Reyes-Cruz', 'username' => 'pia.reyes', 'email' => 'pia@sureprice.test']);
});

test('clients update their contact details and address; usernames and emails must stay unique', function () {
    $client = asCompanyUser('client', null);   // individual client: shown by contact name
    $taken = User::factory()->create(['username' => 'taken_name', 'email' => 'taken@x.test']);

    $this->actingAs($client)->put('/account', ['username' => 'taken_name', 'email' => 'taken@x.test'])
        ->assertSessionHasErrors(['username', 'email', 'contact_person', 'mobile_number', 'street', 'postal']);

    $this->put('/account', [
        'username' => 'juan.dc', 'email' => 'juan@x.test', 'contact_person' => 'Juan P. Dela Cruz', 'mobile_number' => '09181112222',
        'telephone_number' => '', 'street' => '22 Mabini St', 'barangay' => 'Lahug', 'city' => 'City of Cebu', 'state' => 'Cebu', 'postal' => '6000',
    ])->assertRedirect(route('account.edit'));

    expect($client->fresh()->only(['name', 'username', 'email']))->toBe(['name' => 'Juan P. Dela Cruz', 'username' => 'juan.dc', 'email' => 'juan@x.test'])
        ->and($client->company->fresh()->only(['email', 'username', 'contact_person', 'mobile_number', 'city', 'state', 'postal']))->toBe([
            'email' => 'juan@x.test', 'username' => 'juan.dc', 'contact_person' => 'Juan P. Dela Cruz', 'mobile_number' => '09181112222',
            'city' => 'City of Cebu', 'state' => 'Cebu', 'postal' => '6000',
        ]);
});

test('users change their password after confirming the current one', function () {
    $user = asCompanyUser('supplier', 'Acme Supply');

    $this->actingAs($user)->from('/account')->put('/password', ['current_password' => 'wrong', 'password' => 'new-password-1', 'password_confirmation' => 'new-password-1'])
        ->assertSessionHasErrorsIn('updatePassword', ['current_password']);

    $this->from('/account')->put('/password', ['current_password' => 'old-password-1', 'password' => 'new-password-1', 'password_confirmation' => 'new-password-1'])
        ->assertRedirect('/account')->assertSessionHas('status', 'password-updated');
    expect(Hash::check('new-password-1', $user->fresh()->password))->toBeTrue();

    $this->get('/account')->assertSee('Your password was changed.');
});
