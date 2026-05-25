<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PinController extends Controller
{
    public function store(Request $request, Tweet $tweet): JsonResponse
    {
        if ($tweet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You can only pin your own tweets.'], 403);
        }

        $request->user()->update(['pinned_tweet_id' => $tweet->id]);

        return response()->json(null, 201);
    }

    public function destroy(Request $request, Tweet $tweet): JsonResponse|Response
    {
        if ($request->user()->pinned_tweet_id !== $tweet->id) {
            return response()->json(['message' => 'This tweet is not pinned.'], 422);
        }

        $request->user()->update(['pinned_tweet_id' => null]);

        return response()->noContent();
    }
}
