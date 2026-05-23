<?php

use App\Models\Tweet;
use App\Models\User;

test('can search tweets by keyword', function () {
    $user = User::factory()->create();
    Tweet::factory()->for($user)->create(['body' => 'Hello Laravel world']);
    Tweet::factory()->for($user)->create(['body' => 'Hello world']);
    Tweet::factory()->for($user)->create(['body' => 'Something unrelated']);

    $this->actingAs($user)
        ->getJson('/api/v1/tweets/search?search=Laravel')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.body', 'Hello Laravel world');
});

test('search is case insensitive', function () {
    $user = User::factory()->create();
    Tweet::factory()->for($user)->create(['body' => 'Hello Laravel world']);

    $this->actingAs($user)
        ->getJson('/api/v1/tweets/search?search=laravel')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('search returns results in latest order', function () {
    $user = User::factory()->create();
    $older = Tweet::factory()->for($user)->create(['body' => 'Laravel tip', 'created_at' => now()->subMinutes(5)]);
    $newer = Tweet::factory()->for($user)->create(['body' => 'Laravel news', 'created_at' => now()]);

    $ids = $this->actingAs($user)
        ->getJson('/api/v1/tweets/search?search=Laravel')
        ->assertOk()
        ->json('data.*.id');

    expect($ids)->toBe([$newer->id, $older->id]);
});

test('search returns empty data when no matches', function () {
    $user = User::factory()->create();
    Tweet::factory()->for($user)->create(['body' => 'Hello world']);

    $this->actingAs($user)
        ->getJson('/api/v1/tweets/search?search=zzznomatch')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

test('search requires search parameter', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tweets/search')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['search']);
});

test('guest cannot search tweets', function () {
    $this->getJson('/api/v1/tweets/search?search=hello')->assertUnauthorized();
});
