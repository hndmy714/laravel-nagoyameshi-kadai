<?php

use App\Providers\RouteServiceProvider;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'kana' => 'テスト ユーザー',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'postal_code' => '0000000',
        'address' => 'テスト',
        'phone_number' => '00000000000',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});
