<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gudang;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\SalesCar;
use App\Models\SupplierMutasi;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Piutang;
use App\Models\PiutangPayment;
use App\Models\PiutangPaymentBarangSisa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Users
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $salesRole = Role::firstOrCreate(['name' => 'sales']);

        $admin = User::firstOrCreate(['email' => 'admin@stokbar.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        $sales = User::firstOrCreate(['email' => 'sales@stokbar.com'], [
            'name' => 'Angga Sales',
            'password' => Hash::make('password'),
        ]);
        $sales->assignRole($salesRole);

        // 2. Master Data
        $gudangBawah = Gudang::firstOrCreate(['name' => 'Gudang Bawah (Bahan Baku)'], ['type' => 'bahan_baku']);
        $gudangAtas = Gudang::firstOrCreate(['name' => 'Gudang Atas (Barang Jadi)'], ['type' => 'barang_jadi']);

        // Seed Bahan Baku
        $bahanBakus = [
            \App\Models\BahanBaku::firstOrCreate(['name' => 'Kacang Koro Mentah'], ['gudang_id' => $gudangBawah->id, 'stock' => 10000, 'unit' => 'kg', 'safety_stock' => 1000]),
            \App\Models\BahanBaku::firstOrCreate(['name' => 'Minyak Goreng'], ['gudang_id' => $gudangBawah->id, 'stock' => 5000, 'unit' => 'liter', 'safety_stock' => 500]),
            \App\Models\BahanBaku::firstOrCreate(['name' => 'Tepung Terigu'], ['gudang_id' => $gudangBawah->id, 'stock' => 8000, 'unit' => 'kg', 'safety_stock' => 800]),
            \App\Models\BahanBaku::firstOrCreate(['name' => 'Bumbu Penyedap'], ['gudang_id' => $gudangBawah->id, 'stock' => 2000, 'unit' => 'kg', 'safety_stock' => 200]),
        ];

        $suppliers = [];
        for ($i = 1; $i <= 5; $i++) {
            $suppliers[] = Supplier::firstOrCreate(
                ['name' => "Supplier " . fake()->company()],
                ['phone' => fake()->phoneNumber(), 'address' => fake()->address()]
            );
        }

        $barangs = [];
        $barangNames = [
            'Kacang Koro 1000', 'Jagung Marning 500', 'Keripik Pisang', 
            'Sus Keju', 'Kacang Atom', 'Kacang Polong', 'Kuping Gajah'
        ];
        
        foreach ($barangNames as $name) {
            $barangs[] = Barang::firstOrCreate(
                ['name' => $name],
                [
                    'gudang_id' => $gudangAtas->id,
                    'stock' => fake()->numberBetween(100, 1000), // Random starting stock
                    'unit' => 'bal',
                    'price' => fake()->numberBetween(15, 30) * 1000
                ]
            );
        }

        // Seed Resep BOM
        foreach ($barangs as $barang) {
            // Kakang Koro 1000 needs Kacang Koro Mentah and Minyak Goreng
            if (str_contains($barang->name, 'Kacang Koro')) {
                \App\Models\ResepBom::firstOrCreate([
                    'barang_id' => $barang->id,
                    'bahan_baku_id' => $bahanBakus[0]->id,
                ], ['qty_needed' => 1.5]);
                \App\Models\ResepBom::firstOrCreate([
                    'barang_id' => $barang->id,
                    'bahan_baku_id' => $bahanBakus[1]->id,
                ], ['qty_needed' => 0.2]);
            } else {
                // Others need Tepung Terigu and Bumbu
                \App\Models\ResepBom::firstOrCreate([
                    'barang_id' => $barang->id,
                    'bahan_baku_id' => $bahanBakus[2]->id,
                ], ['qty_needed' => 1.0]);
                \App\Models\ResepBom::firstOrCreate([
                    'barang_id' => $barang->id,
                    'bahan_baku_id' => $bahanBakus[3]->id,
                ], ['qty_needed' => 0.1]);
            }
        }

        $customers = [];
        for ($i = 1; $i <= 20; $i++) {
            $customers[] = Customer::firstOrCreate(
                ['name' => fake()->name()],
                [
                    'market' => 'Pasar ' . fake()->city(),
                    'phone' => fake()->phoneNumber(),
                    'credit_limit' => fake()->numberBetween(1000000, 5000000),
                    'credit_period' => 30
                ]
            );
        }

        $salesCars = [
            SalesCar::firstOrCreate(['name' => 'Mobil Box 1', 'driver_name' => 'Angga']),
            SalesCar::firstOrCreate(['name' => 'Mobil Box 2', 'driver_name' => 'Ujang'])
        ];

        // 3. Transactions (Last 6 Months)
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();
        $currentDate = clone $startDate;

        // Loop through every ~3 days
        while ($currentDate <= $endDate) {
            
            // a. Supplier Mutasi (Barang Masuk) & Hutang
            if (fake()->boolean(30)) { // 30% chance every 3 days
                $supplier = fake()->randomElement($suppliers);
                $barang = fake()->randomElement($barangs);
                if ($barang) {
                    $qty = fake()->numberBetween(50, 200);
                    $price = $barang->price - 2000; // Modal lebih murah
                    $totalHutang = $qty * $price;
                    
                    SupplierMutasi::create([
                        'supplier_id' => $supplier->id,
                        'barang_id' => $barang->id,
                        'tanggal' => $currentDate->copy()->format('Y-m-d'),
                        'jenis_transaksi' => 'barang_masuk',
                        'qty_bal' => $qty,
                        'harga_satuan' => $price,
                        'total_hutang' => $totalHutang,
                    ]);
                    $barang->increment('stock', $qty);

                    // Create Hutang
                    $hutang = \App\Models\Hutang::create([
                        'supplier_name' => $supplier->name,
                        'amount' => $totalHutang,
                        'paid_amount' => 0,
                        'status' => 'belum_lunas',
                        'tanggal' => $currentDate->copy()->format('Y-m-d'),
                        'due_date' => $currentDate->copy()->addDays(30)->format('Y-m-d'),
                    ]);

                    // Simulate Hutang Payment (80% chance if older than 45 days)
                    if ($currentDate->diffInDays(Carbon::now()) > 45 && fake()->boolean(80)) {
                        \App\Models\HutangPayment::create([
                            'hutang_id' => $hutang->id,
                            'amount_paid' => $totalHutang,
                            'payment_date' => $currentDate->copy()->addDays(fake()->numberBetween(10, 30))->format('Y-m-d'),
                            'payment_method' => fake()->randomElement(['cash', 'transfer']),
                            'reference_no' => 'PAY-' . fake()->unique()->numberBetween(10000, 99999),
                        ]);
                    }
                }
            }

            // b. Production (every 7 days)
            if ($currentDate->dayOfWeek === Carbon::MONDAY) {
                $production = \App\Models\Produksi::create([
                    'kode_produksi' => 'PROD-' . $currentDate->format('Ymd') . '-' . fake()->unique()->numberBetween(100, 999),
                    'tanggal' => $currentDate->copy()->format('Y-m-d'),
                    'status' => 'draft',
                ]);

                // Create items
                $prodItemsCount = fake()->numberBetween(1, 3);
                $selectedBarangs = fake()->randomElements($barangs, $prodItemsCount);
                foreach ($selectedBarangs as $barang) {
                    \App\Models\ProduksiItem::create([
                        'produksi_id' => $production->id,
                        'barang_id' => $barang->id,
                        'qty_produced' => fake()->numberBetween(20, 100),
                    ]);
                }

                // Complete production to trigger decrement of BahanBaku and increment of Barang
                $production->update(['status' => 'selesai']);
            }

            // c. Sales (Invoice & Piutang)
            // Create 1-3 invoices per iteration
            $numInvoices = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $numInvoices; $i++) {
                $customer = fake()->randomElement($customers);
                $car = fake()->randomElement($salesCars);
                $tipe = fake()->randomElement(['pasar', 'agen', 'eceran_gudang']);
                $isKredit = fake()->boolean(80); // 80% kredit
                
                $invoice = Invoice::create([
                    'no_invoice' => 'INV-' . $currentDate->format('Ymd') . '-' . fake()->unique()->numberBetween(1000, 9999),
                    'customer_id' => $customer->id,
                    'sales_car_id' => $car->id,
                    'tipe_penjualan' => $tipe,
                    'tanggal' => $currentDate->copy()->format('Y-m-d'),
                    'payment_status' => $isKredit ? 'kredit' : 'lunas',
                    'total_price' => 0
                ]);

                $totalInvoice = 0;
                $numItems = fake()->numberBetween(1, 4);
                
                // Invoice Items
                for ($j = 0; $j < $numItems; $j++) {
                    $barang = fake()->randomElement($barangs);
                    $qty = fake()->numberBetween(5, 50);
                    $price = $barang->price;
                    $subtotal = $qty * $price;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'barang_id' => $barang->id,
                        'qty' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal
                    ]);
                    $totalInvoice += $subtotal;
                    
                    // Kurangi stok (karena terjual)
                    if ($barang->stock >= $qty) {
                        $barang->decrement('stock', $qty);
                    }

                    // Seed Canvas Muatan for this car & goods
                    if (fake()->boolean(50)) {
                        $muatan = \App\Models\CanvasMuatan::create([
                            'sales_car_id' => $car->id,
                            'barang_id' => $barang->id,
                            'qty_loaded' => $qty + fake()->numberBetween(5, 15),
                            'tanggal' => $currentDate->copy()->format('Y-m-d'),
                            'status' => 'draft',
                        ]);
                        $muatan->update(['status' => 'confirmed']);

                        // Leftover returned at end of day
                        $leftover = $muatan->qty_loaded - $qty;
                        if ($leftover > 0) {
                            \App\Models\CanvasRetur::create([
                                'sales_car_id' => $car->id,
                                'barang_id' => $barang->id,
                                'qty_returned' => $leftover,
                                'tanggal' => $currentDate->copy()->format('Y-m-d'),
                            ]);
                        }
                    }
                }
                
                $invoice->update(['total_price' => $totalInvoice]);

                if ($isKredit) {
                    $piutang = Piutang::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $totalInvoice,
                        'paid_amount' => 0,
                        'status' => 'belum_lunas',
                        'due_date' => $currentDate->copy()->addDays($customer->credit_period)->format('Y-m-d'),
                    ]);

                    // Simulate payment if the invoice is older than a few weeks
                    if ($currentDate->diffInDays(Carbon::now()) > 20) {
                        if (fake()->boolean(70)) { // 70% chance it's partially or fully paid
                            $paymentDate = $currentDate->copy()->addDays(fake()->numberBetween(7, 30));
                            if ($paymentDate <= $endDate) {
                                $isFull = fake()->boolean(50);
                                $paidCash = $isFull ? $totalInvoice : ($totalInvoice * 0.5); // Bayar 100% atau 50%
                                
                                $payment = PiutangPayment::create([
                                    'piutang_id' => $piutang->id,
                                    'amount_paid' => $paidCash,
                                    'payment_date' => $paymentDate->format('Y-m-d'),
                                    'payment_method' => fake()->randomElement(['cash', 'transfer']),
                                ]);
                                
                                $piutang->increment('paid_amount', $paidCash);

                                // Retur Barang Sisa (20% chance)
                                if (fake()->boolean(20)) {
                                    $returBarang = fake()->randomElement($barangs);
                                    $qtyRetur = fake()->numberBetween(1, 5);
                                    $potongan = $qtyRetur * $returBarang->price;
                                    
                                    PiutangPaymentBarangSisa::create([
                                        'piutang_payment_id' => $payment->id,
                                        'barang_id' => $returBarang->id,
                                        'qty_retur' => $qtyRetur,
                                        'harga_jual' => $returBarang->price,
                                        'subtotal_kredit' => $potongan
                                    ]);
                                    
                                    $piutang->increment('paid_amount', $potongan);
                                    $returBarang->increment('stock', $qtyRetur);
                                }

                                if ($piutang->paid_amount >= $piutang->amount) {
                                    $piutang->update(['status' => 'lunas']);
                                }
                            }
                        }
                    }
                }
            }

            $currentDate->addDays(3);
        }
    }
}
