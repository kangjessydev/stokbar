<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'bahan_baku' atau 'barang_jadi'
            $table->timestamps();
        });

        Schema::create('bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
            $table->string('name');
            $table->decimal('stock', 12, 2)->default(0.00);
            $table->string('unit'); // 'kg', 'liter', 'pcs', dll.
            $table->decimal('safety_stock', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
            $table->string('name');
            $table->integer('stock')->default(0);
            $table->string('unit')->default('pcs');
            $table->decimal('price', 15, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('resep_boms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->decimal('qty_needed', 12, 4); // jumlah bahan baku yang dibutuhkan per 1 barang jadi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_boms');
        Schema::dropIfExists('barangs');
        Schema::dropIfExists('bahan_bakus');
        Schema::dropIfExists('gudangs');
    }
};
