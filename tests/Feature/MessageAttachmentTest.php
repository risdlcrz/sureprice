<?php

use App\Models\Company;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function attClient(string $name): User
{
    $user = User::factory()->create(['role' => 'client', 'user_type' => 'company']);
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username,
        'company_name' => $name, 'designation' => 'client', 'status' => 'approved']);

    return $user;
}

function attConversation(): array
{
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee']);
    $client = attClient('Mabini Realty');
    $conversation = Conversation::create(['client_id' => $client->id, 'admin_id' => $manager->id, 'status' => 'active', 'last_message_at' => now()]);

    return [$conversation, $client, $manager];
}

beforeEach(fn () => Storage::fake('public'));

test('a client sends an image and both participants can load it without the public/storage link', function () {
    [$conversation, $client, $manager] = attConversation();

    $sent = $this->actingAs($client)
        ->postJson(route('messages.store', $conversation), ['image' => UploadedFile::fake()->image('fayah.jpg', 640, 480)])
        ->assertOk()->json();

    $message = Message::findOrFail($sent['id']);
    Storage::disk('public')->assertExists($message->file_path);
    // the JSON the chat appends carries URLs served by the app, not /storage/...
    expect($sent['attachment_url'])->toBe(route('messages.attachment.show', $message))
        ->and($sent['download_url'])->toBe(route('messages.attachment.download', $message));

    foreach ([$client, $manager] as $user) {
        $response = $this->actingAs($user)->get(route('messages.attachment.show', $message))->assertOk();
        expect($response->headers->get('content-type'))->toBe('image/jpeg')
            ->and($response->headers->get('content-disposition'))->toStartWith('inline;')->toContain('fayah.jpg')
            ->and($response->headers->get('x-content-type-options'))->toBe('nosniff');
    }

    // the thread renders the thumbnail and the viewer from that route
    $this->actingAs($manager)->get('/messages?conversation=' . $conversation->id)->assertOk()
        ->assertSee('<img src="' . route('messages.attachment.show', $message) . '"', false)
        ->assertSee('data-attachment-url="' . route('messages.attachment.show', $message) . '"', false)
        ->assertDontSee('/storage/messages/', false);
});

test('people outside the conversation cannot view or download its attachments', function () {
    [$conversation, $client] = attConversation();
    $message = $conversation->messages()->create(['sender_id' => $client->id, 'file_path' => 'messages/fayah.jpg',
        'file_name' => 'fayah.jpg', 'file_type' => 'image/jpeg', 'file_size' => 10]);
    Storage::disk('public')->put('messages/fayah.jpg', UploadedFile::fake()->image('fayah.jpg')->getContent());

    $outsider = attClient('Other Co');
    $this->actingAs($outsider)->get(route('messages.attachment.show', $message))->assertForbidden();
    $this->actingAs($outsider)->get(route('messages.attachment.download', $message))->assertForbidden();
});

test('non-image attachments are never displayed inline', function () {
    [$conversation, $client] = attConversation();
    Storage::disk('public')->put('messages/page.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
    $message = $conversation->messages()->create(['sender_id' => $client->id, 'file_path' => 'messages/page.svg',
        'file_name' => 'page.svg', 'file_type' => 'image/svg+xml', 'file_size' => 80]);

    $response = $this->actingAs($client)->get(route('messages.attachment.show', $message))->assertOk();
    expect($response->headers->get('content-disposition'))->toStartWith('attachment;');

    // and the chat shows it as a file, not an <img>
    $this->actingAs($client)->get('/messages?conversation=' . $conversation->id)->assertOk()
        ->assertSee('chat-attachment--file', false)->assertDontSee('chat-attachment--image', false);
});

test('a missing attachment file returns 404 instead of an error', function () {
    [$conversation, $client] = attConversation();
    $message = $conversation->messages()->create(['sender_id' => $client->id, 'file_path' => 'messages/gone.jpg',
        'file_name' => 'gone.jpg', 'file_type' => 'image/jpeg', 'file_size' => 10]);

    $this->actingAs($client)->get(route('messages.attachment.show', $message))->assertNotFound();
});
