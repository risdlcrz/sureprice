<?php

use App\Models\Company;
use App\Models\Contract;
use App\Models\Party;
use App\Models\QuotationRequest;
use App\Models\User;

// Who may use which internal pages: staff (admin, manager, procurement, contractor) run projects & procurement;
// clients and finance only view contracts (clients just their own); suppliers and warehouse have their own areas.

function acUser(string $role, string $type = 'employee'): User
{
    return User::factory()->create(['role' => $role, 'user_type' => $type]);
}

function acClient(string $name): User
{
    $user = acUser('client', 'company');
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username,
        'company_name' => $name, 'designation' => 'client', 'status' => 'approved']);

    return $user;
}

function acContract(User $clientUser, string $number): Contract
{
    $party = fn (string $type, string $name, string $email, ?int $userId = null) => Party::create(['entity_type' => $type, 'name' => $name,
        'company_name' => $name, 'street' => '1 Rizal St', 'barangay' => 'San Roque', 'city' => 'Makati', 'state' => 'NCR', 'postal' => '1200',
        'phone' => '0917', 'email' => $email, 'user_id' => $userId]);

    return Contract::create(['contractor_id' => $party('contractor', "Builder $number", "b$number@x.test")->id,
        'client_id' => $party('client', $clientUser->company->company_name, $clientUser->email, $clientUser->id)->id,
        'contract_number' => $number, 'title' => 'Works', 'total_amount' => 1000, 'start_date' => now(), 'end_date' => now()->addMonth(),
        'payment_method' => 'bank_transfer', 'payment_terms' => 'Net 30', 'status' => 'draft', 'payment_plan' => 'monthly']);
}

const STAFF_ONLY_PAGES = ['/materials', '/inventory', '/projects', '/purchase-requests', '/quotations', '/inquiries',
    '/transactions', '/warranty-requests', '/supplier-invitations', '/suppliers', '/material-requests', '/history-dashboard',
    '/project-dashboard', '/search/users', '/admin/suppliers/rankings', '/contracts/create'];

test('clients, suppliers, warehouse and finance cannot open the staff pages', function () {
    $outsiders = [acClient('Client Co'), acUser('supplier', 'company'), acUser('warehousing'), acUser('finance')];

    foreach ($outsiders as $user) {
        foreach (STAFF_ONLY_PAGES as $uri) {
            expect($this->actingAs($user)->get($uri)->status())->toBe(403, "{$user->role} should not open $uri");
        }
    }
});

test('staff, including contractors, can open the staff pages', function () {
    foreach (['admin', 'manager', 'procurement', 'contractor'] as $role) {
        $user = acUser($role, $role === 'admin' ? 'admin' : 'employee');
        foreach (['/materials', '/purchase-requests', '/projects', '/contracts', '/project-timeline'] as $uri) {
            expect($this->actingAs($user)->get($uri)->status())->toBe(200, "$role should open $uri");
        }
    }
});

test('contractors share the procurement area', function () {
    $contractor = acUser('contractor');

    expect($contractor->homeRoute())->toBe('procurement.dashboard');
    $this->actingAs($contractor)->get('/procurement/dashboard')->assertOk();
});

test('clients only see their own contracts', function () {
    $alice = acClient('Alice Homes');
    $bob = acClient('Bob Builders');
    $own = acContract($alice, 'CT-ALICE');
    $other = acContract($bob, 'CT-BOB');

    $this->actingAs($alice)->get('/contracts')->assertOk()->assertSee('CT-ALICE')->assertDontSee('CT-BOB')->assertDontSee('New Contract');
    $this->actingAs($alice)->get("/contracts/{$own->id}")->assertOk();
    $this->actingAs($alice)->get("/contracts/{$other->id}")->assertForbidden();
    $this->actingAs($alice)->get("/contracts/{$other->id}/download")->assertForbidden();
    $this->actingAs($alice)->get("/contracts/{$other->id}/items")->assertForbidden();
    $this->actingAs($alice)->get("/contracts/{$own->id}/edit")->assertForbidden();
});

test('finance can view contracts but not change them', function () {
    $contract = acContract(acClient('Client Co'), 'CT-FIN');
    $finance = acUser('finance');

    $this->actingAs($finance)->get("/contracts/{$contract->id}")->assertOk()->assertDontSee('Edit Contract');
    $this->actingAs($finance)->get("/contracts/{$contract->id}/edit")->assertForbidden();
    $this->actingAs($finance)->delete("/contracts/{$contract->id}")->assertForbidden();
    expect(Contract::find($contract->id))->not->toBeNull();
});

test('a client cannot cancel or sign another client\'s quotation request', function () {
    $alice = acClient('Alice Homes');
    $bob = acClient('Bob Builders');
    $request = QuotationRequest::create(['user_id' => $bob->id, 'request_number' => 'QR-BOB', 'status' => 'pending']);

    $this->actingAs($alice)->post("/client/quotation/{$request->id}/cancel")->assertNotFound();
    $this->actingAs($alice)->get("/client/quotation/{$request->id}/contract")->assertNotFound();
    expect($request->fresh()->status)->toBe('pending');

    // Staff never use these client-only actions.
    $this->actingAs(acUser('procurement'))->post("/client/quotation/{$request->id}/cancel")->assertForbidden();
});

test('a client can only open payment proofs for their own contracts', function () {
    $alice = acClient('Alice Homes');
    $bobContract = acContract(acClient('Bob Builders'), 'CT-BOB');
    $payment = App\Models\Payment::create(['payment_number' => 'PAY-BOB-1', 'payable_type' => Contract::class, 'payable_id' => $bobContract->id,
        'contract_id' => $bobContract->id, 'amount' => 500, 'payment_method' => 'bank_transfer', 'due_date' => now(), 'created_by' => $alice->id,
        'status' => 'pending', 'client_payment_proof' => 'payments/bob.pdf']);

    $this->actingAs($alice)->get("/payments/{$payment->id}/proof")->assertForbidden();
    // Staff are not blocked by the ownership check (the file itself doesn't exist here, hence 404).
    $this->actingAs(acUser('finance'))->get("/payments/{$payment->id}/proof")->assertNotFound();
});
