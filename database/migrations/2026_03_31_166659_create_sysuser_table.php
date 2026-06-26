<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sysuser', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('user_name')->unique();
            $table->string('check_number')->nullable()->unique();
            $table->string('user_surname');
            $table->string('user_othername')->nullable();
            $table->string('user_email')->unique();
            $table->string('user_password');
            $table->string('user_telephone')->nullable();
            $table->enum('user_gender', ['Male', 'Female', 'Other'])->nullable();
            $table->unsignedInteger('user_role')->nullable();
            $table->unsignedInteger('dept_id')->nullable();
            $table->enum('user_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('profile_photo')->nullable();
            $table->timestamp('user_last_logged_in')->nullable();
            $table->tinyInteger('user_online')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_role')->references('ur_id')->on('user_roles')->nullOnDelete();
            $table->foreign('dept_id')->references('dept_id')->on('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sysuser');
    }
};
