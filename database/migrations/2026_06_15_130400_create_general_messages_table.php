<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('general_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type'); // ExternalClient or SysUser
            $table->string('subject');
            $table->longText('body');
            $table->enum('status', ['new', 'read', 'replied'])->default('new');
            $table->unsignedBigInteger('replied_by')->nullable();
            $table->longText('reply_body')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('sender_id');
            $table->index('sender_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_messages');
    }
};
