<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public bool $isAdminCopy = false,
        public $ccUsers = null
    ) {
        $this->ccUsers = $ccUsers ?? collect();
    }

    public function envelope(): Envelope
    {
        $subject = $this->isAdminCopy
            ? "[New Ticket] {$this->ticket->ticket_number}: {$this->ticket->subject}"
            : "Ticket Received — {$this->ticket->ticket_number}";

        return new Envelope(
            from: config('mail.from.address'),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.created',
            with: [
                'ticket' => $this->ticket,
                'isAdminCopy' => $this->isAdminCopy,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $mail = $this;

        // CC every assignee so they are aware from the moment the ticket is raised
        foreach ($this->ccUsers as $user) {
            if (!empty($user->user_email)) {
                $mail->cc($user->user_email, $user->full_name);
            }
        }

        return $mail;
    }
}
