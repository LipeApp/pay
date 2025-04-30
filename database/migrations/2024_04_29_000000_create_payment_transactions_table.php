<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('UZS');
            $table->string('status');
            $table->string('transaction_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
}; 