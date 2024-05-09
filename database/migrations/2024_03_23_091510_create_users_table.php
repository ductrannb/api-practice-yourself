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
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->date('birthday')->nullable();
            $table->unsignedTinyInteger('gender')->default(User::GENDER_UNKNOWN)->comment('1. Male, 2. Female, 3. Unknown');
            $table->unsignedTinyInteger('role_id')->default(User::ROLE_USER)->comment('1. User, 2. Teacher, 3. Admin');
            $table->unsignedInteger('balance')->default(0);
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
