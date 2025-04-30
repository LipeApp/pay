<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('basket_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_gateway')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 