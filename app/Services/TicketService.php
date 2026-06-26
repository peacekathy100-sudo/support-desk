<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\TicketComment;
use App\Models\TicketAttachment;
use App\Services\NotificationService;
use App\Services\AuditService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TicketService
{
    public function __construct(
        private NotificationService $notifier,
        private AuditService $auditor
    ) {}

    public function create(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            $dueAt = !empty($data['due_at'])
                ? Carbon::parse($data['due_at'])
                : $this->calculateDueDate($data['priority'] ?? 'normal');

            $ticket = Ticket::create([
                'created_by'         => $user->user_id,
                'category_id'        => $data['category_id'] ?? null,
                'client_id'          => $data['client_id'] ?? null,
                'chargeable'         => $data['chargeable'] ?? 0,
                'subject'            => $data['subject'],
                'description'        => $data['description'],
                'priority'           => $data['priority'] ?? 'normal',
                'status'             => 'open',
                'due_at'             => $dueAt,
                'created_from_dept'  => $user->dept_id,
            ]);

            if (!empty($data['agent_ids'])) {
                $this->syncAssignees($ticket, $data['agent_ids'], $user->user_id);
            }

            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $this->attachFile($ticket, $file);
                }
            }

            $this->logHistory($ticket, 'status', null, 'open', 'Ticket created');
            $this->notifier->ticketCreated($ticket);
            $this->auditor->log('created', 'Ticket', $ticket->ticket_id, null, $ticket->toArray());

            return $ticket;
        });
    }

    public function assign(Ticket $ticket, array $agentIds): Ticket
    {
        return DB::transaction(function () use ($ticket, $agentIds) {
            $actor = Auth::user();

            $oldNames = $ticket->assignees->pluck('full_name')->join(', ') ?: 'Unassigned';

            $this->syncAssignees($ticket, $agentIds, $actor->user_id);

            $ticket->update([
                'assigned_to' => $agentIds[0] ?? null,
                'status'      => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
            ]);

            $ticket->refresh();
            $newNames = $ticket->assignees->pluck('full_name')->join(', ');

            $this->logHistory($ticket, 'assigned_to', $oldNames, $newNames);
            $this->notifier->ticketAssigned($ticket);
            $this->auditor->log('updated', 'Ticket', $ticket->ticket_id);

            return $ticket->fresh();
        });
    }

    private function syncAssignees(Ticket $ticket, array $userIds, int $assignedBy): void
    {
        $pivot = [];
        foreach ($userIds as $uid) {
            $pivot[$uid] = ['assigned_by' => $assignedBy, 'assigned_at' => now()];
        }
        $ticket->assignees()->sync($pivot);
    }

    public function updateStatus(Ticket $ticket, string $status, ?string $note = null): Ticket
    {
        return DB::transaction(function () use ($ticket, $status, $note) {
            $oldStatus = $ticket->status;
            $updates   = ['status' => $status];

            if ($status === 'resolved') {
                $updates['resolved_by'] = Auth::user()->user_id;
                $updates['resolved_at'] = now();
                if ($note) $updates['resolution_note'] = $note;
            }

            if ($status === 'closed') {
                $updates['closed_at'] = now();
            }

            $ticket->update($updates);

            $this->logHistory($ticket, 'status', $oldStatus, $status, $note);
            $this->notifier->ticketStatusChanged($ticket, $oldStatus);
            $this->auditor->log('updated', 'Ticket', $ticket->ticket_id);

            return $ticket->fresh();
        });
    }

    public function updatePriority(Ticket $ticket, string $priority): Ticket
    {
        $old = $ticket->priority;
        $ticket->update(['priority' => $priority]);
        $this->logHistory($ticket, 'priority', $old, $priority);
        return $ticket->fresh();
    }

    public function reopen(Ticket $ticket): Ticket
    {
        return DB::transaction(function () use ($ticket) {
            $ticket->update([
                'status'      => 'open',
                'reopened_by' => Auth::user()->user_id,
                'reopened_at' => now(),
                'resolved_at' => null,
                'resolved_by' => null,
                'closed_at'   => null,
            ]);

            $this->logHistory($ticket, 'status', 'closed', 'open', 'Ticket reopened');
            $this->notifier->ticketReopened($ticket);

            return $ticket->fresh();
        });
    }

    public function addComment(Ticket $ticket, string $comment, bool $isInternal = false): TicketComment
    {
        $record = TicketComment::create([
            'ticket_id'   => $ticket->ticket_id,
            'user_id'     => Auth::user()->user_id,
            'comment'     => $comment,
            'is_internal' => $isInternal,
        ]);

        if (!$isInternal) {
            $this->notifier->commentAdded($ticket, $record);
        }

        return $record;
    }

    public function attachFile(Ticket $ticket, UploadedFile $file): TicketAttachment
    {
        $path = $file->store("tickets/{$ticket->ticket_id}", 'public');

        return TicketAttachment::create([
            'ticket_id'   => $ticket->ticket_id,
            'uploaded_by' => Auth::user()->user_id,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
        ]);
    }

    private function calculateDueDate(string $priority): Carbon
    {
        $hours = match($priority) {
            'urgent' => 4,
            'high'   => 8,
            'normal' => 24,
            'low'    => 72,
            default  => 24,
        };
        return now()->addHours($hours);
    }

    private function logHistory(Ticket $ticket, string $field, mixed $old, mixed $new, ?string $note = null): void
    {
        TicketHistory::create([
            'ticket_id'     => $ticket->ticket_id,
            'changed_by'    => Auth::user()->user_id,
            'field_changed' => $field,
            'old_value'     => $old,
            'new_value'     => $new,
            'note'          => $note,
            'changed_at'    => now(),
        ]);
    }
}
