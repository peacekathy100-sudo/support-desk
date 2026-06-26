<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ChatAttachment;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ExternalClient;
use App\Models\Message;
use App\Models\SysUser;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

class PortalPerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('en_US');

        $bankNames = [
            'Stanbic Bank Uganda',
            'dfcu Bank',
            'Absa Bank Uganda',
            'Centenary Bank',
            'Standard Chartered Bank Uganda',
            'Equity Bank Uganda',
            'Barclays Bank of Uganda',
            'Bank of Africa Uganda',
            'Orient Bank',
            'Finance Trust Bank',
            'Housing Finance Bank',
            'Opportunity Bank Uganda',
            'Pride Microfinance Ltd',
            'Uganda Development Bank',
            'Tropical Bank',
            'KCB Bank Uganda',
            'PostBank Uganda',
            'Stanbic Bank',
            'Centenary Rural Development Bank',
            'Access Bank Uganda',
            'DFCU Bank',
            'Cooperative Bank of Uganda',
            'Eco Bank Uganda',
            'NCBA Bank Uganda',
            'Guaranty Trust Bank Uganda',
        ];

        $businessTypes = [
            'Manufacturing',
            'Retail',
            'Hospitality',
            'Agriculture',
            'Logistics',
            'Mining',
            'Healthcare',
            'Education',
            'Technology',
            'Real Estate',
            'Finance',
            'Consulting',
        ];

        $businessClients = [];
        for ($i = 1; $i <= 175; $i++) {
            $businessClients[] = $faker->company() . ' ' . $faker->randomElement(['Ltd', 'PLC', 'Group', 'Services', 'Corp']);
        }

        $supportUsers = SysUser::whereIn('user_name', ['admin', 'sysadmin', 'superadmin', 'administrators'])
            ->orWhereHas('role', function ($query) {
                $query->where('ur_name', 'Support Agent')->orWhere('ur_name', 'Operations Manager');
            })
            ->limit(18)
            ->get();

        if ($supportUsers->isEmpty()) {
            $this->command->warn('No support SysUsers found. Creating a fallback support account.');
            $supportUsers = collect([
                SysUser::create([
                    'user_name' => 'support1',
                    'user_email' => 'support1@company.com',
                    'check_number' => 'SUP-001',
                    'user_surname' => 'Support',
                    'user_othername' => 'Agent One',
                    'user_password' => Hash::make('password'),
                    'user_role' => DB::table('user_roles')->where('ur_name', 'Support Agent')->value('ur_id') ?? 1,
                    'dept_id' => DB::table('departments')->value('dept_id') ?? 1,
                    'user_status' => 'active',
                    'user_gender' => 'Female',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
            ]);
        }

        $supportUserIds = $supportUsers->pluck('user_id')->values();
        if ($supportUserIds->isEmpty()) {
            $supportUserIds = collect([1]);
        }

        $ticketCategoryIds = TicketCategory::pluck('id')->filter();
        if ($ticketCategoryIds->isEmpty()) {
            $categories = ['Technical Support', 'Billing', 'Account Management', 'Service Request', 'Other'];
            foreach ($categories as $categoryName) {
                $ticketCategoryIds->push(TicketCategory::create(['name' => $categoryName])->id);
            }
        }

        $totalClients = count($bankNames) + count($businessClients);
        $createdClients = 0;

        $allClientNames = array_merge($bankNames, $businessClients);
        shuffle($allClientNames);

        foreach ($allClientNames as $companyName) {
            $companyName = trim($companyName);
            $firstName = explode(' ', $companyName)[0];
            $usernameBase = Str::of($companyName)->slug('-')->substr(0, 20)->toString();
            $username = $usernameBase . '-' . $faker->unique()->numberBetween(100, 999);
            $email = Str::of($username)->replace('-', '.')->append('@ugmail.com')->toString();
            $assignedTo = $supportUserIds->random();
            $category = in_array($companyName, $bankNames, true) ? 'Bank' : $faker->randomElement($businessTypes);
            $status = $faker->randomElement(['active', 'inactive', 'suspended']);

            $client = ExternalClient::create([
                'company_name' => $companyName,
                'full_name' => $companyName . ' Account',
                'email' => $email,
                'phone' => $faker->phoneNumber(),
                'username' => $username,
                'password' => Hash::make('Password123!'),
                'assigned_to_user_id' => $assignedTo,
                'category' => $category,
                'status' => $status,
                'notes' => $faker->sentence(12),
                'created_by' => $supportUserIds->random(),
                'last_login_at' => $faker->dateTimeBetween('-30 days', 'now'),
                'last_activity_at' => $faker->dateTimeBetween('-7 days', 'now'),
                'remember_token' => Str::random(10),
            ]);

            $createdClients++;
            $this->command->info("Created client {$createdClients}/{$totalClients}: {$companyName}");

            $ticketCount = $faker->numberBetween(1, 4);
            for ($j = 0; $j < $ticketCount; $j++) {
                $status = $faker->randomElement(['open', 'in_progress', 'on_hold', 'resolved', 'closed']);
                $ticket = Ticket::create([
                    'created_by' => $assignedTo,
                    'assigned_to' => $assignedTo,
                    'category_id' => $ticketCategoryIds->random(),
                    'subject' => $faker->sentence(5),
                    'description' => $faker->paragraph(3),
                    'priority' => $faker->randomElement(['low', 'normal', 'high', 'urgent']),
                    'status' => $status,
                    'resolved_at' => $status === 'resolved' ? now()->subDays($faker->numberBetween(1, 14)) : null,
                    'closed_at' => $status === 'closed' ? now()->subDays($faker->numberBetween(1, 10)) : null,
                    'due_at' => $faker->optional(0.7)->dateTimeBetween('now', '+10 days'),
                    'created_from_dept' => DB::table('departments')->where('dept_code', 'support-ops')->value('dept_id') ?? 1,
                    'external_client_id' => $client->id,
                    'chargeable' => $faker->boolean(40),
                ]);

                Message::create([
                    'external_client_id' => $client->id,
                    'sent_to_user_id' => $assignedTo,
                    'message' => $faker->sentence(12),
                    'type' => $faker->randomElement(['inquiry', 'follow_up', 'feedback', 'general']),
                    'is_read' => false,
                ]);

                if ($faker->boolean(65)) {
                    Message::create([
                        'external_client_id' => $client->id,
                        'sent_to_user_id' => $assignedTo,
                        'message' => $faker->sentence(14),
                        'type' => $faker->randomElement(['general', 'follow_up', 'feedback']),
                        'is_read' => true,
                    ]);
                }
            }

            $conversation = Conversation::create([
                'type' => 'private',
                'subject' => $faker->sentence(4),
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'participantable_id' => $client->id,
                'participantable_type' => ExternalClient::class,
                'participant_role' => 'member',
                'joined_at' => now(),
                'last_read_at' => now(),
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'participantable_id' => $assignedTo,
                'participantable_type' => SysUser::class,
                'participant_role' => 'support',
                'joined_at' => now(),
                'last_read_at' => now(),
            ]);

            $messageCount = $faker->numberBetween(2, 6);
            $senderType = ExternalClient::class;
            for ($k = 0; $k < $messageCount; $k++) {
                $senderType = $senderType === ExternalClient::class ? SysUser::class : ExternalClient::class;
                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $senderType === ExternalClient::class ? $client->id : $assignedTo,
                    'sender_type' => $senderType,
                    'body' => $faker->paragraph(1),
                    'status' => $senderType === ExternalClient::class ? 'sent' : $faker->randomElement(['sent', 'delivered', 'read']),
                    'delivered_at' => now()->subMinutes($faker->numberBetween(1, 120)),
                    'read_at' => $senderType === SysUser::class ? now() : null,
                ]);
            }
        }

        $this->command->info("Portal performance seed completed: {$createdClients} external clients created.");
    }
}
