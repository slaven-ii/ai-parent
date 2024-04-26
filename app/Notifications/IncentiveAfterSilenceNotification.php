<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class IncentiveAfterSilenceNotification extends Notification
{
    use Queueable;

    private User $user;
    private \App\Models\Notification $notification;
    const TYPE = 'incentive';

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $base = env('SPA_URL') . "/dashboard";
        $subject = $this->user->name;
        $title = $this->notification->title;
        //$content = str_replace("\n", '<br>', $this->notification->content);
        $content = Str::markdown($this->notification->content);
        dump($content);
        return (new MailMessage)
            ->subject("$subject boriš li se još sa $title")
            ->greeting("Pozdrav $subject")
            ->line(new HtmlString($content))
            ->line('Znamo da roditeljstvo ne dolazi sa uputama, ako želite detaljnije o navedenoj temi ili imate novi izazov ulogirajte se u Parentlyo i dobijte potrebnu podršku.')
            ->action('Postavi pitanje', $base)
            ->line('Kao beta testeru, hvala na podršci i potpori u građenju alata za roditelje.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
