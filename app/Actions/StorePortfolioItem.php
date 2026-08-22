<?php

namespace App\Actions;

use App\Enums\PortfolioMediaType;
use App\Enums\XpEventType;
use App\Jobs\GeneratePortfolioThumbnail;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class StorePortfolioItem
{
    public function __construct(
        private AwardXp $awardXp,
        private StoreStatus $storeStatus,
    ) {}

    /**
     * @param  array{
     *     title: string,
     *     description?: string|null,
     *     talent_id?: int|null,
     *     media_type: string|PortfolioMediaType,
     *     file: UploadedFile,
     *     published?: bool
     * }  $data
     */
    public function handle(User $user, array $data): PortfolioItem
    {
        $file = $data['file'];
        $mime = $file->getMimeType();
        $mediaType = $data['media_type'] ?? PortfolioMediaType::Image;

        if (str_starts_with($mime, 'video/')) {
            $mediaType = PortfolioMediaType::Video;
        }

        $path = $file->store('portfolio/'.$user->id, 'public');

        $item = $user->portfolioItems()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'talent_id' => $data['talent_id'] ?? null,
            'media_type' => $mediaType,
            'file_path' => $path,
            'mime_type' => $mime,
            'file_size' => $file->getSize(),
            'published_at' => ($data['published'] ?? true) ? now() : null,
        ]);

        GeneratePortfolioThumbnail::dispatch($item);

        if ($item->isPublished()) {
            $this->awardXp->handle($user, XpEventType::PortfolioPublished, $item);

            if (in_array($item->media_type, [PortfolioMediaType::Image, PortfolioMediaType::Video], true)) {
                $this->storeStatus->handle($user, $item->file_path, $item->title);
            }
        }

        return $item;
    }
}
