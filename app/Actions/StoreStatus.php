<?php

namespace App\Actions;

use App\Models\Status;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StoreStatus
{
    public function handle(User $user, string $imagePath, ?string $caption = null, string $mediaType = 'image'): Status
    {
        return $user->statuses()->create([
            'caption' => $caption,
            'image_path' => $imagePath,
            'media_type' => $mediaType,
            'expires_at' => now()->addDay(),
        ]);
    }

    public function fromUpload(User $user, UploadedFile $file, ?string $caption = null): Status
    {
        $path = $file->store('statuses/'.$user->id, 'public');

        $mime = $file->getMimeType();
        $mediaType = Str::startsWith($mime, 'video/') ? 'video' : 'image';

        return $this->handle($user, $path, $caption, $mediaType);
    }
}
