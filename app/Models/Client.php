<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * CRM company record (internal). Portal login accounts use ExternalClient.
 */
class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'clients';
    protected $primaryKey = 'client_id';

    protected $fillable = [
        'client_code',
        'client_name',
        'client_email',
        'client_address',
        'client_contact',
        'client_representative',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $client) {
            if (empty($client->client_code)) {
                $prefix = 'CLT-' . now()->format('Ymd');
                $lastCode = static::where('client_code', 'like', "{$prefix}%")
                                  ->orderByDesc('client_id')
                                  ->value('client_code');

                $sequence = $lastCode
                    ? (int) substr($lastCode, strrpos($lastCode, '-') + 1) + 1
                    : 1;

                $client->client_code = $prefix . '-' . str_pad((string) $sequence, 4, '0', \STR_PAD_LEFT);
            }
        });
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'client_id', 'client_id');
    }
}
