<?php

namespace App\Policies;

use App\Models\Tweet;
use App\Models\User;

class TweetPolicy
{
    public function delete(User $user, Tweet $tweet): bool
    {
        return $user->id === $tweet->user_id;
    }
}
