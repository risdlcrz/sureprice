<?php

use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\ContractScope;
use App\Models\Party;
use App\Models\Property;
use App\Models\Room;
use App\Models\ScopeType;
use App\Models\User;

function pdfContract(int $itemCount): Contract
{
    $party = fn (string $type, string $name) => Party::create(['entity_type' => $type, 'name' => $name, 'company_name' => "$name Builders",
        'street' => '12 Rizal St', 'barangay' => 'San Roque', 'city' => 'Makati', 'state' => 'NCR', 'postal' => '1200',
        'phone' => '0917 555 0101', 'email' => strtolower($name) . '@x.test']);
    $property = Property::create(['street' => '88 Mabini Ave', 'barangay' => 'Poblacion', 'city' => 'Quezon City', 'state' => 'NCR', 'postal' => '1100']);

    $contract = Contract::create(['contractor_id' => $party('contractor', 'Builder')->id, 'client_id' => $party('client', 'Owner')->id,
        'property_id' => $property->id, 'title' => 'Kitchen works', 'scope_of_work' => 'Kitchen renovation',
        'start_date' => '2026-12-01', 'end_date' => '2027-02-28', 'materials_cost' => 0, 'labor_cost' => 9000, 'total_amount' => 0,
        'payment_method' => 'bank_transfer', 'payment_terms' => 'monthly12', 'payment_plan' => 'monthly12', 'status' => 'draft',
        'contractor_date_signed' => '2026-11-23']);

    $room = Room::create(['contract_id' => $contract->id, 'name' => 'Kitchen', 'length' => 4, 'width' => 3, 'area' => 12]);
    $type = ScopeType::create(['code' => uniqid('TIL'), 'name' => 'Tiling', 'category' => 'Finishes', 'estimated_days' => 5, 'labor_rate' => 500]);
    $scope = ContractScope::create(['room_id' => $room->id, 'scope_type_id' => $type->id, 'scope_name' => 'Tiling', 'scope_category' => 'Finishes']);
    $materials = 0;
    for ($i = 1; $i <= $itemCount; $i++) {
        ContractItem::create(['contract_id' => $contract->id, 'room_id' => $room->id, 'scope_id' => $scope->id,
            'material_name' => "Ceramic floor tile 60x60 #$i", 'unit' => 'pcs', 'supplier_name' => 'Metro Tiles Inc.',
            'quantity' => 40, 'amount' => 125.5, 'total' => 5020]);
        $materials += 5020;
    }
    $contract->update(['materials_cost' => $materials, 'total_amount' => $materials + 9000]);

    return $contract->fresh();
}

function pdfPageCount(string $pdf): int
{
    return preg_match_all('#/Type\s*/Page(?![s\w])#', $pdf);
}

test('the contract PDF shows the header, parties, property, scope totals and signatures', function () {
    $contract = pdfContract(3);
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);

    $response = $this->actingAs($admin)->get("/contracts/{$contract->id}/download");
    $response->assertOk();
    $pdf = $response->getContent();
    if ($to = getenv('DUMP_TO')) file_put_contents($to, $pdf);

    expect($response->headers->get('content-type'))->toContain('application/pdf')
        ->and($pdf)->toMatch('#/MediaBox\s*\[\s*0(\.0+)?\s+0(\.0+)?\s+612(\.0+)?\s+792(\.0+)?\s*\]#') // US Letter, portrait
        ->and(pdfPageCount($pdf))->toBeLessThanOrEqual(2);

    // The HTML itself carries the content (PDF text streams are compressed).
    $html = view('admin.contracts.pdf', ['contract' => $contract, 'contractor' => $contract->contractor, 'client' => $contract->client,
        'property' => $contract->property, 'items' => $contract->items])->render();
    expect($html)->toContain('CONSTRUCTION CONTRACT AGREEMENT', $contract->contract_number, 'November 23, 2026',
        'Builder', 'Owner', '88 Mabini Ave, Brgy. Poblacion, Quezon City, NCR 1100', 'Tiling', 'Metro Tiles Inc.',
        '₱15,060.00', '₱9,000.00', 'Total Contract Amount', '₱24,060.00', 'Month 12',
        'Scope and Standards', 'Contract Price and Payment', 'Schedule and Completion', 'Warranties',
        'IN WITNESS WHEREOF', 'Printed name');
});

// Measured capacity on two pages (parties stacked): 13 materials with a 12-month plan (the longest payment schedule), 14 with 50/50.
// Lists over 8 rows switch the scope table to its denser row style.
test('a contract with 13 materials and a 12-month plan still fits on two pages', function () {
    $contract = pdfContract(13);
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);

    $pdf = $this->actingAs($admin)->get("/contracts/{$contract->id}/download")->assertOk()->getContent();
    if ($to = getenv('DUMP_TO_LONG')) file_put_contents($to, $pdf);

    expect(pdfPageCount($pdf))->toBeLessThanOrEqual(2);
});

test('print preview opens the agreement as a page with print controls instead of downloading a file', function () {
    $contract = pdfContract(3);
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);

    $response = $this->actingAs($admin)->get("/contracts/{$contract->id}/print");

    $response->assertOk()
        ->assertHeader('content-type', 'text/html; charset=UTF-8')
        ->assertSee('CONSTRUCTION CONTRACT AGREEMENT')->assertSee('Total Contract Amount')
        // three separate controls: Close Preview, Print, Save as PDF
        ->assertSee('id="close-preview"', false)->assertSeeText('Close Preview')
        ->assertSee('href="' . route('contracts.show', $contract) . '"', false)
        ->assertSee('id="print-contract"', false)->assertSeeText('Print')
        ->assertSee('id="save-pdf"', false)->assertSeeText('Save as PDF')
        ->assertSee('href="' . route('contracts.download', $contract) . '"', false)
        ->assertDontSeeText('Print / Save as PDF')
        // controls are hidden on paper
        ->assertSee('.preview-bar, .preview-actions, .preview-btn, .no-print { display: none !important; }', false);
    expect($response->headers->get('content-disposition'))->toBeNull();

    // the contract page offers the preview, not a download
    $this->actingAs($admin)->get("/contracts/{$contract->id}")->assertOk()
        ->assertSee('href="' . route('contracts.print', $contract->id) . '"', false)->assertSeeText('Print Preview')
        ->assertDontSee('href="' . route('contracts.download', $contract->id) . '"', false);
});

test('clients cannot open the print preview of another client\'s contract', function () {
    $contract = pdfContract(1);
    $stranger = User::factory()->create(['role' => 'client', 'user_type' => 'company']);
    \App\Models\Company::create(['user_id' => $stranger->id, 'email' => $stranger->email, 'username' => $stranger->username,
        'company_name' => 'Other Co', 'designation' => 'client', 'status' => 'approved']);

    $this->actingAs($stranger)->get("/contracts/{$contract->id}/print")->assertForbidden();
});
