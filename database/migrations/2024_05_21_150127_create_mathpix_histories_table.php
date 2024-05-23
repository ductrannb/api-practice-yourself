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
        Schema::create('mathpix_histories', function (Blueprint $table) {
            $table->id();
            $table->string('pdf_id');
            $table->string('status');
            $table->string('import_status');
            $table->unsignedInteger('num_pages')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mathpix_histories');
    }
};
