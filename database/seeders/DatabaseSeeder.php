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
use App\Models\Piutang;
use App\Models\Produksi;
use App\Models\ResepBom;
use App\Models\SalesCar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $ownerRole  = Role::firstOrCreate(['name' => 'owner']);
        $gudangRole = Role::firstOrCreate(['name' => 'gudang']);
        $salesRole  = Role::firstOrCreate(['name' => 'sales']);

        // 2. Users
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

        $salesUser2 = User::firstOrCreate(
            ['email' => 'sales2@stokbar.com'],
            ['name' => 'Deni Supriadi (Sales)', 'password' => Hash::make('password')]
        );
        $salesUser2->assignRole($salesRole);

        Auth::login($owner);

        // 3. Gudang
        $gudangBahan = Gudang::create(['name' => 'Gudang Bahan Baku (Pusat)', 'type' => 'bahan_baku']);
        $gudangJadi  = Gudang::create(['name' => 'Gudang Barang Jadi (Pusat)', 'type' => 'barang_jadi']);

        // 4. Bahan Baku (8 item — 2 terakhir sengaja kritis untuk demo alert)
        $makaroniMentah = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Makaroni Mentah',       'stock' => 500,  'unit' => 'kg',    'safety_stock' => 50]);
        $baksoIkan      = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Bakso Ikan Mentah',     'stock' => 300,  'unit' => 'kg',    'safety_stock' => 30]);
        $minyak         = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Minyak Goreng Jerigen', 'stock' => 200,  'unit' => 'liter', 'safety_stock' => 20]);
        $bumbuBalado    = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Bumbu Balado',          'stock' => 50,   'unit' => 'kg',    'safety_stock' => 10]);
        $bumbuDaunJeruk = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Bumbu Daun Jeruk',      'stock' => 50,   'unit' => 'kg',    'safety_stock' => 10]);
        $kemasan        = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Plastik Kemasan Pouch', 'stock' => 5000, 'unit' => 'pcs',   'safety_stock' => 500]);
        // 2 bahan kritis (stock < safety_stock) untuk demo widget "Bahan Kritis"
        $tepungTapioka  = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Tepung Tapioka',        'stock' => 8,    'unit' => 'kg',    'safety_stock' => 25]);
        $singkong       = BahanBaku::create(['gudang_id' => $gudangBahan->id, 'name' => 'Singkong Mentah',       'stock' => 12,   'unit' => 'kg',    'safety_stock' => 30]);

        // 5. Hutang ke 3 Supplier
        $hutang1 = Hutang::create([
            'supplier_name' => 'Distributor Sembako H. Asep',
            'amount'        => 2500000.00,
            'paid_amount'   => 1000000.00,
            'status'        => 'belum_lunas',
            'tanggal'       => now()->subDays(5)->toDateString(),
            'due_date'      => now()->addDays(25)->toDateString(),
        ]);
        $hutang1->hutangPayments()->create([
            'amount_paid'    => 1000000.00,
            'payment_date'   => now()->subDays(1)->toDateString(),
            'payment_method' => 'transfer',
            'reference_no'   => 'TRX-BCA-88192',
        ]);

        $hutang2 = Hutang::create([
            'supplier_name' => 'CV. Aneka Bumbu Nusantara',
            'amount'        => 1800000.00,
            'paid_amount'   => 1800000.00,
            'status'        => 'lunas',
            'tanggal'       => now()->subDays(20)->toDateString(),
            'due_date'      => now()->subDays(5)->toDateString(),
        ]);
        $hutang2->hutangPayments()->create([
            'amount_paid'    => 1800000.00,
            'payment_date'   => now()->subDays(7)->toDateString(),
            'payment_method' => 'transfer',
            'reference_no'   => 'TRX-MANDIRI-44501',
        ]);

        Hutang::create([
            'supplier_name' => 'UD. Plastik Kemasan Prima',
            'amount'        => 750000.00,
            'paid_amount'   => 0.00,
            'status'        => 'belum_lunas',
            'tanggal'       => now()->subDays(3)->toDateString(),
            'due_date'      => now()->addDays(12)->toDateString(),
        ]);

        // 6. Barang Jadi (4 produk)
        $makaroniBalado  = Barang::create(['gudang_id' => $gudangJadi->id, 'name' => 'Makaroni Bantet Balado 100g', 'stock' => 0, 'unit' => 'pcs', 'price' => 5000.00]);
        $basrengJeruk    = Barang::create(['gudang_id' => $gudangJadi->id, 'name' => 'Basreng Daun Jeruk 100g',     'stock' => 0, 'unit' => 'pcs', 'price' => 8000.00]);
        $cirengAyam      = Barang::create(['gudang_id' => $gudangJadi->id, 'name' => 'Cireng Isi Ayam 80g',         'stock' => 0, 'unit' => 'pcs', 'price' => 4000.00]);
        $keripikSingkong = Barang::create(['gudang_id' => $gudangJadi->id, 'name' => 'Keripik Singkong Pedas 150g', 'stock' => 0, 'unit' => 'pcs', 'price' => 6000.00]);

        // 7. Resep BOM
        ResepBom::create(['barang_id' => $makaroniBalado->id,  'bahan_baku_id' => $makaroniMentah->id, 'qty_needed' => 0.10]);
        ResepBom::create(['barang_id' => $makaroniBalado->id,  'bahan_baku_id' => $minyak->id,         'qty_needed' => 0.05]);
        ResepBom::create(['barang_id' => $makaroniBalado->id,  'bahan_baku_id' => $bumbuBalado->id,    'qty_needed' => 0.01]);
        ResepBom::create(['barang_id' => $makaroniBalado->id,  'bahan_baku_id' => $kemasan->id,        'qty_needed' => 1]);
        ResepBom::create(['barang_id' => $basrengJeruk->id,    'bahan_baku_id' => $baksoIkan->id,      'qty_needed' => 0.10]);
        ResepBom::create(['barang_id' => $basrengJeruk->id,    'bahan_baku_id' => $minyak->id,         'qty_needed' => 0.05]);
        ResepBom::create(['barang_id' => $basrengJeruk->id,    'bahan_baku_id' => $bumbuDaunJeruk->id, 'qty_needed' => 0.01]);
        ResepBom::create(['barang_id' => $basrengJeruk->id,    'bahan_baku_id' => $kemasan->id,        'qty_needed' => 1]);
        ResepBom::create(['barang_id' => $cirengAyam->id,      'bahan_baku_id' => $tepungTapioka->id,  'qty_needed' => 0.08]);
        ResepBom::create(['barang_id' => $cirengAyam->id,      'bahan_baku_id' => $minyak->id,         'qty_needed' => 0.04]);
        ResepBom::create(['barang_id' => $cirengAyam->id,      'bahan_baku_id' => $kemasan->id,        'qty_needed' => 1]);
        ResepBom::create(['barang_id' => $keripikSingkong->id, 'bahan_baku_id' => $singkong->id,       'qty_needed' => 0.15]);
        ResepBom::create(['barang_id' => $keripikSingkong->id, 'bahan_baku_id' => $minyak->id,         'qty_needed' => 0.06]);
        ResepBom::create(['barang_id' => $keripikSingkong->id, 'bahan_baku_id' => $kemasan->id,        'qty_needed' => 1]);

        // 8. Produksi — isi stok besar di awal (tanpa event tarik bahan supaya stok tidak minus)
        Auth::login($gudangUser);

        // Produksi awal bulan — stok masif untuk bahan historis
        $produksi1 = Produksi::create([
            'kode_produksi' => 'PRD-' . now()->subDays(30)->format('Ymd') . '-01',
            'tanggal'       => now()->subDays(30)->toDateString(),
            'status'        => 'draft',
        ]);
        $produksi1->produksiItems()->create(['barang_id' => $makaroniBalado->id,  'qty_produced' => 5000]);
        $produksi1->produksiItems()->create(['barang_id' => $basrengJeruk->id,    'qty_produced' => 3000]);
        $produksi1->produksiItems()->create(['barang_id' => $cirengAyam->id,      'qty_produced' => 3000]);
        $produksi1->produksiItems()->create(['barang_id' => $keripikSingkong->id, 'qty_produced' => 2000]);
        $produksi1->update(['status' => 'selesai']);

        // Produksi pertengahan bulan
        $produksi2 = Produksi::create([
            'kode_produksi' => 'PRD-' . now()->subDays(15)->format('Ymd') . '-01',
            'tanggal'       => now()->subDays(15)->toDateString(),
            'status'        => 'draft',
        ]);
        $produksi2->produksiItems()->create(['barang_id' => $makaroniBalado->id, 'qty_produced' => 2000]);
        $produksi2->produksiItems()->create(['barang_id' => $basrengJeruk->id,   'qty_produced' => 1500]);
        $produksi2->update(['status' => 'selesai']);

        // Produksi hari ini
        $produksi3 = Produksi::create([
            'kode_produksi' => 'PRD-' . now()->format('Ymd') . '-01',
            'tanggal'       => now()->toDateString(),
            'status'        => 'draft',
        ]);
        $produksi3->produksiItems()->create(['barang_id' => $makaroniBalado->id, 'qty_produced' => 500]);
        $produksi3->produksiItems()->create(['barang_id' => $basrengJeruk->id,   'qty_produced' => 300]);
        $produksi3->update(['status' => 'selesai']);

        // 9. Customer (5 toko)
        $tokoAbah    = Customer::create(['name' => 'Toko Abah (Grosir)',    'market' => 'Pasar Induk Caringin',  'phone' => '081222333444', 'credit_limit' => 5000000, 'credit_period' => 14]);
        $warungEuis  = Customer::create(['name' => 'Warung Teh Euis',       'market' => 'Perumahan Asri',        'phone' => '08555666777',  'credit_limit' => 1000000, 'credit_period' => 7]);
        $depotHaji   = Customer::create(['name' => 'Depot Pak Haji Maman',  'market' => 'Pasar Lembang',         'phone' => '082111222333', 'credit_limit' => 8000000, 'credit_period' => 21]);
        $miniSinar   = Customer::create(['name' => 'Minimarket Sinar Jaya', 'market' => 'Perumahan Bukit Asri',  'phone' => '085333444555', 'credit_limit' => 3000000, 'credit_period' => 30]);
        $warungDedeh = Customer::create(['name' => 'Warung Bu Dedeh',       'market' => 'Kampung Cikaret',       'phone' => '081666777888', 'credit_limit' => 500000,  'credit_period' => 7]);

        // 10. Mobil Sales (2 armada)
        Auth::login($salesUser);
        $mobil1 = SalesCar::create(['name' => 'Grandmax Box D-1234-AB', 'driver_name' => 'Kang Ujang',  'user_id' => $salesUser->id]);
        $mobil2 = SalesCar::create(['name' => 'L300 Box D-5678-CD',    'driver_name' => 'Deni Supriadi','user_id' => $salesUser2->id]);

        // =====================================================================
        // 11. DATA HISTORIS 30 HARI
        // Dibuat tanpa events agar due_date piutang bisa dikontrol per tanggal
        // =====================================================================
        $allCustomers = [$tokoAbah, $warungEuis, $depotHaji, $miniSinar, $warungDedeh];
        $allMobils    = [$mobil1, $mobil2];
        $allProducts  = [
            ['model' => $makaroniBalado,  'price' => 5000],
            ['model' => $basrengJeruk,    'price' => 8000],
            ['model' => $cirengAyam,      'price' => 4000],
            ['model' => $keripikSingkong, 'price' => 6000],
        ];
        // Customer besar yang boleh kredit
        $kreditCustomerIds = [$tokoAbah->id, $depotHaji->id, $miniSinar->id];

        $counter = 1;

        for ($daysAgo = 29; $daysAgo >= 1; $daysAgo--) {
            $tanggal = Carbon::now()->subDays($daysAgo)->toDateString();

            // Skip Minggu (libur)
            if (Carbon::parse($tanggal)->isSunday()) {
                continue;
            }

            $invoicesPerDay = rand(2, 4);

            for ($j = 0; $j < $invoicesPerDay; $j++) {
                $customer = $allCustomers[array_rand($allCustomers)];
                $mobil    = $allMobils[array_rand($allMobils)];
                $isKredit = in_array($customer->id, $kreditCustomerIds) && rand(0, 1) === 1;

                // Pilih 1–2 produk
                shuffle($allProducts);
                $picked    = array_slice($allProducts, 0, rand(1, 2));
                $total     = 0;
                $itemsData = [];
                foreach ($picked as $p) {
                    $qty      = rand(15, 100);
                    $subtotal = $qty * $p['price'];
                    $total   += $subtotal;
                    $itemsData[] = ['model' => $p['model'], 'qty' => $qty, 'price' => $p['price'], 'subtotal' => $subtotal];
                }

                $noInv = 'INV-' . Carbon::parse($tanggal)->format('ymd') . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $counter++;

                // Buat invoice TANPA event agar piutang kita kontrol manual
                $invoice = Invoice::withoutEvents(function () use ($noInv, $customer, $mobil, $total, $isKredit, $tanggal) {
                    return Invoice::create([
                        'no_invoice'     => $noInv,
                        'customer_id'    => $customer->id,
                        'sales_car_id'   => $mobil->id,
                        'total_price'    => $total,
                        'payment_status' => $isKredit ? 'kredit' : 'lunas',
                        'tanggal'        => $tanggal,
                    ]);
                });

                foreach ($itemsData as $item) {
                    $invoice->invoiceItems()->create([
                        'barang_id' => $item['model']->id,
                        'qty'       => $item['qty'],
                        'price'     => $item['price'],
                        'subtotal'  => $item['subtotal'],
                    ]);
                }

                // Piutang manual (hanya untuk kredit)
                if ($isKredit) {
                    $period  = $customer->credit_period ?? 30;
                    $dueDate = Carbon::parse($tanggal)->addDays($period)->toDateString();
                    $isOverdue = Carbon::parse($dueDate)->lt(now());

                    // Tentukan kondisi piutang:
                    // - Sudah lama & tidak overdue → lunas
                    // - Overdue → belum bayar / bayar sebagian (untuk demo alert merah)
                    // - Belum jatuh tempo → belum bayar
                    if (!$isOverdue && $daysAgo > 20) {
                        // Invoice lama yang sudah lunas
                        $paidAmount = $total;
                        $status     = 'lunas';
                    } elseif ($isOverdue) {
                        // Sengaja sebagian overdue tanpa bayar → alert merah
                        $paidAmount = rand(0, 1) === 1 ? round($total * 0.4) : 0;
                        $status     = 'belum_lunas';
                    } else {
                        $paidAmount = 0;
                        $status     = 'belum_lunas';
                    }

                    $piutang = Piutang::create([
                        'invoice_id' => $invoice->id,
                        'amount'     => $total,
                        'paid_amount'=> $paidAmount,
                        'status'     => $status,
                        'due_date'   => $dueDate,
                    ]);

                    if ($paidAmount > 0 && $paidAmount < $total) {
                        $piutang->piutangPayments()->create([
                            'amount_paid'    => $paidAmount,
                            'payment_date'   => Carbon::parse($tanggal)->addDays(rand(1, max(1, (int) ($period * 0.7))))->toDateString(),
                            'payment_method' => rand(0, 1) === 1 ? 'cash' : 'transfer',
                            'reference_no'   => rand(0, 1) === 1 ? 'TRX-' . strtoupper(substr(md5((string) rand()), 0, 8)) : null,
                        ]);
                    } elseif ($paidAmount === $total) {
                        $piutang->piutangPayments()->create([
                            'amount_paid'    => $paidAmount,
                            'payment_date'   => Carbon::parse($tanggal)->addDays(rand(1, $period))->toDateString(),
                            'payment_method' => rand(0, 1) === 1 ? 'cash' : 'transfer',
                        ]);
                    }
                }
            }
        }

        // =====================================================================
        // 12. Skenario Hari Ini — Muatan + Penjualan + Retur
        // =====================================================================
        Auth::login($gudangUser);
        $muatanMakaroni = CanvasMuatan::create(['sales_car_id' => $mobil1->id, 'barang_id' => $makaroniBalado->id,  'qty_loaded' => 200, 'tanggal' => now()->toDateString(), 'status' => 'draft']);
        $muatanBasreng  = CanvasMuatan::create(['sales_car_id' => $mobil1->id, 'barang_id' => $basrengJeruk->id,    'qty_loaded' => 100, 'tanggal' => now()->toDateString(), 'status' => 'draft']);
        $muatanCireng   = CanvasMuatan::create(['sales_car_id' => $mobil2->id, 'barang_id' => $cirengAyam->id,      'qty_loaded' => 150, 'tanggal' => now()->toDateString(), 'status' => 'draft']);
        $muatanKeripik  = CanvasMuatan::create(['sales_car_id' => $mobil2->id, 'barang_id' => $keripikSingkong->id, 'qty_loaded' => 100, 'tanggal' => now()->toDateString(), 'status' => 'draft']);

        $muatanMakaroni->update(['status' => 'confirmed']);
        $muatanBasreng->update(['status' => 'confirmed']);
        $muatanCireng->update(['status' => 'confirmed']);
        $muatanKeripik->update(['status' => 'confirmed']);

        Auth::login($salesUser);

        // Invoice kredit hari ini ke Toko Abah (via event normal → piutang auto dibuat)
        $invKredit = Invoice::create([
            'no_invoice'     => 'INV-' . now()->format('ymd') . '-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
            'customer_id'    => $tokoAbah->id,
            'sales_car_id'   => $mobil1->id,
            'total_price'    => (100 * 5000) + (50 * 8000),
            'payment_status' => 'kredit',
            'tanggal'        => now()->toDateString(),
        ]);
        $invKredit->invoiceItems()->create(['barang_id' => $makaroniBalado->id, 'qty' => 100, 'price' => 5000, 'subtotal' => 500000]);
        $invKredit->invoiceItems()->create(['barang_id' => $basrengJeruk->id,   'qty' => 50,  'price' => 8000, 'subtotal' => 400000]);

        // Invoice tunai hari ini ke Warung Euis
        $invTunai = Invoice::create([
            'no_invoice'     => 'INV-' . now()->format('ymd') . '-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
            'customer_id'    => $warungEuis->id,
            'sales_car_id'   => $mobil1->id,
            'total_price'    => (20 * 5000) + (10 * 8000),
            'payment_status' => 'lunas',
            'tanggal'        => now()->toDateString(),
        ]);
        $invTunai->invoiceItems()->create(['barang_id' => $makaroniBalado->id, 'qty' => 20, 'price' => 5000, 'subtotal' => 100000]);
        $invTunai->invoiceItems()->create(['barang_id' => $basrengJeruk->id,   'qty' => 10, 'price' => 8000, 'subtotal' => 80000]);

        // Cicil piutang hari ini dari Toko Abah
        $piutangHariIni = $invKredit->fresh()->piutang;
        if ($piutangHariIni) {
            $piutangHariIni->piutangPayments()->create([
                'amount_paid'    => 400000.00,
                'payment_date'   => now()->toDateString(),
                'payment_method' => 'cash',
            ]);
        }

        // Retur sore hari
        Auth::login($gudangUser);
        CanvasRetur::create(['sales_car_id' => $mobil1->id, 'barang_id' => $makaroniBalado->id, 'qty_returned' => 80, 'tanggal' => now()->toDateString()]);
        CanvasRetur::create(['sales_car_id' => $mobil1->id, 'barang_id' => $basrengJeruk->id,   'qty_returned' => 40, 'tanggal' => now()->toDateString()]);
        CanvasRetur::create(['sales_car_id' => $mobil2->id, 'barang_id' => $cirengAyam->id,      'qty_returned' => 60, 'tanggal' => now()->toDateString()]);
    }
}
