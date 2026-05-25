<?php

use App\Models\Tweet;
use App\Models\User;

test('feed includes tweets from followed users', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $user->following()->attach($followed->id);

    $tweet = Tweet::factory()->for($followed)->create();

    $ids = $this->actingAs($user)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->toContain($tweet->id);
});

test('feed includes the authenticated users own tweets', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();

    $ids = $this->actingAs($user)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->toContain($tweet->id);
});

test('feed excludes tweets from non-followed users', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $tweet = Tweet::factory()->for($other)->create();

    $ids = $this->actingAs($user)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->not->toContain($tweet->id);
});

test('guest cannot access feed', function () {
    $this->getJson('/api/v1/feed')->assertUnauthorized();
});
