<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PurchaseOrder;

class PurchaseOrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    public $po;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseOrder $po)
    {
        $this->po = $po;
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
        return (new MailMessage)
            ->subject('Purchase Order Shipped')
            ->line('PO #' . $this->po->po_number . ' has been marked as shipped.')
            ->action('View PO', url('/purchase-orders/' . $this->po->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'po_id' => $this->po->id,
            'status' => $this->po->status,
        ];
    }
}
