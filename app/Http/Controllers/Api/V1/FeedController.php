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
        $tweets = Tweet::with('user')
            ->whereIn('user_id', $request->user()->following()->select('users.id'))
            ->latest()
            ->paginate(10);

        return TweetResource::collection($tweets);
    }
}
