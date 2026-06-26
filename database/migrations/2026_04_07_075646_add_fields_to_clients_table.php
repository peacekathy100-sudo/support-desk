<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_email', 150)->nullable()->unique()->after('client_name');
            $table->string('client_address', 255)->nullable()->after('client_email');
            $table->string('client_contact', 20)->nullable()->after('client_address');
            $table->string('client_representative', 150)->nullable()->after('client_contact');
            $table->boolean('is_active')->default(1)->after('client_representative');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'client_email',
                'client_address',
                'client_contact',
                'client_representative',
                'is_active',
            ]);
        });
    }
};
