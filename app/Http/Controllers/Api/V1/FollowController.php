<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\FollowUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(Request $request, User $user, FollowUser $followUser): JsonResponse
    {
        $result = $followUser->handle($request->user(), $user);

        return response()->json([
            'following' => $result['following'],
        ]);
    }
}
