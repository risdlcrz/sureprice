<?php

test('registration screen can be rendered', function () {
    $this->get('/register')->assertStatus(200);
});

test('registration requires a valid account type and does not log anyone in', function () {
    $this->post('/register/company', ['name' => 'Test User', 'email' => 'test@example.com'])
        ->assertSessionHasErrors('type');

    $this->assertGuest();
});

test('company registration validates required fields', function () {
    $this->post('/register/company', ['type' => 'company'])->assertSessionHasErrors();

    $this->assertGuest();
});