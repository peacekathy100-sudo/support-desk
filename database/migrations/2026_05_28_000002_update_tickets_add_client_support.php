<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Add external_client_id to support external clients submitting tickets
            if (!Schema::hasColumn('tickets', 'external_client_id')) {
                $table->unsignedBigInteger('external_client_id')->nullable()->after('ticket_id');
                $table->foreign('external_client_id')
                    ->references('id')
                    ->on('external_clients')
                    ->nullOnDelete();
            }

            // Track who created the ticket (user or external_client)
            if (!Schema::hasColumn('tickets', 'created_by_type')) {
                $table->enum('created_by_type', ['user', 'external_client'])->default('user')->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'external_client_id')) {
                $table->dropForeign(['external_client_id']);
                $table->dropColumn('external_client_id');
            }

            if (Schema::hasColumn('tickets', 'created_by_type')) {
                $table->dropColumn('created_by_type');
            }
        });
    }
};

