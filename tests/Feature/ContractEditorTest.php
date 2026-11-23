<?php

use App\Models\Contract;
use App\Models\Party;
use App\Models\User;

function ceContract(): Contract
{
    $party = fn (string $type, string $name) => Party::create(['entity_type' => $type, 'name' => $name, 'company_name' => $name,
        'street' => '1 Rizal St', 'barangay' => 'San Roque', 'city' => 'Makati', 'state' => 'NCR', 'postal' => '1200',
        'phone' => '0917', 'email' => strtolower($name) . '@x.test']);

    return Contract::create(['contractor_id' => $party('contractor', 'Builder')->id, 'client_id' => $party('client', 'Owner')->id,
        'title' => 'Works', 'total_amount' => 1000, 'start_date' => now(), 'end_date' => now()->addMonth(),
        'payment_method' => 'bank_transfer', 'payment_terms' => 'Net 30', 'status' => 'draft', 'payment_plan' => 'monthly']);
}

test('contract numbers run CT<year>0001, 0002, … and nextNumber previews the next one', function () {
    $year = date('Y');

    expect(Contract::nextNumber())->toBe("CT{$year}0001");
    expect(ceContract()->contract_number)->toBe("CT{$year}0001");
    expect(Contract::nextNumber())->toBe("CT{$year}0002");
    expect(ceContract()->contract_number)->toBe("CT{$year}0002");
});

test('the contract editor shows the upcoming contract number and the redesigned sections', function () {
    ceContract();
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);

    $this->actingAs($admin)->get('/contracts/create')
        ->assertOk()
        ->assertSee('CT' . date('Y') . '0002')
        ->assertSee('Assigned when saved')
        ->assertSee('id="contractor_contact_display"', false)
        ->assertSee('name="client[name]" id="client_name"', false)
        ->assertSee('Same as client address')
        ->assertSee('Scope Summary');
});
