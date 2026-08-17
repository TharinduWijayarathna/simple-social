<?php

namespace App\Actions;

use App\Models\Status;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class StoreStatus
{
    public function handle(User $user, string $imagePath, ?string $caption = null): Status
    {
        return $user->statuses()->create([
            'caption' => $caption,
            'image_path' => $imagePath,
            'expires_at' => now()->addDay(),
        ]);
    }

    public function fromUpload(User $user, UploadedFile $image, ?string $caption = null): Status
    {
        $path = $image->store('statuses/'.$user->id, 'public');

        return $this->handle($user, $path, $caption);
    }
}
