<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('external_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name')->index();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedInteger('assigned_to_user_id')->nullable();
            $table->string('category')->nullable()->default('Standard'); // Gold, Silver, Bronze, etc.
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by')->nullable(); // Admin who created this client
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('assigned_to_user_id')
                ->references('user_id')
                ->on('sysuser')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('user_id')
                ->on('sysuser')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_clients');
    }
};

