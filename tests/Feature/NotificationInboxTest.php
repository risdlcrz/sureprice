<?php

use App\Models\Notification;
use App\Models\Quotation;
use App\Models\User;

test('the inbox shows a user\'s notifications however they were addressed', function () {
    [$me, $other] = User::factory()->count(2)->create(['role' => 'manager', 'user_type' => 'manager']);

    Notification::create(['user_id' => $me->id, 'type' => 'a', 'data' => ['title' => 'By user_id']]);
    Notification::create(['notifiable_type' => User::class, 'notifiable_id' => $me->id, 'type' => 'b', 'data' => ['title' => 'By morph']]);
    // Addressed to someone else, but with a Quotation morph whose id equals mine: must not leak.
    Notification::create(['user_id' => $other->id, 'notifiable_type' => Quotation::class, 'notifiable_id' => $me->id, 'type' => 'c', 'data' => ['title' => 'Leak']]);

    expect(Notification::forUser($me)->pluck('data')->pluck('title')->sort()->values()->all())->toBe(['By morph', 'By user_id']);

    $this->actingAs($me)->get('/notifications')->assertOk()->assertSee('By user_id')->assertDontSee('Leak');
    $this->actingAs($me)->getJson('/api/unread-counts')->assertJson(['notifications' => 2]);
});

test('opening a notification marks it read and follows its link', function () {
    $me = User::factory()->create(['role' => 'manager', 'user_type' => 'manager']);
    $n = Notification::create(['user_id' => $me->id, 'type' => 'x', 'data' => ['title' => 'Go', 'link' => 'http://localhost/somewhere']]);

    $this->actingAs($me)->get("/notifications/{$n->id}/open")->assertRedirect('http://localhost/somewhere');
    expect($n->fresh()->read_at)->not->toBeNull();
});

test('users cannot open other people\'s notifications', function () {
    [$me, $other] = User::factory()->count(2)->create();
    $n = Notification::create(['user_id' => $other->id, 'type' => 'x', 'data' => ['title' => 'Private']]);

    $this->actingAs($me)->get("/notifications/{$n->id}/open")->assertNotFound();
});

test('laravel database notifications are stored in the integer-id table', function () {
    $user = User::factory()->create();
    $user->notify(new class extends Illuminate\Notifications\Notification {
        public function via($n) { return ['database']; }
        public function toArray($n) { return ['title' => 'Channel works']; }
    });

    expect(Notification::forUser($user)->first()?->title)->toBe('Channel works');
});
