<?php

use App\Models\Tweet;
use App\Models\User;

test('can get user detail with their tweets', function () {
    $user = User::factory()->create();
    Tweet::factory()->count(3)->for($user)->create();

    $this->actingAs($user)
        ->getJson("/api/v1/users/{$user->id}")
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'username', 'avatar', 'profile', 'location', 'link', 'link_text', 'created_at', 'tweets'])
        ->assertJsonPath('id', $user->id)
        ->assertJsonPath('username', $user->username)
        ->assertJsonCount(3, 'tweets');
});

test('user tweets are returned in latest order', function () {
    $user = User::factory()->create();
    $first = Tweet::factory()->for($user)->create(['created_at' => now()->subMinutes(5)]);
    $second = Tweet::factory()->for($user)->create(['created_at' => now()]);

    $ids = $this->actingAs($user)
        ->getJson("/api/v1/users/{$user->id}")
        ->assertOk()
        ->json('tweets.*.id');

    expect($ids)->toBe([$second->id, $first->id]);
});

test('returns 404 for non-existent user', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/users/999')
        ->assertNotFound()
        ->assertJsonPath('message', 'User not found.');
});

test('guest cannot access user detail', function () {
    $user = User::factory()->create();

    $this->getJson("/api/v1/users/{$user->id}")->assertUnauthorized();
});

test('can get user tweet list', function () {
    $user = User::factory()->create();
    Tweet::factory()->count(3)->for($user)->create();

    $this->actingAs($user)
        ->getJson("/api/v1/users/{$user->id}/tweets")
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'body', 'created_at', 'user']], 'links', 'meta'])
        ->assertJsonCount(3, 'data');
});

test('user tweet list is returned in latest order', function () {
    $user = User::factory()->create();
    $first = Tweet::factory()->for($user)->create(['created_at' => now()->subMinutes(5)]);
    $second = Tweet::factory()->for($user)->create(['created_at' => now()]);

    $ids = $this->actingAs($user)
        ->getJson("/api/v1/users/{$user->id}/tweets")
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->toBe([$second->id, $first->id]);
});

test('user tweet list returns 404 for non-existent user', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/users/999/tweets')
        ->assertNotFound()
        ->assertJsonPath('message', 'User not found.');
});

test('guest cannot access user tweet list', function () {
    $user = User::factory()->create();

    $this->getJson("/api/v1/users/{$user->id}/tweets")->assertUnauthorized();
});
