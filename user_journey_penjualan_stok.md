# Alur Perjalanan Pengguna (User Journey) - Sistem Penjualan & Stok Barang Jadi

Dokumen ini menjelaskan skenario harian dalam sistem distribusi **Sonia Paradise** menggunakan nama peran, nama produk nyata (*makanan ringan*), dan nama pelanggan riil agar mudah dipahami oleh seluruh tim operasional (non-IT).

---

## 👥 Tokoh & Peran dalam Perjalanan Ini
*   **Sonia (Admin Gudang & Keuangan):** Mengelola seluruh stok barang jadi, buku supplier, pembayaran, dan melayani pembeli eceran langsung di gudang.
*   **Angga (Sales Lapangan):** Keliling rute pasar dan agen untuk menjual produk dan menagih piutang lewat HP/tablet.
*   **Yadi (Supir Armada):** Mengemudikan mobil pengiriman dan membantu bongkar muat barang.
*   **Teh Eel (Pelanggan Pasar - Toko Jempol):** Pelanggan setia di rute pasar yang sering membeli grosir dan membayar piutang tempo.
*   **Pa Yusuf (Pelanggan Eceran):** Pembeli eceran yang langsung datang belanja ke area gudang.
*   **Supplier Linggarsari:** Pihak ketiga yang menyuplai produk jadi (misal: *Kacang Gajih 1000*).

---

## 🍿 Nama Produk Nyata yang Digunakan (Kemasan Eceran)
*   **Jagung Marning 500** (Kemasan eceran Rp 500)
*   **Kacang Koro 1000** (Kemasan eceran Rp 1.000)
*   **Kacang Gajih 1000** (Kemasan eceran Rp 1.000)
*   **Makaroni 2000** (Kemasan eceran Rp 2.000)

---

## 📅 Skenario Perjalanan Harian (Daily Journey)

### 🌅 1. Pagi Hari: Muat Barang & Persiapan Penjualan
*   **Langkah 1 (Persiapan Fisik):** Sales **Angga** bersama supir **Yadi** menyiapkan mobil box untuk rute pasar hari ini. Tim gudang memuat stok barang jadi ke dalam mobil box, contohnya:
    *   `Jagung Marning 500` sebanyak **72 Bal**
    *   `Kacang Koro 1000` sebanyak **30 Bal**
    *   `Kacang Gajih 1000` sebanyak **222 Bal**
*   **Langkah 2 (Pencatatan Sistem):** Admin **Sonia** membuka menu **"Muat Mobil Canvas"** di sistem, memilih nama Sales **Angga**, lalu memasukkan kuantiti muatan produk-produk di atas.
*   **Langkah 3 (Verifikasi Sales):** Sebelum berangkat, Sales **Angga** membuka aplikasi di HP-nya, mencocokkan jumlah fisik di mobil dengan data dari **Sonia**, lalu menekan tombol **"Setuju Muatan"**. Stok di mobil box kini resmi terisi di sistem.

---

### ☀️ 2. Siang Hari: Aktivitas Penjualan Pasar & Pembayaran Piutang
*   **Langkah 1 (Kunjungan Toko):** Mobil sales tiba di pasar. **Angga** mengunjungi Toko Jempol milik **Teh Eel**.
*   **Langkah 2 (Transaksi Penjualan):** **Teh Eel** memesan `Jagung Marning 500` sebanyak **40 Bal**. Sales **Angga** menginput pesanan tersebut di aplikasi HP-nya sebagai **Penjualan Tempo (Piutang)**. Invoice digital otomatis terbentuk dan stok barang di mobil box langsung berkurang 40 Bal.
*   **Langkah 3 (Pembayaran Piutang dengan Barang Sisa):** **Teh Eel** ingin melunasi nota tagihan minggu lalu sebesar **Rp 2.000.000,-**. Cara bayarnya digabung:
    1.  **Uang Tunai (Cash):** Sebesar **Rp 1.592.000,-** diserahkan ke Angga.
    2.  **Barang Sisa (Retur Produk):** **Teh Eel** mengembalikan `Kacang Koro 1000` sebanyak **6 Bal** (barang sisa minggu lalu yang tidak laku).
