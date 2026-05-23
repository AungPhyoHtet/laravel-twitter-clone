<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TweetResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function show(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'profile' => $user->profile,
            'location' => $user->location,
            'link' => $user->link,
            'link_text' => $user->link_text,
            'created_at' => $user->created_at,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'is_following' => $request->user()->following()->where('following_id', $user->id)->exists(),
            'tweets' => $user->tweets()->latest()->get(['id', 'body', 'created_at']),
        ]);
    }

    public function tweets(int $id): JsonResponse|AnonymousResourceCollection
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return TweetResource::collection($user->tweets()->with('user')->latest()->paginate(10));
    }
}
