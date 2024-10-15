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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_offer')->default(false);
            $table->double('offer')->nullable();
            $table->text('description');
            $table->double('price');
            $table->double('quantity');
            $table->boolean('is_quantity')->default(false);
            $table->date('expiration');
            $table->string('image')->nullable();
            $table->string('meta_subtitle')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
