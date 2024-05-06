<?php

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
        Schema::create('question_choice_selected', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('question_choice_id');
            $table->unsignedBigInteger('assignable_id');
            $table->unsignedBigInteger('sub_assignable_id')->nullable();
            $table->tinyInteger('assignable_type')->comment('1. Course, 2. Exam');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_choice_selected');
    }
};
