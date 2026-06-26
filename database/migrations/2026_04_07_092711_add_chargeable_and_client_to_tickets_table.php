<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('chargeable')->default(false)->after('description');
            $table->unsignedBigInteger('client_id')->nullable()->after('chargeable');

            $table->foreign('client_id')->references('client_id')->on('clients');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['chargeable', 'client_id']);
        });
    }
};