*   **Langkah 4 (Input Pembayaran):** Sales **Angga** membuka menu **"Terima Bayar Piutang"** untuk Toko **Teh Eel**:
    *   Menginput Kas/Tunai: **Rp 1.592.000,-**.
    *   Menginput detail retur Barang Sisa: `Kacang Koro 1000` sebanyak **6 Bal** (sistem otomatis menghitung nilai kredit retur = $6 \text{ Bal} \times \text{Harga Jual asli } \text{Rp } 68.000 = \text{Rp } 408.000$).
    *   Sistem otomatis menjumlahkan: $\text{Rp } 1.592.000 + \text{Rp } 408.000 = \text{Rp } 2.000.000$. Tagihan **Teh Eel** dinyatakan **Lunas**, dan struk digital dikirim ke WhatsApp Teh Eel.

---

### 🏪 3. Transaksi Langsung di Gudang (Eceran Gudang)
*   **Langkah 1 (Pelanggan Datang):** Pembeli eceran **Pa Yusuf** datang langsung ke kantor gudang untuk berbelanja secara dadakan.
*   **Langkah 2 (Pelayanan & Kasir):** Admin **Sonia** melayani langsung pembelian **Pa Yusuf** yang membeli `Makaroni 2000` sebanyak **2 Bal**.
*   **Langkah 3 (Input Penjualan Gudang):** **Sonia** membuka menu **"Kasir Gudang"** di komputer, menginput penjualan produk `Makaroni 2000` sebanyak **2 Bal** dengan tipe penjualan **"Eceran Gudang"**, lalu menerima uang tunai langsung di tempat. Stok `Makaroni 2000` di Gudang Utama langsung terpotong otomatis di sistem.

---

### 🌆 4. Sore Hari: Penyetoran Sisa Barang & Rekonsiliasi Kas
*   **Langkah 1 (Bongkar Sisa Mobil):** Sales **Angga** dan supir **Yadi** kembali ke gudang. Bagian gudang membongkar sisa fisik barang di mobil box dan menghitungnya. Sisa barang dicocokkan dengan hitungan sistem:
    $$\text{Muatan Pagi} - \text{Total Terjual} = \text{Sisa Fisik}$$
*   **Langkah 2 (Pencatatan Retur Barang Sisa Toko):** Bagian gudang memisahkan **6 Bal** `Kacang Koro 1000` (Barang Sisa retur dari Teh Eel) dan menyerahkan data fisiknya ke **Sonia**.
*   **Langkah 3 (Settlement):** Admin **Sonia** mengonfirmasi retur sisa mobil di sistem, lalu mencocokkan uang setoran fisik dari Sales **Angga** (sejumlah Rp 1.592.000,- hasil bayar piutang Teh Eel + hasil penjualan tunai lainnya). Setelah semua klop, sesi penjualan hari itu ditutup (*closed*). Barang Sisa retur (6 Bal) dipindahkan kembali ke gudang atas/pabrik bawah untuk diolah ulang.

---

### 🚚 5. Alur Masuk Barang dari Supplier
*   **Langkah 1 (Kedatangan Barang):** Armada pengiriman dari **Supplier Linggarsari** datang membawa kiriman produk `Kacang Gajih 1000` sebanyak **300 Bal**.
*   **Langkah 2 (Pengecekan Fisik):** Bagian gudang mengecek kondisi barang dan menghitung jumlahnya. Setelah cocok, surat jalan ditandatangani.
*   **Langkah 3 (Pencatatan Buku Terpusat):** Admin **Sonia** membuka menu **"Buku Supplier"** di sistem, memilih supplier **Linggarsari**, lalu mencatat transaksi **"Barang Masuk"** untuk produk `Kacang Gajih 1000` sebanyak **300 Bal**.
*   **Langkah 4 (Update Otomatis):** Stok produk `Kacang Gajih 1000` di Gudang Utama otomatis bertambah 300 Bal, dan sistem otomatis menambahkan saldo hutang/kewajiban bayar kita kepada Supplier **Linggarsari** untuk diverifikasi saat jatuh tempo pembayaran.
