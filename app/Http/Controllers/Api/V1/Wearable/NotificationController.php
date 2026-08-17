<?php

namespace App\Http\Controllers\Api\V1\Wearable;

use App\Actions\HandleWearableNotificationAction;
use App\Enums\WearableNotificationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WearableNotificationActionRequest;
use App\Http\Resources\Api\V1\Wearable\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate((int) config('vibecraft.wearable.notification_page_size'));

        return NotificationResource::collection($notifications);
    }

    public function action(
        WearableNotificationActionRequest $request,
        string $notification,
        HandleWearableNotificationAction $handleAction,
    ): NotificationResource {
        $databaseNotification = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $updated = $handleAction->handle(
            $request->user(),
            $databaseNotification,
            WearableNotificationAction::from($request->validated('action')),
        );

        return NotificationResource::make($updated);
    }
}
