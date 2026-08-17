<?php

use App\Http\Controllers\Api\V1\Admin\AnalyticsController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CollaborationController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\FollowController;
use App\Http\Controllers\Api\V1\LeaderboardController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\PortfolioItemController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RatingController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\TalentController;
use App\Http\Controllers\Api\V1\Wearable\EventController as WearableEventController;
use App\Http\Controllers\Api\V1\Wearable\LeaderboardController as WearableLeaderboardController;
use App\Http\Controllers\Api\V1\Wearable\NotificationController as WearableNotificationController;
use App\Http\Controllers\Api\V1\Wearable\SummaryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::get('talents', [TalentController::class, 'index']);
    Route::get('portfolio-items', [PortfolioItemController::class, 'index']);
    Route::get('portfolio-items/{portfolio_item}', [PortfolioItemController::class, 'show']);
    Route::get('students', [ProfileController::class, 'index']);
    Route::get('students/{user}', [ProfileController::class, 'show']);
    Route::get('events', [EventController::class, 'index']);
    Route::get('events/{event}', [EventController::class, 'show']);
    Route::get('leaderboard', [LeaderboardController::class, 'index']);

    Route::middleware('auth:device')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::patch('profile', [ProfileController::class, 'update']);
        Route::post('portfolio-items', [PortfolioItemController::class, 'store']);
        Route::delete('portfolio-items/{portfolio_item}', [PortfolioItemController::class, 'destroy']);

        Route::post('portfolio-items/{portfolio_item}/likes', [LikeController::class, 'store']);
        Route::post('portfolio-items/{portfolio_item}/comments', [CommentController::class, 'store']);
        Route::post('portfolio-items/{portfolio_item}/ratings', [RatingController::class, 'store']);
        Route::post('portfolio-items/{portfolio_item}/reports', [ReportController::class, 'store']);
        Route::post('students/{user}/follow', [FollowController::class, 'store']);

        Route::post('events', [EventController::class, 'store']);
        Route::post('events/{event}/apply', [EventController::class, 'apply']);
        Route::post('events/{event}/rsvp', [EventController::class, 'rsvp']);

        Route::get('collaborations', [CollaborationController::class, 'index']);
        Route::post('collaborations', [CollaborationController::class, 'store']);
        Route::get('collaborations/{collaboration}', [CollaborationController::class, 'show']);
        Route::post('collaborations/{collaboration}/requests', [CollaborationController::class, 'requestToJoin']);
        Route::post('collaboration-requests/{collaboration_request}/respond', [CollaborationController::class, 'respond']);

        Route::post('devices', [DeviceController::class, 'store']);
        Route::delete('devices/{device}', [DeviceController::class, 'destroy']);

        Route::patch('reports/{report}', [ReportController::class, 'update'])->middleware('role:admin');
        Route::get('admin/analytics', AnalyticsController::class)->middleware('role:admin');

        Route::prefix('wearable')->middleware('throttle:wearable')->group(function (): void {
            Route::get('summary', SummaryController::class);
            Route::get('notifications', [WearableNotificationController::class, 'index']);
            Route::post('notifications/{notification}/action', [WearableNotificationController::class, 'action']);
            Route::get('leaderboard', WearableLeaderboardController::class);
            Route::get('events/upcoming', [WearableEventController::class, 'upcoming']);
            Route::post('events/{event}/rsvp', [WearableEventController::class, 'rsvp']);
        });
    });
});
