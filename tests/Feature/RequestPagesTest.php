<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\QuotationRequest;
use App\Models\Supplier;
use App\Models\User;

function reqUser(string $role, string $type = 'employee'): User
{
    return User::factory()->create(['role' => $role, 'user_type' => $type]);
}

function reqPurchaseRequest(string $status, User $by, array $extra = []): PurchaseRequest
{
    return PurchaseRequest::create($extra + ['request_number' => uniqid('PR-'), 'requested_by' => $by->id, 'status' => $status]);
}

test('purchase request list filters by status group, type and search, and counts every record', function () {
    $admin = reqUser('admin', 'admin');
    $pending = reqPurchaseRequest('pending_admin_approval', $admin, ['request_number' => 'PR-WAIT-1']);
    $approved = reqPurchaseRequest('approved', $admin, ['request_number' => 'PR-DONE-1', 'is_project_related' => true]);
    reqPurchaseRequest('rejected', $admin, ['request_number' => 'PR-NO-1']);

    $this->actingAs($admin)->get('/purchase-requests')
        ->assertOk()
        ->assertSee('PR-WAIT-1')->assertSee('PR-DONE-1')->assertSee('PR-NO-1')
        ->assertSee('Awaiting approval')
        ->assertDontSee('linear-gradient');

    $this->get('/purchase-requests?status=awaiting')->assertOk()->assertSee('PR-WAIT-1')->assertDontSee('PR-DONE-1');
    $this->get('/purchase-requests?type=project')->assertOk()->assertSee('PR-DONE-1')->assertDontSee('PR-WAIT-1');
    $this->get('/purchase-requests?q=DONE')->assertOk()->assertSee('PR-DONE-1')->assertDontSee('PR-NO-1');

    expect(PurchaseRequest::statusGroupCounts())->toMatchArray(['awaiting' => 1, 'supplier' => 0, 'approved' => 1, 'rejected' => 1, 'all' => 3]);
});

test('purchase request page shows the approval tracker, items and its purchase orders', function () {
    $admin = reqUser('admin', 'admin');
    $supplier = Supplier::create(['company_name' => 'Acme Supply', 'contact_person' => 'Ann', 'phone' => '0917', 'email' => 'acme@example.com', 'status' => 'active']);
    $category = Category::create(['name' => 'Lumber', 'slug' => 'lumber']);
    $material = Material::create(['name' => 'Plywood', 'code' => 'PLY-1', 'unit' => 'pcs', 'category_id' => $category->id]);
    $pr = reqPurchaseRequest('approved', $admin, ['admin_approved' => true, 'supplier_approved' => true, 'total_amount' => 900]);
    PurchaseRequestItem::create(['purchase_request_id' => $pr->id, 'material_id' => $material->id, 'description' => 'Marine plywood', 'quantity' => 3,
        'unit' => 'pcs', 'estimated_unit_price' => 300, 'total_amount' => 900, 'preferred_supplier_id' => $supplier->id]);
    $po = PurchaseOrder::create(['po_number' => 'PO-77', 'purchase_request_id' => $pr->id, 'supplier_id' => $supplier->id, 'status' => 'pending_payment',
        'total_amount' => 900, 'delivery_date' => now()->addWeek(), 'payment_terms' => 'Net 30', 'shipping_terms' => 'FOB']);

    $this->actingAs($admin)->get("/purchase-requests/{$pr->id}")
        ->assertOk()
        ->assertSee('Approval progress')
        ->assertSee('Marine plywood')
        ->assertSee('Acme Supply')
        ->assertSee('PO-77')                                           // the orders section used a relation that didn't exist
        ->assertSee('id="submitPaymentModal' . $po->id . '"', false)
        ->assertDontSee('Create Purchase Order');                      // it already has one
});

