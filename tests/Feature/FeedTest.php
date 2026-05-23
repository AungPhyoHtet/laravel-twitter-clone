<?php

use App\Models\Tweet;
use App\Models\User;

test('feed returns tweets from followed users in latest order', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $user->following()->attach($followed->id);

    $older = Tweet::factory()->for($followed)->create(['created_at' => now()->subMinutes(5)]);
    $newer = Tweet::factory()->for($followed)->create(['created_at' => now()]);

    $ids = $this->actingAs($user)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->toBe([$newer->id, $older->id]);
});

test('feed does not include own tweets or unfollowed users tweets', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $unfollowed = User::factory()->create();
    $user->following()->attach($followed->id);

    Tweet::factory()->for($user)->create();
    Tweet::factory()->for($unfollowed)->create();
    $followedTweet = Tweet::factory()->for($followed)->create();

    $ids = $this->actingAs($user)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->toBe([$followedTweet->id]);
});

test('feed returns empty data when following nobody', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

test('guest cannot access feed', function () {
    $this->getJson('/api/v1/feed')->assertUnauthorized();
});
