<?php

use App\Models\User;

// follow
test('can follow a user', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($user)
        ->postJson("/api/v1/users/{$target->id}/follow")
        ->assertCreated();

    expect($user->following()->where('following_id', $target->id)->exists())->toBeTrue();
});

test('cannot follow yourself', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson("/api/v1/users/{$user->id}/follow")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'You cannot follow yourself.');
});

test('cannot follow a user already followed', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();
    $user->following()->attach($target->id);

    $this->actingAs($user)
        ->postJson("/api/v1/users/{$target->id}/follow")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'You are already following this user.');
});

test('returns 404 when following non-existent user', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/users/999/follow')
        ->assertNotFound()
        ->assertJsonPath('message', 'User not found.');
});

test('guest cannot follow a user', function () {
    $target = User::factory()->create();

    $this->postJson("/api/v1/users/{$target->id}/follow")->assertUnauthorized();
});

// unfollow
test('can unfollow a user', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();
    $user->following()->attach($target->id);

    $this->actingAs($user)
        ->deleteJson("/api/v1/users/{$target->id}/follow")
        ->assertNoContent();

    expect($user->following()->where('following_id', $target->id)->exists())->toBeFalse();
});

test('cannot unfollow a user not followed', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/users/{$target->id}/follow")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'You are not following this user.');
});

test('returns 404 when unfollowing non-existent user', function () {
    $this->actingAs(User::factory()->create())
        ->deleteJson('/api/v1/users/999/follow')
        ->assertNotFound()
        ->assertJsonPath('message', 'User not found.');
});

test('guest cannot unfollow a user', function () {
    $target = User::factory()->create();

    $this->deleteJson("/api/v1/users/{$target->id}/follow")->assertUnauthorized();
});

// followers
test('can get followers list', function () {
    $user = User::factory()->create();
    $followers = User::factory()->count(3)->create();
    $followers->each(fn ($f) => $f->following()->attach($user->id));

    $this->actingAs($user)
        ->getJson("/api/v1/users/{$user->id}/followers")
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'username', 'avatar']], 'links', 'meta'])
        ->assertJsonCount(3, 'data');
});

test('followers returns 404 for non-existent user', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/users/999/followers')
        ->assertNotFound()
        ->assertJsonPath('message', 'User not found.');
});

test('guest cannot access followers list', function () {
    $user = User::factory()->create();

    $this->getJson("/api/v1/users/{$user->id}/followers")->assertUnauthorized();
});

// following
test('can get following list', function () {
    $user = User::factory()->create();
    $targets = User::factory()->count(3)->create();
    $targets->each(fn ($t) => $user->following()->attach($t->id));

    $this->actingAs($user)
        ->getJson("/api/v1/users/{$user->id}/following")
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'username', 'avatar']], 'links', 'meta'])
        ->assertJsonCount(3, 'data');
});

test('following returns 404 for non-existent user', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/users/999/following')
        ->assertNotFound()
        ->assertJsonPath('message', 'User not found.');
});

test('guest cannot access following list', function () {
    $user = User::factory()->create();

    $this->getJson("/api/v1/users/{$user->id}/following")->assertUnauthorized();
});
