<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_assignees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('assigned_by');
            $table->timestamp('assigned_at')->useCurrent();

            $table->unique(['ticket_id', 'user_id']);

            $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->cascadeOnDelete();
            $table->foreign('user_id')->references('user_id')->on('sysuser');
            $table->foreign('assigned_by')->references('user_id')->on('sysuser');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_assignees');
    }
};
