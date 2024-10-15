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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('status',['PREPARING','SHIPPING','DELIVERED'])->default('PREPARING');
            $table->boolean('payment_status')->default(false);
            $table->timestamp('time')->nullable();
            $table->boolean('is_time')->default(false);
            $table->text('note')->nullable();
            $table->boolean('accepted_by_user')->default(true);
            $table->boolean('is_prescription')->default(false);
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
