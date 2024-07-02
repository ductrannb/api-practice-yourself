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
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('assignable_id');
            $table->dropColumn('assignable_type');
            $table->unsignedBigInteger('learning_module_id')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('assignable_id');
            $table->tinyInteger('assignable_type')->comment('1. Lesson, 2. Exam');
            $table->dropColumn('learning_module_id');
        });
    }
};
