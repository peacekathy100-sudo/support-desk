<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('client_id');
            $table->integer('rating')->comment('1-5 stars');
            $table->longText('comment')->nullable();
            $table->timestamps();
            $table->unique(['ticket_id', 'client_id']);
            $table->index('ticket_id');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_ratings');
    }
};
