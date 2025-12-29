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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // User yang melakukan aktivitas
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Tipe aktivitas (login, logout, create, update, delete, export)
            $table->string('type', 50)->index();

            // Model yang diakses (User, Customer, Product, Invoice, dll)
            $table->string('model_type')->nullable()->index();
            $table->unsignedBigInteger('model_id')->nullable()->index();

            // Deskripsi aktivitas yang mudah dibaca
            $table->text('description');

            // Detail perubahan (JSON format untuk old & new value)
            $table->json('properties')->nullable();

            // IP Address user
            $table->string('ip_address', 45)->nullable();

            // User Agent (browser, device info)
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Index untuk performa query
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
