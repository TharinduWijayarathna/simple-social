<?php

namespace App\Jobs;

use App\Enums\PortfolioMediaType;
use App\Models\PortfolioItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GeneratePortfolioThumbnail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var list<int>
     */
    public array $backoff = [1, 5, 10];

    public function __construct(public PortfolioItem $portfolioItem) {}

    public function handle(): void
    {
        if ($this->portfolioItem->media_type !== PortfolioMediaType::Image) {
            $this->portfolioItem->update([
                'thumbnail_path' => $this->portfolioItem->file_path,
            ]);

            return;
        }

        if (! function_exists('imagecreatetruecolor')) {
            $this->portfolioItem->update([
                'thumbnail_path' => $this->portfolioItem->file_path,
            ]);

            return;
        }

        $contents = Storage::disk('public')->get($this->portfolioItem->file_path);

        if ($contents === null) {
            return;
        }

        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            $this->portfolioItem->update([
                'thumbnail_path' => $this->portfolioItem->file_path,
            ]);

            return;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $max = 256;
        $scale = min($max / $width, $max / $height, 1);
        $thumbWidth = (int) max(1, round($width * $scale));
        $thumbHeight = (int) max(1, round($height * $scale));

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        ob_start();
        imagejpeg($thumb, quality: 80);
        $encoded = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($thumb);

        $thumbnailPath = 'portfolio/thumbs/'.$this->portfolioItem->id.'.jpg';
        Storage::disk('public')->put($thumbnailPath, $encoded);

        $this->portfolioItem->update([
            'thumbnail_path' => $thumbnailPath,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Portfolio thumbnail generation failed', [
            'portfolio_item_id' => $this->portfolioItem->id,
            'error' => $exception?->getMessage(),
        ]);
    }
}
