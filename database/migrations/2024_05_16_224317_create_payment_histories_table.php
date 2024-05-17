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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->uuid('id');
            $table->unsignedBigInteger('order_code')->unique();
            $table->unsignedInteger('amount');
            $table->tinyInteger('type');
            $table->string('checkout_url');
            $table->string('status');
            $table->timestamp('expired_at')->nullable();
            $table->json('transactions');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
