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
        Schema::create('piutang_payment_barang_sisas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_payment_id')->constrained('piutang_payments')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs'); // Barang Jadi yang diretur
            $table->integer('qty_retur'); // Jumlah kemasan (dalam Bal/Dus/Iket)
            $table->decimal('harga_jual', 10, 2); // Harga jual asli barang saat retur
            $table->decimal('subtotal_kredit', 12, 2); // Calculated: qty_retur * harga_jual
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang_payment_barang_sisas');
    }
};
