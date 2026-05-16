<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTweetRequest;
use App\Http\Resources\Api\V1\TweetResource;
use App\Models\Tweet;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TweetController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $tweets = Tweet::with('user')->latest()->paginate();

        return TweetResource::collection($tweets);
    }

    public function store(StoreTweetRequest $request): TweetResource
    {
        $tweet = $request->user()->tweets()->create($request->validated());

        return new TweetResource($tweet->load('user'));
    }

    public function show(Tweet $tweet): TweetResource
    {
        return new TweetResource($tweet->load('user'));
    }

    public function destroy(Tweet $tweet): Response
    {
        Gate::authorize('delete', $tweet);

        $tweet->delete();

        return response()->noContent();
    }
}
