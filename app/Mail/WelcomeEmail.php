<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $password = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Auralis8 – Account Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'heading' => 'Welcome to Auralis8!',
            ],
        );
    }
}
