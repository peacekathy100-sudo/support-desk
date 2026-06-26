<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Client;
use App\Models\ExternalClient;

class ExternalClientObserver
{
    /**
     * Handle the ExternalClient "created" event.
     * Auto-sync ExternalClient data to Client (CRM) table
     */
    public function created(ExternalClient $externalClient): void
    {
        $this->syncToClient($externalClient);
    }

    /**
     * Handle the ExternalClient "updated" event.
     * Keep Client (CRM) data in sync
     */
    public function updated(ExternalClient $externalClient): void
    {
        $this->syncToClient($externalClient);
    }

    /**
     * Handle the ExternalClient "deleted" event.
     */
    public function deleted(ExternalClient $externalClient): void
    {
        // Optionally delete or deactivate corresponding CRM client
        $client = Client::where('client_email', $externalClient->email)->first();
        if ($client) {
            $client->update(['is_active' => 0]);
        }
    }

    /**
     * Sync ExternalClient data to Client (CRM)
     */
    private function syncToClient(ExternalClient $externalClient): void
    {
        // Find or create CRM Client by email
        $client = Client::where('client_email', $externalClient->email)->first();

        if ($client) {
            // Update existing CRM client
            $client->update([
                'client_name' => $externalClient->company_name,
                'client_email' => $externalClient->email,
                'client_contact' => $externalClient->phone,
                'is_active' => $externalClient->status === 'active' ? 1 : 0,
            ]);
        } else {
            // Create new CRM client from ExternalClient data
            Client::create([
                'client_name' => $externalClient->company_name,
                'client_email' => $externalClient->email,
                'client_contact' => $externalClient->phone,
                'client_representative' => $externalClient->full_name,
                'is_active' => $externalClient->status === 'active' ? 1 : 0,
            ]);
        }
    }
}
