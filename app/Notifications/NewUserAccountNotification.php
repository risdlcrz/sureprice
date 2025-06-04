<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class NewUserAccountNotification extends Notification
{
    use Queueable;

    private $password;
    private $role;

    /**
     * Create a new notification instance.
     */
    public function __construct($password, $role)
    {
        $this->password = $password;
        $this->role = $role;
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
        try {
            Log::info('Sending temporary password notification to: ' . $notifiable->email);

            return (new MailMessage)
                ->subject('Welcome to SurePrice - Your Temporary Password')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('An account has been created for you as a ' . ucfirst($this->role) . ' user in the SurePrice system.')
                ->line('Your temporary login credentials are:')
                ->line('Email: ' . $notifiable->email)
                ->line('Temporary Password: ' . $this->password)
                ->line('You will receive a separate email to verify your email address.')
                ->line('For security reasons, you will need to verify your email and change your password before accessing the system.')
                ->line('If you did not expect to receive this invitation, please ignore this email.');
        } catch (\Exception $e) {
            Log::error('Failed to send temporary password notification: ' . $e->getMessage());
            throw $e;
        }
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