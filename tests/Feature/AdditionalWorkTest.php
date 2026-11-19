<?php

use App\Models\AdditionalWork;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Material;
use App\Models\Party;
use App\Models\User;

test('an additional work request saves with its labour, timeline and material details', function () {
    $party = fn (string $type) => Party::create(['entity_type' => $type, 'name' => ucfirst($type), 'street' => 's', 'barangay' => 'b',
        'city' => 'c', 'state' => 'st', 'postal' => '1000', 'phone' => '0917']);
    $contract = Contract::create(['contractor_id' => $party('contractor')->id, 'client_id' => $party('client')->id, 'contract_number' => 'CT-AW',
        'title' => 'Lobby', 'total_amount' => 1000, 'start_date' => now(), 'end_date' => now()->addMonth(), 'payment_method' => 'cash',
        'payment_terms' => 'Net 30', 'status' => 'approved']);
    $material = Material::create(['name' => 'Plywood', 'code' => 'PLY-AW', 'unit' => 'pcs',
        'category_id' => Category::create(['name' => 'Lumber', 'slug' => 'lumber'])->id]);

    $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->postJson(route('warranty-requests.additional-work'), [
            'contract_id' => $contract->id,
            'work_type' => 'repair',
            'description' => 'Replace damaged panels',
            'materials' => [['material_id' => $material->id, 'quantity' => 4, 'notes' => 'marine grade']],
            'estimated_hours' => 6,
            'required_skills' => 'carpentry',
            'preferred_start_date' => now()->addDay()->toDateString(),
            'preferred_end_date' => now()->addDays(3)->toDateString(),
        ])
        ->assertOk()
        ->assertJson(['success' => true]);

    $work = AdditionalWork::with('materials')->sole();
    expect($work->work_type)->toBe('repair')
        ->and((float) $work->estimated_hours)->toBe(6.0)
        ->and($work->required_skills)->toBe('carpentry')
        ->and($work->status)->toBe('pending')
        ->and($work->materials)->toHaveCount(1)
        ->and((float) $work->materials->first()->quantity)->toBe(4.0);
});
