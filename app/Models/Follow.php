<?php

namespace App\Models;

use Database\Factories\FollowFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['follower_id', 'following_id'])]
class Follow extends Model
{
    /** @use HasFactory<FollowFactory> */
    use HasFactory;
}
