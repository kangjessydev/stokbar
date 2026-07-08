<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Piutang;
use App\Models\SalesCar;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanBisnis extends Page
{

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel       = 'Laporan Bisnis';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan Sistem';
    protected static ?string $title                 = 'Laporan Bisnis';
    protected static ?int $navigationSort           = 99;

    protected static string $view = 'filament.pages.laporan-bisnis';

    // Filter state
    public ?string $periode    = 'bulan_ini';
    public ?string $date_from  = null;
    public ?string $date_to    = null;
    public ?int    $customer_id = null;
    public ?int    $sales_car_id = null;

    public function mount(): void
    {
        $this->periode   = 'bulan_ini';
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to   = now()->toDateString();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function getDateRange(): array
    {
        return match ($this->periode) {
            'hari_ini'    => [now()->toDateString(), now()->toDateString()],
            'minggu_ini'  => [now()->startOfWeek()->toDateString(), now()->toDateString()],
            'bulan_ini'   => [now()->startOfMonth()->toDateString(), now()->toDateString()],
            'bulan_lalu'  => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
            'custom'      => [$this->date_from ?? now()->startOfMonth()->toDateString(), $this->date_to ?? now()->toDateString()],
            default       => [now()->startOfMonth()->toDateString(), now()->toDateString()],
        };
    }

    // ── Data Laporan Penjualan ────────────────────────────────────────────────

    public function getLaporanPenjualan(): array
    {
        [$from, $to] = $this->getDateRange();

        $query = Invoice::whereBetween('tanggal', [$from, $to]);

        if ($this->customer_id) {
            $query->where('customer_id', $this->customer_id);
        }
        if ($this->sales_car_id) {
            $query->where('sales_car_id', $this->sales_car_id);
        }

        $invoices  = $query->with(['customer', 'salesCar', 'invoiceItems.barang'])->orderByDesc('tanggal')->get();
        $totalOmzet = $invoices->sum('total_price');
        $totalLunas = $invoices->where('payment_status', 'lunas')->sum('total_price');
        $totalKredit = $invoices->where('payment_status', 'kredit')->sum('total_price');

        return [
            'invoices'     => $invoices,
            'total_omzet'  => $totalOmzet,
            'total_lunas'  => $totalLunas,
            'total_kredit' => $totalKredit,
            'count'        => $invoices->count(),
            'date_from'    => $from,
            'date_to'      => $to,
        ];
    }

    public function getTopProdukPeriode(): \Illuminate\Support\Collection
    {
        [$from, $to] = $this->getDateRange();

        return InvoiceItem::select(
                'barangs.name',
                DB::raw('SUM(invoice_items.qty) as total_qty'),
                DB::raw('SUM(invoice_items.subtotal) as total_omzet')
            )
            ->join('barangs', 'barangs.id', '=', 'invoice_items.barang_id')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereBetween('invoices.tanggal', [$from, $to])
            ->groupBy('barangs.id', 'barangs.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    }

    // ── Data Laporan Piutang ──────────────────────────────────────────────────

    public function getLaporanPiutang(): array
    {
        $overdue = Piutang::where('status', 'belum_lunas')
            ->where('due_date', '<', now()->toDateString())
            ->with(['invoice.customer', 'invoice.salesCar'])
            ->orderBy('due_date')
            ->get();

        $segera = Piutang::where('status', 'belum_lunas')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with(['invoice.customer', 'invoice.salesCar'])
            ->orderBy('due_date')
            ->get();

        $belumLunas = Piutang::where('status', 'belum_lunas')
            ->where('due_date', '>', now()->addDays(7)->toDateString())
            ->with(['invoice.customer', 'invoice.salesCar'])
            ->orderBy('due_date')
            ->get();

        $lunas = Piutang::where('status', 'lunas')
            ->whereMonth('updated_at', now()->month)
            ->with(['invoice.customer'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return [
            'overdue'           => $overdue,
            'segera'            => $segera,
            'belum_lunas'       => $belumLunas,
            'lunas_bulan_ini'   => $lunas,
            'total_overdue'     => $overdue->sum(fn ($p) => $p->amount - $p->paid_amount),
            'total_segera'      => $segera->sum(fn ($p) => $p->amount - $p->paid_amount),
            'total_belum_lunas' => $belumLunas->sum(fn ($p) => $p->amount - $p->paid_amount),
        ];
    }

    // ── Data Laporan Stok ─────────────────────────────────────────────────────

    public function getLaporanStok(): \Illuminate\Support\Collection
    {
        return \App\Models\Barang::with('gudang')
            ->orderBy('stock', 'asc')
            ->get()
            ->map(function ($barang) {
                // Rata-rata penjualan 7 hari terakhir
                $avgPerDay = InvoiceItem::join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->where('invoice_items.barang_id', $barang->id)
                    ->where('invoices.tanggal', '>=', now()->subDays(7)->toDateString())
                    ->avg(DB::raw('invoice_items.qty / 7'));

                $hariHabis = ($avgPerDay && $avgPerDay > 0)
                    ? round($barang->stock / $avgPerDay)
                    : null;

                return (object) [
                    'name'       => $barang->name,
                    'gudang'     => $barang->gudang?->name ?? '-',
                    'stock'      => $barang->stock,
                    'unit'       => $barang->unit,
                    'hari_habis' => $hariHabis,
                    'status'     => $barang->stock <= 10 ? 'kritis' : ($barang->stock <= 50 ? 'menipis' : 'aman'),
                ];
            });
    }

    // ── Data Laporan Kinerja Sales ────────────────────────────────────────────

    public function getLaporanKinerjaSales(): \Illuminate\Support\Collection
    {
        [$from, $to] = $this->getDateRange();

        return SalesCar::withCount(['invoices as total_invoice' => function ($q) use ($from, $to) {
                $q->whereBetween('tanggal', [$from, $to]);
            }])
            ->withSum(['invoices as total_omzet' => function ($q) use ($from, $to) {
                $q->whereBetween('tanggal', [$from, $to]);
            }], 'total_price')
            ->orderByDesc('total_omzet')
            ->get()
            ->map(function ($car) {
                $retur = \App\Models\CanvasRetur::where('sales_car_id', $car->id)
                    ->sum('qty_returned');
                return (object) [
                    'name'          => $car->name,
                    'driver'        => $car->driver_name,
                    'total_invoice' => $car->total_invoice ?? 0,
                    'total_omzet'   => $car->total_omzet ?? 0,
                    'total_retur'   => $retur,
                ];
            });
    }

    // ── Filter Choices ────────────────────────────────────────────────────────

    public function getCustomerOptions(): array
    {
        return Customer::pluck('name', 'id')->toArray();
    }

    public function getSalesCarOptions(): array
    {
        return SalesCar::select(DB::raw("name || ' (' || driver_name || ')' as label"), 'id')
            ->pluck('label', 'id')
            ->toArray();
    }

    public function updatedPeriode(): void
    {
        if ($this->periode !== 'custom') {
            [$from, $to]    = $this->getDateRange();
            $this->date_from = $from;
            $this->date_to   = $to;
        }
    }
}
