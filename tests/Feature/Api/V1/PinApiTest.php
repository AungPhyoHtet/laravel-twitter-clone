<?php

use App\Models\Tweet;
use App\Models\User;

test('authenticated user can pin their own tweet', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();

    $this->actingAs($user)
        ->postJson("/api/v1/tweets/{$tweet->id}/pin")
        ->assertCreated();

    expect($user->fresh()->pinned_tweet_id)->toBe($tweet->id);
});

test('pinning a tweet replaces the previous pinned tweet', function () {
    $user = User::factory()->create();
    $first = Tweet::factory()->for($user)->create();
    $second = Tweet::factory()->for($user)->create();

    $user->update(['pinned_tweet_id' => $first->id]);

    $this->actingAs($user)
        ->postJson("/api/v1/tweets/{$second->id}/pin")
        ->assertCreated();

    expect($user->fresh()->pinned_tweet_id)->toBe($second->id);
});

test('user cannot pin another users tweet', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    $this->actingAs($user)
        ->postJson("/api/v1/tweets/{$tweet->id}/pin")
        ->assertForbidden()
        ->assertJsonPath('message', 'You can only pin your own tweets.');
});

test('authenticated user can unpin their pinned tweet', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();
    $user->update(['pinned_tweet_id' => $tweet->id]);

    $this->actingAs($user)
        ->deleteJson("/api/v1/tweets/{$tweet->id}/pin")
        ->assertNoContent();

    expect($user->fresh()->pinned_tweet_id)->toBeNull();
});

test('unpinning a tweet that is not pinned returns 422', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/tweets/{$tweet->id}/pin")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'This tweet is not pinned.');
});

test('guests cannot access pin endpoints', function () {
    $tweet = Tweet::factory()->create();

    $this->postJson("/api/v1/tweets/{$tweet->id}/pin")->assertUnauthorized();
    $this->deleteJson("/api/v1/tweets/{$tweet->id}/pin")->assertUnauthorized();
});

test('tweet resource includes is_pinned field', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();
    $user->update(['pinned_tweet_id' => $tweet->id]);

    $this->actingAs($user)
        ->getJson("/api/v1/tweets/{$tweet->id}")
        ->assertOk()
        ->assertJsonPath('data.is_pinned', true);
});

test('is_pinned is false for non-pinned tweet', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    $this->actingAs($user)
        ->getJson("/api/v1/tweets/{$tweet->id}")
        ->assertOk()
        ->assertJsonPath('data.is_pinned', false);
});
