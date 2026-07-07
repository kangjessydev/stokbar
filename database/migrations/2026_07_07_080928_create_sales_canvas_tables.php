<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver_name');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // akun sales
            $table->timestamps();
        });

        Schema::create('canvas_muatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_car_id')->constrained('sales_cars')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('qty_loaded');
            $table->date('tanggal');
            $table->string('status')->default('pending'); // 'pending', 'confirmed'
            $table->timestamps();
        });

        Schema::create('canvas_returs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_car_id')->constrained('sales_cars')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('qty_returned');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canvas_returs');
        Schema::dropIfExists('canvas_muatans');
        Schema::dropIfExists('sales_cars');
    }
};
