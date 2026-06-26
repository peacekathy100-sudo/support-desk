<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Department;
use App\Models\SysUser;
use App\Models\TicketCategory;
use App\Models\UserRole;
use App\Models\ExternalClient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create departments
        $depts = [
            [
                'dept_id' => 1,
                'dept_name' => 'Support',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'dept_id' => 2,
                'dept_name' => 'Engineering',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'dept_id' => 3,
                'dept_name' => 'Sales',
                'is_active' => 1,
                'created_at' => now(),
            ],
        ];

        foreach ($depts as $dept) {
            Department::firstOrCreate(['dept_id' => $dept['dept_id']], $dept);
        }

        // Create user roles
        $roles = [
            [
                'ur_id' => 1,
                'ur_name' => 'Super Admin',
                'permissions' => json_encode(['*']),
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'ur_id' => 2,
                'ur_name' => 'Admin',
                'permissions' => json_encode(['*']),
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'ur_id' => 3,
                'ur_name' => 'Support Agent',
                'permissions' => json_encode(['tickets.*', 'view_clients']),
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'ur_id' => 4,
                'ur_name' => 'Support Lead',
                'permissions' => json_encode(['tickets.*', 'view_clients', 'view_users', 'edit_users']),
                'is_active' => 1,
                'created_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            UserRole::firstOrCreate(['ur_id' => $role['ur_id']], $role);
        }

        // Create ticket categories
        $categories = [
            ['id' => 1, 'name' => 'Technical Support', 'is_active' => 1],
            ['id' => 2, 'name' => 'Billing', 'is_active' => 1],
            ['id' => 3, 'name' => 'Feature Request', 'is_active' => 1],
            ['id' => 4, 'name' => 'General Inquiry', 'is_active' => 1],
        ];

        foreach ($categories as $cat) {
            TicketCategory::firstOrCreate(['id' => $cat['id']], $cat);
        }

        // Create system users
        $users = [
            [
                'user_id' => 1,
                'user_name' => 'admin',
                'user_surname' => 'Administrator',
                'user_othername' => 'Super',
                'user_email' => 'admin@flaxem.local',
                'user_password' => Hash::make('password123'),
                'user_telephone' => '1234567890',
                'user_gender' => 'Male',
                'user_role' => 1,
                'dept_id' => 1,
                'user_status' => 'active',
                'created_at' => now(),
            ],
            [
                'user_id' => 2,
                'user_name' => 'agent1',
                'user_surname' => 'Johnson',
                'user_othername' => 'Support',
                'user_email' => 'agent1@flaxem.local',
                'user_password' => Hash::make('password123'),
                'user_telephone' => '1234567891',
                'user_gender' => 'Female',
                'user_role' => 3,
                'dept_id' => 1,
                'user_status' => 'active',
                'created_at' => now(),
            ],
            [
                'user_id' => 3,
                'user_name' => 'agent2',
                'user_surname' => 'Williams',
                'user_othername' => 'Support',
                'user_email' => 'agent2@flaxem.local',
                'user_password' => Hash::make('password123'),
                'user_telephone' => '1234567892',
                'user_gender' => 'Male',
                'user_role' => 3,
                'dept_id' => 1,
                'user_status' => 'active',
                'created_at' => now(),
            ],
            [
                'user_id' => 4,
                'user_name' => 'lead',
                'user_surname' => 'Brown',
                'user_othername' => 'Support',
                'user_email' => 'lead@flaxem.local',
                'user_password' => Hash::make('password123'),
                'user_telephone' => '1234567893',
                'user_gender' => 'Female',
                'user_role' => 4,
                'dept_id' => 1,
                'user_status' => 'active',
                'created_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            SysUser::firstOrCreate(['user_id' => $user['user_id']], $user);
        }

        // Create clients
        $clients = [
            [
                'client_id' => 1,
                'client_name' => 'Acme Corporation',
                'client_representative' => 'John Doe',
                'client_email' => 'john@acme.com',
                'client_contact' => '555-0001',
                'client_address' => '123 Main St, City',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'client_id' => 2,
                'client_name' => 'TechStart Inc',
                'client_representative' => 'Jane Smith',
                'client_email' => 'jane@techstart.com',
                'client_contact' => '555-0002',
                'client_address' => '456 Tech Ave, Tech City',
                'is_active' => 1,
                'created_at' => now(),
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(['client_id' => $client['client_id']], $client);
        }

        // Create external clients (portal users)
        $externalClients = [
            [
                'id' => 1,
                'company_name' => 'Acme Corporation',
                'full_name' => 'John Doe',
                'email' => 'john.doe@acme.com',
                'phone' => '555-0001',
                'username' => 'johndoe',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'company_name' => 'TechStart Inc',
                'full_name' => 'Jane Smith',
                'email' => 'jane.smith@techstart.com',
                'phone' => '555-0002',
                'username' => 'janesmith',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'created_at' => now(),
            ],
        ];

        foreach ($externalClients as $ec) {
            ExternalClient::firstOrCreate(['id' => $ec['id']], $ec);
        }

        $this->command->info('Initial data seeded successfully!');
    }
}
