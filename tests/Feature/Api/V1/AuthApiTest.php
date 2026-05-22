<?php

use App\Models\User;

test('user can register and receive a token', function () {
    $this->postJson('/api/v1/register', [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'iPhone 15',
    ])
        ->assertCreated()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'username', 'email', 'avatar']])
        ->assertJsonPath('user.username', 'johndoe');

    $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'username' => 'johndoe']);
});

test('register validates required fields', function (array $payload) {
    $this->postJson('/api/v1/register', $payload)->assertUnprocessable();
})->with([
    'missing name' => [['username' => 'johndoe', 'email' => 'john@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'device_name' => 'iPhone 15']],
    'missing username' => [['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'device_name' => 'iPhone 15']],
    'missing email' => [['name' => 'John Doe', 'username' => 'johndoe', 'password' => 'password', 'password_confirmation' => 'password', 'device_name' => 'iPhone 15']],
    'missing password' => [['name' => 'John Doe', 'username' => 'johndoe', 'email' => 'john@example.com', 'device_name' => 'iPhone 15']],
    'missing device_name' => [['name' => 'John Doe', 'username' => 'johndoe', 'email' => 'john@example.com', 'password' => 'password', 'password_confirmation' => 'password']],
]);

test('register fails with duplicate email or username', function () {
    User::factory()->create(['email' => 'john@example.com', 'username' => 'johndoe']);

    $this->postJson('/api/v1/register', [
        'name' => 'Another User',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'iPhone 15',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'username']);
});

test('register fails when passwords do not match', function () {
    $this->postJson('/api/v1/register', [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'different',
        'device_name' => 'iPhone 15',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

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
