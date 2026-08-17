<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProfileController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->with(['profile.talents'])
            ->students()
            ->latest()
            ->paginate(20);

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        $user->load(['profile.talents', 'portfolioItems' => fn ($query) => $query->published()->latest()->limit(6)]);

        return UserResource::make($user);
    }

    public function update(UpdateProfileRequest $request): ProfileResource
    {
        $user = $request->user();
        $profile = $user->profile;

        if ($request->filled('name')) {
            $user->update(['name' => $request->validated('name')]);
        }

        $profile->update($request->safe()->only([
            'headline',
            'bio',
            'faculty',
            'department',
            'experience_level',
        ]));

        if ($request->has('talent_ids')) {
            $favorites = collect($request->validated('favorite_talent_ids', []));

            $sync = collect($request->validated('talent_ids'))
                ->mapWithKeys(fn (int $talentId): array => [
                    $talentId => ['is_favorite' => $favorites->contains($talentId)],
                ])
                ->all();

            $profile->talents()->sync($sync);
        }

        return ProfileResource::make($profile->load('talents'));
    }
}
