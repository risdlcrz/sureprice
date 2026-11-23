<?php

test('registration screen can be rendered', function () {
    $this->get('/register')->assertStatus(200);
});

test('registration requires a valid account type and does not log anyone in', function () {
    $this->post('/register/company', ['name' => 'Test User', 'email' => 'test@example.com'])
        ->assertSessionHasErrors('type');

    $this->assertGuest();
});

test('company registration validates required fields', function () {
    $this->post('/register/company', ['type' => 'company'])->assertSessionHasErrors();

    $this->assertGuest();
});
test('registration page offers the address dropdowns and the shared option lists', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('data-ph-address', false)
        ->assertSee('name="state"', false)
        ->assertSee('name="barangay"', false)
        ->assertSee(\App\Models\Company::TYPES[0]);

    expect(public_path('data/ph-address/locations.json'))->toBeFile();
});

function supplierRegistration(array $overrides = []): array
{
    $pdf = fn () => \Illuminate\Http\UploadedFile::fake()->create('doc.pdf', 50, 'application/pdf');

    return array_merge([
        'type' => 'company',
        'designation' => 'supplier',
        'username' => 'acme',
        'email' => 'acme@example.com',
        'password' => 'secret-pass-1',
        'password_confirmation' => 'secret-pass-1',
        'company_name' => 'Acme Builders',
        'contact_person' => 'Juan Dela Cruz',
        'mobile_number' => '09171234567',
        'supplier_type' => 'Manufacturing',
        'street' => '123 Rizal St.',
        'barangay' => 'Poblacion',
        'city' => 'City of Makati',
        'state' => 'Metro Manila',
        'postal' => '1210',
        'vat_registered' => '1',
        'use_sureprice' => '1',
        'agree_terms' => '1',
        'dti_sec_registration' => $pdf(),
        'business_permit_mayor_permit' => $pdf(),
        'valid_id_owner_rep' => $pdf(),
    ], $overrides);
}

test('a supplier can register with a dropdown-picked address', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    $this->post('/register/company', supplierRegistration())
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('pending.approval'));

    $company = \App\Models\Company::where('email', 'acme@example.com')->firstOrFail();
    expect($company->only(['street', 'barangay', 'city', 'state', 'postal', 'status']))->toBe([
        'street' => '123 Rizal St.',
        'barangay' => 'Poblacion',
        'city' => 'City of Makati',
        'state' => 'Metro Manila',
        'postal' => '1210',
        'status' => 'pending',
    ]);
    expect($company->documents()->count())->toBe(3);
    $this->assertGuest();
});

test('registration rejects a bad mobile number, ZIP code, short password or missing province', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    $this->post('/register/company', supplierRegistration([
        'mobile_number' => '12345',
        'postal' => '12a',
        'state' => '',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]))->assertSessionHasErrors(['mobile_number', 'postal', 'state', 'password']);

    expect(\App\Models\Company::count())->toBe(0);
});
