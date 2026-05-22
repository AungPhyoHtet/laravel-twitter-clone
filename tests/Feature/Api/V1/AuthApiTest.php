<?php

use App\Models\User;

test('user can login and receive a token', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'iPhone 15',
    ])
        ->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'username', 'email', 'avatar']])
        ->assertJsonPath('user.id', $user->id);
});

test('login fails with incorrect credentials', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'iPhone 15',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('login validates required fields', function (array $payload) {
    $this->postJson('/api/v1/login', $payload)
        ->assertUnprocessable();
})->with([
    'missing email' => [['password' => 'password', 'device_name' => 'iPhone 15']],
    'missing password' => [['email' => 'test@example.com', 'device_name' => 'iPhone 15']],
    'missing device_name' => [['email' => 'test@example.com', 'password' => 'password']],
]);

test('authenticated user can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('iPhone 15')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');

    expect($user->tokens()->count())->toBe(0);
});

test('guest cannot logout', function () {
    $this->postJson('/api/v1/logout')->assertUnauthorized();
});
