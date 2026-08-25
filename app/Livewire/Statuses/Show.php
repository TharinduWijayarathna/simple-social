<?php

namespace App\Livewire\Statuses;

use App\Models\Status;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Status')]
class Show extends Component
{
    public Status $status;

    public function mount(Status $status): void
    {
        abort_unless($status->isActive(), 404);
        $this->authorize('view', $status);
        $this->status = $status->load('user.profile');
    }

    public function render(): View
    {
        return view('livewire.statuses.show');
    }
}
