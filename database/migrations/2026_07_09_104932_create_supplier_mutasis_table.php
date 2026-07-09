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
        Schema::create('supplier_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('barang_id')->constrained('barangs'); // Hanya barang dengan source = supplier
            $table->date('tanggal');
            $table->enum('jenis_transaksi', ['barang_masuk', 'barang_retur']);
            $table->integer('qty_bal'); // Jumlah dalam satuan Bal/Dus/Iket
            $table->decimal('harga_satuan', 12, 2)->default(0.00);
            $table->decimal('total_hutang', 12, 2)->default(0.00);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_mutasis');
    }
};
