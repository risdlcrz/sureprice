<?php

use App\Models\ClientFeedback;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Party;
use App\Models\User;

function feedbackClient(): User
{
    $u = User::factory()->create(['role' => 'client', 'user_type' => 'company']);
    Company::create(['user_id' => $u->id, 'email' => $u->email, 'username' => $u->username, 'company_name' => 'Client Co', 'designation' => 'client', 'status' => 'approved']);

    return $u;
}

function feedbackFor(User $client, array $attrs = []): ClientFeedback
{
    $party = fn (string $type) => Party::create(['entity_type' => $type, 'name' => ucfirst($type), 'street' => 's', 'barangay' => 'b',
        'city' => 'c', 'state' => 'st', 'postal' => '1000', 'phone' => '0917']);
    $contract = Contract::create(['contractor_id' => $party('contractor')->id, 'client_id' => $party('client')->id, 'contract_number' => 'CT-FB-1',
        'title' => 'Lobby', 'total_amount' => 10000, 'start_date' => now(), 'end_date' => now()->addMonth(),
        'payment_method' => 'cash', 'payment_terms' => 'Net 30', 'status' => 'completed']);

    return ClientFeedback::create($attrs + ['contract_id' => $contract->id, 'client_id' => $contract->client_id, 'user_id' => $client->id,
        'overall_rating' => 2, 'recommendation_likelihood' => 3, 'comments' => 'Late delivery']);
}

test('admins see priority, analytics and export; drafts no longer crash', function () {
    $feedback = feedbackFor(feedbackClient()); // draft: submitted_at is null
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);

    $this->actingAs($admin)->get("/admin/feedback/{$feedback->id}")
        ->assertOk()->assertSee('Priority Assessment')->assertSee('Export Data')->assertSee('Not submitted');
});

test('the client sees their own feedback without admin tools and can edit a draft', function () {
    $client = feedbackClient();
    $feedback = feedbackFor($client);

    $this->actingAs($client)->get("/client/feedback/{$feedback->id}/show")
        ->assertOk()->assertSee('Your Ratings')->assertSee('Edit Feedback')
        ->assertDontSee('Priority Assessment')->assertDontSee('Export Data');
});

test('other clients cannot open someone else\'s feedback', function () {
    $feedback = feedbackFor(feedbackClient());

    $this->actingAs(feedbackClient())->get("/client/feedback/{$feedback->id}/show")->assertForbidden();
});
