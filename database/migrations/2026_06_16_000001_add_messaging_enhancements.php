<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add typing indicators table
        if (!Schema::hasTable('typing_indicators')) {
            Schema::create('typing_indicators', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
                $table->morphs('user');
                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('expires_at')->useCurrent();
                $table->timestamps();
                
                $table->index(['conversation_id', 'expires_at']);
            });
        }

        // Add message reactions table
        if (!Schema::hasTable('message_reactions')) {
            Schema::create('message_reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
                $table->morphs('user');
                $table->string('reaction'); // emoji
                $table->timestamps();
                
                $table->unique(['message_id', 'user_id', 'user_type', 'reaction']);
                $table->index(['message_id', 'reaction']);
            });
        }

        // Add message pinning table
        if (!Schema::hasTable('pinned_messages')) {
            Schema::create('pinned_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
                $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
                $table->morphs('pinned_by');
                $table->text('pin_reason')->nullable();
                $table->timestamps();
                
                $table->unique(['message_id', 'conversation_id']);
                $table->index(['conversation_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pinned_messages');
        Schema::dropIfExists('message_reactions');
        Schema::dropIfExists('typing_indicators');
    }
};
