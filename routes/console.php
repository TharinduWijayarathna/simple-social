<?php

use App\Enums\EventApplicationStatus;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\Status;
use App\Notifications\EventReminderNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('rankings:recompute')
    ->hourly()
    ->withoutOverlapping();

Schedule::call(function (): void {
    Status::query()->where('expires_at', '<', now())->delete();
})->hourly()->name('statuses:prune')->withoutOverlapping();

Schedule::call(function (): void {
    Event::query()
        ->published()
        ->whereBetween('starts_at', [now(), now()->addDay()])
        ->with(['applications' => fn ($query) => $query->where('status', EventApplicationStatus::RsvpYes)->with('user')])
        ->get()
        ->each(function (Event $event): void {
            $event->applications->each(function (EventApplication $application) use ($event): void {
                $application->user->notify(new EventReminderNotification($event));
            });
        });
})->dailyAt('08:00')->name('events:remind')->withoutOverlapping();
