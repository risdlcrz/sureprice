<?php

use App\Models\Contract;
use App\Models\Party;
use App\Models\User;

test('the contract details page renders every section', function () {
    $party = fn (string $type, string $name) => Party::create(['entity_type' => $type, 'name' => $name, 'company_name' => "$name Co",
        'street' => '12 Rizal St', 'unit' => '3B', 'barangay' => 'San Roque', 'city' => 'Makati', 'state' => 'NCR', 'postal' => '1200',
        'phone' => '0917', 'email' => strtolower($name) . '@x.test']);
    $contract = Contract::create(['contractor_id' => $party('contractor', 'Builder')->id, 'client_id' => $party('client', 'Owner')->id,
        'contract_number' => 'CT-SHOW-1', 'title' => 'Lobby works', 'total_amount' => 120000, 'start_date' => now(), 'end_date' => now()->addMonths(2),
        'payment_method' => 'bank_transfer', 'payment_terms' => 'Net 30', 'status' => 'draft', 'payment_plan' => 'monthly']);

    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);
    $response = $this->actingAs($admin)->get("/contracts/{$contract->id}");
    if ($to = getenv('DUMP_TO')) file_put_contents($to, $response->getContent());
    $response->assertOk()
        ->assertSee('Builder')->assertSee('Owner')->assertSee('Barangay San Roque')
        ->assertSee('Payment Details')->assertSee('Scope of Work')->assertSee('Contract Items')->assertSee('Signatures')
        ->assertSee($contract->fresh()->contract_number);
});
