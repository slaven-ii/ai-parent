<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\NotificationServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Retrieve pending notifications from the database
        $notifications = Notification::whereNull('sent_at')->get();

        foreach ($notifications as $notification) {
            $notificationType = $notification->type;
            $user = $notification->user;

            // Dispatch the appropriate notification using the factory
            $notificationInstance = NotificationServices::create($notificationType, $user, $notification);
            \Illuminate\Support\Facades\Notification::route('mail', $user->email)->notify($notificationInstance);

            // Mark notification as sent
            $notification->update(['sent_at' => now()]);
        }
    }
}
