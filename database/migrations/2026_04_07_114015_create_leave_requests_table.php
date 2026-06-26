<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->increments('leave_id');
            $table->string('leave_number')->unique();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('supervisor_id')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->enum('leave_type', [
                'sick',
                'bereavement',
                'time_off_without_pay',
                'personal_annual',
                'maternity_paternity',
                'other',
            ]);
            $table->string('other_specify')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('days_requested')->nullable();
            $table->text('reason');
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('user_id')->on('sysuser');
            $table->foreign('supervisor_id')->references('user_id')->on('sysuser');
            $table->foreign('approved_by')->references('user_id')->on('sysuser');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
