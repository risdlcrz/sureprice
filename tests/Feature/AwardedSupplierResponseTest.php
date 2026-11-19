<?php

use App\Models\Quotation;
use App\Models\QuotationResponse;
use App\Models\Supplier;

function rfq(string $n): Quotation
{
    return Quotation::create(['rfq_number' => $n, 'due_date' => now()->addWeek()]);
}

test('forSupplier returns the response to the first RFQ (in order) that has one, in one query', function () {
    $supplier = Supplier::create(['company_name' => 'Acme', 'contact_person' => 'A', 'phone' => '1', 'email' => 'a@x.test', 'status' => 'active']);
    $other = Supplier::create(['company_name' => 'Other', 'contact_person' => 'B', 'phone' => '2', 'email' => 'b@x.test', 'status' => 'active']);
    [$r1, $r2, $r3] = [rfq('RFQ-1'), rfq('RFQ-2'), rfq('RFQ-3')];

    QuotationResponse::create(['quotation_id' => $r1->id, 'supplier_id' => $other->id, 'total_amount' => 10]);
    $second = QuotationResponse::create(['quotation_id' => $r2->id, 'supplier_id' => $supplier->id, 'total_amount' => 1000,
        'discount_type' => 'bulk', 'discount_percentage' => 5, 'final_amount' => 950]);
    QuotationResponse::create(['quotation_id' => $r3->id, 'supplier_id' => $supplier->id, 'total_amount' => 2000]);

    DB::enableQueryLog();
    $found = QuotationResponse::forSupplier([$r1, $r2, $r3], $supplier->id);
    expect(count(DB::getQueryLog()))->toBe(1)
        ->and($found->id)->toBe($second->id)
        ->and($found->discountSummary())->toMatchArray(['type' => 'bulk', 'value' => '5.00%', 'found' => true])
        ->and(QuotationResponse::forSupplier([$r1], $supplier->id))->toBeNull()
        ->and(QuotationResponse::forSupplier([], $supplier->id))->toBeNull();
});
