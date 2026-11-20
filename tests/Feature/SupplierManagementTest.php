<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\User;

function supplierFixture(): array
{
    // A client company is created first so company IDs and supplier IDs differ (as in real data).
    $clientUser = User::factory()->create(['role' => 'client', 'user_type' => 'company']);
    $clientCompany = Company::create(['user_id' => $clientUser->id, 'email' => $clientUser->email, 'username' => $clientUser->username,
        'company_name' => 'Client Co', 'designation' => 'client', 'status' => 'approved']);

    $supplierUser = User::factory()->create(['role' => 'supplier', 'user_type' => 'company']);
    $supplierCompany = Company::create(['user_id' => $supplierUser->id, 'email' => $supplierUser->email, 'username' => $supplierUser->username,
        'company_name' => 'Acme Supply', 'designation' => 'supplier', 'status' => 'approved']);
    $supplier = Supplier::create(['company_name' => 'Acme Supply', 'contact_person' => 'Ann', 'phone' => '0917',
        'user_id' => $supplierUser->id, 'email' => 'acme@example.com', 'status' => 'active']);

    $category = Category::create(['name' => 'Lumber', 'slug' => 'lumber']);
    $material = Material::create(['name' => 'Plywood', 'code' => 'PLY-1', 'unit' => 'pcs', 'category_id' => $category->id]);
    $supplier->materials()->attach($material->id, ['price' => 450, 'lead_time' => 3]);

    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);

    return compact('admin', 'clientUser', 'clientCompany', 'supplierUser', 'supplierCompany', 'supplier', 'material');
}

test('the supplier is linked to its own company account', function () {
    ['supplier' => $supplier, 'supplierCompany' => $company] = supplierFixture();

    expect($supplier->company->is($company))->toBeTrue();
});

test('admins can open the supplier edit form', function () {
    ['admin' => $admin, 'supplier' => $supplier] = supplierFixture();

    $this->actingAs($admin)->get(route('suppliers.edit', $supplier))
        ->assertOk()
        ->assertSee('Edit Supplier')
        ->assertSee('name="company_name"', false)
        ->assertSee(route('suppliers.update', $supplier), false)
        ->assertDontSee('no longer used');
});

test('updating a supplier saves its details and keeps its material links', function () {
    ['admin' => $admin, 'supplier' => $supplier, 'material' => $material] = supplierFixture();

    $this->actingAs($admin)->put(route('suppliers.update', $supplier), [
        'company_name' => 'Acme Supply Inc.', 'contact_person' => 'Ben', 'email' => 'sales@acme.example',
        'phone' => '0918', 'address' => 'Manila', 'status' => 'inactive',
    ])->assertRedirect(route('suppliers.index'));

    $supplier->refresh();
    expect($supplier->company_name)->toBe('Acme Supply Inc.')
        ->and($supplier->contact_person)->toBe('Ben')
        ->and($supplier->status)->toBe('inactive')
        ->and($supplier->materials()->whereKey($material->id)->exists())->toBeTrue();
});

test('clients and suppliers cannot manage suppliers', function () {
    ['clientUser' => $client, 'supplierUser' => $supplierUser, 'supplier' => $supplier] = supplierFixture();

    foreach ([$client, $supplierUser] as $user) {
        expect($this->actingAs($user)->get(route('suppliers.index'))->status())->not->toBe(200);
        $this->actingAs($user)->put(route('suppliers.update', $supplier), [
            'company_name' => 'Hacked', 'contact_person' => 'X', 'email' => 'x@example.com', 'phone' => '1', 'status' => 'active',
        ]);
    }

    expect($supplier->fresh()->company_name)->toBe('Acme Supply');
});

test('supplier list approval buttons target the supplier\'s company, not a company with the same id', function () {
    ['admin' => $admin, 'supplierCompany' => $supplierCompany, 'clientCompany' => $clientCompany] = supplierFixture();

    $this->actingAs($admin)->get(route('suppliers.index'))
        ->assertOk()
        ->assertSee(route('admin.companies.reject', $supplierCompany), false)
        ->assertDontSee(route('admin.companies.reject', $clientCompany), false);
});
