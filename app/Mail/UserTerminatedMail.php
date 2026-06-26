<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SysUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserTerminatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SysUser $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Deactivated — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.users.terminated',
            with: ['user' => $this->user],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
