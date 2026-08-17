<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\IssueDeviceToken;
use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PairDeviceRequest;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceController extends Controller
{
    public function store(PairDeviceRequest $request, IssueDeviceToken $issueDeviceToken): JsonResponse
    {
        $issued = $issueDeviceToken->handle(
            $request->user(),
            $request->validated('name'),
            $request->enum('type', DeviceType::class),
            $request->enum('platform', DevicePlatform::class),
            $request->validated('push_token'),
        );

        return response()->json([
            'id' => $issued['device']->id,
            'token' => $issued['plain_text_token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    public function destroy(Request $request, Device $device): Response
    {
        abort_unless($device->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $device->revoke();

        return response()->noContent();
    }
}
