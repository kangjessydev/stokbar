# Spesifikasi Teknis Pengembangan Sistem (stokbar-app)

Dokumen ini memetakan hasil diskusi kebutuhan bisnis Sonia Paradise ke dalam arsitektur teknis Laravel & Filament, dibagi menjadi 2 fase pengembangan.

---

## 🗺️ Rencana Implementasi Bertahap

```
┌────────────────────────────────────────────────────────┐
│                   FASE 1: RETAIL & SALES               │
│ - Tipe Invoice & Hak Akses (Admin vs Sales)            │
│ - Gudang Atas: Stok Barang Jadi                        │
│ - Pembayaran Piutang & Retur Barang Sisa               │
│ - Buku Terpusat Transaksi Supplier                     │
└───────────────────────────┬────────────────────────────┘
                            │ (Setelah Fase 1 Stabil)
                            ▼
┌────────────────────────────────────────────────────────┐
│                   FASE 2: SISTEM PABRIK                │
│ - Gaji Pekerja Borongan (Rp 500, Rp 1000, Rp 2000)     │
│ - Gudang Bawah: Stok Bahan Baku Mentah                 │
│ - Log Koreksi Selisih Gaji & Konversi Sisa Produksi    │
└────────────────────────────────────────────────────────┘
```

---

## 🚀 FASE 1: RETAIL, PENJUALAN SALES, & STOK BARANG JADI (PRIORITAS)

### 💾 1.1 Skema Database & Migrasi (Fase 1)

#### A. Transaksi Penjualan & Pembayaran Piutang

##### 1. Tambah Kolom di Tabel `invoices`
Untuk membedakan jenis transaksi penjualan berdasarkan jalurnya.
```php
Schema::table('invoices', function (Blueprint $table) {
    $table->enum('tipe_penjualan', ['pasar', 'agen', 'eceran_gudang'])->default('pasar');
});
```

##### 2. Tabel `piutang_payment_barang_sisas`
Menampung rincian Barang Sisa (barang tidak laku) dari pelanggan yang dikembalikan saat pembayaran piutang. Subtotal retur ini langsung memotong saldo piutang.
```php
Schema::create('piutang_payment_barang_sisas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('piutang_payment_id')->constrained('piutang_payments')->onDelete('cascade');
    $table->foreignId('barang_id')->constrained('barangs'); // Barang Jadi yang diretur
    $table->integer('qty_retur'); // Jumlah kemasan (dalam Bal/Dus/Iket)
    $table->decimal('harga_jual', 10, 2); // Harga jual asli barang saat retur
    $table->decimal('subtotal_kredit', 12, 2); // Calculated: qty_retur * harga_jual
    $table->timestamps();
});
```

#### B. Pembelian & Retur Supplier

##### 1. Tabel `supplier_mutasis` (Buku Catatan Supplier Terpusat)
Mencatat stok masuk (menambah stok gudang), retur barang rusak ke supplier (mengurangi stok gudang), serta mutasi keuangan hutang kepada supplier bersangkutan.
```php
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
```

---

### ⚙️ 1.2 Logika Bisnis & Model (Fase 1)

#### A. Model `PiutangPayment` & `PiutangPaymentBarangSisa`
Kalkulasi pengurangan saldo piutang berdasarkan kombinasi pembayaran Tunai (Cash) + Retur Barang Sisa.
```php
// Di dalam model PiutangPayment
public function settlePayment()
{
    DB::transaction(function () {
        $totalTunai = $this->amount_paid;
        $totalReturBarangSisa = $this->barangSisaPayments()->sum('subtotal_kredit');
        
        $totalKreditPiutang = $totalTunai + $totalReturBarangSisa;
        
        // 1. Kurangi Saldo Piutang Pelanggan
        $this->piutang->decrement('remaining_balance', $totalKreditPiutang);
        
        // 2. Tambahkan Barang Sisa ke database stok untuk dilacak (masuk ke log retur)
        foreach ($this->barangSisaPayments as $retur) {
            $retur->barang->increment('stock_barang_sisa', $retur->qty_retur);
        }
    });
}
```

#### B. Model `SupplierMutasi` (Penyesuaian Otomatis Stok Gudang Utama)
```php
protected static function booted()
{
    static::created(function ($mutasi) {
        if ($mutasi->jenis_transaksi === 'barang_masuk') {
            $mutasi->barang->increment('stock', $mutasi->qty_bal);
        } elseif ($mutasi->jenis_transaksi === 'barang_retur') {
            $mutasi->barang->decrement('stock', $mutasi->qty_bal);
        }
    });
}
```

