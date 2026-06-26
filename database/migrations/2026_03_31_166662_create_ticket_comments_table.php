<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('user_id');
            $table->text('comment');
            $table->tinyInteger('is_internal')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ticket_id')->references('ticket_id')->on('tickets');
            $table->foreign('user_id')->references('user_id')->on('sysuser');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
