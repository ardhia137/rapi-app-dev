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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_customer');
            $table->string('no_hp')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->integer('point')->nullable()->default(0);
            $table->enum('kategori_pelanggan', ['Toko', 'Konsumen', 'Aplikator/Tukang'])->default('Konsumen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