---

### 🖥️ 1.3 Perubahan UI & Modul Filament (Fase 1)

1.  **`InvoiceResource`:**
    *   Tambahkan kolom filter `tipe_penjualan` (Pasar, Agen, Eceran Gudang) di tabel utama Filament.
2.  **`PiutangPaymentResource` (Halaman Pembayaran):**
    *   Tambahkan `Repeater` untuk mencatat retur Barang Sisa (Nama Barang, Qty, Harga Jual). Subtotal dari repeater ini ditambahkan ke total potongan piutang secara dinamis (*Reactive*).
3.  **`SupplierMutasiResource`:**
    *   Halaman input terpusat (seperti format buku tulis fisik) untuk mencatat kiriman masuk supplier, retur keluar, dan mutasi keuangan hutang.

---

### 🔐 1.4 Hak Akses Pengguna (Spatie Permissions) (Fase 1)

1.  **Role: `admin`**
    *   Akses penuh ke seluruh sistem (Stok barang jadi, Keuangan, Supplier, Piutang).
2.  **Role: `sales`**
    *   Hanya memiliki izin akses ke `InvoiceResource` (terbatas pada pembuatan/view tipe `pasar` & `agen`) dan `CustomerResource`.
    *   Resource sensitif seperti `SupplierMutasiResource`, data stok modal, dan pembukuan piutang utama disembunyikan menggunakan Policy.

---
---

## 🏭 FASE 2: SISTEM PABRIK, BAHAN MENTAH, & GAJI BORONGAN (DELAYED)

Implementasi di bawah ini ditangguhkan sampai modul penjualan dan stok barang jadi (Fase 1) sudah stabil dan berjalan di lapangan.

### 💾 2.1 Skema Database & Migrasi (Fase 2)

#### A. Tabel `pekerjas` (Pekerja Borongan)
```php
Schema::create('pekerjas', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('tarif_kemasan_500', 8, 2)->default(0.00);
    $table->decimal('tarif_kemasan_1000', 8, 2)->default(0.00);
    $table->decimal('tarif_kemasan_2000', 8, 2)->default(0.00);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### B. Tabel `borongan_kerjas` (Catatan Harian Gaji Pekerja)
```php
Schema::create('borongan_kerjas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pekerja_id')->constrained('pekerjas');
    $table->date('tanggal');
    $table->integer('qty_500')->default(0);
    $table->integer('qty_1000')->default(0);
    $table->integer('qty_2000')->default(0);
    $table->decimal('uang_makan', 10, 2)->default(0.00);
    $table->decimal('ongkos', 10, 2)->default(0.00);
    $table->decimal('total_upah', 12, 2)->default(0.00);
    $table->timestamps();
});
```

#### C. Tabel `produksi_bahan_bakus` (Pemakaian Bahan Baku Mentah Aktual)
```php
Schema::create('produksi_bahan_bakus', function (Blueprint $table) {
    $table->id();
    $table->foreignId('produksi_id')->constrained('produksis')->onDelete('cascade');
    $table->foreignId('bahan_baku_id')->constrained('bahan_bakus'); // Kacang, Makaroni, Keripik, Jagung
    $table->decimal('qty_used', 10, 2); // Pengurangan stok bahan mentah
    $table->timestamps();
});
```

#### D. Tabel `koreksi_borongans` (Log Selisih Selama Rekonsiliasi)
```php
Schema::create('koreksi_borongans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('borongan_kerja_id')->constrained('borongan_kerjas')->onDelete('cascade');
    $table->string('pekerja_name_corrected');
    $table->string('nama_barang');
    $table->integer('qty_selisih_kurang');
    $table->decimal('potongan_upah', 10, 2);
    $table->timestamps();
});
```

#### E. Tabel `konversi_kantors` (Pengumpulan Sisa Eceran ke Bal)
```php
Schema::create('konversi_kantors', function (Blueprint $table) {
    $table->id();
    $table->foreignId('barang_id')->constrained('barangs');
    $table->date('tanggal');
    $table->integer('qty_pak_sisa');
    $table->integer('qty_bal_terbentuk');
    $table->timestamps();
});
```
