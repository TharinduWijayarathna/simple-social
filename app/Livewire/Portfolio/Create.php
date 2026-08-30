<?php

namespace App\Livewire\Portfolio;

use App\Actions\StorePortfolioItem;
use App\Actions\StoreStatus;
use App\Enums\PortfolioMediaType;
use App\Models\Talent;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
#[Title('Create & Share')]
class Create extends Component
{
    use WithFileUploads;

    #[Url(as: 'type')]
    public string $upload_type = 'post'; // 'post' or 'story'

    public string $title = '';

    public string $description = '';

    public ?int $talent_id = null;

    public string $media_type = 'image';

    public mixed $file = null;

    public function updatedFile(): void
    {
        if ($this->file) {
            $mime = $this->file->getMimeType();
            if (str_starts_with($mime, 'video/')) {
                $this->media_type = 'video';
            } elseif (str_starts_with($mime, 'audio/')) {
                $this->media_type = 'audio';
            } elseif (str_starts_with($mime, 'image/')) {
                $this->media_type = 'image';
            }
        }
    }

    public function save(StorePortfolioItem $storePortfolioItem, StoreStatus $storeStatus): void
    {
        if ($this->upload_type === 'story') {
            $validated = $this->validate([
                'description' => ['nullable', 'string', 'max:255'],
                'file' => ['required', 'file', 'max:102400', 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,webm,mkv'],
            ]);

            $caption = $this->title ? $this->title.($this->description ? ' — '.$this->description : '') : $this->description;
            $storeStatus->fromUpload(auth()->user(), $validated['file'], $caption ?: null);

            session()->flash('status', 'Your 24h Campus Story has been published!');
            $this->redirect(route('home'), navigate: true);

            return;
        }

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'media_type' => ['required', Rule::enum(PortfolioMediaType::class)],
            'file' => ['required', 'file', 'max:102400', 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,webm,mkv,mp3,wav,pdf,doc,docx'],
        ]);

        $storePortfolioItem->handle(auth()->user(), $validated);

        session()->flash('status', 'Your portfolio post/reel has been published on the campus wall!');
        $this->redirect(route('home'), navigate: true);
    }

    public function render(): View
    {
        $campusId = auth()->user()->campus_id;

        return view('livewire.portfolio.create', [
            'talents' => Talent::query()->forCampus($campusId)->orderBy('name')->get(),
        ]);
    }
}
