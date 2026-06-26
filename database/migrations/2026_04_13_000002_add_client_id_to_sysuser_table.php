<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sysuser', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('dept_id');
            $table->foreign('client_id')->references('client_id')->on('clients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sysuser', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};
