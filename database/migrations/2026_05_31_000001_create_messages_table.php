<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_client_id');
            $table->unsignedInteger('sent_to_user_id')->nullable();
            $table->text('message');
            $table->enum('type', ['inquiry', 'follow_up', 'feedback', 'general'])->default('general');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('external_client_id')->references('id')->on('external_clients')->onDelete('cascade');
            $table->foreign('sent_to_user_id')->references('user_id')->on('sysuser')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
