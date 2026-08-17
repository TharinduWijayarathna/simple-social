<?php

namespace App\Livewire\Statuses;

use App\Actions\StoreStatus;
use App\Models\Status;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
#[Title('Create story')]
class Create extends Component
{
    use WithFileUploads;

    public string $caption = '';

    public mixed $image = null;

    public function mount(): void
    {
        $this->authorize('create', Status::class);
    }

    public function save(StoreStatus $storeStatus): void
    {
        $this->authorize('create', Status::class);

        $validated = $this->validate([
            'caption' => ['nullable', 'string', 'max:160'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        $storeStatus->fromUpload(auth()->user(), $validated['image'], $validated['caption'] ?: null);

        $this->redirect(route('home'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.statuses.create');
    }
}
