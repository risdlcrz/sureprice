<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SupplierRankingService;
use Illuminate\Support\Facades\Cache;

function roleUser(string $role, string $type = 'employee'): User
{
    return User::factory()->create(['role' => $role, 'user_type' => $type]);
}

function makeSupplier(string $name): Supplier
{
    return Supplier::create(['company_name' => $name, 'contact_person' => 'C', 'phone' => '1', 'email' => strtolower(str_replace(' ', '', $name)) . '@x.test', 'status' => 'active']);
}

test('supplier rankings: admins can evaluate, procurement only views', function () {
    makeSupplier('Acme Lumber');

    $this->actingAs(roleUser('admin', 'admin'))->get('/supplier-rankings')
        ->assertOk()->assertSee('Acme Lumber')->assertSee('id="evaluationModal"', false);

    $this->actingAs(roleUser('procurement'))->get('/procurement/suppliers/rankings')
        ->assertOk()->assertSee('Acme Lumber')->assertDontSee('id="evaluationModal"', false);
});

test('supplier rankings search filters by company name', function () {
    makeSupplier('Acme Lumber');
    makeSupplier('Zenith Paints');

    $this->actingAs(roleUser('admin', 'admin'))->get('/supplier-rankings?search=Zenith')
        ->assertOk()->assertSee('Zenith Paints')->assertDontSee('Acme Lumber');
});

test('saving supplier metrics refreshes the cached rankings', function () {
    $supplier = makeSupplier('Acme Lumber');
    app(SupplierRankingService::class)->cachedRankings();
    expect(Cache::has('supplier.rankings.light'))->toBeTrue();

    $supplier->metrics()->create(['total_deliveries' => 1]);

    expect(Cache::has('supplier.rankings.light'))->toBeFalse();
});

test('analytics hub shows each role its own links', function () {
    $this->actingAs(roleUser('admin', 'admin'))->get('/analytics-dashboard')
        ->assertOk()->assertSee(route('admin.feedback.analytics'), false);

    $this->actingAs(roleUser('procurement'))->get('/procurement/analytics')
        ->assertOk()->assertSee(route('procurement.suppliers.rankings'), false)->assertDontSee(route('admin.feedback.analytics'), false);
});

test('project dashboard: project section for admin only, spend excludes cancelled orders', function () {
    $admin = roleUser('admin', 'admin');
    $supplier = makeSupplier('Acme Lumber');
    $pr = PurchaseRequest::create(['request_number' => 'PR-77', 'requested_by' => $admin->id, 'status' => 'approved']);
    $base = ['purchase_request_id' => $pr->id, 'supplier_id' => $supplier->id, 'delivery_date' => now()->addWeek(), 'payment_terms' => 'Net 30', 'shipping_terms' => 'FOB'];
    PurchaseOrder::create($base + ['po_number' => 'PO-1', 'status' => 'paid', 'total_amount' => 1000]);
    PurchaseOrder::create($base + ['po_number' => 'PO-2', 'status' => 'cancelled', 'total_amount' => 5000]);

    $this->actingAs($admin)->get('/project-dashboard')
        ->assertOk()->assertSee('Project Management')->assertSee('₱1,000.00')->assertSee('PR PR-77');

    $this->actingAs(roleUser('procurement'))->get('/procurement/projects')
        ->assertOk()->assertDontSee('Project Management')->assertSee('₱1,000.00');
});
