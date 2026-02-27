<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invitation $invitation)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation à rejoindre une colocation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: [
                'invitation' => $this->invitation,
                'acceptUrl' => route('invitations.accept', $this->invitation->token),
                'refuseUrl' => route('invitations.refuse', $this->invitation->token),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