test('material request list and page render, with filters and search', function () {
    $manager = reqUser('manager');
    $category = Category::create(['name' => 'Cement', 'slug' => 'cement']);
    $material = Material::create(['name' => 'Portland cement', 'code' => 'CEM-1', 'unit' => 'bag', 'category_id' => $category->id]);
    $mr = MaterialRequest::create(['requested_by' => $manager->id, 'status' => 'pending', 'notes' => 'Deliver to gate 2']);
    MaterialRequestItem::create(['material_request_id' => $mr->id, 'material_id' => $material->id, 'quantity' => 10, 'unit' => 'bag', 'fulfilled_quantity' => 4]);
    $done = MaterialRequest::create(['requested_by' => $manager->id, 'status' => 'completed']);

    $this->actingAs($manager)->get('/material-requests')->assertOk()->assertSee("MR-{$mr->id}")->assertSee("MR-{$done->id}");
    $this->get('/material-requests?status=pending')->assertOk()->assertSee("MR-{$mr->id}")->assertDontSee("MR-{$done->id}<", false);
    $this->get("/material-requests?q=MR-{$done->id}")->assertOk()->assertSee("MR-{$done->id}")->assertDontSee("MR-{$mr->id}<", false);

    $this->get("/material-requests/{$mr->id}")
        ->assertOk()
        ->assertSee('Portland cement')
        ->assertSee('Deliver to gate 2')
        ->assertSee('40% filled from stock');

    $warehouse = reqUser('warehousing');
    $this->actingAs($warehouse)->get('/warehouse/material-requests')->assertOk()->assertSee("MR-{$mr->id}")->assertSee('Approve');
    $this->get("/warehouse/material-requests/{$mr->id}")->assertOk()->assertSee('Waiting for warehouse approval');
});

test('suppliers see their purchase requests with an approve call-to-action', function () {
    $admin = reqUser('admin', 'admin');
    $supplierUser = reqUser('supplier', 'company');
    Company::create(['user_id' => $supplierUser->id, 'email' => $supplierUser->email, 'username' => $supplierUser->username,
        'company_name' => 'Acme Supply', 'designation' => 'supplier', 'status' => 'approved']);
    $supplier = Supplier::create(['company_name' => 'Acme Supply', 'contact_person' => 'Ann', 'phone' => '0917', 'user_id' => $supplierUser->id,
        'email' => 'acme2@example.com', 'status' => 'active']);
    $category = Category::create(['name' => 'Lumber', 'slug' => 'lumber']);
    $material = Material::create(['name' => 'Plywood', 'code' => 'PLY-1', 'unit' => 'pcs', 'category_id' => $category->id]);
    $pr = reqPurchaseRequest('pending_supplier_approval', $admin, ['request_number' => 'PR-SUP-1', 'admin_approved' => true]);
    PurchaseRequestItem::create(['purchase_request_id' => $pr->id, 'material_id' => $material->id, 'description' => 'Plywood', 'quantity' => 2,
        'unit' => 'pcs', 'estimated_unit_price' => 100, 'total_amount' => 200, 'preferred_supplier_id' => $supplier->id]);

    $this->actingAs($supplierUser)->get('/supplier/purchase-requests')->assertOk()->assertSee('PR-SUP-1')->assertSee('Review &amp; approve', false);
    $this->get("/supplier/purchase-requests/{$pr->id}")->assertOk()->assertSee('Waiting for your approval')->assertSee('Approve request');
});

test('quotation request pages render for the manager, and a sent request no longer offers "send"', function () {
    $manager = reqUser('manager');
    $client = reqUser('client', 'company');
    $new = QuotationRequest::create(['user_id' => $client->id, 'request_number' => 'QR-NEW', 'status' => 'pending']);
    $sent = QuotationRequest::create(['user_id' => $client->id, 'request_number' => 'QR-SENT', 'status' => 'reviewed']);

    $this->actingAs($manager)->get('/manager/quotations')->assertOk()->assertSee('QR-NEW')->assertSee('QR-SENT');
    $this->get('/manager/quotations?tab=supplier')->assertOk()->assertSee('Supplier RFQs');

    $this->get("/manager/quotation-requests/{$new->id}/view")->assertOk()->assertSee('Send to all suppliers')->assertSee($client->name);
    $this->get("/manager/quotation-requests/{$sent->id}/view")->assertOk()->assertDontSee('Send to all suppliers')->assertSee('Sent to suppliers.');

    $this->get("/admin/quotation-requests/{$new->id}/review")->assertOk()->assertSee('QR-NEW')->assertDontSee('recommendAllBtn');
});

