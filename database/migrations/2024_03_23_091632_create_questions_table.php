<?php

use App\Models\Question;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assignable_id');
            $table->tinyInteger('assignable_type')->comment('1. Lesson, 2. Exam');
            $table->longText('content');
            $table->longText('solution')->nullable();
            $table->unsignedTinyInteger('level')->default(Question::LEVEL_EASY)->comment('1. Easy, 2. Medium, 3. Hard');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
