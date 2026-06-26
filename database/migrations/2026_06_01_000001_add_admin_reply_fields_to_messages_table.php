<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->enum('sent_by', ['client', 'admin'])->default('client')->after('sent_to_user_id');
            $table->unsignedInteger('sent_by_user_id')->nullable()->after('sent_by');
            $table->foreign('sent_by_user_id')->references('user_id')->on('sysuser')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sent_by_user_id']);
            $table->dropColumn(['sent_by', 'sent_by_user_id']);
        });
    }
};
