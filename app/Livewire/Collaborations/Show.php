<?php

namespace App\Livewire\Collaborations;

use App\Actions\RespondToCollaborationRequest;
use App\Enums\CollaborationRequestStatus;
use App\Models\Collaboration;
use App\Notifications\CollaborationRequestedNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Collaboration')]
class Show extends Component
{
    public Collaboration $collaboration;

    public string $message = '';

    public function mount(Collaboration $collaboration): void
    {
        $this->collaboration = $collaboration;
    }

    public function requestToJoin(): void
    {
        $validated = $this->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $request = $this->collaboration->requests()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'message' => $validated['message'] ?: null,
                'status' => CollaborationRequestStatus::Pending,
            ],
        );

        $this->collaboration->owner->notify(
            (new CollaborationRequestedNotification(auth()->user(), $request->load('collaboration')))->afterCommit(),
        );

        session()->flash('status', 'Request sent. The owner can accept from the watch.');
    }

    public function respond(int $requestId, bool $accept, RespondToCollaborationRequest $respond): void
    {
        $collaborationRequest = $this->collaboration->requests()->with('collaboration')->findOrFail($requestId);
        $this->authorize('respondToRequests', $this->collaboration);
        $respond->handle(auth()->user(), $collaborationRequest, $accept);
    }

    public function render(): View
    {
        $this->collaboration->load([
            'owner:id,name',
            'talent:id,name',
            'members.user:id,name',
            'requests' => fn ($query) => $query->where('status', CollaborationRequestStatus::Pending)->with('user:id,name'),
        ]);

        return view('livewire.collaborations.show');
    }
}
