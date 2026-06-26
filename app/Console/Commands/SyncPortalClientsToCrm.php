<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ExternalClient;
use Illuminate\Console\Command;

class SyncPortalClientsToCrm extends Command
{
    protected $signature = 'sync:portal-clients';
    protected $description = 'Sync all existing portal clients (ExternalClient) to CRM (Client) table';

    public function handle(): int
    {
        $this->info('🔄 Starting to sync portal clients to CRM...');

        $externalClients = ExternalClient::all();
        $synced = 0;

        foreach ($externalClients as $externalClient) {
            $crmClient = Client::where('client_email', $externalClient->email)->first();

            if ($crmClient) {
                // Update existing
                $crmClient->update([
                    'client_name' => $externalClient->company_name,
                    'client_email' => $externalClient->email,
                    'client_contact' => $externalClient->phone,
                    'client_representative' => $externalClient->full_name,
                    'is_active' => $externalClient->status === 'active' ? 1 : 0,
                ]);
                $this->line("✅ Updated: {$externalClient->company_name}");
                $synced++;
            } else {
                // Create new
                Client::create([
                    'client_name' => $externalClient->company_name,
                    'client_email' => $externalClient->email,
                    'client_contact' => $externalClient->phone,
                    'client_representative' => $externalClient->full_name,
                    'is_active' => $externalClient->status === 'active' ? 1 : 0,
                ]);
                $this->line("✨ Created: {$externalClient->company_name}");
                $synced++;
            }
        }

        $this->info("✅ Sync complete! {$synced} clients synced.");
        return Command::SUCCESS;
    }
}
