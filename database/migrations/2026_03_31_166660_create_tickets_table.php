<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('ticket_id');
            $table->string('ticket_number')->unique();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['open', 'in_progress', 'on_hold', 'resolved', 'closed'])->default('open');
            $table->text('resolution_note')->nullable();
            $table->unsignedInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->unsignedInteger('reopened_by')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->unsignedInteger('created_from_dept')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('user_id')->on('sysuser');
            $table->foreign('assigned_to')->references('user_id')->on('sysuser');
            $table->foreign('category_id')->references('id')->on('ticket_categories');
            $table->foreign('resolved_by')->references('user_id')->on('sysuser');
            $table->foreign('reopened_by')->references('user_id')->on('sysuser');
            $table->foreign('created_from_dept')->references('dept_id')->on('departments');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