function reqQuotation(User $client, string $status, array $extra = []): QuotationRequest
{
    $qr = QuotationRequest::create($extra + ['user_id' => $client->id, 'request_number' => uniqid('QR-'), 'status' => $status]);
    $room = $qr->rooms()->create(['name' => 'Master bedroom', 'length' => 4, 'width' => 3, 'height' => 2.5]);
    $category = Category::firstOrCreate(['slug' => 'paint'], ['name' => 'Paint']);
    $material = Material::firstOrCreate(['code' => 'PNT-1'], ['name' => 'Latex paint', 'unit' => 'gal', 'category_id' => $category->id]);
    $scopeType = App\Models\ScopeType::firstOrCreate(['code' => 'PAINT'], ['name' => 'Painting', 'category' => 'Finishing', 'estimated_days' => 2, 'labor_rate' => 500]);
    $room->scopes()->create(['scope_type_id' => $scopeType->id, 'scope_name' => 'Painting', 'scope_category' => 'Finishing',
        'selected_materials' => [['material_id' => $material->id, 'quantity' => 3, 'unit' => 'gal']]]);

    return $qr;
}

function reqClient(): User
{
    $client = reqUser('client', 'company');
    Company::create(['user_id' => $client->id, 'email' => $client->email, 'username' => $client->username,
        'company_name' => 'Client Co', 'designation' => 'client', 'status' => 'approved']);

    return $client;
}

test('clients see their quotation request details: rooms, materials, progress and next step', function () {
    $client = reqClient();
    $pending = reqQuotation($client, 'pending', ['notes' => 'Prefer low-VOC paint']);
    $done = reqQuotation($client, 'proceeded', ['contract_data' => ['property_address' => '12 Rizal St., Makati', 'payment_plan' => '50/50', 'payment_method' => 'bank_transfer']]);

    $this->actingAs($client)->get('/client/quotation')
        ->assertOk()->assertSee($pending->request_number)->assertSee('Collecting supplier quotes')->assertSee('Master bedroom');

    $this->get('/client/quotation/view?id=' . $pending->id)
        ->assertOk()
        ->assertSee('Request progress')
        ->assertSee("We're collecting quotes from suppliers.", false)
        ->assertSee('Master bedroom')->assertSee('12 m²', false)
        ->assertSee('Painting')->assertSee('Latex paint')->assertSee('3 gal')
        ->assertSee('Prefer low-VOC paint')
        ->assertDontSee('linear-gradient');

    $this->get('/client/quotation/view?id=' . $done->id)
        ->assertOk()->assertSee('Thank you for proceeding!')->assertSee('12 Rizal St., Makati')->assertSee('Payment schedule')
        ->assertSee('50% down, 50% on completion');

    // another client's request is not shown
    $other = reqQuotation(reqUser('client', 'company'), 'pending');
    $this->get('/client/quotation/view?id=' . $other->id)->assertRedirect(route('client.quotation.index'));
});

test('staff quotation pages show the client\'s requested quantities per room', function () {
    $qr = reqQuotation(reqUser('client', 'company'), 'pending');

    $this->actingAs(reqUser('manager'))->get("/manager/quotation-requests/{$qr->id}/view")
        ->assertOk()->assertSee('Rooms &amp; scope of work', false)->assertSee('Latex paint')->assertSee('3 gal');
    $this->get("/admin/quotation-requests/{$qr->id}/review")->assertOk()->assertSee('Latex paint')->assertSee('3 gal');
});
