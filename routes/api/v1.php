<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FeedController;
use App\Http\Controllers\Api\V1\FollowController;
use App\Http\Controllers\Api\V1\PinController;
use App\Http\Controllers\Api\V1\TweetController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/{id}/tweets', [UserController::class, 'tweets']);
    Route::post('/users/{id}/follow', [FollowController::class, 'store']);
    Route::delete('/users/{id}/follow', [FollowController::class, 'destroy']);
    Route::get('/users/{id}/followers', [FollowController::class, 'followers']);
    Route::get('/users/{id}/following', [FollowController::class, 'following']);
    Route::get('/feed', [FeedController::class, 'index']);
    Route::get('/tweets/search', [TweetController::class, 'search']);
    Route::apiResource('tweets', TweetController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::post('/tweets/{tweet}/pin', [PinController::class, 'store']);
    Route::delete('/tweets/{tweet}/pin', [PinController::class, 'destroy']);
});
