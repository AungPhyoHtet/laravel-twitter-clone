<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TweetResource;
use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeedController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $followingIds = $request->user()->following()->select('users.id');

        $tweets = Tweet::with('user')
            ->where(function ($query) use ($request, $followingIds) {
                $query->whereIn('user_id', $followingIds)
                    ->orWhere('user_id', $request->user()->id);
            })
            ->latest()
            ->paginate(10);

        return TweetResource::collection($tweets);
    }
}
