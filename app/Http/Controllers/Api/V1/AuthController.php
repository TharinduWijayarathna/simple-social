<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\IssueDeviceToken;
use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private IssueDeviceToken $issueDeviceToken) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => $request->validated('role', Role::Student->value),
        ]);

        $user->profile()->firstOrCreate([]);

        $issued = $this->issueDeviceToken->handle(
            $user,
            $request->validated('device_name'),
            $request->enum('device_type', DeviceType::class),
            $request->enum('device_platform', DevicePlatform::class),
            $request->validated('push_token'),
        );

        return response()->json([
            'token' => $issued['plain_text_token'],
            'token_type' => 'Bearer',
            'user' => UserResource::make($user->load('profile')),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if ($user === null || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $issued = $this->issueDeviceToken->handle(
            $user,
            $request->validated('device_name'),
            $request->enum('device_type', DeviceType::class),
            $request->enum('device_platform', DevicePlatform::class),
            $request->validated('push_token'),
        );

        return response()->json([
            'token' => $issued['plain_text_token'],
            'token_type' => 'Bearer',
            'user' => UserResource::make($user->load('profile')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $bearer = $request->bearerToken();

        if ($bearer !== null && str_contains($bearer, '|')) {
            [$id, $plainTextToken] = explode('|', $bearer, 2);

            Device::query()
                ->whereKey($id)
                ->where('token_hash', hash('sha256', $plainTextToken))
                ->first()
                ?->revoke();
        }

        Auth::guard('device')->forgetUser();

        return response()->json(['message' => 'Device unpaired.']);
    }

    public function me(Request $request): UserResource
    {
        return UserResource::make($request->user()->load(['profile.talents']));
    }
}
