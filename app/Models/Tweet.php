<?php

namespace App\Models;

use Database\Factories\TweetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $body
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\TweetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tweet whereUserId($value)
 * @mixin \Eloquent
 */
#[Fillable(['user_id', 'body'])]
class Tweet extends Model
{
    /** @use HasFactory<TweetFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
