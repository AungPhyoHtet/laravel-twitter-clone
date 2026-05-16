<?php

use App\Models\Tweet;
use App\Models\User;

test('tweet belongs to a user', function () {
    $tweet = Tweet::factory()->create();

    expect($tweet->user)->toBeInstanceOf(User::class);
});

test('user has many tweets', function () {
    $user = User::factory()->create();
    Tweet::factory()->count(3)->for($user)->create();

    expect($user->tweets)->toHaveCount(3);
});

test('tweet body is limited to 280 characters', function () {
    $tweet = Tweet::factory()->create(['body' => str_repeat('a', 280)]);

    expect($tweet->body)->toHaveLength(280);
});
