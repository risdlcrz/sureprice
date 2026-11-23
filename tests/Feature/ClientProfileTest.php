<?php

use App\Http\Controllers\ContractController;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Party;
use App\Models\Payment;
use App\Models\User;

function cpClient(string $name = 'Mabini Realty'): User
{
    $user = User::factory()->create(['role' => 'client', 'user_type' => 'company']);
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username,
        'company_name' => $name, 'designation' => 'client', 'status' => 'approved']);

    return $user;
}

function cpParty(string $type, string $email, ?int $userId = null, ?string $company = null): Party
{
    return Party::create(['type' => $type, 'entity_type' => $company ? 'company' : 'person', 'name' => 'Juan Dela Cruz', 'company_name' => $company,
        'street' => '1 Rizal St', 'barangay' => 'San Roque', 'city' => 'Makati', 'state' => 'Metro Manila', 'postal' => '1200',
        'phone' => '0917', 'email' => $email, 'user_id' => $userId]);
}

function cpContract(Party $client, string $status = 'approved'): Contract
{
    return Contract::create(['contractor_id' => cpParty('contractor', uniqid('b') . '@x.test')->id, 'client_id' => $client->id,
        'contract_number' => uniqid('CT-'), 'title' => 'Kitchen renovation', 'total_amount' => 1000, 'start_date' => now(), 'end_date' => now()->addMonth(),
        'payment_method' => 'bank_transfer', 'payment_terms' => 'Net 30', 'status' => $status, 'payment_plan' => 'monthly']);
}

test('a new client without any contract gets friendly empty pages, not "no client profile found"', function () {
    $this->actingAs(cpClient());

    foreach (['/client/payments', '/client/payments/dashboard', '/client/feedback'] as $page) {
        $this->get($page)->assertOk()->assertDontSee('No client profile found');
    }
    $this->get('/client/payments')->assertSee('Payments appear here once you have an approved contract');
    $this->get('/client/payments/dashboard')->assertSee('No payments yet');
    $this->get('/client/feedback')->assertSee('Once a contract is completed and fully paid');
    $this->get('/client/dashboard')->assertOk()->assertDontSee('No client profile found');
});

test('a contract linked to the client account shows its payments even when the contract email differs', function () {
    $client = cpClient();
    $party = cpParty('client', 'typo-' . $client->email, $client->id);   // linked by user_id, email doesn't match
    $contract = cpContract($party);
    Payment::create(['payment_number' => 'PAY-1', 'payable_type' => Contract::class, 'payable_id' => $contract->id, 'contract_id' => $contract->id, 'amount' => 500, 'payment_method' => 'bank_transfer', 'status' => 'pending',
        'due_date' => now()->addWeek(), 'payment_type' => 'client_payment', 'created_by' => $client->id]);

    $this->actingAs($client)->get('/client/payments')->assertOk()->assertSee($contract->contract_number);
    $this->get('/client/payments/dashboard')->assertOk()->assertSee('500');

    // another client never sees it
    $this->actingAs(cpClient('Other Co'))->get('/client/payments')->assertOk()->assertDontSee($contract->contract_number);
});

test('creating a contract links the client party to the client account', function () {
    $link = fn (Party $party, ?int $userId = null) => (fn () => $this->linkClientParty($party, $userId))->call(app(ContractController::class));

    // from the quotation request's user, even with a different email on the contract
    $client = cpClient();
    $party = cpParty('client', 'someone-else@x.test');
    $link($party, $client->id);
    expect($party->fresh()->user_id)->toBe($client->id)
        ->and($client->clientParty()?->id)->toBe($party->id);

    // by matching client email
    $byEmail = cpClient('Bayanihan Foods');
    $party2 = cpParty('client', $byEmail->email);
    $link($party2);
    expect($party2->fresh()->user_id)->toBe($byEmail->id);

    // by client company name
    $byCompany = cpClient('Lakbay Travel');
    $party3 = cpParty('client', 'office@lakbay.test', null, 'Lakbay Travel');
    $link($party3);
    expect($party3->fresh()->user_id)->toBe($byCompany->id);

    // an existing link is never overwritten, and no match leaves it alone
    $link($party, $byEmail->id);
    expect($party->fresh()->user_id)->toBe($client->id);
    $orphan = cpParty('client', 'nobody@x.test');
    $link($orphan);
    expect($orphan->fresh()->user_id)->toBeNull();
});
