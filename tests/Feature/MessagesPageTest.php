<?php

use App\Models\Company;
use App\Models\Conversation;
use App\Models\Supplier;
use App\Models\User;

function msgCompanyUser(string $designation, string $name): User
{
    $user = User::factory()->create(['role' => $designation, 'user_type' => 'company']);
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username,
        'company_name' => $name, 'designation' => $designation, 'status' => 'approved']);

    return $user;
}

test('the messages page lists conversations and shows the open thread with day separators', function () {
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee', 'name' => 'Maria Manager']);
    $client = msgCompanyUser('client', 'Mabini Realty');
    $conversation = Conversation::create(['client_id' => $client->id, 'admin_id' => $manager->id, 'status' => 'active', 'last_message_at' => now()]);
    $conversation->messages()->create(['sender_id' => $client->id, 'content' => 'Hello, when can you start?']);
    $conversation->messages()->create(['sender_id' => $manager->id, 'content' => '<b>Next week</b>']);

    $this->actingAs($manager)->get('/messages?conversation=' . $conversation->id)
        ->assertOk()
        ->assertSee('Mabini Realty')                        // client shown by company name
        ->assertSee('Client')                               // role under the name
        ->assertSee('Hello, when can you start?')
        ->assertSee('&lt;b&gt;Next week&lt;/b&gt;', false)  // message text is escaped
        ->assertSee('Today')
        ->assertSee('id="deleteConversationModal"', false)
        ->assertDontSee('ui-avatars.com')                   // names are no longer sent to a third-party avatar service
        ->assertDontSee('#1877f2');

    $this->actingAs($client)->get('/messages?conversation=' . $conversation->id)
        ->assertOk()->assertSee('Maria Manager')->assertSee('Manager')->assertSee('<optgroup label="Project managers">', false);
});

test('a user with no conversations gets an empty state and can start one', function () {
    $this->actingAs(msgCompanyUser('client', 'Bayanihan Foods'))->get('/messages')
        ->assertOk()
        ->assertSee('No conversations yet.')
        ->assertSee('Your messages')
        ->assertSee('id="startConversationModal"', false);
});

test('the recipient search returns the supplier record id that starting a conversation expects', function () {
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee']);
    $supplierUser = msgCompanyUser('supplier', 'Acme Supply');
    $supplier = Supplier::create(['company_name' => 'Acme Supply', 'contact_person' => 'Ann', 'phone' => '0917', 'email' => 'acme@x.test',
        'status' => 'active', 'user_id' => $supplierUser->id, 'company_id' => $supplierUser->company->id]);
    $client = msgCompanyUser('client', 'Acme Homes');

    $data = $this->actingAs($manager)->getJson('/admin/companies/search-for-chat?search=Acme')->assertOk()->json('data');
    $byKind = collect($data)->keyBy('designation');

    expect($byKind['Supplier']['id'])->toBe($supplier->id)
        ->and($byKind['Client']['id'])->toBe($client->id);

    $this->post('/messages/start', ['supplier_id' => $supplier->id, 'message' => 'Do you have plywood in stock?'])->assertRedirect();
    expect(Conversation::where('supplier_id', $supplierUser->company->id)->where('admin_id', $manager->id)->exists())->toBeTrue();
});

test('admins see their own conversations, can start one with a client or supplier, and clients can write to them', function () {
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin', 'name' => 'Ana Admin']);
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee']);
    $supplierUser = msgCompanyUser('supplier', 'Acme Supply');
    $client = msgCompanyUser('client', 'Mabini Realty');

    // a supplier writes to the admin; a manager's conversation stays private to that manager
    $fromSupplier = Conversation::create(['supplier_id' => $supplierUser->company->id, 'admin_id' => $admin->id, 'status' => 'active', 'last_message_at' => now()]);
    $fromSupplier->messages()->create(['sender_id' => $supplierUser->id, 'content' => 'Invoice attached']);
    $managersOwn = Conversation::create(['client_id' => $client->id, 'admin_id' => $manager->id, 'status' => 'active', 'last_message_at' => now()]);
    $managersOwn->messages()->create(['sender_id' => $client->id, 'content' => 'Private to the manager']);

    $this->actingAs($admin)->get('/messages')
        ->assertOk()
        ->assertSee('Acme Supply')->assertSee('Invoice attached')
        ->assertDontSee('Private to the manager')
        ->assertSee('id="startConversationModal"', false)
        ->assertSee('Search for a client or supplier');

    // /messages/{id} opens the same page
    $this->get("/messages/{$fromSupplier->id}")->assertRedirect(route('messages.index', ['conversation' => $fromSupplier->id]));
    $this->get("/messages/{$managersOwn->id}")->assertForbidden();

    // the admin starts a conversation with a client
    $this->post('/messages/start', ['client_id' => $client->id, 'message' => 'Welcome aboard!'])->assertRedirect();
    $started = Conversation::where('client_id', $client->id)->where('admin_id', $admin->id)->first();
    expect($started)->not->toBeNull()->and($started->messages()->first()->content)->toBe('Welcome aboard!');

    // the client sees it, labelled as coming from an administrator, and can pick admins as recipients
    $this->actingAs($client)->get('/messages?conversation=' . $started->id)
        ->assertOk()->assertSee('Ana Admin')->assertSee('Administrator')->assertSee('Welcome aboard!')
        ->assertSee('<optgroup label="Administrators">', false);

    $other = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);
    $this->post('/messages/start', ['manager_id' => $other->id, 'message' => 'Question about billing'])->assertRedirect();
    expect(Conversation::where('client_id', $client->id)->where('admin_id', $other->id)->exists())->toBeTrue();
});
