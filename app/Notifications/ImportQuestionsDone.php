<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportQuestionsDone extends Notification
{
    use Queueable;

    private $record;
    private $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($record, $type)
    {
        $this->record = $record;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'name' => $this->record->name ?? null,
            'type' => $this->type,
        ];
    }
}
