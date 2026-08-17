<?php

namespace App\Livewire\Portfolio;

use App\Actions\StorePortfolioItem;
use App\Enums\PortfolioMediaType;
use App\Models\Talent;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
#[Title('New post')]
class Create extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public ?int $talent_id = null;

    public string $media_type = 'image';

    public mixed $file = null;

    public function save(StorePortfolioItem $storePortfolioItem): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'media_type' => ['required', Rule::enum(PortfolioMediaType::class)],
            'file' => ['required', 'file', 'max:51200', 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,mp3,wav,pdf,doc,docx'],
        ]);

        $storePortfolioItem->handle(auth()->user(), $validated);

        $this->redirect(route('home'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.portfolio.create', [
            'talents' => Talent::query()->orderBy('name')->get(),
        ]);
    }
}
