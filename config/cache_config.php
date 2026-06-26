<?php

/**
 * Cache configuration for performance optimization
 * Define TTLs and cache keys for frequently accessed data
 */

return [
    'ticket_categories' => [
        'key' => 'ticket_categories_active',
        'ttl' => 300, // 5 minutes
        'query' => 'TicketCategory::where("is_active", 1)->get()',
    ],

    'active_clients' => [
        'key' => 'clients_active',
        'ttl' => 300, // 5 minutes
        'query' => 'Client::where("is_active", 1)->orderBy("client_name")->get()',
    ],

    'active_departments' => [
        'key' => 'departments_active',
        'ttl' => 300, // 5 minutes
        'query' => 'Department::where("is_active", 1)->get()',
    ],

    'dashboard_stats' => [
        'key' => 'dashboard_stats_',
        'ttl' => 600, // 10 minutes - per user
        'description' => 'Cache dashboard statistics per user',
    ],

    'user_permissions' => [
        'key' => 'user_permissions_',
        'ttl' => 3600, // 1 hour - per user
        'description' => 'Cache user roles and permissions',
    ],

    'online_users' => [
        'key' => 'user_online_',
        'ttl' => 300, // 5 minutes - per user
        'description' => 'Track online status in cache before DB update',
    ],
];
