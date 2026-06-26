<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexes('tickets', [
            'status',
            'priority',
            'created_at',
            'due_at',
            'client_id',
            'created_by',
            'assigned_to',
            'resolved_by',
            ['status', 'created_at'],
            ['client_id', 'status'],
            ['created_by', 'status'],
        ]);

        $this->addIndexes('sysuser', [
            'user_status',
            'dept_id',
            'user_online',
            'user_last_logged_in',
            ['dept_id', 'user_status'],
        ]);

        $this->addIndexes('leave_requests', [
            'user_id',
            'status',
            'created_at',
            ['user_id', 'status'],
        ]);

        $this->addIndexes('ticket_history', [
            'ticket_id',
            'changed_at',
            ['ticket_id', 'changed_at'],
        ]);

        $this->addIndexes('ticket_comments', [
            'ticket_id',
            'created_at',
        ]);

        $this->addIndexes('audit_trails', [
            'model',
            'model_id',
            'created_at',
            ['model', 'model_id'],
        ]);

        $this->addIndexes('ticket_attachments', [
            'ticket_id',
        ]);

        $this->addIndexes('clients', [
            'is_active',
            'client_name',
        ]);

        $this->addIndexes('departments', [
            'is_active',
        ]);

        $this->addIndexes('ticket_categories', [
            'is_active',
        ]);

        if (Schema::hasTable('external_clients')) {
            $this->addIndexes('external_clients', [
                'username',
                'deleted_at',
                ['username', 'deleted_at'],
            ]);
        }

        $this->addIndexes('ticket_assignees', [
            'ticket_id',
            'user_id',
            ['ticket_id', 'user_id'],
        ]);
    }

    public function down(): void
    {
        // Indexes are safe to leave in place on rollback in production.
    }

    private function addIndexes(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $table) {
            foreach ($columns as $column) {
                $indexName = is_array($column)
                    ? $this->compositeIndexName($table, $column)
                    : "{$table}_{$column}_index";

                if ($this->indexExists($table, $indexName)) {
                    continue;
                }

                is_array($column)
                    ? $blueprint->index($column, $indexName)
                    : $blueprint->index($column, $indexName);
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");

            return collect($indexes)->contains(fn ($index) => $index->name === $indexName);
        }

        if ($driver === 'pgsql') {
            $indexes = DB::select(
                'SELECT indexname FROM pg_indexes WHERE tablename = ?',
                [$table]
            );

            return collect($indexes)->pluck('indexname')->contains($indexName);
        }

        $indexes = DB::select("SHOW INDEX FROM `{$table}`");

        return collect($indexes)->pluck('Key_name')->contains($indexName);
    }

    private function compositeIndexName(string $table, array $columns): string
    {
        return $table . '_' . implode('_', $columns) . '_index';
    }
};
