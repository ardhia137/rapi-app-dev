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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('batch_number')->unique();
            $table->integer('quantity_masuk');
            $table->integer('quantity_sekarang')->default(0);
            $table->decimal('harga_beli', 15, 2);
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->string('supplier')->nullable();
            $table->enum('status', ['active', 'expired', 'sold_out'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
