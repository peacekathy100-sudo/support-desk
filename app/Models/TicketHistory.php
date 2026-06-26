<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Ticket;
use App\Models\SysUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'ticket_history';

    protected $fillable = [
        'ticket_id',
        'changed_by',
        'field_changed',
        'old_value',
        'new_value',
        'note',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'changed_by', 'user_id');
    }
}
