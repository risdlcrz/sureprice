<?php

use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use App\Models\SupplierMetrics;
use App\Models\User;
use App\Services\SupplierRankingService;

function rkSupplier(string $name, float $evaluationScore): Supplier
{
    $supplier = Supplier::create(['company_name' => $name, 'contact_person' => 'Contact', 'phone' => '0917',
        'email' => strtolower(str_replace(' ', '', $name)) . '@x.test', 'status' => 'active']);
    SupplierEvaluation::create(['supplier_id' => $supplier->id, 'evaluation_date' => now(),
        'delivery_speed_score' => $evaluationScore, 'quality_score' => $evaluationScore, 'cost_variance_score' => $evaluationScore,
        'performance_score' => $evaluationScore, 'engagement_score' => $evaluationScore, 'sustainability_score' => $evaluationScore,
        'delivery_ontime_ratio' => 1, 'defect_ratio' => 0, 'cost_variance_ratio' => 0, 'final_score' => $evaluationScore]);
    SupplierMetrics::forceCreate(['supplier_id' => $supplier->id, 'total_deliveries' => 10, 'ontime_deliveries' => 9,
        'total_units' => 100, 'defective_units' => 2, 'actual_cost' => 1000, 'estimated_cost' => 1000]);

    return $supplier;
}

beforeEach(fn () => SupplierRankingService::forgetCache());

test('the rankings page shows the overall top three with podium badges, gaps and aligned scores', function () {
    rkSupplier('Pilipinas Prime Supply', 4.8);
    rkSupplier('Luntian Hardware', 4.6);
    rkSupplier('Silangan Steel & Cement', 4.2);
    rkSupplier('Bagong Lakas Trading', 3.0);
    Supplier::create(['company_name' => 'Unrated Co', 'contact_person' => 'U', 'phone' => '1', 'email' => 'u@x.test', 'status' => 'active']);

    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);
    $response = $this->actingAs($admin)->get('/admin/suppliers/rankings')->assertOk();

    $top = $response->viewData('topSuppliers');
    expect($top->pluck('name')->all())->toBe(['Pilipinas Prime Supply', 'Luntian Hardware', 'Silangan Steel & Cement'])
        ->and($top->pluck('rank')->all())->toBe([1, 2, 3]);

    $response->assertSee('rk-badge--1', false)->assertSee('rk-badge--2', false)->assertSee('rk-badge--3', false)
        ->assertSeeText('Leader')->assertSee('<span class="rk-delta">&minus;', false)->assertSee('vs #1')
        // one rank number per row: a plain list, not an <ol> next to the badge
        ->assertSee('<div class="rk-list" role="list">', false)->assertDontSee('<ol class="rk-list"', false)
        ->assertSee('class="rk-num fw-semibold"', false)
        // compact chart data only (no whole supplier models in the page)
        ->assertSee('topSuppliers', false)->assertDontSee('window.rankingsData', false);
});

test('searching filters the table but the chart keeps the overall top three', function () {
    rkSupplier('Pilipinas Prime Supply', 4.8);
    rkSupplier('Luntian Hardware', 4.6);
    rkSupplier('Silangan Steel & Cement', 4.2);
    rkSupplier('Bagong Lakas Trading', 3.0);

    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);
    $response = $this->actingAs($admin)->get('/admin/suppliers/rankings?search=Bagong')->assertOk();

    expect($response->viewData('rankings')->pluck('supplier.company_name')->all())->toBe(['Bagong Lakas Trading'])
        ->and($response->viewData('topSuppliers')->pluck('name')->first())->toBe('Pilipinas Prime Supply');
    // rank 4 keeps its number, no podium badge
    $response->assertSee('<span class="rk-rank-plain">4</span>', false);
});

test('the chart card shows an empty state before any supplier is evaluated', function () {
    Supplier::create(['company_name' => 'Unrated Co', 'contact_person' => 'U', 'phone' => '1', 'email' => 'u@x.test', 'status' => 'active']);
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);

    $this->actingAs($admin)->get('/admin/suppliers/rankings')->assertOk()
        ->assertSeeText('No supplier has been evaluated yet.')->assertDontSee('id="topSuppliersChart"', false);
});
