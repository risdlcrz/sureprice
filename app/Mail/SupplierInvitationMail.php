<?php

namespace App\Mail;

use App\Models\SupplierInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    public function __construct(SupplierInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this->markdown('emails.supplier-invitation')
                    ->subject('Invitation to Supply Materials - ' . $this->invitation->project->name);
    }
} 