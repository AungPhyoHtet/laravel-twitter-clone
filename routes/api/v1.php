<?php

use App\Http\Controllers\Api\V1\TweetController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tweets', TweetController::class)->only(['index', 'show']);

Route::middleware('auth')->group(function () {
    Route::apiResource('tweets', TweetController::class)->only(['store', 'destroy']);
});
