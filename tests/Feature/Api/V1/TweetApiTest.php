<?php

use App\Models\Tweet;
use App\Models\User;

test('guests can list latest tweets', function () {
    Tweet::factory()->count(3)->create();

    $this->getJson('/api/v1/tweets')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data' => [['id', 'body', 'user', 'created_at']]]);
});

test('tweets are returned in latest order', function () {
    $first = Tweet::factory()->create(['created_at' => now()->subMinutes(5)]);
    $second = Tweet::factory()->create(['created_at' => now()]);

    $ids = $this->getJson('/api/v1/tweets')
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->toBe([$second->id, $first->id]);
});

test('guests can view a single tweet', function () {
    $tweet = Tweet::factory()->create();

    $this->getJson("/api/v1/tweets/{$tweet->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $tweet->id);
});

test('guests cannot create or delete tweets', function () {
    $tweet = Tweet::factory()->create();

    $this->postJson('/api/v1/tweets')->assertUnauthorized();
    $this->deleteJson("/api/v1/tweets/{$tweet->id}")->assertUnauthorized();
});

test('authenticated user can create a tweet', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/tweets', ['body' => 'Hello, world!'])
        ->assertCreated()
        ->assertJsonPath('data.body', 'Hello, world!')
        ->assertJsonPath('data.user.id', $user->id);

    $this->assertDatabaseHas('tweets', ['body' => 'Hello, world!', 'user_id' => $user->id]);
});

test('tweet body is required and max 280 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/tweets', ['body' => ''])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body']);

    $this->actingAs($user)
        ->postJson('/api/v1/tweets', ['body' => str_repeat('a', 281)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body']);
});

test('owner can delete their tweet', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/tweets/{$tweet->id}")
        ->assertNoContent();

    $this->assertModelMissing($tweet);
});

test('user cannot delete another users tweet', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/tweets/{$tweet->id}")
        ->assertForbidden();
});
