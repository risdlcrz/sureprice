<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class PaymentStatusNotification extends Notification
{
    use Queueable;

    public $payment;
    public $status;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, string $status, string $message = '')
    {
        $this->payment = $payment;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match($this->status) {
            'verified' => 'Payment Verified Successfully',
            'rejected' => 'Payment Rejected - Action Required',
            'info_requested' => 'Additional Information Required',
            default => 'Payment Status Update'
        };

        $greeting = match($this->status) {
            'verified' => 'Great news! Your payment has been verified.',
            'rejected' => 'Your payment submission requires attention.',
            'info_requested' => 'We need additional information about your payment.',
            default => 'Your payment status has been updated.'
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line('Payment Details:')
            ->line('Payment Number: ' . $this->payment->payment_number)
            ->line('Amount: ₱' . number_format($this->payment->amount, 2))
            ->line('Contract: ' . ($this->payment->contract->contract_number ?? 'Contract #' . $this->payment->contract_id))
            ->when($this->message, function($mail) {
                return $mail->line('Additional Information:')
                           ->line($this->message);
            })
            ->action('View Payment Details', url('/client/payments/' . $this->payment->id))
            ->line('Thank you for using SurePrice!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'status' => $this->status,
            'message' => $this->message,
            'amount' => $this->payment->amount,
            'contract_id' => $this->payment->contract_id,
        ];
    }
}
