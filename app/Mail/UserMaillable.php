<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserMaillable extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public function __construct(
        User $user,
    ) {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation et activation du compte',
            from: config('mail.from.address', 'dsi-dgess@mid.gov.bf')
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.compte-user',
            with: [
                'user' => $this->user,
                'url' => route('login')
            ]
        );
    }
}
