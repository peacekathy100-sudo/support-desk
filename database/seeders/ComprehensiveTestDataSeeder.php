<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\SysUser;
use App\Models\Department;
use App\Models\AuditTrail;
use App\Models\LeaveRequest;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\TicketCategory;
use App\Models\TicketAttachment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ComprehensiveTestDataSeeder extends Seeder
{
    private array $userIds = [];
    private array $clientIds = [];
    private array $departmentIds = [];
    private array $categoryIds = [];
    private array $ticketIds = [];

    public function run(): void
    {
        echo "🚀 Starting Comprehensive Test Data Seeding...\n";
        
        DB::transaction(function () {
            $this->seedDepartments();
            $this->seedCategories();
            $this->seedUsers();
            $this->seedClients();
            $this->seedTickets();
            $this->seedComments();
            $this->seedAttachments();
            $this->seedTicketHistory();
            $this->seedLeaveRequests();
            $this->seedAuditTrails();
        });

        echo "\n✅ Test data seeding completed!\n";
        echo "Summary:\n";
        echo "  📊 Departments: " . count($this->departmentIds) . "\n";
        echo "  👥 Users: " . count($this->userIds) . "\n";
        echo "  🏢 Clients: " . count($this->clientIds) . "\n";
        echo "  🎫 Tickets: " . count($this->ticketIds) . "\n";
        echo "  💬 Comments: Created\n";
        echo "  📎 Attachments: Created\n";
        echo "  📜 History: Created\n";
        echo "  🗓️ Leave Requests: Created\n";
    }

    private function seedDepartments(): void
    {
        echo "\n📁 Seeding Departments...\n";

        $departments = [
            ['dept_name' => 'Support', 'dept_code' => 'SUP'],
            ['dept_name' => 'Sales', 'dept_code' => 'SAL'],
            ['dept_name' => 'Finance', 'dept_code' => 'FIN'],
            ['dept_name' => 'HR', 'dept_code' => 'HR'],
            ['dept_name' => 'Development', 'dept_code' => 'DEV'],
            ['dept_name' => 'Operations', 'dept_code' => 'OPS'],
            ['dept_name' => 'QA', 'dept_code' => 'QA'],
            ['dept_name' => 'Marketing', 'dept_code' => 'MKT'],
        ];

        foreach ($departments as $dept) {
            $d = Department::firstOrCreate(
                ['dept_code' => $dept['dept_code']],
                [
                    'dept_name' => $dept['dept_name'],
                    'is_active' => 1,
                ]
            );
            $this->departmentIds[] = $d->dept_id;
        }

        echo "✅ Created " . count($this->departmentIds) . " departments\n";
    }

    private function seedCategories(): void
    {
        echo "\n📚 Seeding Ticket Categories...\n";

        $categories = [
            'Technical Issue',
            'Feature Request',
            'Bug Report',
            'Documentation',
            'Billing',
            'General Inquiry',
            'Enhancement',
            'Integration',
            'Performance',
            'Security',
            'Configuration',
            'Training',
        ];

        foreach ($categories as $cat) {
            $c = TicketCategory::firstOrCreate(
                ['name' => $cat],
                ['is_active' => 1]
            );
            $this->categoryIds[] = $c->id;
        }

        echo "✅ Created " . count($this->categoryIds) . " categories\n";
    }

    private function seedUsers(): void
    {
        echo "\n👥 Seeding 1000+ Users...\n";

        // Main admin users
        $mainUsers = [
            ['user_name' => 'System Admin', 'role' => 'main_admin'],
            ['user_name' => 'Super User', 'role' => 'super_user'],
            ['user_name' => 'Support Manager', 'role' => 'super_user'],
            ['user_name' => 'Support Lead', 'role' => 'agent'],
        ];

        foreach ($mainUsers as $user) {
            $u = SysUser::firstOrCreate(
                ['user_name' => $user['user_name']],
                [
                    'user_surname' => 'Administrator',
                    'user_email' => strtolower(str_replace(' ', '.', $user['user_name'])) . '@test.local',
                    'user_password' => Hash::make('123'),
                    'dept_id' => $this->departmentIds[0],
                    'user_status' => 'active',
                    'user_online' => rand(0, 1),
                    'user_last_logged_in' => now()->subHours(rand(0, 720)),
                ]
            );
            $this->userIds[] = $u->user_id;
        }

        // Create 1000+ operator/agent users
        for ($i = 1; $i <= 1200; $i++) {
            $dept = $this->departmentIds[array_rand($this->departmentIds)];
            $username = 'operator_' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
            
            $u = SysUser::firstOrCreate(
                ['user_name' => $username],
                [
                    'user_surname' => 'Operator',
                    'user_othername' => 'User ' . $i,
                    'user_email' => $username . '@test.local',
                    'user_password' => Hash::make('123'),
                    'dept_id' => $dept,
                    'user_status' => rand(0, 100) > 20 ? 'active' : 'inactive',
                    'user_online' => rand(0, 1),
                    'user_last_logged_in' => now()->subHours(rand(0, 720)),
                ]
            );
            $this->userIds[] = $u->user_id;

            if ($i % 100 == 0) {
                echo "  ✓ Created $i users\n";
            }
        }

        echo "✅ Created " . count($this->userIds) . " users total\n";
    }

    private function seedClients(): void
    {
        echo "\n🏢 Seeding Clients...\n";

        $companies = [
            'Acme Corp', 'Tech Solutions', 'Global Industries', 'Digital Ventures',
            'Cloud Systems', 'Network Pro', 'Data Labs', 'Innovation Hub',
            'Smart Software', 'Enterprise Plus', 'Future Tech', 'Velocity Inc',
            'Quantum Systems', 'Nexus Technologies', 'Prime Solutions',
            'Elite Services', 'Dynamic Corp', 'PowerPoint Tech', 'Swift Systems',
            'Stellar Solutions', 'Aurora Tech', 'Zenith Corporation', 'Nova Systems',
        ];

        foreach ($companies as $company) {
            $c = Client::firstOrCreate(
                ['client_code' => strtoupper(substr(str_replace(' ', '', $company), 0, 5))],
                [
                    'client_name' => $company,
                ]
            );
            $this->clientIds[] = $c->client_id;
        }

        echo "✅ Created " . count($this->clientIds) . " clients\n";
    }

    private function seedTickets(): void
    {
        echo "\n🎫 Seeding Tickets with 6-month history...\n";

        $statuses = ['open', 'in_progress', 'on_hold', 'resolved', 'closed'];
        $priorities = ['low', 'normal', 'high', 'urgent'];
        $ticketsPerDay = 50;
        $daysToSimulate = 180; // 6 months

        $ticketCount = 0;

        // Create tickets for the last 6 months
        for ($day = $daysToSimulate; $day >= 0; $day--) {
            $date = now()->subDays($day);

            for ($j = 0; $j < $ticketsPerDay; $j++) {
                $createdBy = $this->userIds[array_rand($this->userIds)];
                $clientId = $this->clientIds[array_rand($this->clientIds)];
                $categoryId = $this->categoryIds[array_rand($this->categoryIds)];
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];

                $ticket = Ticket::create([
                    'ticket_number' => $this->generateTicketNumber($date),
                    'created_by' => $createdBy,
                    'assigned_to' => $status !== 'open' ? $this->userIds[array_rand($this->userIds)] : null,
                    'category_id' => $categoryId,
                    'subject' => $this->generateTicketSubject(),
                    'description' => $this->generateDescription(),
                    'priority' => $priority,
                    'status' => $status,
                    'client_id' => $clientId,
                    'created_from_dept' => $this->departmentIds[array_rand($this->departmentIds)],
                    'chargeable' => rand(0, 1),
                    'created_at' => $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                    'updated_at' => $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                    'due_at' => $status !== 'closed' ? $date->copy()->addDays(rand(1, 14)) : null,
                    'resolved_at' => in_array($status, ['resolved', 'closed']) ? $date->copy()->addDays(rand(0, 7)) : null,
                    'resolved_by' => in_array($status, ['resolved', 'closed']) ? $this->userIds[array_rand($this->userIds)] : null,
                    'closed_at' => $status === 'closed' ? $date->copy()->addDays(rand(8, 14)) : null,
                ]);

                $this->ticketIds[] = $ticket->ticket_id;
                $ticketCount++;
            }

            if ($ticketCount % 500 == 0) {
                echo "  ✓ Created $ticketCount tickets\n";
            }
        }

        echo "✅ Created $ticketCount tickets total\n";
    }

    private function seedComments(): void
    {
        echo "\n💬 Seeding Ticket Comments...\n";

        $commentCount = 0;
        $commentsPerTicket = rand(1, 5);

        foreach ($this->ticketIds as $ticketId) {
            $ticket = Ticket::find($ticketId);
            
            for ($i = 0; $i < $commentsPerTicket; $i++) {
                TicketComment::create([
                    'ticket_id' => $ticketId,
                    'user_id' => $this->userIds[array_rand($this->userIds)],
                    'comment' => $this->generateComment(),
                    'is_internal' => rand(0, 1),
                    'created_at' => $ticket->created_at->copy()->addHours($i + 1),
                    'updated_at' => $ticket->created_at->copy()->addHours($i + 1),
                ]);
                $commentCount++;
            }
        }

        echo "✅ Created $commentCount comments\n";
    }

    private function seedAttachments(): void
    {
        echo "\n📎 Seeding Ticket Attachments...\n";

        $attachmentCount = 0;
        $attachmentNames = [
            'screenshot.png', 'error_log.txt', 'config.json', 'database_dump.sql',
            'user_report.xlsx', 'system_logs.zip', 'invoice.pdf', 'proposal.docx',
            'test_results.csv', 'performance_metrics.json',
        ];

        $mimeTypes = [
            'png' => 'image/png',
            'txt' => 'text/plain',
            'json' => 'application/json',
            'sql' => 'text/plain',
            'xlsx' => 'application/vnd.ms-excel',
            'zip' => 'application/zip',
            'pdf' => 'application/pdf',
            'docx' => 'application/msword',
            'csv' => 'text/csv',
        ];

        foreach (array_slice($this->ticketIds, 0, (int)(count($this->ticketIds) / 2)) as $ticketId) {
            if (rand(0, 100) > 60) {
                $ticket = Ticket::find($ticketId);
                
                for ($i = 0; $i < rand(1, 3); $i++) {
                    $fileName = $attachmentNames[array_rand($attachmentNames)];
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $mimeType = substr($mimeTypes[$ext] ?? 'application/octet-stream', 0, 50);
                    
                    TicketAttachment::create([
                        'ticket_id' => $ticketId,
                        'uploaded_by' => $this->userIds[array_rand($this->userIds)],
                        'file_name' => $fileName,
                        'file_path' => 'tickets/' . $ticketId . '/' . time() . '_' . $i . '.' . $ext,
                        'file_type' => $mimeType,
                        'file_size' => rand(1024, 5242880),
                        'created_at' => $ticket->created_at,
                    ]);
                    $attachmentCount++;
                }
            }
        }

        echo "✅ Created $attachmentCount attachments\n";
    }

    private function seedTicketHistory(): void
    {
        echo "\n📜 Seeding Ticket History...\n";

        $historyCount = 0;

        foreach (array_slice($this->ticketIds, 0, (int)(count($this->ticketIds) / 3)) as $ticketId) {
            $ticket = Ticket::find($ticketId);

            $changes = [
                ['field' => 'status', 'old' => 'open', 'new' => 'in_progress'],
                ['field' => 'priority', 'old' => 'normal', 'new' => 'high'],
                ['field' => 'status', 'old' => 'in_progress', 'new' => 'on_hold'],
                ['field' => 'status', 'old' => 'on_hold', 'new' => 'resolved'],
                ['field' => 'assigned_to', 'old' => 'unassigned', 'new' => 'assigned'],
            ];

            for ($i = 0; $i < rand(2, 6); $i++) {
                $change = $changes[array_rand($changes)];
                TicketHistory::create([
                    'ticket_id' => $ticketId,
                    'changed_by' => $this->userIds[array_rand($this->userIds)],
                    'field_changed' => $change['field'],
                    'old_value' => $change['old'],
                    'new_value' => $change['new'],
                    'changed_at' => $ticket->created_at->copy()->addHours($i),
                ]);
                $historyCount++;
            }
        }

        echo "✅ Created $historyCount history records\n";
    }

    private function seedLeaveRequests(): void
    {
        echo "\n🗓️ Seeding Leave Requests...\n";

        $leaveCount = 0;
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];
        $types = ['sick', 'bereavement', 'time_off_without_pay', 'personal_annual', 'maternity_paternity'];

        foreach (array_slice($this->userIds, 4, (int)count($this->userIds)) as $userId) {
            for ($i = 0; $i < rand(2, 6); $i++) {
                $fromDate = now()->subDays(rand(30, 180));
                $toDate = $fromDate->copy()->addDays(rand(1, 10));
                $daysRequested = $toDate->diffInDays($fromDate) + 1;

                LeaveRequest::create([
                    'user_id' => $userId,
                    'leave_type' => $types[array_rand($types)],
                    'from_date' => $fromDate->format('Y-m-d'),
                    'to_date' => $toDate->format('Y-m-d'),
                    'days_requested' => $daysRequested,
                    'reason' => $this->generateLeaveReason(),
                    'status' => $statuses[array_rand($statuses)],
                    'supervisor_id' => rand(0, 1) ? $this->userIds[array_rand($this->userIds)] : null,
                    'approved_by' => rand(0, 1) ? $this->userIds[array_rand($this->userIds)] : null,
                    'approved_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                    'created_at' => $fromDate->subDays(rand(1, 30)),
                    'updated_at' => $fromDate->subDays(rand(1, 30)),
                ]);
                $leaveCount++;
            }
        }

        echo "✅ Created $leaveCount leave requests\n";
    }

    private function seedAuditTrails(): void
    {
        echo "\n🔐 Seeding Audit Trails...\n";

        $auditCount = 0;
        $actions = ['created', 'updated', 'deleted', 'viewed', 'assigned', 'closed'];

        foreach (array_slice($this->ticketIds, 0, (int)(count($this->ticketIds) / 2)) as $ticketId) {
            for ($i = 0; $i < rand(2, 4); $i++) {
                AuditTrail::create([
                    'user_id' => $this->userIds[array_rand($this->userIds)],
                    'action' => $actions[array_rand($actions)],
                    'model' => 'Ticket',
                    'model_id' => $ticketId,
                    'old_values' => json_encode(['status' => 'open']),
                    'new_values' => json_encode(['status' => 'in_progress']),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0',
                    'url' => '/tickets/' . $ticketId,
                    'created_at' => now()->subDays(rand(0, 180)),
                ]);
                $auditCount++;
            }
        }

        echo "✅ Created $auditCount audit trails\n";
    }

    private function generateTicketNumber($date): string
    {
        static $sequencesByDate = [];
        
        $prefix = 'TKT-' . $date->format('Ymd');
        $sequencesByDate[$prefix] = ($sequencesByDate[$prefix] ?? 0) + 1;
        
        return $prefix . '-' . str_pad((string)$sequencesByDate[$prefix], 5, '0', STR_PAD_LEFT);
    }

    private function generateTicketSubject(): string
    {
        $subjects = [
            'Login issue - cannot access account',
            'Performance degradation on dashboard',
            'Feature request: Dark mode support',
            'Bug: Export to PDF not working',
            'Billing inquiry about subscription',
            'Integration with third-party API',
            'Database backup procedure',
            'SSL certificate renewal',
            'User permission configuration',
            'Password reset not working',
            'Email notification delay',
            'Database query optimization needed',
            'Report generation timeout',
            'API rate limiting issues',
            'Mobile app compatibility',
            'Data export failing',
            'Two-factor authentication setup',
            'System maintenance schedule',
            'Backup recovery procedure',
            'Configuration documentation needed',
        ];

        return $subjects[array_rand($subjects)];
    }

    private function generateDescription(): string
    {
        $descriptions = [
            'User is unable to log into their account. Multiple attempts made but keeps getting invalid credentials error.',
            'The dashboard is loading very slowly. It takes 30+ seconds to fully load.',
            'We would like to have a dark mode option in the application for better accessibility.',
            'The PDF export feature is returning an error when trying to export large reports.',
            'Need clarification on billing charges from last month.',
            'Looking to integrate with Salesforce CRM system.',
            'Need to schedule database maintenance window.',
            'SSL certificate is expiring soon, needs renewal.',
            'User needs elevated permissions to view department reports.',
            'Password reset email not being received by user.',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function generateComment(): string
    {
        $comments = [
            'I have investigated the issue and found the root cause.',
            'The update has been applied to the system.',
            'Please provide additional information to help troubleshoot.',
            'This issue is awaiting approval from the manager.',
            'We have scheduled this for next release.',
            'The fix has been deployed to production.',
            'Waiting for client feedback on this request.',
            'This is a known limitation of the current version.',
            'I will follow up with you on this matter.',
            'The issue has been resolved successfully.',
        ];

        return $comments[array_rand($comments)];
    }

    private function generateLeaveReason(): string
    {
        $reasons = [
            'Annual vacation',
            'Personal day',
            'Sick leave',
            'Family emergency',
            'Medical appointment',
            'Conference attendance',
            'Training session',
            'Company event',
            'Unpaid leave for personal matters',
        ];

        return $reasons[array_rand($reasons)];
    }
}
