<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\TicketAssignedMail;
use App\Mail\TicketChargeableMail;
use App\Mail\TicketCommentMail;
use App\Mail\TicketCreatedMail;
use App\Mail\TicketStatusChangedMail;
use App\Models\SysUser;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketNotification;
use App\Services\SmsService;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(private ?SmsService $smsService = null)
    {
        $this->smsService ??= new SmsService();
    }

    public function ticketCreated(Ticket $ticket): void
    {
        $ticket->loadMissing('assignees');
        $assignees = $ticket->assignees;

        $this->create($ticket->creator, $ticket, 'ticket_created',
            "Your ticket [{$ticket->ticket_number}] has been received. We will respond shortly."
        );
        $this->sendMail(
            $ticket->creator->user_email,
            new TicketCreatedMail($ticket, false, $assignees)
        );
        $this->sendSmsToUser(
            $ticket->creator,
            "Your ticket [{$ticket->ticket_number}] has been received. We will respond shortly."
        );

        $admins = SysUser::whereHas('role',
            fn($q) => $q->where('permissions', 'like', '%"*"%')
        )->get();
        foreach ($admins as $admin) {
            if ($admin->user_id === $ticket->created_by) continue;
            $this->create($admin, $ticket, 'new_ticket',
                "New ticket [{$ticket->ticket_number}]: {$ticket->subject}"
            );
            $this->sendMail(
                $admin->user_email,
                new TicketCreatedMail($ticket, true, $assignees)
            );
            $this->sendSmsToUser($admin, "New ticket [{$ticket->ticket_number}] needs attention.");
        }

        $this->notifyClientEmail($ticket, 'ticket_created',
            "A new support ticket [{$ticket->ticket_number}] has been raised for your account: {$ticket->subject}",
            $assignees
        );
    }

    public function ticketAssigned(Ticket $ticket): void
    {
        $ticket->loadMissing('assignees');

        foreach ($ticket->assignees as $assignee) {
            $this->create($assignee, $ticket, 'ticket_assigned',
                "Ticket [{$ticket->ticket_number}] has been assigned to you. Priority: " . ucfirst($ticket->priority)
            );
            $this->sendMail($assignee->user_email, new TicketAssignedMail($ticket));
            $this->sendSmsToUser($assignee, "Ticket [{$ticket->ticket_number}] has been assigned to you. Priority: " . ucfirst($ticket->priority));
        }

        $this->notifyClientRepUsers($ticket, 'ticket_assigned',
            "Ticket [{$ticket->ticket_number}] has been assigned and is now being handled."
        );
    }

    public function ticketStatusChanged(Ticket $ticket, string $oldStatus): void
    {
        $msg = "Ticket [{$ticket->ticket_number}] status changed from " .
               ucfirst(str_replace('_', ' ', $oldStatus)) . " to " .
               ucfirst(str_replace('_', ' ', $ticket->status)) . ".";

        $this->create($ticket->creator, $ticket, 'status_changed', $msg);
        $this->sendMail($ticket->creator->user_email, new TicketStatusChangedMail($ticket, $oldStatus));
        $this->sendSmsToUser($ticket->creator, $msg);

        $ticket->loadMissing('assignees');
        foreach ($ticket->assignees as $assignee) {
            if ($assignee->user_id === $ticket->created_by) continue;
            $this->create($assignee, $ticket, 'status_changed', $msg);
            $this->sendMail($assignee->user_email, new TicketStatusChangedMail($ticket, $oldStatus));
            $this->sendSmsToUser($assignee, $msg);
        }

        $this->notifyClientRepUsers($ticket, 'status_changed', $msg);
        $this->notifyClientEmail($ticket, 'status_changed', $msg);
    }

    public function commentAdded(Ticket $ticket, TicketComment $comment): void
    {
        $commenter = $comment->author;
        $msg       = "New reply on ticket [{$ticket->ticket_number}].";

        if ($ticket->created_by !== $commenter->user_id) {
            $this->create($ticket->creator, $ticket, 'comment_added', $msg);
            $this->sendMail($ticket->creator->user_email, new TicketCommentMail($ticket, $comment));
            $this->sendSmsToUser($ticket->creator, $msg);
        }

        $ticket->loadMissing('assignees');
        foreach ($ticket->assignees as $assignee) {
            if ($assignee->user_id === $commenter->user_id) continue;
            if ($assignee->user_id === $ticket->created_by) continue;
            $this->create($assignee, $ticket, 'comment_added',
                "New comment on ticket [{$ticket->ticket_number}] you are handling."
            );
            $this->sendMail($assignee->user_email, new TicketCommentMail($ticket, $comment));
            $this->sendSmsToUser($assignee, "New comment on ticket [{$ticket->ticket_number}] you are handling.");
        }

        if (!$comment->is_internal) {
            $this->notifyClientRepUsers($ticket, 'comment_added', $msg);
            $this->notifyClientEmail($ticket, 'comment_added', $msg);
        }
    }

    public function notifyClientChargeable(Ticket $ticket): void
    {
        $label = $ticket->chargeable ? 'chargeable' : 'not chargeable';
        $msg   = "Ticket [{$ticket->ticket_number}] has been marked as {$label}.";

        $this->notifyClientRepUsers($ticket, 'chargeable_update', $msg);

        $ticket->loadMissing('client');
        if ($ticket->client && $ticket->client->client_email) {
            $this->sendMail($ticket->client->client_email, new TicketChargeableMail($ticket));
        }

        if ($ticket->client && $ticket->client->client_contact) {
            $this->smsService->send($ticket->client->client_contact, $msg);
        }
    }

    public function ticketReopened(Ticket $ticket): void
    {
        $msg = "Ticket [{$ticket->ticket_number}] has been reopened.";

        $ticket->loadMissing('assignees');
        foreach ($ticket->assignees as $assignee) {
            $this->create($assignee, $ticket, 'ticket_reopened', $msg);
            $this->sendMail($assignee->user_email, new TicketAssignedMail($ticket));
            $this->sendSmsToUser($assignee, $msg);
        }

        $this->notifyClientRepUsers($ticket, 'ticket_reopened', $msg);
        $this->notifyClientEmail($ticket, 'ticket_reopened', $msg);
    }

    private function notifyClientRepUsers(Ticket $ticket, string $type, string $message): void
    {
        if (!$ticket->client_id) return;

        $reps = SysUser::where('client_id', $ticket->client_id)
            ->where('user_status', 'active')
            ->get();

        foreach ($reps as $rep) {
            $this->create($rep, $ticket, $type, $message);
            $this->sendSmsToUser($rep, $message);
        }
    }

    private function notifyClientEmail(Ticket $ticket, string $type, string $message, ?Collection $ccUsers = null): void
    {
        $ticket->loadMissing('client');
        if (!$ticket->client || !$ticket->client->client_email) return;

        $ccUsers ??= collect();

        $mailable = match ($type) {
            'ticket_created' => new TicketCreatedMail($ticket, false, $ccUsers),
            'status_changed' => new TicketStatusChangedMail($ticket, ''),
            default => null,
        };

        if ($mailable) {
            $this->sendMail($ticket->client->client_email, $mailable);
        }

        if ($ticket->client->client_contact) {
            $this->smsService->send($ticket->client->client_contact, $message);
        }
    }

    private function create(SysUser $user, Ticket $ticket, string $type, string $message): void
    {
        TicketNotification::create([
            'user_id' => $user->user_id,
            'ticket_id' => $ticket->ticket_id,
            'type' => $type,
            'message' => $message,
            'is_read' => 0,
        ]);
    }

    private function sendSmsToUser(SysUser $user, string $message): void
    {
        if (blank($user->user_telephone)) {
            return;
        }

        $this->smsService->send($user->user_telephone, $message);
    }

    private function sendMail(string $email, Mailable $mailable): void
    {
        try {
            @set_time_limit(0);
            Mail::to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::warning("Mail dispatch failed to [{$email}]: " . $e->getMessage());
        }
    }
}

