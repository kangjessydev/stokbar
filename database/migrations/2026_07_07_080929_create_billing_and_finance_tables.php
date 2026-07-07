<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('market');
            $table->string('phone')->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->integer('credit_period')->nullable(); // dalam hari, misal 30 hari
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('sales_car_id')->constrained('sales_cars')->onDelete('cascade');
            $table->decimal('total_price', 15, 2)->default(0.00);
            $table->string('payment_status')->default('lunas'); // 'lunas', 'kredit'
            $table->date('tanggal');
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        Schema::create('piutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->string('status')->default('belum_lunas'); // 'belum_lunas', 'lunas'
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('piutang_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_id')->constrained('piutangs')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash'); // 'cash', 'transfer'
            $table->string('reference_no')->nullable(); // no rekening / slip transfer
            $table->timestamps();
        });

        Schema::create('hutangs', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->string('status')->default('belum_lunas'); // 'belum_lunas', 'lunas'
            $table->date('tanggal');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('hutang_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hutang_id')->constrained('hutangs')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash'); // 'cash', 'transfer'
            $table->string('reference_no')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang_payments');
        Schema::dropIfExists('hutangs');
        Schema::dropIfExists('piutang_payments');
        Schema::dropIfExists('piutangs');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('customers');
    }
};
