<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\ExternalClient;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\TicketNotification;
use App\Models\Message;
use App\Models\SysUser;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Ticket operations for portal users (ExternalClient).
 */
class ClientTicketService
{
    public function createTicket(ExternalClient $client, array $data): Ticket
    {
        if (!$client->assigned_to_user_id) {
            throw new InvalidArgumentException(
                'This account has no assigned representative. Contact support before creating tickets.'
            );
        }

        $ticket = Ticket::create([
            'external_client_id' => $client->id,
            'created_by_type' => 'external_client',
            'created_by' => $client->assigned_to_user_id,
            'assigned_to' => $client->assigned_to_user_id,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'open',
        ]);

        // Handle attachments if provided
        if (!empty($data['attachments'])) {
            $this->handleAttachments($ticket, $client, $data['attachments']);
        }

        $this->recordHistory($ticket, $client, 'Ticket created by portal client');

        $this->logActivity('Ticket created by portal client', $ticket);

        $this->notifyRepresentative($ticket, $client);

        return $ticket;
    }

    public function getClientTickets(ExternalClient $client, array $filters = []): Paginator
    {
        $query = Ticket::where('external_client_id', $client->id);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('ticket_number', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('subject', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest('updated_at')->paginate(10);
    }

    public function getTicketForClient(Ticket $ticket, ExternalClient $client): ?Ticket
    {
        if ($ticket->external_client_id === $client->id) {
            return $ticket->load(['assignee', 'comments', 'attachments', 'history']);
        }

        return null;
    }

    public function addClientComment(Ticket $ticket, ExternalClient $client, string $message): TicketComment
    {
        if (!$client->assigned_to_user_id) {
            throw new InvalidArgumentException('Cannot add comment: no assigned representative.');
        }

        $comment = TicketComment::create([
            'ticket_id' => $ticket->ticket_id,
            'user_id' => $client->assigned_to_user_id,
            'comment' => '[' . $client->full_name . ']: ' . $message,
            'is_internal' => false,
        ]);

        $ticket->touch();

        $this->recordHistory($ticket, $client, 'Comment added by portal client');

        $this->notifyRepresentativeComment($ticket, $client);

        return $comment;
    }

    public function uploadAttachment(
        Ticket $ticket,
        ExternalClient $client,
        $file
    ): TicketAttachment {
        if (!$client->assigned_to_user_id) {
            throw new InvalidArgumentException('Cannot upload file: no assigned representative.');
        }

        $path = $file->store("tickets/{$ticket->ticket_id}", 'public');

        $attachment = TicketAttachment::create([
            'ticket_id' => $ticket->ticket_id,
            'uploaded_by' => $client->assigned_to_user_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $ticket->touch();

        return $attachment;
    }

    public function getTicketStatistics(ExternalClient $client): array
    {
        $stats = Ticket::where('external_client_id', $client->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_count,
                SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_count
            ")
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'open' => (int) ($stats->open_count ?? 0),
            'in_progress' => (int) ($stats->in_progress_count ?? 0),
            'resolved' => (int) ($stats->resolved_count ?? 0),
            'closed' => (int) ($stats->closed_count ?? 0),
            'urgent' => (int) ($stats->urgent_count ?? 0),
        ];
    }

    public function getRecentTickets(ExternalClient $client, int $limit = 5)
    {
        return Ticket::where('external_client_id', $client->id)
            ->latest('updated_at')
            ->limit($limit)
            ->get();
    }

    public function getTicketForClientView(int $ticketId, ExternalClient $client): ?Ticket
    {
        return Ticket::where('ticket_id', $ticketId)
            ->where('external_client_id', $client->id)
            ->with(['assignee', 'comments.author', 'attachments', 'history', 'category'])
            ->first();
    }

    public function getTicketComments(Ticket $ticket)
    {
        return $ticket->comments()
            ->where('is_internal', false)
            ->with('author')
            ->latest('created_at')
            ->paginate(10);
    }

    protected function recordHistory(Ticket $ticket, ExternalClient $client, string $note): void
    {
        if (!$client->assigned_to_user_id) {
            return;
        }

        TicketHistory::create([
            'ticket_id' => $ticket->ticket_id,
            'changed_by' => $client->assigned_to_user_id,
            'field_changed' => 'note',
            'old_value' => null,
            'new_value' => null,
            'note' => $note,
            'changed_at' => now(),
        ]);
    }

    protected function notifyRepresentative(Ticket $ticket, ExternalClient $client): void
    {
        try {
            $mainAdmin = SysUser::findMainAdmin();

            if (!$mainAdmin) {
                Log::warning('No main admin found to notify about new portal ticket');
                return;
            }

            // 1. Create a system comment in the ticket
            TicketComment::create([
                'ticket_id' => $ticket->ticket_id,
                'user_id' => $mainAdmin->user_id,
                'comment' => '🔔 **SYSTEM NOTIFICATION**: New ticket created by external client: ' . $client->full_name . ' (Portal Client)',
                'is_internal' => true, // Internal note only
            ]);

            // 2. Create a ticket notification
            TicketNotification::create([
                'user_id' => $mainAdmin->user_id,
                'ticket_id' => $ticket->ticket_id,
                'type' => 'new_portal_ticket',
                'message' => 'New support ticket #' . $ticket->ticket_number . ' created by portal client ' . $client->full_name,
                'is_read' => false,
            ]);

            // 3. Send a direct message to the main admin
            Message::create([
                'external_client_id' => $client->id,
                'sent_to_user_id' => $mainAdmin->user_id,
                'sent_by' => 'system',
                'sent_by_user_id' => null,
                'message' => '📋 **New Portal Ticket Created**\n\n' .
                            'Client: ' . $client->full_name . '\n' .
                            'Ticket #: ' . $ticket->ticket_number . '\n' .
                            'Subject: ' . $ticket->subject . '\n' .
                            'Priority: ' . ucfirst($ticket->priority) . '\n' .
                            'Created: ' . $ticket->created_at->format('Y-m-d H:i:s') . '\n\n' .
                            'This ticket has been assigned to: ' . ($ticket->assignee?->getFullNameAttribute() ?? 'Unassigned'),
                'type' => 'portal_ticket_created',
                'is_read' => false,
            ]);

            Log::info('Portal ticket notifications sent', [
                'ticket_id' => $ticket->ticket_id,
                'external_client_id' => $client->id,
                'admin_notified' => $mainAdmin->user_name,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to notify representative about portal ticket: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id,
                'external_client_id' => $client->id,
            ]);
        }
    }

    protected function notifyRepresentativeComment(Ticket $ticket, ExternalClient $client): void
    {
        try {
            $mainAdmin = SysUser::findMainAdmin();

            if (!$mainAdmin) {
                Log::warning('No main admin found to notify about portal ticket comment');
                return;
            }

            // 1. Create a ticket notification
            TicketNotification::create([
                'user_id' => $mainAdmin->user_id,
                'ticket_id' => $ticket->ticket_id,
                'type' => 'portal_ticket_comment',
                'message' => 'New comment from portal client ' . $client->full_name . ' on ticket #' . $ticket->ticket_number,
                'is_read' => false,
            ]);

            // 2. Send a direct message
            Message::create([
                'external_client_id' => $client->id,
                'sent_to_user_id' => $mainAdmin->user_id,
                'sent_by' => 'system',
                'sent_by_user_id' => null,
                'message' => '💬 **New Comment on Ticket #' . $ticket->ticket_number . '**\n\n' .
                            'From: ' . $client->full_name . '\n' .
                            'Ticket: ' . $ticket->subject . '\n' .
                            'Status: ' . ucfirst($ticket->status),
                'type' => 'portal_ticket_comment',
                'is_read' => false,
            ]);

            Log::info('Portal ticket comment notification sent', [
                'ticket_id' => $ticket->ticket_id,
                'external_client_id' => $client->id,
                'admin_notified' => $mainAdmin->user_name,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to notify representative about portal comment: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id,
                'external_client_id' => $client->id,
            ]);
        }
    }

    protected function logActivity(string $activity, Ticket $ticket): void
    {
        try {
            AuditTrail::create([
                'user_id' => null,
                'action' => $activity,
                'model' => 'Ticket',
                'model_id' => $ticket->ticket_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    protected function handleAttachments(
        Ticket $ticket,
        ExternalClient $client,
        array $files
    ): void {
        if (!$client->assigned_to_user_id) {
            return;
        }

        foreach ($files as $file) {
            try {
                $path = $file->store("tickets/{$ticket->ticket_id}", 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->ticket_id,
                    'uploaded_by' => $client->assigned_to_user_id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to upload attachment for ticket {$ticket->ticket_id}: " . $e->getMessage());
            }
        }
    }
}
