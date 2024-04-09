<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->date('birthday')->nullable();
            $table->unsignedTinyInteger('gender')->default(User::GENDER_UNKNOWN);
            $table->unsignedTinyInteger('role_id')->default(User::ROLE_USER);
            $table->unsignedInteger('balance')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
