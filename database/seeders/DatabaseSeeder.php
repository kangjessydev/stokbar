<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\Barang;
use App\Models\CanvasMuatan;
use App\Models\CanvasRetur;
use App\Models\Customer;
use App\Models\Gudang;
use App\Models\Hutang;
use App\Models\Invoice;
use App\Models\Produksi;
use App\Models\ResepBom;
use App\Models\SalesCar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $gudangRole = Role::firstOrCreate(['name' => 'gudang']);
        $salesRole = Role::firstOrCreate(['name' => 'sales']);

        // 2. Setup Users
        $owner = User::firstOrCreate(
            ['email' => 'owner@stokbar.com'],
            ['name' => 'Budi Owner', 'password' => Hash::make('password')]
        );
        $owner->assignRole($ownerRole);

        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang@stokbar.com'],
            ['name' => 'Asep Gudang', 'password' => Hash::make('password')]
        );
        $gudangUser->assignRole($gudangRole);

        $salesUser = User::firstOrCreate(
            ['email' => 'sales@stokbar.com'],
            ['name' => 'Kang Ujang (Sales)', 'password' => Hash::make('password')]
        );
        $salesUser->assignRole($salesRole);

        // Login as owner so all the audit trails record this as Owner's manual input initially
        Auth::login($owner);

        // 3. Setup Gudang
        $gudangBahan = Gudang::create([
            'name' => 'Gudang Bahan Baku (Pusat)',
            'type' => 'bahan_baku',
        ]);

        $gudangJadi = Gudang::create([
            'name' => 'Gudang Barang Jadi (Pusat)',
            'type' => 'barang_jadi',
        ]);

        // 4. Setup Bahan Baku
        $makaroniMentah = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Makaroni Mentah', 'stock' => 500, 'unit' => 'kg', 'safety_stock' => 50]);
        $baksoIkan = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Bakso Ikan Mentah', 'stock' => 300, 'unit' => 'kg', 'safety_stock' => 30]);
        $minyak = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Minyak Goreng Jerigen', 'stock' => 200, 'unit' => 'liter', 'safety_stock' => 20]);
        $bumbuBalado = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Bumbu Balado', 'stock' => 50, 'unit' => 'kg', 'safety_stock' => 10]);
        $bumbuDaunJeruk = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Bumbu Daun Jeruk', 'stock' => 50, 'unit' => 'kg', 'safety_stock' => 10]);
        $kemasan = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Plastik Kemasan Pouch', 'stock' => 5000, 'unit' => 'pcs', 'safety_stock' => 500]);

        // 5. Setup Hutang Pembelian Bahan Baku
        $hutang = Hutang::create([
            'supplier_name' => 'Distributor Sembako H. Asep',
            'amount' => 2500000.00,
            'paid_amount' => 0.00,
            'status' => 'belum_lunas',
            'tanggal' => now()->subDays(5)->toDateString(),
            'due_date' => now()->addDays(25)->toDateString(),
        ]);
        
        // Simulasi Owner mencicil hutang bahan baku
        $hutang->hutangPayments()->create([
            'amount_paid' => 1000000.00,
            'payment_date' => now()->subDays(1)->toDateString(),
            'payment_method' => 'transfer',
            'reference_no' => 'TRX-BCA-88192',
        ]);
        // Trigger save untuk update parent jika perlu (Model event sudah menangani paid_amount)

        // 6. Setup Barang Jadi (Awalnya kosong, akan diisi oleh produksi)
        $makaroniBalado = Barang::create(['gudang_id' => $gudangJadi->id, 'name' => 'Makaroni Bantet Balado 100g', 'stock' => 0, 'unit' => 'pcs', 'price' => 5000.00]);
        $basrengJeruk = Barang::create(['gudang_id' => $gudangJadi->id, 'name' => 'Basreng Daun Jeruk 100g', 'stock' => 0, 'unit' => 'pcs', 'price' => 8000.00]);

        // 7. Setup Resep BOM
        // Makaroni
        ResepBom::create(['barang_id' => $makaroniBalado->id, 'bahan_baku_id' => $makaroniMentah->id, 'qty_needed' => 0.10]);
        ResepBom::create(['barang_id' => $makaroniBalado->id, 'bahan_baku_id' => $minyak->id, 'qty_needed' => 0.05]);
        ResepBom::create(['barang_id' => $makaroniBalado->id, 'bahan_baku_id' => $bumbuBalado->id, 'qty_needed' => 0.01]);
        ResepBom::create(['barang_id' => $makaroniBalado->id, 'bahan_baku_id' => $kemasan->id, 'qty_needed' => 1]);

        // Basreng
        ResepBom::create(['barang_id' => $basrengJeruk->id, 'bahan_baku_id' => $baksoIkan->id, 'qty_needed' => 0.10]);
        ResepBom::create(['barang_id' => $basrengJeruk->id, 'bahan_baku_id' => $minyak->id, 'qty_needed' => 0.05]);
        ResepBom::create(['barang_id' => $basrengJeruk->id, 'bahan_baku_id' => $bumbuDaunJeruk->id, 'qty_needed' => 0.01]);
        ResepBom::create(['barang_id' => $basrengJeruk->id, 'bahan_baku_id' => $kemasan->id, 'qty_needed' => 1]);

        // 8. Skenario Produksi Pabrik
        Auth::login($gudangUser); // Asep Gudang mencatat produksi
        $produksi = Produksi::create([
            'kode_produksi' => 'PRD-' . date('Ymd') . '-01',
            'tanggal' => now()->toDateString(),
            'status' => 'draft',
        ]);
        
        $produksi->produksiItems()->create(['barang_id' => $makaroniBalado->id, 'qty_produced' => 500]); // Produksi 500 pcs makaroni
        $produksi->produksiItems()->create(['barang_id' => $basrengJeruk->id, 'qty_produced' => 300]); // Produksi 300 pcs basreng
        
        // Selesaikan produksi (event hook akan otomatis memotong bahan baku & menambah stok barang jadi)
        $produksi->update(['status' => 'selesai']);

        // 9. Setup Customer
        $tokoAbah = Customer::create(['name' => 'Toko Abah (Grosir)', 'market' => 'Pasar Induk', 'phone' => '081222333444', 'credit_limit' => 5000000, 'credit_period' => 14]);
        $warungEuis = Customer::create(['name' => 'Warung Teh Euis', 'market' => 'Perumahan Asri', 'phone' => '08555666777', 'credit_limit' => 1000000, 'credit_period' => 7]);

        // 10. Setup Mobil Sales & Canvaser
        Auth::login($salesUser); // Sales beraktivitas
        $mobil = SalesCar::create(['name' => 'Grandmax Box D-1234-AB', 'driver_name' => 'Kang Ujang', 'user_id' => $salesUser->id]);

        // 11. Skenario Operasional Canvas (Muatan)
        Auth::login($gudangUser); // Gudang approve muatan
        $muatanMakaroni = CanvasMuatan::create(['sales_car_id' => $mobil->id, 'barang_id' => $makaroniBalado->id, 'qty_loaded' => 200, 'tanggal' => now()->toDateString(), 'status' => 'draft']);
        $muatanBasreng = CanvasMuatan::create(['sales_car_id' => $mobil->id, 'barang_id' => $basrengJeruk->id, 'qty_loaded' => 100, 'tanggal' => now()->toDateString(), 'status' => 'draft']);
        
        // Konfirmasi muatan (event hook akan memotong stok gudang utama berpindah ke mobil)
        $muatanMakaroni->update(['status' => 'confirmed']);
        $muatanBasreng->update(['status' => 'confirmed']);

        // 12. Transaksi Penjualan oleh Sales
        Auth::login($salesUser);
        
        // Transaksi 1: Jual Kredit ke Toko Abah (100 Makaroni, 50 Basreng)
        $invoiceKredit = Invoice::create([
            'no_invoice' => 'INV-KRD-001',
            'customer_id' => $tokoAbah->id,
            'sales_car_id' => $mobil->id,
            'total_price' => (100 * 5000) + (50 * 8000), // 500k + 400k = 900k
            'payment_status' => 'kredit',
            'tanggal' => now()->toDateString(),
        ]);
        $invoiceKredit->invoiceItems()->create(['barang_id' => $makaroniBalado->id, 'qty' => 100, 'price' => 5000, 'subtotal' => 500000]);
        $invoiceKredit->invoiceItems()->create(['barang_id' => $basrengJeruk->id, 'qty' => 50, 'price' => 8000, 'subtotal' => 400000]);
        // Event hook pada Invoice akan OTOMATIS membuat data Piutang untuk Toko Abah

        // Transaksi 2: Jual Tunai ke Warung Teh Euis (20 Makaroni, 10 Basreng)
        $invoiceTunai = Invoice::create([
            'no_invoice' => 'INV-TUN-002',
            'customer_id' => $warungEuis->id,
            'sales_car_id' => $mobil->id,
            'total_price' => (20 * 5000) + (10 * 8000), // 100k + 80k = 180k
            'payment_status' => 'lunas',
            'tanggal' => now()->toDateString(),
        ]);
        $invoiceTunai->invoiceItems()->create(['barang_id' => $makaroniBalado->id, 'qty' => 20, 'price' => 5000, 'subtotal' => 100000]);
        $invoiceTunai->invoiceItems()->create(['barang_id' => $basrengJeruk->id, 'qty' => 10, 'price' => 8000, 'subtotal' => 80000]);

        // 13. Skenario Cicilan Piutang dari Toko Abah
        $piutangTokoAbah = $invoiceKredit->piutang;
        $piutangTokoAbah->piutangPayments()->create([
            'amount_paid' => 400000.00, // Cicil 400rb dari 900rb
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ]);
        // Event hook pada PiutangPayment akan otomatis mengupdate paid_amount di tabel Piutang

        // 14. Skenario Retur Barang Sisa di Sore Hari
        Auth::login($gudangUser);
        // Sisa Makaroni = 200 bawa - 100 jual - 20 jual = 80 sisa
        CanvasRetur::create(['sales_car_id' => $mobil->id, 'barang_id' => $makaroniBalado->id, 'qty_returned' => 80, 'tanggal' => now()->toDateString()]);
        // Sisa Basreng = 100 bawa - 50 jual - 10 jual = 40 sisa
        CanvasRetur::create(['sales_car_id' => $mobil->id, 'barang_id' => $basrengJeruk->id, 'qty_returned' => 40, 'tanggal' => now()->toDateString()]);
        // Event hook CanvasRetur akan otomatis mengembalikan stok sisa ke Gudang Pusat
    }
}
