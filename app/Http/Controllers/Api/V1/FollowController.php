<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FollowController extends Controller
{
    public function store(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'You cannot follow yourself.'], 422);
        }

        if ($request->user()->following()->where('following_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are already following this user.'], 422);
        }

        $request->user()->following()->attach($user->id);

        return response()->json(null, 201);
    }

    public function destroy(Request $request, int $id): JsonResponse|Response
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (! $request->user()->following()->where('following_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are not following this user.'], 422);
        }

        $request->user()->following()->detach($user->id);

        return response()->noContent();
    }

    public function followers(int $id): AnonymousResourceCollection|JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return UserResource::collection($user->followers()->paginate(10));
    }

    public function following(int $id): AnonymousResourceCollection|JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return UserResource::collection($user->following()->paginate(10));
    }
}
