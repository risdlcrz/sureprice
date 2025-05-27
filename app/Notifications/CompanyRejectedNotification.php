<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyRejectedNotification extends Notification
{
    use Queueable;

    /**
     * The reason for rejection.
     */
    protected $reason; // ✅ Declare the property

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Company Registration Status')
            ->line('We regret to inform you that your company registration has not been approved.')
            ->line('Reason: ' . $this->reason)
            ->line('Please contact support if you have any questions.');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Add any data you want to store in the database (if using the "database" channel)
        ];
    }
}
