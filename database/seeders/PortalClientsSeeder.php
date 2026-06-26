<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ExternalClient;
use App\Models\SysUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PortalClientsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n🔐 Seeding 200 External Portal Clients...\n";

        // Get some admins to assign as creators
        $admins = SysUser::where('is_system', 1)->pluck('user_id')->toArray();
        if (empty($admins)) {
            $admins = SysUser::limit(5)->pluck('user_id')->toArray();
        }

        $categories = ['Standard', 'Gold', 'Silver', 'Bronze', 'Premium'];
        $companies = [
            'Acme Corp', 'Tech Solutions', 'Global Industries', 'Digital Ventures',
            'Cloud Systems', 'Network Pro', 'Data Labs', 'Innovation Hub',
            'Smart Software', 'Enterprise Plus', 'Future Tech', 'Velocity Inc',
            'Quantum Systems', 'Nexus Technologies', 'Prime Solutions',
        ];

        $clientCount = 0;

        for ($i = 1; $i <= 200; $i++) {
            $username = 'c' . $i;
            $email = $username . '@portal.local';
            $company = $companies[array_rand($companies)] . ' ' . $i;

            // Check if already exists
            $exists = ExternalClient::where('username', $username)
                ->orWhere('email', $email)
                ->exists();

            if ($exists) {
                echo "  ⊘ Skipped $username (already exists)\n";
                continue;
            }

            ExternalClient::create([
                'company_name' => $company,
                'full_name' => 'Client User ' . $i,
                'email' => $email,
                'phone' => '+1-' . rand(200, 999) . '-' . rand(200, 999) . '-' . rand(1000, 9999),
                'username' => $username,
                'password' => Hash::make('123'),
                'assigned_to_user_id' => $admins[array_rand($admins)] ?? null,
                'category' => $categories[array_rand($categories)],
                'status' => rand(0, 100) > 15 ? 'active' : 'inactive',
                'notes' => 'Auto-generated portal client #' . $i,
                'created_by' => $admins[array_rand($admins)] ?? null,
                'last_login_at' => now()->subDays(rand(0, 180)),
                'last_activity_at' => now()->subDays(rand(0, 180)),
            ]);

            $clientCount++;

            if ($clientCount % 50 === 0) {
                echo "  ✓ Created $clientCount clients\n";
            }
        }

        echo "✅ Created $clientCount external portal clients\n";
        echo "   Login format: c1, c2, c3... c200\n";
        echo "   Password: 123\n";
    }
}
