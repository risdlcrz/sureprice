<?php

use App\Models\Contract;
use App\Models\MaterialRequest;
use App\Models\Party;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\Quotation;
use App\Models\User;

test('corrected relations resolve through the columns that actually exist', function () {
    $party = fn (string $type) => Party::create(['entity_type' => $type, 'name' => ucfirst($type), 'street' => 's', 'barangay' => 'b',
        'city' => 'c', 'state' => 'st', 'postal' => '1000', 'phone' => '0917']);
    $contract = Contract::create(['contractor_id' => $party('contractor')->id, 'client_id' => $party('client')->id, 'contract_number' => 'CT-REL',
        'title' => 'Lobby', 'total_amount' => 1000, 'start_date' => now(), 'end_date' => now()->addMonth(), 'payment_method' => 'cash',
        'payment_terms' => 'Net 30', 'status' => 'approved']);
    $user = User::factory()->create();
    $project = Project::create(['project_number' => 'PRJ-REL', 'contract_id' => $contract->id, 'name' => 'Lobby', 'start_date' => now(),
        'end_date' => now()->addMonth(), 'project_manager_id' => $user->id, 'client_representative_id' => $user->id, 'budget' => 1000, 'status' => 'active']);
    $pr = PurchaseRequest::create(['request_number' => 'PR-REL', 'contract_id' => $contract->id, 'requested_by' => $user->id, 'status' => 'approved']);
    $rfq = Quotation::create(['rfq_number' => 'RFQ-REL', 'due_date' => now()->addWeek(), 'purchase_request_id' => $pr->id]);
    $mr = MaterialRequest::create(['contract_id' => $contract->id, 'requested_by' => $user->id, 'status' => 'pending']);

    expect($contract->project?->id)->toBe($project->id)
        ->and($pr->project?->id)->toBe($project->id)
        ->and($rfq->contract?->id)->toBe($contract->id)
        ->and($mr->user?->id)->toBe($user->id)
        // eager loading works too (it silently returned null before)
        ->and(Quotation::with('contract')->find($rfq->id)->contract?->contract_number)->toBe($contract->fresh()->contract_number)
        ->and(PurchaseRequest::with('project')->find($pr->id)->project?->name)->toBe('Lobby');
});
