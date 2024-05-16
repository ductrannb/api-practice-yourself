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
        Schema::table('question_choice_selected', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('assignable_type');
            $table->boolean('is_correct')->after('user_id')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_choice_selected', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('is_correct');
        });
    }
};
