<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stream(TicketAttachment $attachment): Response
    {
        $user   = auth()->user();
        $ticket = $attachment->ticket;

        $canView = $user->hasPermission('view_tickets')
            || ($user->isClientRep() && $ticket->client_id === $user->client_id)
            || $ticket->created_by  === $user->user_id
            || $ticket->assigned_to === $user->user_id;

        abort_unless($canView, 403, 'You do not have permission to view this attachment.');

        $disk = Storage::disk('public');

        abort_unless($disk->exists($attachment->file_path), 404, 'Attachment file not found.');

        return response()->file(
            $disk->path($attachment->file_path),
            ['Content-Type' => $attachment->file_type ?: 'application/octet-stream']
        );
    }
}
